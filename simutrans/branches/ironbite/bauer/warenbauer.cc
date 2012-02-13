/*
 * Copyright (c) 1997 - 2002 Hj. Malthaner
 *
 * This file is part of the Simutrans project under the artistic licence.
 * (see licence.txt)
 */

#include "../simdebug.h"
#include "../besch/ware_besch.h"
#include "../besch/spezial_obj_tpl.h"
#include "../simware.h"
#include "../simcolor.h"
#include "warenbauer.h"
#include "../dataobj/translator.h"


stringhashtable_tpl<const freight_desc_t *> freight_builder_t::besch_names;

vector_tpl<freight_desc_t *> freight_builder_t::waren;

uint8 freight_builder_t::max_catg_index = 0;

const freight_desc_t *freight_builder_t::passagiere = NULL;
const freight_desc_t *freight_builder_t::post = NULL;
const freight_desc_t *freight_builder_t::nichts = NULL;

freight_desc_t *freight_builder_t::load_passagiere = NULL;
freight_desc_t *freight_builder_t::load_post = NULL;
freight_desc_t *freight_builder_t::load_nichts = NULL;

static spezial_obj_tpl<freight_desc_t> spezial_objekte[] = {
	{ &freight_builder_t::passagiere,    "Passagiere" },
	{ &freight_builder_t::post,	    "Post" },
	{ &freight_builder_t::nichts,	    "None" },
	{ NULL, NULL }
};


bool freight_builder_t::alles_geladen()
{
	if(!::alles_geladen(spezial_objekte)) {
		return false;
	}

	/**
	* Put special items in front:
	* Volker Meyer
	*/
	waren.insert_at(0,load_nichts);
	waren.insert_at(0,load_post);
	waren.insert_at(0,load_passagiere);

	if(waren.get_count()>=255) {
		dbg->fatal("freight_builder_t::alles_geladen()","Too many different goods %i>255",waren.get_count()-1 );
	}

	// assign indexes
	for(  uint8 i=3;  i<waren.get_count();  i++  ) {
		waren.get(i)->ware_index = i;
	}

	// now assign unique category indexes for unique categories
	max_catg_index = 0;
	// first assign special freight (which always needs an own category)
	for( unsigned i=0;  i<waren.get_count();  i++  ) {
		if(waren.get(i)->get_catg()==0) {
			waren.get(i)->catg_index = max_catg_index++;
		}
	}
	// mapping of waren_t::catg to catg_index, map[catg] = catg_index
	uint8 map[255] = {0};

	for(  uint8 i=0;  i<waren.get_count();  i++  ) {
		const uint8 catg = waren.get(i)->get_catg();
		if(  catg > 0  ) {
			if(  map[catg] == 0  ) { // We didn't found this category yet -> just create new index.
				map[catg] = max_catg_index++;
			}
			waren.get(i)->catg_index = map[catg];
		}
	}

	// init the lookup table in freight_t
	for( unsigned i=0;  i<256;  i++  ) {
		if(i>=waren.get_count()) {
			// these entries will be never looked at;
			// however, if then this will generate an error
			freight_t::index_to_besch[i] = NULL;
		}
		else {
			assert(waren.get(i)->get_index()==i);
			freight_t::index_to_besch[i] = waren.get(i);
			if(waren.get(i)->color==255) {
				waren.get(i)->color = 16+4+((i-2)*8)%207;
			}
		}
	}
	// passenger and good colors
	if(waren.get(0)->color==255) {
		waren.get(0)->color = COL_GREY3;
	}
	if(waren.get(1)->color==255) {
		waren.get(1)->color = COL_YELLOW;
	}
	// none should never be loaded to something ...
	// however, some place do need the dummy ...
	freight_t::index_to_besch[2] = NULL;

	DBG_MESSAGE("freight_builder_t::alles_geladen()","total goods %i, different kind of categories %i", waren.get_count(), max_catg_index );

	return true;
}


static bool compare_ware_besch(const freight_desc_t* a, const freight_desc_t* b)
{
	int diff = strcmp(a->get_name(), b->get_name());
	return diff < 0;
}

bool freight_builder_t::register_besch(freight_desc_t *besch)
{
	besch->value = besch->base_value;
	::register_besch(spezial_objekte, besch);
	// avoid duplicates with same name
	freight_desc_t *old_besch = const_cast<freight_desc_t *>(besch_names.get(besch->get_name()));
	if(  old_besch  ) {
		dbg->warning( "freight_builder_t::register_besch()", "Object %s was overlaid by addon!", besch->get_name() );
		besch_names.remove(besch->get_name());
		waren.remove( old_besch );
	}
	besch_names.put(besch->get_name(), besch);

	if(besch==passagiere) {
		besch->ware_index = INDEX_PAS;
		load_passagiere = besch;
	} else if(besch==post) {
		besch->ware_index = INDEX_MAIL;
		load_post = besch;
	} else if(besch != nichts) {
		waren.insert_ordered(besch,compare_ware_besch);
	}
	else {
		load_nichts = besch;
		besch->ware_index = INDEX_NONE;
	}
	return true;
}


const freight_desc_t *freight_builder_t::get_info(const char* name)
{
	const freight_desc_t *ware = besch_names.get(name);
	if(  ware==NULL  ) {
		ware = besch_names.get(translator::compatibility_name(name));
	}
	return ware;
}


const freight_desc_t *freight_builder_t::get_info_catg(const uint8 catg)
{
	if(catg>0) {
		for(unsigned i=0;  i<get_waren_anzahl();  i++  ) {
			if(waren.get(i)->catg==catg) {
				return waren.get(i);
			}
		}
	}
	dbg->warning("freight_builder_t::get_info()", "No info for good catg %d available, set to passengers", catg);
	return waren.get(0);
}


const freight_desc_t *freight_builder_t::get_info_catg_index(const uint8 catg_index)
{
	for(unsigned i=0;  i<get_waren_anzahl();  i++  ) {
		if(waren.get(i)->get_catg_index()==catg_index) {
			return waren.get(i);
		}
	}
	// return none as default
	return waren.get(2);
}


// adjuster for dummies ...
void freight_builder_t::set_multiplier(sint32 multiplier)
{
//DBG_MESSAGE("freight_builder_t::set_multiplier()","new factor %i",multiplier);
	for(unsigned i=0;  i<get_waren_anzahl();  i++  ) {
		sint32 long_base_value = waren.get(i)->base_value;
		waren.get(i)->value = (uint16)((long_base_value*multiplier)/1000l);
	}
}
