/*
 * Copyright (c) 1997 - 2004 Hj. Malthaner
 *
 * Line management
 *
 * This file is part of the Simutrans project under the artistic licence.
 * (see licence.txt)
 */

#include "../besch/baum_besch.h"
#include "baum_edit.h"

#include "../simwerkz.h"

#include "../dings/baum.h"
#include "../dataobj/translator.h"
#include "components/list_button.h"

#include "../besch/grund_besch.h"

#include "../utils/cbuffer_t.h"

#include "components/gui_label.h"


// new tool definition
wkz_plant_tree_t baum_edit_frame_t::tree_tool;
char baum_edit_frame_t::param_str[256];



static bool compare_baum_besch(const baum_besch_t* a, const baum_besch_t* b)
{
	int diff = strcmp( translator::translate(a->get_name()), translator::translate(b->get_name()) );
	if(diff ==0) {
		diff = strcmp( a->get_name(), b->get_name() );
	}
	return diff < 0;
}


baum_edit_frame_t::baum_edit_frame_t(spieler_t* sp_, karte_t* welt) :
	extend_edit_gui_t(translator::translate("baum builder"), sp_, welt)
{
	baumlist = new vector_tpl<const baum_besch_t *>(16);
	
	bt_timeline.set_text( "Random age" );

	remove_komponente( &bt_obsolete );
	offset_of_comp -= BUTTON_HEIGHT;

	besch = NULL;
	tree_tool.set_default_param(NULL);

	fill_list( is_show_trans_name );

	resize( koord(0,0) );
}

baum_edit_frame_t::~baum_edit_frame_t()
{
	delete baumlist;
}


// fill the current fablist
void baum_edit_frame_t::fill_list( bool translate )
{
	baumlist->clear();
	const vector_tpl<const baum_besch_t *> * s = tree_t::get_all_besch();

	for(uint i=0; i<s->get_count(); i++)
	{
		baumlist->insert_ordered(s->get(i), compare_baum_besch );
	}

	// now buil scrolled list
	scl.clear_elements();
	scl.set_selection(-1);
	for (vector_tpl<const baum_besch_t *>::const_iterator i = baumlist->begin(), end = baumlist->end(); i != end; ++i) {
		scl.append_element( new gui_scrolled_list_t::const_text_scrollitem_t(
			translate ? translator::translate( (*i)->get_name() ):(*i)->get_name(),
			COL_BLACK )
		);
		if(  (*i) == besch  ) {
			scl.set_selection(scl.get_count()-1);
		}
	}
	// always update current selection (since the tool may depend on it)
	change_item_info( scl.get_selection() );
}



void baum_edit_frame_t::change_item_info(sint32 entry)
{
	for(int i=0;  i<4;  i++  ) {
		img[i].set_image( IMG_LEER );
	}
	buf.clear();
	if(entry>=0  &&  entry<(sint32)baumlist->get_count()) {

		besch = baumlist->get(entry);

		buf.append(translator::translate(besch->get_name()));
		buf.append("\n\n");

		// climates
		buf.append( translator::translate("allowed climates:\n") );
		uint16 cl = besch->get_allowed_climate_bits();
		if(cl==0) {
			buf.append( translator::translate("None") );
			buf.append("\n");
		}
		else {
			for(uint16 i=0;  i<=arctic_climate;  i++  ) {
				if(cl &  (1<<i)) {
					buf.append(" - ");
					buf.append(translator::translate(grund_besch_t::get_climate_name_from_bit((climate)i)));
					buf.append("\n");
				}
			}
		}

		buf.printf( "\n%s %i\n", translator::translate("Seasons"), besch->get_seasons() );

		if (char const* const maker = besch->get_copyright()) {
			buf.append("\n");
			buf.printf(translator::translate("Constructed by %s"), maker);
			buf.append("\n");
		}

		info_text.recalc_size();
		cont.set_groesse( info_text.get_groesse() + koord(0, 20) );

		img[3].set_image( besch->get_bild_nr( 0, 3 ) );

		sprintf( param_str, "%i%i,%s", bt_climates.pressed, bt_timeline.pressed, besch->get_name() );
		tree_tool.set_default_param(param_str);
		tree_tool.cursor = werkzeug_t::general_tool.get(WKZ_PLANT_TREE)->cursor;
		welt->set_werkzeug( &tree_tool, sp );
	}
	else if(welt->get_werkzeug(sp->get_player_nr())==&tree_tool) {
		besch = NULL;
		welt->set_werkzeug( werkzeug_t::general_tool.get(WKZ_ABFRAGE), sp );
	}
}
