/*
 * Copyright (c) 1997 - 2001 Hansjörg Malthaner
 *
 * This file is part of the Simutrans project under the artistic licence.
 * (see licence.txt)
 */

#include <string.h>

#include "../../macros.h"
#include "../../simdebug.h"
#include "../../simevent.h"
#include "../../simgraph.h"
#include "../../simcolor.h"
#include "../../simwin.h"
#include "../../utils/simstring.h"

#include "gui_combobox.h"
#include "gui_textinput.h"
#include "gui_button.h"
#include "gui_scrolled_list.h"
#include "gui_scrolled_item.h"

class gui_combobox_data_t
{
public:
	
	gui_combobox_data_t() : droplist(gui_scrolled_list_t::select)
	{
	}
	
	char editstr[128], old_editstr[128];

	// buttons for setting selection manually
	gui_textinput_t textinp;
	button_t bt_prev;
	button_t bt_next;

	/**
	 * the drop box list
	 * @author hsiegeln
	 */
	gui_scrolled_list_t droplist;

	/*
	 * flag for first call
	 */
	bool first_call;

	/*
	 * flag for ooo->finish selection
	 */
	bool finish;

	/**
	 * the max size this component can have
	 * @author hsiegeln
	 */
	koord max_size;	
};


gui_combobox_t::gui_combobox_t() : gui_komponente_t(true)	
{
	ooo = new gui_combobox_data_t();
	
	ooo->bt_prev.set_typ(button_t::arrowleft);
	ooo->bt_prev.set_pos( koord(0,2) );
	ooo->bt_prev.set_groesse( koord(10,10) );

	ooo->bt_next.set_typ(button_t::arrowright);
	ooo->bt_next.set_groesse( koord(10,10) );

	ooo->editstr[0] = 0;
	ooo->old_editstr[0] = 0;
	ooo->textinp.add_listener(this);

	ooo->first_call = true;
	ooo->finish = false;
	ooo->droplist.set_visible(false);
	ooo->droplist.add_listener(this);
	ooo->max_size = koord(0,100);

	set_groesse(get_groesse());
	set_highlight_color(0);
}

gui_combobox_t::~gui_combobox_t()
{
	delete ooo;
	ooo = 0;
}
/**
 * Events werden hiermit an die GUI-Komponenten
 * gemeldet
 * @author Hj. Malthaner
 */
bool gui_combobox_t::infowin_event(const event_t *ev)
{
	if (!ooo->droplist.is_visible()) {
DBG_MESSAGE("event","%d,%d",ev->cx, ev->cy);
		if(ooo->bt_prev.getroffen(ev->cx, ev->cy)) {
DBG_MESSAGE("event","HOWDY!");
			ooo->bt_prev.pressed = IS_LEFT_BUTTON_PRESSED(ev);
			if(IS_LEFTRELEASE(ev)) {
				value_t p;
				ooo->bt_prev.pressed = false;
				set_selection( ooo->droplist.get_selection() - 1 );
				p.i = ooo->droplist.get_selection();
				call_listeners( p );
			}
			return true;
		}
		else if(ooo->bt_next.getroffen(ev->cx, ev->cy)) {
			ooo->bt_next.pressed = IS_LEFT_BUTTON_PRESSED(ev);
			if(IS_LEFTRELEASE(ev)) {
				ooo->bt_next.pressed = false;
				value_t p;
				set_selection( ooo->droplist.get_selection() + 1 );
				p.i = ooo->droplist.get_selection();
				call_listeners(p);
			}
			return true;
		}
	}
	else if(  IS_WHEELUP(ev)  ||  IS_WHEELDOWN(ev)  ) {
		// scroll the list
		ooo->droplist.infowin_event(ev);
		return true;
	}

	// got to next/previous choice
	if(  ev->ev_class == EVENT_KEYBOARD  &&  (ev->ev_code==SIM_KEY_UP  ||  ev->ev_code==SIM_KEY_DOWN)  ) {
		value_t p;
		set_selection( ooo->droplist.get_selection() + (ev->ev_code==SIM_KEY_UP ? -1 : +1 ) );
		p.i = ooo->droplist.get_selection();
		call_listeners( p );
		return true;
	}

	if(IS_LEFTCLICK(ev) || IS_LEFTDRAG(ev) || IS_LEFTRELEASE(ev)  ) {

		if(ooo->first_call) {
			// prepare for selection

			// swallow the first mouseclick
			if(IS_LEFTRELEASE(ev)) {
				ooo->first_call = false;
			}

			ooo->droplist.set_visible(true);
			ooo->droplist.set_pos(koord(this->pos.x, this->pos.y + 16));
			ooo->droplist.request_groesse(koord(this->groesse.x, ooo->max_size.y - 16));
			set_groesse(ooo->droplist.get_groesse() + koord(0, 16));
			int sel = ooo->droplist.get_selection();
			if((uint32)sel>=(uint32)ooo->droplist.get_count()  ||  !ooo->droplist.get_element(sel)->is_valid()) {
				sel = 0;
			}
			ooo->droplist.show_selection(sel);
		}
		else if (ooo->droplist.is_visible()) {
			event_t ev2 = *ev;
			translate_event(&ev2, 0, -16);

			if(ooo->droplist.getroffen(ev->cx + pos.x, ev->cy + pos.y)  ||  IS_WHEELUP(ev)  ||  IS_WHEELDOWN(ev)) {
				ooo->droplist.infowin_event(&ev2);
				// we selected something?
				if(ooo->finish  &&  IS_LEFTRELEASE(ev)) {
					close_box();
				}
			}
			else {
				// acting on "release" is better than checking for "new selection"
				if (IS_LEFTRELEASE(ev)) {
DBG_MESSAGE("gui_combobox_t::infowin_event()","close");
					close_box();
				}
			}
		}
	} else if(ev->ev_class==INFOWIN  &&  (ev->ev_code==WIN_CLOSE  ||  ev->ev_code==WIN_UNTOP)  ) {
DBG_MESSAGE("gui_combobox_t::infowin_event()","close");
		ooo->textinp.infowin_event(ev);
		ooo->droplist.set_visible(false);
		close_box();
		// update "mouse-click-catch-area"
		set_groesse(koord(groesse.x, ooo->droplist.is_visible() ? ooo->max_size.y : 14));
	}
	else {
		// finally handle textinput
		event_t ev2 = *ev;
		translate_event(&ev2, -ooo->textinp.get_pos().x, -ooo->textinp.get_pos().y);
		return ooo->textinp.infowin_event(ev);
	}
	return true;
}



/* selction now handled via callback */
bool gui_combobox_t::action_triggered( gui_action_creator_t *komp,value_t p)
{
	if (komp == &ooo->droplist) {
DBG_MESSAGE("gui_combobox_t::infowin_event()","scroll selected %i",p.i);
		ooo->finish = true;
		set_selection(p.i);
	}
	else if (komp == &ooo->textinp) {
		rename_selected_item();
	}
	return false;
}



/**
 * Zeichnet die Komponente
 * @author Hj. Malthaner
 */
void gui_combobox_t::zeichnen(koord offset)
{
	// text changed? Then update it
	scrollitem_t *item = ooo->droplist.get_element( ooo->droplist.get_selection() );
	if(  item  &&  item->is_valid()  &&  strncmp(item->get_text(),ooo->old_editstr,127)!=0  ) {
		reset_selected_item_name();
	}

	ooo->textinp.display_with_focus( offset, (win_get_focus()==this) );

	if (ooo->droplist.is_visible()) {
		ooo->droplist.zeichnen(offset);
	}
	else {
		offset += pos;
		ooo->bt_prev.zeichnen(offset);
		ooo->bt_next.zeichnen(offset);
	}
}



/**
 * sets the selection
 * @author hsiegeln
 */
void gui_combobox_t::set_selection(int s)
{
	// try to ooo->finish renaming first
	rename_selected_item();

	if (ooo->droplist.is_visible()) {
		// visible? change also offset of scrollbar
		ooo->droplist.show_selection( s );
	}
	else {
		// just set it
		ooo->droplist.set_selection(s);
	}
	// edit the text
	reset_selected_item_name();
}


/**
 * Check whether we should rename selected item
 */
void gui_combobox_t::rename_selected_item()
{
	scrollitem_t *item = ooo->droplist.get_element(ooo->droplist.get_selection());
	// if name was not changed in the meantime, we can rename it
	if(  item  &&  item->is_valid()  &&  strncmp(item->get_text(),ooo->old_editstr,127)==0  &&  strncmp(item->get_text(),ooo->editstr,127)) {
		item->set_text(ooo->editstr);
	}
}

void gui_combobox_t::reset_selected_item_name()
{
	scrollitem_t *item = ooo->droplist.get_element(ooo->droplist.get_selection());
	if(  item==NULL  ) {
		ooo->editstr[0] = 0;
		ooo->textinp.set_text( ooo->editstr, 0  );
		ooo->droplist.set_selection(-1);
	}
	else if(  item->is_valid()  &&  strncmp(ooo->editstr,item->get_text(),127)!=0  ) {
		tstrncpy(ooo->editstr, item->get_text(), lengthof(ooo->editstr));
		ooo->textinp.set_text( ooo->editstr, sizeof(ooo->editstr));
	}
	tstrncpy(ooo->old_editstr, ooo->editstr, sizeof(ooo->old_editstr));
}



/**
* Release the focus if we had it
*/
void gui_combobox_t::close_box()
{
	if(ooo->finish) {
//DBG_MESSAGE("gui_combobox_t::infowin_event()","prepare selected %i for %d listerners",get_selection(),listeners.get_count());
		value_t p;
		p.i = ooo->droplist.get_selection();
		call_listeners(p);
		ooo->finish = false;
	}
	ooo->droplist.set_visible(false);
	set_groesse(koord(groesse.x, 14));
	ooo->first_call = true;
}


/**
 * add element to ooo->droplist
 * @author hsiegeln
 */
void gui_combobox_t::append_element(scrollitem_t *item) 
{
	ooo->droplist.append_element( item ); 
}

/**
 * remove all elements from ooo->droplist
 * @author hsiegeln
 */
void gui_combobox_t::clear_elements() { ooo->droplist.clear_elements(); }

/**
 * remove all elements from ooo->droplist
 * @author hsiegeln
 */
int gui_combobox_t::count_elements() const { return ooo->droplist.get_count(); }

/**
 * remove all elements from ooo->droplist
 * @author hsiegeln
 */
scrollitem_t * gui_combobox_t::get_element(sint32 idx) const 
{
	return ooo->droplist.get_element(idx); 
}

/**
 * sets the highlight color for the ooo->droplist
 * @author hsiegeln
 */
void gui_combobox_t::set_highlight_color(int color) { ooo->droplist.set_highlight_color(color); }

/**
 * returns the selection id
 * @author hsiegeln
 */
int gui_combobox_t::get_selection() { return ooo->droplist.get_selection(); }


void gui_combobox_t::set_groesse(koord gr)
{
	ooo->textinp.set_pos( pos+koord(12,0) );
	ooo->textinp.set_groesse( koord(gr.x-26,14) );
	ooo->bt_next.set_pos( koord(gr.x-12,2) );
	gui_komponente_t::groesse = gr;
}

/**
* set maximum size for control
* @author hsiegeln, Dwachs
*/
void gui_combobox_t::set_max_size(koord max)
{
	ooo->max_size = max;
	ooo->droplist.request_groesse(koord(this->groesse.x, ooo->max_size.y - 16));
	if (ooo->droplist.is_visible()) {
		set_groesse(ooo->droplist.get_groesse() + koord(0, 16));
	}
}
