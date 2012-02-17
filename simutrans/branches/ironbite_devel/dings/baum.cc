/*
 * Copyright (c) 1997 - 2001 Hj. Malthaner
 *
 * This file is part of the Simutrans project under the artistic licence.
 * (see licence.txt)
 */

#include <math.h>

#include "../besch/baum_besch.h"

#include "../simdebug.h"
#include "../simworld.h"
#include "../simimg.h"
#include "../player/simplay.h"
#include "../simtools.h"
#include "../simtypes.h"

#include "../boden/grund.h"


#include "../dings/groundobj.h"

#include "../utils/cbuffer_t.h"

#include "../dataobj/loadsave.h"
#include "../dataobj/translator.h"
#include "../dataobj/umgebung.h"
#include "../dataobj/freelist.h"


#include "baum.h"

static const uint8 baum_bild_alter[12] =
{
	0,1,2,3,3,3,3,3,3,4,4,4
};

PLAYER_COLOR_VAL tree_t::outline_color = 0;

// quick lookup of an image, assuming always five seaons and five ages
// missing images hve just identical entires
static image_id baumtype_to_bild[256][5*5];


// static for administration
static stringhashtable_tpl<const baum_besch_t *> besch_names;


/*
 * Diese Tabelle ermoeglicht das Auffinden dient zur Auswahl eines Baumtypen
 */
static vector_tpl<const baum_besch_t *> tree_typen;

// index vector into baumtypen, accessible per climate
static vector_tpl<weighted_vector_tpl<uint32> *> tree_typen_per_climate;

/*
 * Diese Tabelle ermoeglicht das Auffinden einer Beschreibung durch ihren Namen
 */
const vector_tpl<const baum_besch_t *> * tree_t::get_all_besch() { return &tree_typen; }


const baum_besch_t *tree_t::find_tree( const char *tree_name ) { return tree_typen.is_empty() ? NULL : besch_names.get(tree_name); }

const baum_besch_t * tree_t::random_tree_for_climate(climate cl)
{
	uint16 b = random_tree_for_climate_intern(cl);  
	return b!=0xFFFF ? tree_typen.get(b) : 0; 
}

int tree_t::get_anzahl_besch() 
{
	return tree_typen.get_count(); 
}
	

// distributes trees on a map
void tree_t::distribute_trees(karte_t *welt, int dichte)
{
	// now we can proceed to tree planting routine itself
	// best forests results are produced if forest size is tied to map size -
	// but there is some nonlinearity to ensure good forests on small maps
	settings_t const& s             = welt->get_settings();
	sint32     const  x             = welt->get_groesse_x();
	sint32     const  y             = welt->get_groesse_y();
	unsigned   const t_forest_size  = (unsigned)pow(((double)x * (double)y), 0.25) * s.get_forest_base_size() / 11 + (x + y) / (2 * s.get_forest_map_size_divisor());
	uint8      const c_forest_count = (unsigned)pow(((double)x * (double)y), 0.5)  / s.get_forest_count_divisor();

DBG_MESSAGE("verteile_baeume()","creating %i forest",c_forest_count);
	for (uint8 c1 = 0 ; c1 < c_forest_count ; c1++) {
		// to have same execution order for simrand
		koord const start = koord::koord_random(x, y);
		koord const size  = koord(t_forest_size,t_forest_size) + koord::koord_random(t_forest_size, t_forest_size);
		create_forest( welt, start, size );
	}

	fill_trees(welt, dichte);
}


/*************************** first the static function for the tree_t and baum_besch_t administration ***************/



// total number of trees
// the same for a certain climate
int tree_t::get_anzahl_besch(climate cl)
{
	return tree_typen_per_climate.get(cl)->get_count();
}


/**
 * tree planting function - it takes care of checking suitability of area
 */
uint8 tree_t::plant_tree_on_coordinate(karte_t * welt, 
				       const koord pos, 
				       const uint8 maximum_count, 
				       const uint8 count)
{
	grund_t * const gr = welt->lookup_kartenboden(pos);
	if(gr) 
	{
		if(get_anzahl_besch(welt->get_climate(gr->get_pos().z))>0  &&
			gr->ist_natur()  &&
			gr->get_top()<maximum_count)
		{
			const ding_t * const ding = gr->obj_bei(0);
			
			if(ding) 
			{
				switch(ding->get_typ()) 
				{
					case ding_t::wolke:
					case ding_t::aircraft:
					case ding_t::baum:
					case ding_t::leitung:
					case ding_t::label:
					case ding_t::zeiger:
						// ok to built here
						break;
					case ding_t::groundobj:
						if(((const groundobj_t *)ding)->get_besch()->can_built_trees_here()) 
						{
							break;
						}
						// leave these (and all other empty)
					default:
						return 0;
				}
			}
			
			const uint8 count_planted = min( maximum_count - gr->get_top(), count);
			for (uint8 i=0; i<count_planted; i++) 
			{
				//plants the tree(s)
				gr->obj_add(new tree_t(welt, gr->get_pos())); 
			}
			return count_planted;
		}
	}
	return 0;
}


/**
 * tree planting function - it takes care of checking suitability of area
 */
bool tree_t::plant_tree_on_coordinate(karte_t * welt, koord pos, const baum_besch_t *besch, const bool check_climate, const bool random_age )
{
	// none there
	if(  besch_names.is_empty()  ) {
		return false;
	}
	grund_t *gr = welt->lookup_kartenboden(pos);
	if(gr) {
		if( gr->ist_natur()  &&
			gr->get_top() < welt->get_settings().get_max_no_of_trees_on_square() &&
			(!check_climate  ||  besch->is_allowed_climate(welt->get_climate(gr->get_hoehe())))
			)
		{
			if(gr->get_top()>0) {
				switch(gr->obj_bei(0)->get_typ()) {
					case ding_t::wolke:
					case ding_t::aircraft:
					case ding_t::baum:
					case ding_t::leitung:
					case ding_t::label:
					case ding_t::zeiger:
						// ok to built here
						break;
					case ding_t::groundobj:
						if(((groundobj_t *)(gr->obj_bei(0)))->get_besch()->can_built_trees_here()) {
							break;
						}
						// leave these (and all other empty)
					default:
						return false;
				}
			}
			tree_t *b = new tree_t(welt, gr->get_pos(), besch); //plants the tree
			if(random_age) {
				b->geburt = welt->get_current_month() - simrand(400);
			}
			gr->obj_add( b );
			return true; //tree was planted - currently unused value is not checked
		}
	}
	return false;
}


uint32 tree_t::create_forest(karte_t *welt, koord new_center, koord wh )
{
	// none there
	if(  besch_names.is_empty()  ) {
		return 0;
	}
	const sint16 xpos_f = new_center.x;
	const sint16 ypos_f = new_center.y;
	uint32 number_of_new_trees = 0;
	for( sint16 j = 0; j < wh.x; j++) {
		for( sint16 i = 0; i < wh.y; i++) {

			const sint32 x_tree_pos = (j-(wh.x>>1));
			const sint32 y_tree_pos = (i-(wh.y>>1));

			const uint64 distance = 1 + ((uint64) sqrt( ((double)x_tree_pos*x_tree_pos*(wh.y*wh.y) + (double)y_tree_pos*y_tree_pos*(wh.x*wh.x))));
			const uint32 tree_probability = (uint32)( ( 8 * (uint32)((wh.x*wh.x)+(wh.y*wh.y)) ) / distance );

			if (tree_probability < 38) {
				continue;
			}

			uint8 number_to_plant = 0;
			uint8 const max_trees_here = min(welt->get_settings().get_max_no_of_trees_on_square(), (tree_probability - 38 + 1) / 2);
			for (uint8 c2 = 0 ; c2<max_trees_here; c2++) {
				const uint32 rating = simrand(10) + 38 + c2*2;
				if (rating < tree_probability ) {
					number_to_plant++;
				}
			}

			number_of_new_trees += tree_t::plant_tree_on_coordinate(welt, koord((sint16)(xpos_f + x_tree_pos), (sint16)(ypos_f + y_tree_pos)), welt->get_settings().get_max_no_of_trees_on_square(), number_to_plant);
		}
	}
	return number_of_new_trees;
}


void tree_t::fill_trees(karte_t *welt, int dichte)
{
	// none there
	if(  besch_names.is_empty()  ) {
		return;
	}
DBG_MESSAGE("verteile_baeume()","distributing single trees");
	koord pos;
	for(  pos.y=0;  pos.y<welt->get_groesse_y();  pos.y++  ) {
		for(  pos.x=0;  pos.x<welt->get_groesse_x();  pos.x++  ) {
			grund_t *gr = welt->lookup_kartenboden(pos);
			if(gr->get_top() == 0  &&  gr->get_typ() == grund_t::boden)  {
				// plant spare trees, (those with low preffered density) or in an entirely tree climate
				uint16 cl = 1<<welt->get_climate(gr->get_hoehe());
				settings_t const& s = welt->get_settings();
				if ((cl & s.get_no_tree_climates()) == 0 && ((cl & s.get_tree_climates()) != 0 || simrand(s.get_forest_inverse_spare_tree_density() * dichte) < 100)) {
					plant_tree_on_coordinate(welt, pos, 1, 1);
				}
			}
		}
	}
}


static bool compare_baum_besch(const baum_besch_t* a, const baum_besch_t* b)
{
	/* Gleiches Level - wir f�hren eine k�nstliche, aber eindeutige Sortierung
	 * �ber den Namen herbei. */
	return strcmp(a->get_name(), b->get_name())<0;
}


bool tree_t::alles_geladen()
{
	if (besch_names.is_empty()) {
		DBG_MESSAGE("tree_t", "No trees found - feature disabled");
		tree_typen.append( NULL );
	}
	else 
	{

		stringhashtable_iterator_tpl<const baum_besch_t*> iter(besch_names);
		while(  iter.next()  ) {
			tree_typen.insert_ordered( iter.get_current(), compare_baum_besch );
			if(  tree_typen.get_count()==254  ) {
				dbg->error( "tree_t::alles_geladen()", "Maximum tree count exceeded! (max 254 instead of %i)", besch_names.get_count() );
				break;
			}
		}

		tree_typen_per_climate.clear();

		// fill the vector with zeros
		for (uint8 j=0; j<MAX_CLIMATES; j++) 
		{
			tree_typen_per_climate.append( new weighted_vector_tpl<uint32>() );
		}

		// clear cache
		memset( baumtype_to_bild, -1, lengthof(baumtype_to_bild) );
		// now register all trees for all fitting climates
		for(  uint32 typ=0;  typ<tree_typen.get_count();  typ++  ) {
			// add this tree to climates
			for(  uint8 j=0;  j<MAX_CLIMATES;  j++  ) {
				if(  tree_typen.get(typ)->is_allowed_climate((climate)j)  ) {
					tree_typen_per_climate.get(j)->append(typ, tree_typen.get(typ)->get_distribution_weight(), /*extend weighted vector if necess by*/ 4 );
				}
			}
			// create cache images
			for(  uint8 season=0;  season<5;  season++  ) {
				for(  uint8 age=0;  age<5;  age++  ) {
					uint8 use_season = 0;
					const sint16 seasons = tree_typen.get(typ)->get_seasons();
					if(seasons>1) {
						use_season = season;
						// three possibilities
						if(  seasons<4  ) {
							// only summer and winter => seaons 2 and 5 with winter image
							use_season = (season==2  ||  season==5);
						}
						else if(  seasons==4  ) {
							// all there, but the snowy special image
							if(  season==4  ) {
								// snowy winter graphics (3 or 5)
								use_season = 2;
							}
						}
					}
					baumtype_to_bild[typ][season*5+age] = tree_typen.get(typ)->get_bild_nr( use_season, age );
				}
			}
		}
	}
	return true;
}


bool tree_t::register_besch(baum_besch_t *besch)
{
	// avoid duplicates with same name
	if(besch_names.remove(besch->get_name())) {
		dbg->warning( "tree_t::register_besch()", "Object %s was overlaid by addon!", besch->get_name() );
	}
	besch_names.put(besch->get_name(), besch );
	return true;
}


// calculates tree position on a tile
// takes care of slopes
void tree_t::calc_off(uint8 slope, sint8 x_, sint8 y_)
{
	sint16 random = (sint16)( get_pos().x + get_pos().y + get_pos().z + slope + (long)this );
	// point on tile (imaginary origin at sw corner, x axis: north, y axis: east
	sint16 x = x_==-128 ? (random + baumtype) & 31  : x_;
	sint16 y = y_==-128 ? (random + get_age()) & 31 : y_;

	// the last bit has to be the same
	y = y ^ (x&1);

	// bilinear interpolation of tile height
	uint32 zoff_ = ((corner3(slope)*x*y + corner4(slope)*x*(32-y)
	                 + corner2(slope)*(32-x)*y + corner1(slope)*(32-x)*(32-y)) * TILE_HEIGHT_STEP) / (32*32);
	// now zoff between 0 and TILE_HEIGHT_STEP-1
	zoff = zoff_ < (uint32)TILE_HEIGHT_STEP ? zoff_ : TILE_HEIGHT_STEP-1u;

	// xoff must be even
	set_xoff( x + y - 32 );
	set_yoff( (y - x)/2 - zoff);
}


void tree_t::recalc_off()
{
	// reconstruct position on tile
	const sint8 xoff = get_xoff() + 32;       // = x+y
	const sint8 yoff = 2*(get_yoff() + zoff); // = y-x
	sint8 x = (xoff - yoff) / 2;
	sint8 y = (xoff + yoff) / 2;
	calc_off(x, y);
}


void tree_t::rotate90()
{
	// cant use ding_t::rotate90 to rotate offsets as it rotates them only if xoff!=0
	sint8 old_yoff = get_yoff() + zoff;
	sint8 old_xoff = get_xoff();
	// rotate position
	ding_t::rotate90();
	// .. and the offsets
	set_xoff( -2 * old_yoff );
	set_yoff( old_xoff/2 - zoff);
}


// actually calculates onyl the season
void tree_t::calc_bild()
{
	const sint16 seasons = get_besch()->get_seasons();

	season = 0;
	if(seasons>1) {
		// two possibilities
		if(seasons<4) {
			// only summer and winter
			season = welt->get_snowline()<=get_pos().z;
		}
		else {
			// summer autumn winter spring
			season = welt->get_jahreszeit();
			if(welt->get_snowline()<=get_pos().z) {
				// change to winter
				if(seasons==5) {
					// snowy winter graphics (3 or 5)
					season = 4;
				}
				else {
					// no special winter graphics
					season = 2;
				}
			}
			else if(welt->get_snowline()<=get_pos().z+Z_TILE_STEP  &&  season==0) {
				// snowline crossing in summer
				// so at least some weeks spring/autumn
				season = welt->get_last_month() <=5 ? 3 : 1;
			}
		}
	}
}


image_id tree_t::get_bild() const
{
	if(  umgebung_t::hide_trees  ) {
		if(  umgebung_t::hide_with_transparency  ) {
			// we need the real age for transparency or real image
			return IMG_LEER;
		}
		else {
			return baumtype_to_bild[ baumtype ][ season*5 ];
		}
	}
	const uint8 baum_alter = baum_bild_alter[min(get_age()>>6, 11u)];
	return baumtype_to_bild[ baumtype ][ season*5 + baum_alter ];
//	return get_besch()->get_bild_nr( season, baum_alter );
}


// image which transparent outline is used
image_id tree_t::get_outline_bild() const
{
	const uint8 baum_alter = baum_bild_alter[min(get_age()>>6, 11u)];
	return baumtype_to_bild[ baumtype ][ season*5 + baum_alter ];
//	return get_besch()->get_bild_nr( season, baum_alter );
}


uint32 tree_t::get_age() const
{
	return  welt->get_current_month()-geburt;
}


/* also checks for distribution values
 * @author prissi
 */
uint16 tree_t::random_tree_for_climate_intern(climate cl)
{
	// now weight their distribution
	weighted_vector_tpl<uint32> const * t = tree_typen_per_climate.get(cl);
	return t->is_empty() ? 0xFFFF : pick_any_weighted(t);
}


tree_t::tree_t(karte_t *welt, loadsave_t *file) : ding_t(welt)
{
	season = 0;
	geburt = welt->get_current_month();
	baumtype = 0;
	rdwr(file);
}


tree_t::tree_t(karte_t *welt, koord3d pos) : ding_t(welt, pos)
{
	// Hajo: auch aeltere Baeume erzeugen
	geburt = welt->get_current_month() - simrand(703);
	baumtype = (uint8)random_tree_for_climate_intern(welt->get_climate(pos.z));
	season = 0;
	calc_off( welt->lookup( get_pos())->get_grund_hang() );
	calc_bild();
}


tree_t::tree_t(karte_t *welt, koord3d pos, uint8 type, sint32 age, uint8 slope ) : ding_t(welt, pos)
{
	geburt = welt->get_current_month()-age;
	baumtype = type;
	season = 0;
	calc_off( slope );
	calc_bild();
}


tree_t::tree_t(karte_t *welt, koord3d pos, const baum_besch_t *besch) : ding_t(welt, pos)
{
	geburt = welt->get_current_month();
	baumtype = tree_typen.index_of(besch);
	season = 0;
	calc_off( welt->lookup( get_pos())->get_grund_hang() );
	calc_bild();
}


bool tree_t::saee_baum()
{
	// Hajo: try to spawn a new tree in an area 7x7 tiles

	// to have same execution order for simrand
	const sint16 sx = simrand(7)-3;
	const sint16 sy = simrand(7)-3;
	const koord k = get_pos().get_2d() + koord(sx,sy);

	return plant_tree_on_coordinate(welt, k, tree_typen.get(baumtype), true, false);
}


/**
 * This routine should be as fast as possible, because trees are nearly
 * the most common object on a map 
 * @author Hj. Malthaner
 */
bool tree_t::check_season(const long month)
{
	// take care of birth/death and seasons
	long alter = (month - geburt);

	// attention: integer underflow (geburt is 16bit, month 32bit);
	while (alter < 0) 
	{
		alter += 0x7fff;
	}

	// Hajo: Trees will try three times to seed new trees.
	if((alter >= 412 && alter <= 415)  ||
	   (alter >= 512 && alter <= 515)  ||
	   (alter >= 612 && alter <= 615)) 
	{
		// only in this months a tree can span new trees
		// only 1-3 trees will be planted....
		const int c_plant_tree_max = 1 + simrand(welt->get_settings().get_max_no_of_trees_on_square());

		for(int n=0; n<c_plant_tree_max; n++)
		{
			saee_baum();
		}
		
		// Hajo: we make the tree four months older now to avoid
		// a second spawning of new trees during this seeding season
		geburt -= 4;
	}

	// tree will die after 704 month (i.e. 58 years 8 month)
	if(alter >= 704) 
	{
		mark_image_dirty(get_bild(), 0);
		return false;
	}

	calc_bild();

	return true;
}



void tree_t::rdwr(loadsave_t *file)
{
	xml_tag_t d( file, "tree_t" );

	ding_t::rdwr(file);

	sint32 alter = (welt->get_current_month() - geburt)<<18;
	file->rdwr_long(alter);

	// after loading, calculate new
	geburt = welt->get_current_month() - (alter>>18);

	if(file->is_loading()) {
		char buf[128];
		file->rdwr_str(buf, lengthof(buf));
		const baum_besch_t *besch = besch_names.get(buf);
		if(  tree_typen.is_contained(besch)  ) {
			baumtype = tree_typen.index_of( besch );
		}
		else {
			// replace with random tree
			baumtype = simrand(tree_typen.get_count());
		}
	}
	else {
		const char *c = get_besch()->get_name();
		file->rdwr_str(c);
	}

	// z-offset
	if(file->get_version() > 111000) {
		uint8 zoff_ = zoff;
		file->rdwr_byte(zoff_);
		zoff = zoff_;
	}
	else {
		// correct z-offset
		if(file->is_loading()) {
			// this will trigger recalculation of offset in laden_abschliessen()
			// we cant call calc_off() since this->pos is still invalid
			set_xoff(-128);
		}
	}
}


void tree_t::laden_abschliessen()
{
	if(get_xoff()==-128) {
		calc_off(welt->lookup( get_pos())->get_grund_hang());
	}
}


/**
 * �ffnet ein neues Beobachtungsfenster f�r das Objekt.
 * @author Hj. Malthaner
 */
void tree_t::zeige_info()
{
	if(umgebung_t::tree_info) {
		ding_t::zeige_info();
	}
}


void tree_t::recalc_outline_color() 
{
	outline_color = (umgebung_t::hide_trees  &&  umgebung_t::hide_with_transparency) ? (TRANSPARENT25_FLAG | OUTLINE_FLAG | COL_BLACK) : 0; 
}

/**
 * @return Einen Beschreibungsstring f�r das Objekt, der z.B. in einem
 * Beobachtungsfenster angezeigt wird.
 * @author Hj. Malthaner
 */
void tree_t::info(cbuffer_t & buf) const
{
	ding_t::info(buf);

	buf.append( translator::translate(get_besch()->get_name()) );
	buf.append( "\n" );
	int age = welt->get_current_month() - geburt;
	buf.printf( translator::translate("%i years %i months old."), age/12, (age%12) );
}



void tree_t::entferne(spieler_t *sp)
{
	spieler_t::accounting(sp, welt->get_settings().cst_remove_tree, get_pos().get_2d(), COST_CONSTRUCTION);
	mark_image_dirty( get_bild(), 0 );
}

const baum_besch_t* tree_t::get_besch() const 
{
	return tree_typen.get(baumtype); 
}



void *tree_t::operator new(size_t /*s*/)
{
	return freelist_t::gimme_node(sizeof(tree_t));
}



void tree_t::operator delete(void *p)
{
	freelist_t::putback_node(sizeof(tree_t),p);
}
