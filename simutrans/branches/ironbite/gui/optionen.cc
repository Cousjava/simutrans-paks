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

#include "components/gui_button.h"
#include "components/gui_label.h"
#include "components/gui_divider.h"

#include "components/action_listener.h"


class optionen_gui_data_t : action_listener_t
{
public:

	button_t bt_lang;
	button_t bt_color;
	button_t bt_display;
	button_t bt_sound;
	button_t bt_player;
	button_t bt_load;
	button_t bt_save;
	button_t bt_new;
	button_t bt_quit;

	gui_divider_t seperator_map;
	gui_divider_t seperator_file;
	gui_divider_t seperator;

	karte_t * welt;
	gui_frame_t * window;

	optionen_gui_data_t(gui_frame_t * f)
	{
		window = f;
		bt_new.add_listener(this);
		bt_load.add_listener(this);
		bt_save.add_listener(this);
		bt_lang.add_listener(this);
		bt_color.add_listener(this);
		bt_display.add_listener(this);
		bt_sound.add_listener(this);
		bt_player.add_listener(this);
		bt_quit.add_listener(this);
	};

	bool action_triggered(gui_action_creator_t *, value_t);

};


/**
 * Build a dialog to access various settings
 *
 * @author Hj. Malthaner
 */
optionen_gui_t::optionen_gui_t(karte_t *welt) :
	gui_frame_t( translator::translate("Einstellungen"))
{
	ooo = new optionen_gui_data_t(this);
	
	ooo->welt = welt;

	// Hajo: run-variables for element positioning
	int ypos = D_MARGIN_TOP;
	int xpos = D_MARGIN_LEFT;
	
	// Hajo: text starts a bit lower ...
	ypos += 2;
	
	ooo->bt_new.set_typ(button_t::roundbox);
	ooo->bt_new.set_pos(xpos, ypos);
	ooo->bt_new.set_size(BUTTON_WIDTH, BUTTON_TALL_HEIGHT);
	ooo->bt_new.set_text("Neue Karte");
	add_komponente( &ooo->bt_new );

	ypos += BUTTON_TALL_HEIGHT + D_V_SPACE;

	ooo->seperator_map.set_pos(xpos,  ypos);
	ooo->seperator_map.set_size(BUTTON_WIDTH, 1);
	add_komponente( &ooo->seperator_map );

	ypos += 3 + D_V_SPACE;

	ooo->bt_load.set_typ(button_t::roundbox);
	ooo->bt_load.set_pos(xpos, ypos);
	ooo->bt_load.set_size(BUTTON_WIDTH, BUTTON_TALL_HEIGHT);
	ooo->bt_load.set_text("Laden");
	add_komponente( &ooo->bt_load );

	ypos += BUTTON_TALL_HEIGHT + D_V_SPACE;
	
	ooo->bt_save.set_typ(button_t::roundbox);
	ooo->bt_save.set_pos(xpos, ypos);
	ooo->bt_save.set_size(BUTTON_WIDTH, BUTTON_TALL_HEIGHT);
	ooo->bt_save.set_text("Speichern");
	add_komponente( &ooo->bt_save );

	ypos += BUTTON_TALL_HEIGHT + D_V_SPACE;
	
	ooo->seperator_file.set_pos(xpos,  ypos);
	ooo->seperator_file.set_size(BUTTON_WIDTH, 1);
	add_komponente( &ooo->seperator_file );

	ypos += 3 + D_V_SPACE;
	
	ooo->bt_lang.set_typ(button_t::roundbox);
	ooo->bt_lang.set_pos(xpos,  ypos);
	ooo->bt_lang.set_size(BUTTON_WIDTH, BUTTON_TALL_HEIGHT);
	ooo->bt_lang.set_text("Sprache");
	add_komponente( &ooo->bt_lang );

	ypos += BUTTON_TALL_HEIGHT + D_V_SPACE;
	
	ooo->bt_color.set_typ(button_t::roundbox);
	ooo->bt_color.set_pos(xpos, ypos);
	ooo->bt_color.set_size(BUTTON_WIDTH, BUTTON_TALL_HEIGHT);
	ooo->bt_color.set_text("Farbe");
	add_komponente( &ooo->bt_color );

	ypos += BUTTON_TALL_HEIGHT + D_V_SPACE;
	
	ooo->bt_display.set_typ(button_t::roundbox);
	ooo->bt_display.set_pos(xpos, ypos);
	ooo->bt_display.set_size(BUTTON_WIDTH, BUTTON_TALL_HEIGHT);
	ooo->bt_display.set_text("Helligk.");
	add_komponente( &ooo->bt_display );

	ypos += BUTTON_TALL_HEIGHT + D_V_SPACE;
	
	ooo->bt_sound.set_typ(button_t::roundbox);
	ooo->bt_sound.set_pos(xpos, ypos);
	ooo->bt_sound.set_size(BUTTON_WIDTH, BUTTON_TALL_HEIGHT);
	ooo->bt_sound.set_text("Sound");
	add_komponente( &ooo->bt_sound );

	ypos += BUTTON_TALL_HEIGHT + D_V_SPACE;
	
	ooo->bt_player.set_typ(button_t::roundbox);
	ooo->bt_player.set_pos(xpos, ypos);
	ooo->bt_player.set_size(BUTTON_WIDTH, BUTTON_TALL_HEIGHT);
	ooo->bt_player.set_text("Spieler(mz)");
	add_komponente( &ooo->bt_player );

	ypos += BUTTON_TALL_HEIGHT + D_V_SPACE;
	
	ooo->seperator.set_pos(xpos,  ypos);
	ooo->seperator.set_size(BUTTON_WIDTH, 1);
	add_komponente( &ooo->seperator );

	ypos += 3 + D_V_SPACE;
	
	// 01-Nov-2001      Markus Weber    Added
	ooo->bt_quit.set_typ(button_t::roundbox);
	ooo->bt_quit.set_pos(xpos, ypos);
	ooo->bt_quit.set_size(BUTTON_WIDTH, BUTTON_TALL_HEIGHT);
	ooo->bt_quit.set_text("Beenden");
	add_komponente( &ooo->bt_quit );

	ypos += BUTTON_TALL_HEIGHT + D_V_SPACE;

	// Hajo: compute total window size
	set_window_size(D_MARGIN_LEFT+BUTTON_WIDTH + D_MARGIN_RIGHT, 
	                        ypos + D_TITLEBAR_HEIGHT+2);
}

optionen_gui_t::~optionen_gui_t() 
{
	delete ooo;
	ooo = 0;
}

/**
 * This method is called if an action is triggered
 *
 * @author Hj. Malthaner
 */
bool optionen_gui_data_t::action_triggered( gui_action_creator_t *comp,value_t /* */)
{
	if(comp==&bt_lang) {
		create_win(new sprachengui_t(), w_info, magic_sprachengui_t);
	}
	else if(comp==&bt_color) {
		create_win(D_MARGIN_LEFT+BUTTON_WIDTH + D_MARGIN_RIGHT-32, 36, new farbengui_t(welt->get_active_player()), w_info, magic_farbengui_t);
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
		destroy_win(window);
		create_win(new loadsave_frame_t(welt, true), w_info, magic_load_t);
	}
	else if(comp==&bt_save) {
		destroy_win(window);
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
