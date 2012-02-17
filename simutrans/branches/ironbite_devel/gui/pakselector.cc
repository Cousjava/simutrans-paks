#include <string>
#include <stdio.h>

#include "../simdebug.h"
#include "pakselector.h"
#include "../dataobj/umgebung.h"
#include "../simsys.h"
#include "components/list_button.h"


/**
 * what to do after loading
 */
void pakselector_t::action(const char *filename)
{
	umgebung_t::objfilename = (std::string)filename + "/";
	umgebung_t::default_einstellungen.set_with_private_paks( false );
}


bool pakselector_t::del_action(const char *filename)
{
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


void pakselector_t::zeichnen(koord p, koord gr)
{
	gui_frame_t::zeichnen( p, gr );

	display_multiline_text( p.x+10, p.y+gr.y-3*LINESPACE-4,
		"To avoid seeing this dialogue define a path by:\n"
		" - adding 'pak_file_path = pak/' to your simuconf.tab\n"
		" - using '-objects pakxyz/' on the command line", COL_BLACK );
}


bool pakselector_t::check_file( const char *filename, const char * )
{
	char buf[1024];
	sprintf( buf, "%s/ground.Outside.pak", filename );
	FILE *f=fopen( buf, "r" );
	if(f) {
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

	fnlabel.set_text( "Choose one graphics set for playing:" );

	resize(koord(360-488,0));
}


void pakselector_t::fill_list()
{
	// do the search ...
	savegame_frame_t::fill_list();

	int y = 0;

	slist_iterator_tpl <entry> iter (entries);

	while(iter.next()) 
	{
		const entry & entry = iter.get_current();

		char path[1024];
		sprintf(path,"%saddons/%s", umgebung_t::user_dir, entry.button->get_text() );
		koord size = entry.del->get_groesse();
		entry.del->set_groesse(size + koord(150, 0));
		entry.del->set_text( "Load with addons" );
		entry.button->set_pos( koord(150,0)+entry.button->get_pos() );
		if(  chdir( path )!=0  ) {
			// no addons for this
			entry.del->set_visible( false );
			entry.del->disable();
			if(entries.get_count()==1) {
				// only single entry and no addons => no need to question further ...
				umgebung_t::objfilename = (std::string)entry.button->get_text() + "/";
			}
		}
		y += BUTTON_HEIGHT;
	}
	chdir( umgebung_t::program_dir );

	button_frame.set_groesse( koord( get_window_size().x-1, y ) );
	set_window_size(koord(get_window_size().x, TITLEBAR_HEIGHT+30+y+3*LINESPACE+4+1));
}


void pakselector_t::set_window_size(koord groesse)
{
	if(groesse.y>display_get_height()-70) {
		// too large ...
		groesse.y = ((display_get_height()-TITLEBAR_HEIGHT-30-3*LINESPACE-4-1)/BUTTON_HEIGHT)*BUTTON_HEIGHT+TITLEBAR_HEIGHT+30+3*LINESPACE+4+1-70;
		// position adjustment will be done automatically ... nice!
	}
	gui_frame_t::set_window_size(groesse);
	groesse = get_window_size();

	sint16 y = 0;

	slist_iterator_tpl <entry> iter (entries);

	while(iter.next()) 
	{
		const entry & entry = iter.get_current();

		// resize all but delete button
		if(  entry.button->is_visible()  ) {
			button_t*    button1 = entry.del;
			button1->set_pos( koord( button1->get_pos().x, y ) );
			button_t*    button2 = entry.button;
			gui_label_t* label   = entry.label;
			button2->set_pos( koord( button2->get_pos().x, y ) );
			button2->set_groesse(koord( groesse.x/2-40, BUTTON_HEIGHT));
			label->set_pos(koord(groesse.x/2-40+30, y+2));
			y += BUTTON_HEIGHT;
		}
	}

	button_frame.set_groesse(koord(groesse.x,y));
	scrolly.set_groesse(koord(groesse.x,groesse.y-TITLEBAR_HEIGHT-30-3*LINESPACE-4-1));
}
