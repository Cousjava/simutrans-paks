/*
 * Copyright (c) 1997 - 2001 Hj. Malthaner
 * Copyright (c) 2012 Hj. Malthaner
 *
 * This file is part of the Simutrans project under the artistic licence.
 * (see licence.txt)
 */

#include "../simevent.h"
#include "../besch/skin_besch.h"
#include "../simskin.h"
#include "../dataobj/translator.h"
#include "kennfarbe.h"
#include "../player/simplay.h"


/**
 * This dialog allows the player to choose their preferred color for
 * vehicles and buildings.
 *
 * @author Hj. Malthaner
 */
farbengui_t::farbengui_t(spieler_t * sp) :
	gui_frame_t( translator::translate("Player Color Selection"), 0),
	bild(skinverwaltung_t::color_options->get_bild_nr(0), 
	     sp->get_player_nr())
{
	this->sp = sp;

	const int width = D_LEFT_MARGIN*2 + 14 * 24 - 2;
	set_fenstergroesse( koord(width, 202+TITLEBAR_HEIGHT) );
	
	txt.set_text("Please choose your preferred player colors.");
	txt.set_pos( koord(D_LEFT_MARGIN, D_TOP_MARGIN+2) );
	add_komponente( &txt );

	bild.set_pos( koord(width - 64 - D_RIGHT_MARGIN, D_TOP_MARGIN) );
	add_komponente( &bild );
	
	const int top_y = 52;
	const int bot_y = 130;
	
	primary.set_text("Your primary color:");
	primary.set_pos(koord(D_LEFT_MARGIN, top_y));
	add_komponente(&primary);

	const unsigned int color_sets = 28;

	// player color 1 buttons
	for(unsigned int i=0; i<color_sets; i++) 
	{
		player_color_1[i].init(button_t::box_state, 
		                            "", 
		                            koord(D_LEFT_MARGIN + (i % 14) * 24, top_y+15 + (i/14) * 24), 
		                            koord(22, 22) 
		                            );
		
		player_color_1[i].background = i*8 + 4;
		player_color_1[i].add_listener(this);
		add_komponente(player_color_1+i);
	}
	player_color_1[sp->get_player_color1()/8].pressed = true;
	
	secondary.set_text("Your secondary color:");
	secondary.set_pos(koord(D_LEFT_MARGIN, bot_y));
	add_komponente(&secondary);

	// player color 2 buttons
	for(unsigned int i=0; i<color_sets; i++) 
	{
		player_color_2[i].init( button_t::box_state,
		                            "", 
		                            koord(D_LEFT_MARGIN + (i % 14) * 24, bot_y+15 + (i/14) * 24), 
		                            koord(22, 22) 
		                            );
		player_color_2[i].background = i*8 + 4;
		player_color_2[i].add_listener(this);
		add_komponente(player_color_2+i);
	}
	player_color_2[sp->get_player_color2()/8].pressed = true;

}



/**
 * This method is called if an action is triggered
 *
 * @author V. Meyer
 */
bool farbengui_t::action_triggered( gui_action_creator_t *komp,value_t /* */)
{
	for(unsigned i=0;  i<28;  i++) {
		// new player 1 color ?
		if(komp==player_color_1+i) {
			for(unsigned j=0;  j<28;  j++) {
				player_color_1[j].pressed = false;
			}
			player_color_1[i].pressed = true;
			sp->set_player_color( i*8, sp->get_player_color2() );
			return true;
		}
		// new player color 2?
		if(komp==player_color_2+i) {
			for(unsigned j=0;  j<28;  j++) {
				player_color_2[j].pressed = false;
			}
			player_color_2[i].pressed = true;
			sp->set_player_color( sp->get_player_color1(), i*8 );
			return true;
		}
	}
	return false;
}
