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
#include "../simworld.h"
#include "../simskin.h"
#include "../simwin.h"
#include "../simsys.h"
#include "../simversion.h"
#include "../simgraph.h"
#include "../besch/skin_besch.h"
#include "../dataobj/umgebung.h"

#include "components/gui_button.h"
#include "components/action_listener.h"

#include "banner.h"
#include "loadsave_frame.h"
#include "server_frame.h"

static const int margin = 40;

class banner_data_t : public action_listener_t
{
public:
	button_t new_map;
	button_t load_map;
	button_t join_map;
	button_t quit;

	karte_t *welt;

	banner_data_t()
	{
		new_map.add_listener(this);
		load_map.add_listener(this);
		join_map.add_listener(this);
		quit.add_listener(this);
	};
	
	virtual bool action_triggered(gui_action_creator_t*, value_t) OVERRIDE;
};


banner_t::banner_t(karte_t *welt) : gui_frame_t("")
{
	ooo = new banner_data_t();
        ooo->welt = welt;
	
	// last_ms = system_time();
	last_ms = dr_time();
	line = 0;

        const int height = 302;
        const int width = height * 160/100 + 1;
	
	set_window_size(width, height+20);

	const int button_bottom_margin = 18;
	const int button_y = height-14-button_bottom_margin;
	
	// Hajo: testing larger buttons
	const koord button_size (BUTTON_WIDTH, BUTTON_HEIGHT+3);
	
	ooo->new_map.init(button_t::roundbox, "Neue Karte", koord(margin, button_y), button_size);
	add_komponente( &ooo->new_map );

	ooo->load_map.init(button_t::roundbox, "Load game", koord(margin+BUTTON_WIDTH+11, button_y), button_size);
	add_komponente( &ooo->load_map );

	ooo->join_map.init(button_t::roundbox, "join game", koord(margin+2*BUTTON_WIDTH+22, button_y), button_size);
	add_komponente( &ooo->join_map );

	ooo->quit.init(button_t::roundbox, "Beenden", koord(margin+3*BUTTON_WIDTH+33, button_y), button_size);
	add_komponente( &ooo->quit );
}

banner_t::~banner_t()
{
	delete ooo;
	ooo = 0;
}

/**
 * Draw the iron bite logo in the background.
 * @author Hj. Malthaner
 */
static void draw_iron_backdrop(const int xpos, const int ypos, const int width, const int height)
{
	const int imgx = (xpos + width - 320) / 2 + 60;
	const int imgy = (ypos + height - 192) / 2 + 12;
	
	for(int y=0; y<3; y++)
	{
		for(int x=0; x<5; x++)
		{
			const int nr = y*5 + x;
			const int img_nr = skinverwaltung_t::iron_backdrop->get_bild_nr(nr);
			
			display_base_img(img_nr,
					 imgx + x*64, imgy + y*64, 
					 0, false, false);
		}
	}
}	


/**
 * Display game splash screen (aka banner)
 *
 * @author Hj. Malthaner
 */
void banner_t::zeichnen(const koord pos, const koord gr )
{
	gui_frame_t::zeichnen( pos, gr );
	
	// Hajo: set up colors. Maybe we need some global rules to
	// define which colors to use for which semantics of text ...
	const int color_text = COL_WHITE;
	const int color_high = 207;
        const int color_shadow = COL_GREY2;
        
	// Hajo: layout constants for this dialog
        const int indent = 60;
        const int line_space = large_font_p->line_spacing;
	
	// Hajo: 8 pixels outer border
	int yp = pos.y + 31 + 8;
	
	// Hajo: display backdrop image if there is one
	if(skinverwaltung_t::iron_backdrop)
	{
		draw_iron_backdrop(pos.x, pos.y, gr.x, gr.y);
	}

	if(skinverwaltung_t::iron_skin)
	{
		draw_corner_decorations(pos.x, pos.y, gr.x, gr.y, 0, 0);
	}
	
	// Hajo: now display the intro message
	/*
	display_outline_proportional( pos.x + margin, yp, color_high, color_shadow,
                                     "          W e l c o m e  t o  S i m u t r a n s  I r o n  B i t e", true );
	*/
	yp += line_space+6;
#ifdef REVISION
	display_proportional(pos.x + margin + indent, yp+1,
                             "      Version " VERSION_NUMBER " " VERSION_DATE " r" QUOTEME(REVISION), 
			     ALIGN_LEFT,
			     90,
			     false);
#else
	display_shadow_proportional( pos.x + margin + indent, yp, COL_GREY4, color_shadow,
                                     "  Version " VERSION_NUMBER " " VERSION_DATE, true );
#endif
	yp += line_space+6;
	
	
	display_outline_proportional( pos.x + margin + indent, yp, color_text, color_shadow,
                                     "Simutrans Iron Bite is developed by Hj. Malthaner,", true );
	yp += line_space;
	display_outline_proportional( pos.x + margin + indent, yp, color_text, color_shadow,
                                     " based on Simutrans 111.2 by Markus Pristovsek", true );
	yp += line_space;
	display_outline_proportional( pos.x + margin + indent, yp, color_text, color_shadow,
                                     "  and the Simutrans Team, which is based on", true );
	yp += line_space;
	display_outline_proportional( pos.x + margin + indent, yp, color_text, color_shadow,
                                     "   Simutrans 0.84.21.2 by Hj. Malthaner et. al.", true );
	yp += line_space;
	display_outline_proportional( pos.x + margin + indent, yp, color_text, color_shadow,
                                     "Simutrans is available under the Artistic Licence.", true );
	yp += line_space+5;

	display_outline_proportional( pos.x + margin, yp, color_high, color_shadow,
                                     "   Simutrans is free software. If you paid for it, ask for a refund!", true );
	yp += line_space+5;

	display_outline_proportional( pos.x + margin + indent, yp, color_text, color_shadow,
                                     "     For questions and support please visit:", true );
	yp += line_space+5;
	display_outline_proportional( pos.x + margin + indent, yp, color_text, color_shadow,
                                     "           http://forum.simutrans.com", true );
	yp += line_space;
	display_outline_proportional( pos.x + margin + indent, yp, color_text, color_shadow,
                                     "        http://wiki.simutrans-germany.com", true );

	yp += line_space+9;

	// now the scrolling intro
	static const char* const scrolltext[] = {
#include "../scrolltext.h"
	};

        // Hajo: display a white line at top, because there is no title bar
	display_fillbox_wh(pos.x, pos.y + 16, gr.x, 1, MN_GREY4, false);

	const int text_line = (line / 9) * 2;
	const int text_offset = line % 9;
	const int left = pos.x + margin;
	const int width = gr.x - margin*2;

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
	
	/*
	display_proportional_clip( left + 4, yp + 1 - text_offset, scrolltext[text_line + 0], ALIGN_LEFT, 71, false);
	display_proportional_clip( left + width - 4, yp + 1 - text_offset, scrolltext[text_line + 1], ALIGN_RIGHT, 71, false);
	display_proportional( left + 4, yp + 11 - text_offset, scrolltext[text_line + 2], ALIGN_LEFT, 71, false);
	display_proportional( left + width - 4, yp + 11 - text_offset, scrolltext[text_line + 3], ALIGN_RIGHT, 71, false);
	display_proportional( left + 4, yp + 21 - text_offset, scrolltext[text_line + 4], ALIGN_LEFT, 70, false);
	display_proportional( left + width - 4, yp + 21 - text_offset, scrolltext[text_line + 5], ALIGN_RIGHT, 70, false);
	display_proportional( left + 4, yp + 31 - text_offset, scrolltext[text_line + 6], ALIGN_LEFT, 69, false);
	display_proportional( left + width - 4, yp + 31 - text_offset, scrolltext[text_line + 7], ALIGN_RIGHT, 69, false);
	display_proportional( left + 4, yp + 41 - text_offset, scrolltext[text_line + 8], ALIGN_LEFT, 68, false);
	display_proportional( left + width - 4, yp + 41 - text_offset, scrolltext[text_line + 9], ALIGN_RIGHT, 68, false);
	display_proportional_clip( left + 4, yp + 51 - text_offset, scrolltext[text_line + 10], ALIGN_LEFT, 67, false);
	display_proportional_clip( left + width - 4, yp + 51 - text_offset, scrolltext[text_line + 11], ALIGN_RIGHT, 67, false);
	*/
	POP_CLIP();

	// scroll on every 70 ms
	// if(system_time()>last_ms+70u) 
	if(dr_time()>last_ms+70u) 
	{
		last_ms += 70u;
		line ++;
	}

	if (scrolltext[text_line + 12] == 0) 
	{
		line = 0;
	}

	// Hajo: add inner bevel border
	display_ddd_box_clip(pos.x + 4, pos.y + 4 + D_TITLEBAR_HEIGHT, 
	                     gr.x-8, gr.y-8-D_TITLEBAR_HEIGHT, MN_GREY0, MN_GREY4);
	
}

bool banner_data_t::action_triggered( gui_action_creator_t *komp, value_t)
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
