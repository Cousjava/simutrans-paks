/*
 * Copyright (c) 1997 - 2004 Hj. Malthaner
 *
 * This file is part of the Simutrans project under the artistic licence.
 * (see licence.txt)
 *
 * Intro and everything else
 */

#include "../simcolor.h"
#include "../font.h"
#include "../simevent.h"
#include "../simimg.h"
#include "../simworld.h"
#include "../simskin.h"
#include "../simwin.h"
#include "../simsys.h"
#include "../simversion.h"
#include "../simgraph.h"
#include "../macros.h"
#include "../besch/skin_besch.h"
#include "../dataobj/umgebung.h"

#include "components/list_button.h"
#include "banner.h"
#include "loadsave_frame.h"
#include "server_frame.h"

static const int margin = 40;

banner_t::banner_t(karte_t *welt) : gui_frame_t(""),
	logo( skinverwaltung_t::logosymbol->get_bild_nr(0), 0 )
{
        this->welt = welt;
	last_ms = system_time();
	line = 0;

        const int height = 16+113+12*LINESPACE+2*BUTTON_HEIGHT+12;
        const int width = height * 160/100 + 1;
	const koord size(width, height);
	set_fenstergroesse(size);

	// logo.set_pos(koord(width - margin - 80, 34));
	// add_komponente( &logo );


	new_map.init( button_t::roundbox, "Neue Karte", koord(margin, size.y-16-BUTTON_HEIGHT-12 ), koord( BUTTON_WIDTH, BUTTON_HEIGHT ) );
	new_map.add_listener( this );
	add_komponente( &new_map );
	load_map.init( button_t::roundbox, "Load game", koord(margin+BUTTON_WIDTH+11, size.y-16-BUTTON_HEIGHT-12 ), koord( BUTTON_WIDTH, BUTTON_HEIGHT ) );
	load_map.add_listener( this );
	add_komponente( &load_map );
	join_map.init( button_t::roundbox, "join game", koord(margin+2*BUTTON_WIDTH+22, size.y-16-BUTTON_HEIGHT-12 ), koord( BUTTON_WIDTH, BUTTON_HEIGHT ) );
	join_map.add_listener( this );
	add_komponente( &join_map );
	quit.init( button_t::roundbox, "Beenden", koord(margin+3*BUTTON_WIDTH+33, size.y-16-BUTTON_HEIGHT-12 ), koord( BUTTON_WIDTH, BUTTON_HEIGHT ) );
	quit.add_listener( this );
	add_komponente( &quit );
}



bool banner_t::infowin_event(const event_t *ev)
{
	if(  gui_frame_t::getroffen( ev->cx, ev->cy  )  ) {
		gui_frame_t::infowin_event( ev );
	}
	return false;
}



bool banner_t::action_triggered( gui_action_creator_t *komp, value_t)
{
	if(  komp == &quit  ) {
		umgebung_t::quit_simutrans = true;
		destroy_all_win(true);
	}
	else if(  komp == &new_map  ) {
		destroy_all_win(true);
	}
	else if(  komp == &load_map  ) {
		destroy_all_win(true);
		create_win( new loadsave_frame_t(welt, true), w_info, magic_load_t);
	}
	else if(  komp == &join_map  ) {
		destroy_all_win(true);
		create_win( new server_frame_t(welt), w_info, magic_server_frame_t );
	}
	return true;
}

/**
 * Display game splash screen (aka banner)
 *
 * @author Hj. Malthaner
 */
void banner_t::zeichnen(koord pos, koord gr )
{
	gui_frame_t::zeichnen( pos, gr );
	
	// Hajo: set up colors. Maybe we need some global rules to
	// define which colors to use for which semantics of text ...
	const int color_text = COL_WHITE;
	const int color_high = 207;
        // const int color_high = COL_WHITE;
        const int color_shadow = COL_GREY1;
        // const int color_warn = 127;
        
	// Hajo: layout constants for this dialog
        const int indent = 60;
        const int line_space = large_font_p->line_spacing;
	
	KOORD_VAL yp = pos.y+29;

	display_shadow_proportional( pos.x + margin, yp, color_high, color_shadow,
                                     "***** W e l c o m e  t o  S i m u t r a n s  I r o n  B i t e *****", true );
	yp += line_space+5;
#ifdef REVISION
	display_shadow_proportional( pos.x + margin + indent, yp, color_high, color_shadow,
                                     "    Version " VERSION_NUMBER " " VERSION_DATE " r" QUOTEME(REVISION), true );
#else
	display_shadow_proportional( pos.x + margin + indent, yp, color_text, color_shadow,
                                     "Version " VERSION_NUMBER " " VERSION_DATE, true );
#endif
	yp += line_space+6;

	display_shadow_proportional( pos.x + margin + indent, yp, color_text, color_shadow,
                                     "Simutrans Iron Bite is developed by Hj. Malthaner,", true );
	yp += line_space;
	display_shadow_proportional( pos.x + margin + indent, yp, color_text, color_shadow,
                                     " based on Simutrans 111.2 by Markus Pristovsek", true );
	yp += line_space;
	display_shadow_proportional( pos.x + margin + indent, yp, color_text, color_shadow,
                                     "  and the Simutrans Team, which is based on", true );
	yp += line_space;
	display_shadow_proportional( pos.x + margin + indent, yp, color_text, color_shadow,
                                     "   Simutrans 0.84.21.2 by Hj. Malthaner et. al.", true );
	yp += line_space;
	display_shadow_proportional( pos.x + margin + indent, yp, color_text, color_shadow,
                                     "Simutrans is available under the Artistic Licence.", true );
	yp += line_space+6;

	display_shadow_proportional( pos.x + margin, yp, color_high, color_shadow,
                                     " Simutrans is free software, if you payed for it, ask for a refund!", true );
	yp += line_space+6;

	display_shadow_proportional( pos.x + margin + indent, yp, color_text, color_shadow,
                                     "     For questions and support please visit:", true );
	yp += line_space+5;
	display_shadow_proportional( pos.x + margin + indent, yp, color_text, color_shadow,
                                     "           http://forum.simutrans.com", true );
	yp += line_space;
	display_shadow_proportional( pos.x + margin + indent, yp, color_text, color_shadow,
                                     "         http://wiki.simutrans-germany.com", true );

	yp += line_space+9;

	// now the scrolling intro
	static const char* const scrolltext[] = {
#include "../scrolltext.h"
	};

        // Hajo: display a white line at top, because there is no title bar
	display_fillbox_wh(pos.x, pos.y + 16, gr.x, 1, COL_GREY6, false);

	const KOORD_VAL text_line = (line / 9) * 2;
	const KOORD_VAL text_offset = line % 9;
	const KOORD_VAL left = pos.x + margin;
	const KOORD_VAL width = gr.x - margin*2;

	display_fillbox_wh(left, yp, width, 52, COL_GREY1, true);
	display_fillbox_wh(left, yp - 1, width, 1, COL_GREY3, false);
	display_fillbox_wh(left, yp + 52, width, 1, COL_GREY6, false);

	PUSH_CLIP( left, yp, width, 52 );
	display_proportional_clip( left + 4, yp + 1 - text_offset, scrolltext[text_line + 0], ALIGN_LEFT, COL_WHITE, false);
	display_proportional_clip( left + width - 4, yp + 1 - text_offset, scrolltext[text_line + 1], ALIGN_RIGHT, COL_WHITE, false);
	display_proportional( left + 4, yp + 11 - text_offset, scrolltext[text_line + 2], ALIGN_LEFT, COL_WHITE, false);
	display_proportional( left + width - 4, yp + 11 - text_offset, scrolltext[text_line + 3], ALIGN_RIGHT, COL_WHITE, false);
	display_proportional( left + 4, yp + 21 - text_offset, scrolltext[text_line + 4], ALIGN_LEFT, COL_GREY6, false);
	display_proportional( left + width - 4, yp + 21 - text_offset, scrolltext[text_line + 5], ALIGN_RIGHT, COL_GREY6, false);
	display_proportional( left + 4, yp + 31 - text_offset, scrolltext[text_line + 6], ALIGN_LEFT, COL_GREY5, false);
	display_proportional( left + width - 4, yp + 31 - text_offset, scrolltext[text_line + 7], ALIGN_RIGHT, COL_GREY5, false);
	display_proportional( left + 4, yp + 41 - text_offset, scrolltext[text_line + 8], ALIGN_LEFT, COL_GREY4, false);
	display_proportional( left + width - 4, yp + 41 - text_offset, scrolltext[text_line + 9], ALIGN_RIGHT, COL_GREY4, false);
	display_proportional_clip( left + 4, yp + 51 - text_offset, scrolltext[text_line + 10], ALIGN_LEFT, COL_GREY3, false);
	display_proportional_clip( left + width - 4, yp + 51 - text_offset, scrolltext[text_line + 11], ALIGN_RIGHT, COL_GREY3, false);
	POP_CLIP();

	// scroll on every 70 ms
	if(system_time()>last_ms+70u) {
		last_ms += 70u;
		line ++;
	}

	if (scrolltext[text_line + 12] == 0) {
		line = 0;
	}
}
