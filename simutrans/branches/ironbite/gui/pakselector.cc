#include <string>
#include <stdio.h>

#include "pakselector.h"

#include "../simdebug.h"
#include "../simskin.h"
#include "../simsys.h"

#include "../besch/skin_besch.h"
#include "../dataobj/umgebung.h"

#include "components/gui_component_colors.h"



/**
 * what to do after loading
 */
void pakselector_t::action(const char *filename)
{
	char * p = strchr(filename, ',');
	*p = '\0';
	
	umgebung_t::objfilename = (std::string)filename + "/";
	umgebung_t::default_einstellungen.set_with_private_paks( false );
}


bool pakselector_t::del_action(const char *filename)
{
	char * p = strchr(filename, ',');
	*p = '\0';

	// cannot delete set => use this for selection
	umgebung_t::objfilename = (std::string)filename + "/";
	umgebung_t::default_einstellungen.set_with_private_paks( true );
	return true;
}


const char *pakselector_t::get_info(const char *)
{
	return "";
}


/**
 * This method is called if an action is triggered
 * @author Hj. Malthaner
 */
bool pakselector_t::action_triggered( gui_action_creator_t *komp,value_t v)
{
	if(komp == &savebutton) {
		savebutton.pressed ^= 1;
		return true;
	}
	else if(komp != &input) {
		return savegame_frame_t::action_triggered( komp, v );
	}
	return false;
}


bool pakselector_t::check_file( const char *filename, const char * )
{
	char buf[1024];
	sprintf( buf, "%s/ground.Outside.pak", filename );
	if (FILE* const f = fopen(buf, "r")) {
		fclose(f);
		return true;
	}
	return false;
}


pakselector_t::pakselector_t() : savegame_frame_t( NULL, umgebung_t::program_dir, true )
{
	at_least_one_add = false;

	// remove unneccessary buttons
	remove_komponente( &input );
	remove_komponente( &savebutton );
	remove_komponente( &cancelbutton );
	remove_komponente( &divider1 );
	remove_komponente( &fnlabel );

	scrolly.set_pos(koord(0, D_MARGIN_TOP+3*LINESPACE-1));
	
	koord size = get_fenstergroesse();
	resize(koord(448-size.x, 448-size.y));
}


void pakselector_t::fill_list()
{
	// do the search ...
	savegame_frame_t::fill_list();

	int y = 0;
	FOR(slist_tpl<entry>, const& i, entries) {
		char path[1024];
		sprintf(path,"%saddons/%s", umgebung_t::user_dir, i.button->get_text());

		// i.del->set_text("Load with addons");
				
		if(chdir(path) != 0) 
		{
			// no addons for this
			if(entries.get_count()==1) {
				// only single entry and no addons => no need to question further ...
				umgebung_t::objfilename = (std::string)i.button->get_text() + "/";
			}
		}
		y += 2*D_BUTTON_HEIGHT;
	}
	chdir( umgebung_t::program_dir );

	button_frame.set_size(get_fenstergroesse().x-1, y);
	set_min_windowsize(koord(448, 420));
	set_fenstergroesse(koord(get_fenstergroesse().x, D_TITLEBAR_HEIGHT+30+y+3*LINESPACE+4+1));
}


void pakselector_t::set_fenstergroesse(koord groesse)
{
	if(groesse.y>display_get_height()-70) {
		// too large ...
		groesse.y = ((display_get_height()-D_TITLEBAR_HEIGHT-30-3*LINESPACE-4-1)/D_BUTTON_HEIGHT)*D_BUTTON_HEIGHT+D_TITLEBAR_HEIGHT+30+3*LINESPACE+4+1-70;
		// position adjustment will be done automatically ... nice!
	}
	gui_frame_t::set_fenstergroesse(groesse);
	groesse = get_fenstergroesse();

	int y = 0;
	int n = 0;
	FOR(slist_tpl<entry>, const& i, entries) 
	{
		const int row_x = 18 + (n & 1) * 214;

		if (i.button->is_visible()) 
		{
			button_t* const bt_load_pak = i.button;
			button_t* const bt_load_addons = i.del;

			// Hajo: first we resize and re-order the buttons
			bt_load_addons->set_pos( koord( row_x, y ) );
			bt_load_addons->set_size(196, 3*D_BUTTON_HEIGHT-2);
			
			bt_load_pak->set_pos( koord( row_x, y+3*D_BUTTON_HEIGHT-2) );
			bt_load_pak->set_size(196, D_BUTTON_HEIGHT+4);

			const char * tmp = bt_load_pak->get_text();
			
			// Hajo: then we build new labels for the buttons
			if(strchr(tmp, ',') == 0)
			{
				char * const pak_name = new char[1024];
				sprintf(pak_name, "« Play %s »", tmp);
				
				char * const only_pak_name = new char[1024];
				sprintf(only_pak_name, "%s, no addons", tmp);

				bt_load_pak->set_text(only_pak_name);
				bt_load_addons->set_text(pak_name);
			}
			
			i.label->set_pos(koord(groesse.x / 2 - 40 + 30, y + 2));

			// Hajo: need a new row of buttons?
			if(n & 1)
			{
				y += 5*D_BUTTON_HEIGHT;
			}
			
			n++;
		}
	}

	y += D_MARGIN_TOP;
	
	button_frame.set_size(groesse.x, y);
	
	koord scroll_top = scrolly.get_pos();
	scrolly.set_size(groesse.x, groesse.y-scroll_top.y - D_TITLEBAR_HEIGHT-4*LINESPACE-4-1);
}


/**
 * Draw the backdrop image.
 * @author Hj. Malthaner
 */
static void draw_iron_backdrop(const int xpos, const int ypos, const int width, const int height)
{
	const int imgx = (xpos + width - 448) / 2 + 64;
	const int imgy = (ypos + height - 448) / 2 + 34;
	
	for(int y=0; y<7; y++)
	{
		for(int x=0; x<7; x++)
		{
			// Hajo: pak selector background image starts at tile 20
			const int nr = 20 + y*7 + x;
			const int img_nr = skinverwaltung_t::iron_backdrop->get_bild_nr(nr);
			
			display_base_img(img_nr,
					 imgx + x*64, imgy + y*64, 
					 0, false, false);
		}
	}
}	


/**
 * Draw the frame body.
 * @author Hj. Malthaner
 */
void pakselector_t::draw_frame_body(const koord pos, const koord gr)
{
	draw_iron_backdrop(pos.x, pos.y, gr.x, gr.y);
}

void pakselector_t::zeichnen(koord pos, koord gr)
{
	display_fillbox_wh(0, 0, display_get_width(), display_get_height(), COL_DARK_TURQUOISE-2, true);	

	display_ddd_box_clip(pos.x-4, pos.y+D_TITLEBAR_HEIGHT-4, 
			     gr.x+8, gr.y-D_TITLEBAR_HEIGHT+8, MN_GREY4, MN_GREY0);
	display_fillbox_wh(pos.x-3, pos.y+D_TITLEBAR_HEIGHT-3, gr.x+6, gr.y-D_TITLEBAR_HEIGHT+6, MN_GREY2, true);

	gui_frame_t::zeichnen(pos, gr);

	if(skinverwaltung_t::iron_skin)
	{
		draw_corner_decorations(pos.x, pos.y, gr.x, gr.y, -4, 0);
	}

	display_fillbox_wh(pos.x+20, pos.y+D_TITLEBAR_HEIGHT+1, gr.x-40, 34, MN_GREY2, true);
	display_fillbox_wh(pos.x+20, pos.y+gr.y-42, gr.x-40, 40, MN_GREY2, true);

	const int margin = 34;
	
	display_proportional(pos.x + margin, pos.y + D_TITLEBAR_HEIGHT+15, 
			     "Please choose a pak set for playing:",
			     ALIGN_LEFT, COLOR_TEXT, false);
	
	display_proportional(pos.x+margin, pos.y+gr.y-3*LINESPACE-4,
			     "To skip this dialog you can select a pak set by:",
			     ALIGN_LEFT, MN_GREY0, false);
	
	display_proportional(pos.x+margin, pos.y+gr.y-2*LINESPACE-3,
			     " - adding 'pak_file_path = pak/' to your simuconf.tab",
			     ALIGN_LEFT, MN_GREY0, false);
	
	display_proportional(pos.x+margin, pos.y+gr.y-1*LINESPACE-2,
			     " - using '-objects pakxyz/' on the command line",
			     ALIGN_LEFT, MN_GREY0, false);

	/*
	display_outline_proportional(pos.x + margin, pos.y + D_TITLEBAR_HEIGHT+16, 
				     COLOR_TEXT, MN_GREY3,
                                     "Please choose a pak set for playing:", true );
	
	display_outline_proportional(pos.x+margin, pos.y+gr.y-3*LINESPACE-4,
				     COLOR_TEXT, MN_GREY3,
				     "To avoid seeing this dialogue you can select a pak set by:", true);
	
	display_outline_proportional(pos.x+margin, pos.y+gr.y-2*LINESPACE-3,
				     COLOR_TEXT, MN_GREY3,
				     " - adding 'pak_file_path = pak/' to your simuconf.tab", true);
	
	display_outline_proportional(pos.x+margin, pos.y+gr.y-1*LINESPACE-2,
				     COLOR_TEXT, MN_GREY3,
				     " - using '-objects pakxyz/' on the command line", true);
	*/

	display_ddd_box_clip(pos.x, pos.y+D_TITLEBAR_HEIGHT, 
			     gr.x, gr.y-D_TITLEBAR_HEIGHT, MN_GREY0, MN_GREY4);
}
