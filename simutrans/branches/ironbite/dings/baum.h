/*
 * Copyright (c) 1997 - 2001 Hj. Malthaner
 *
 * This file is part of the Simutrans project under the artistic licence.
 * (see licence.txt)
 */

#ifndef dings_baum_h
#define dings_baum_h

#include <string>
#include "../tpl/stringhashtable_tpl.h"
#include "../tpl/vector_tpl.h"
#include "../tpl/weighted_vector_tpl.h"
#include "../besch/baum_besch.h"
#include "../simcolor.h"
#include "../simdings.h"
#include "../dataobj/umgebung.h"

/**
 * B�ume in Simutrans.
 * @author Hj. Malthaner
 */
class tree_t : public ding_t
{
private:
	static PLAYER_COLOR_VAL outline_color;

	// month of birth
	uint16 geburt;

	// type of tree (was 9 but for more compact saves now only 254 different ree types are allowed)
	uint8 baumtype;

	uint8 season:3;

	// z-offset, max TILE_HEIGHT_STEP ie 4 bits
	uint8 zoff:4;
	// one bit free ;)

	// static for administration
	static stringhashtable_tpl<const baum_besch_t *> besch_names;
	static vector_tpl<const baum_besch_t *> tree_typen;
	static vector_tpl<weighted_vector_tpl<uint32> > tree_typen_per_climate;

	bool saee_baum();

	/**
	 * calculate offsets for new trees
	 */
	void calc_off(uint8 slope, sint8 x=-128, sint8 y=-128);

	static uint16 random_tree_for_climate_intern(climate cl);

	static uint8 plant_tree_on_coordinate(karte_t *welt, koord pos, const uint8 maximum_count, const uint8 count);

public:
	// only the load save constructor should be called outside
	// otherwise I suggest use the plant tree function (see below)
	tree_t(karte_t *welt, loadsave_t *file);
	tree_t(karte_t *welt, koord3d pos);
	tree_t(karte_t *welt, koord3d pos, uint8 type, sint32 age, uint8 slope );
	tree_t(karte_t *welt, koord3d pos, const baum_besch_t *besch);

	void rdwr(loadsave_t *file);

	void laden_abschliessen();

	image_id get_bild() const;

	// hide trees eventually with transparency
	PLAYER_COLOR_VAL get_outline_colour() const { return outline_color; }
	image_id get_outline_bild() const;

	static void recalc_outline_color() { outline_color = (umgebung_t::hide_trees  &&  umgebung_t::hide_with_transparency) ? (TRANSPARENT25_FLAG | OUTLINE_FLAG | COL_BLACK) : 0; }

	/**
	 * Berechnet Alter und Bild abh�ngig vom Alter
	 * @author Hj. Malthaner
	 */
	void calc_bild();

	void rotate90();

	/**
	 * re-calculate z-offset if slope of the tile has changed
	 */
	void recalc_off();

	const char *get_name() const {return "Baum";}
	typ get_typ() const { return baum; }

	/**
	 * This routine should be as fast as possible, because trees are nearly
	 * the most common object on a map 
	 * @author Hj. Malthaner
	 */
	bool check_season(const long delta_t);

	void zeige_info();

	void info(cbuffer_t & buf) const;

	void entferne(spieler_t *sp);

	void * operator new(size_t s);
	void operator delete(void *p);

	const baum_besch_t* get_besch() const { return tree_typen[baumtype]; }
	uint16 get_besch_id() const { return baumtype; }
	uint32 get_age() const;

	// static functions to handle trees

	// distributes trees on a map
	static void distribute_trees(karte_t *welt, int dichte);

	static bool plant_tree_on_coordinate(karte_t *welt, koord pos, const baum_besch_t *besch, const bool check_climate, const bool random_age );

	static bool register_besch(baum_besch_t *besch);
	static bool alles_geladen();

	static uint32 create_forest(karte_t *welt, koord center, koord size );
	static void fill_trees(karte_t *welt, int dichte);


	// return list to beschs
	static const vector_tpl<const baum_besch_t *> *get_all_besch() { return &tree_typen; }

	static const baum_besch_t *random_tree_for_climate(climate cl) { uint16 b = random_tree_for_climate_intern(cl);  return b!=0xFFFF ? tree_typen[b] : NULL; }

	static const baum_besch_t *find_tree( const char *tree_name ) { return tree_typen.empty() ? NULL : besch_names.get(tree_name); }

	static int get_anzahl_besch() { return tree_typen.get_count(); }
	static int get_anzahl_besch(climate cl);

};

#endif
