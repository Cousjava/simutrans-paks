/*
 * Copyright (c) 1997 - 2001 Hj. Malthaner
 *
 * This file is part of the Simutrans project under the artistic license.
 * (see license.txt)
 */

#include <stdio.h>
#include <string.h>

#include "simmem.h"
#include "simdebug.h"
#include "simfab.h"
#include "simhalt.h"
#include "simtypes.h"
#include "simware.h"
#include "simworld.h"
#include "dataobj/loadsave.h"
#include "dataobj/koord.h"

#include "besch/ware_besch.h"
#include "bauer/warenbauer.h"



const freight_desc_t *freight_t::index_to_besch[256];



freight_t::freight_t() : ziel(), zwischenziel(), zielpos(-1, -1)
{
	menge = 0;
	index = 0;
	to_factory = 0;
}


freight_t::freight_t(const freight_desc_t *wtyp) : ziel(), zwischenziel(), zielpos(-1, -1)
{
	menge = 0;
	index = wtyp->get_index();
	to_factory = 0;
}

freight_t::freight_t(karte_t *welt,loadsave_t *file)
{
	rdwr(welt,file);
}


void freight_t::set_besch(const freight_desc_t* type)
{
	index = type->get_index();
}



void freight_t::rdwr(karte_t *welt,loadsave_t *file)
{
	sint32 amount = menge;
	file->rdwr_long(amount);
	menge = amount;
	if(file->get_version()<99008) {
		sint32 max;
		file->rdwr_long(max);
	}

	if(  file->get_version()>=110005  ) {
		uint8 factory_going = to_factory;
		file->rdwr_byte(factory_going);
		to_factory = factory_going;
	}
	else if(  file->is_loading()  ) {
		to_factory = 0;
	}

	uint8 catg=0;
	if(file->get_version()>=88005) {
		file->rdwr_byte(catg);
	}

	if(file->is_saving()) {
		const char *typ = NULL;
		typ = get_besch()->get_name();
		file->rdwr_str(typ);
	}
	else {
		char typ[256];
		file->rdwr_str(typ, lengthof(typ));
		const freight_desc_t *type = freight_builder_t::get_info(typ);
		if(type==NULL) {
			dbg->warning("freight_t::rdwr()","unknown ware of catg %d!",catg);
			index = freight_builder_t::get_info_catg(catg)->get_index();
			menge = 0;
		}
		else {
			index = type->get_index();
		}
	}
	// convert coordinate to halt indices
	if(file->get_version() > 110005) {
		// save halt id directly
		if(file->is_saving()) {
			uint16 halt_id = ziel.is_bound() ? ziel.get_id() : 0;
			file->rdwr_short(halt_id);
			halt_id = zwischenziel.is_bound() ? zwischenziel.get_id() : 0;
			file->rdwr_short(halt_id);
		}
		else {
			uint16 halt_id;
			file->rdwr_short(halt_id);
			ziel.set_id(halt_id);
			file->rdwr_short(halt_id);
			zwischenziel.set_id(halt_id);
		}

	}
	else {
		// save halthandles via coordinates
		if(file->is_saving()) {
			koord ziel_koord = ziel.is_bound() ? ziel->get_basis_pos() : koord::invalid;
			ziel_koord.rdwr(file);
			koord zwischenziel_koord = zwischenziel.is_bound() ? zwischenziel->get_basis_pos() : koord::invalid;
			zwischenziel_koord.rdwr(file);
		}
		else {
			koord ziel_koord;
			ziel_koord.rdwr(file);
			ziel = welt->get_halt_koord_index(ziel_koord);
			koord zwischen_ziel_koord;
			zwischen_ziel_koord.rdwr(file);
			zwischenziel = welt->get_halt_koord_index(zwischen_ziel_koord);
		}
	}
	zielpos.rdwr(file);
	// restore factory-flag
	if(  file->get_version()<110005  &&  file->is_loading()  ) {
		if (fabrik_t::get_fab(welt, zielpos)) {
			to_factory = 1;
		}
	}
}



void freight_t::laden_abschliessen(karte_t *welt,spieler_t * /*sp*/)
{
	// since some halt was referred by with several koordinates
	// this routine will correct it
	if(ziel.is_bound()) {
		ziel = welt->lookup(ziel->get_init_pos())->get_halt();
	}
	if(zwischenziel.is_bound()) {
		zwischenziel = welt->lookup(zwischenziel->get_init_pos())->get_halt();
	}
}
