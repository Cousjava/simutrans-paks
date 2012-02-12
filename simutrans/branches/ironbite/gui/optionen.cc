/*
 * Copyright (c) 1997 - 2001 Hj. Malthaner
 *
 * This file is part of the Simutrans project under the artistic licence.
 * (see licence.txt)
 */

/*
 * Dialog fuer Spieloptionen
 * Niels Roest, Hj. Malthaner, 2000
 */

#include "../simworld.h"
#include "../simwin.h"
#include "optionen.h"
#include "display_settings.h"
#include "sprachen.h"
#include "player_frame_t.h"
#include "kennfarbe.h"
#include "sound_frame.h"
#include "loadsave_frame.h"
#include "../dataobj/translator.h"
#include "components/list_button.h"

/**
 * Build a dialog to access various settings
 *
 * @author Hj. Malthaner
 */
optionen_gui_t::optionen_gui_t(karte_t *welt) :
	gui_frame_t( translator::translate("Einstellungen"))
{
	this->welt = welt;

	// Hajo: run-variables for element positioning
	int ypos = D_TOP_MARGIN;
	int xpos = D_LEFT_MARGIN;
	
	// Hajo: text starts a bit lower ...
	ypos += 2;
	
	// txt.set_pos( koord(xpos-1, ypos) );
	// add_komponente( &txt );

	// init buttons
	// ypos += BUTTON_HEIGHT + D_COMP_Y_SPACE;

	bt_new.set_groesse( koord(BUTTON_WIDTH, BUTTON_HEIGHT) );
	bt_new.set_typ(button_t::roundbox);
	bt_new.set_pos( koord(xpos, ypos) );
	bt_new.set_text("Neue Karte");
	bt_new.add_listener(this);
	add_komponente( &bt_new );

	ypos += BUTTON_HEIGHT + D_COMP_Y_SPACE;

	bt_load.set_groesse( koord(BUTTON_WIDTH, BUTTON_HEIGHT) );
	bt_load.set_typ(button_t::roundbox);
	bt_load.set_pos( koord(xpos, ypos) );
	bt_load.set_text("Laden");
	bt_load.add_listener(this);
	add_komponente( &bt_load );

	ypos += BUTTON_HEIGHT + D_COMP_Y_SPACE;
	
	bt_save.set_groesse( koord(BUTTON_WIDTH, BUTTON_HEIGHT) );
	bt_save.set_typ(button_t::roundbox);
	bt_save.set_pos( koord(xpos, ypos) );
	bt_save.set_text("Speichern");
	bt_save.add_listener(this);
	add_komponente( &bt_save );

	ypos += BUTTON_HEIGHT + D_COMP_Y_SPACE;
	
	seperator_file.set_pos( koord(xpos,  ypos) );
	seperator_file.set_groesse( koord(BUTTON_WIDTH, 1) );
	add_komponente( &seperator_file );

	ypos += 3 + D_COMP_Y_SPACE;
	
	bt_lang.set_groesse( koord(BUTTON_WIDTH, BUTTON_HEIGHT) );
	bt_lang.set_typ(button_t::roundbox);
	bt_lang.set_pos( koord(xpos,  ypos) );
	bt_lang.set_text("Sprache");
	bt_lang.add_listener(this);
	add_komponente( &bt_lang );

	ypos += BUTTON_HEIGHT + D_COMP_Y_SPACE;
	
	bt_color.set_groesse( koord(BUTTON_WIDTH, BUTTON_HEIGHT) );
	bt_color.set_typ(button_t::roundbox);
	bt_color.set_pos( koord(xpos, ypos) );
	bt_color.set_text("Farbe");
	bt_color.add_listener(this);
	add_komponente( &bt_color );

	ypos += BUTTON_HEIGHT + D_COMP_Y_SPACE;
	
	bt_display.set_groesse( koord(BUTTON_WIDTH, BUTTON_HEIGHT) );
	bt_display.set_typ(button_t::roundbox);
	bt_display.set_pos( koord(xpos, ypos) );
	bt_display.set_text("Helligk.");
	bt_display.add_listener(this);
	add_komponente( &bt_display );

	ypos += BUTTON_HEIGHT + D_COMP_Y_SPACE;
	
	bt_sound.set_groesse( koord(BUTTON_WIDTH, BUTTON_HEIGHT) );
	bt_sound.set_typ(button_t::roundbox);
	bt_sound.set_pos( koord(xpos, ypos) );
	bt_sound.set_text("Sound");
	bt_sound.add_listener(this);
	add_komponente( &bt_sound );

	ypos += BUTTON_HEIGHT + D_COMP_Y_SPACE;
	
	bt_player.set_groesse( koord(BUTTON_WIDTH, BUTTON_HEIGHT) );
	bt_player.set_typ(button_t::roundbox);
	bt_player.set_pos( koord(xpos, ypos) );
	bt_player.set_text("Spieler(mz)");
	bt_player.add_listener(this);
	add_komponente( &bt_player );

	ypos += BUTTON_HEIGHT + D_COMP_Y_SPACE;
	
	seperator.set_pos( koord(xpos,  ypos) );
	seperator.set_groesse( koord(BUTTON_WIDTH, 1) );
	add_komponente( &seperator );

	ypos += 3 + D_COMP_Y_SPACE;
	
	// 01-Nov-2001      Markus Weber    Added
	bt_quit.set_groesse( koord(BUTTON_WIDTH, BUTTON_HEIGHT) );
	bt_quit.set_typ(button_t::roundbox);
	bt_quit.set_pos( koord(xpos, ypos) );
	bt_quit.set_text("Beenden");
	bt_quit.add_listener(this);
	add_komponente( &bt_quit );

	ypos += BUTTON_HEIGHT + D_COMP_Y_SPACE;

	// Hajo: compute total window size
	set_window_size( koord(D_LEFT_MARGIN+BUTTON_WIDTH + D_RIGHT_MARGIN, 
	                                    ypos + TITLEBAR_HEIGHT+2) );
}


/**
 * This method is called if an action is triggered
 *
 * @author Hj. Malthaner
 */
bool optionen_gui_t::action_triggered( gui_action_creator_t *comp,value_t /* */)
{
	if(comp==&bt_lang) {
		create_win(new sprachengui_t(), w_info, magic_sprachengui_t);
	}
	else if(comp==&bt_color) {
		create_win(new farbengui_t(welt->get_active_player()), w_info, magic_farbengui_t);
	}
	else if(comp==&bt_display) {
		create_win(new color_gui_t(welt), w_info, magic_color_gui_t);
	}
	else if(comp==&bt_sound) {
		create_win(new sound_frame_t(), w_info, magic_sound_kontroll_t);
	}
	else if(comp==&bt_player) {
		create_win(new ki_kontroll_t(welt), w_info, magic_ki_kontroll_t);
	}
	else if(comp==&bt_load) {
		destroy_win(this);
		create_win(new loadsave_frame_t(welt, true), w_info, magic_load_t);
	}
	else if(comp==&bt_save) {
		destroy_win(this);
		create_win(new loadsave_frame_t(welt, false), w_info, magic_save_t);
	}
	else if(comp==&bt_new) {
		destroy_all_win( true );
		welt->beenden(false);
	}
	else if(comp==&bt_quit) {
		welt->beenden(true);
	}
	else {
		// not our?
		return false;
	}
	return true;
}
