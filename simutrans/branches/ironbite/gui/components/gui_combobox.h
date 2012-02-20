/*
 * with a connected edit field
 *
 * Copyright (c) 1997 - 2001 Hansjörg Malthaner
 *
 * This file is part of the Simutrans project under the artistic licence.
 * (see licence.txt)
 */

#ifndef gui_components_gui_combobox_h
#define gui_components_gui_combobox_h

#include "gui_komponente.h"
#include "gui_action_creator.h"


class scrollitem_t;
class gui_combobox_data_t;

class gui_combobox_t :
	public gui_action_creator_t,
	public gui_komponente_t,
	public action_listener_t
{
private:

	/**
	 * renames the selected item if necessary
	 */
	void rename_selected_item();

	/**
	 * resets the input field to the name of the item
	 */
	void reset_selected_item_name();

	gui_combobox_data_t * ooo;

public:
	gui_combobox_t();
	virtual ~gui_combobox_t();

	bool infowin_event(event_t const*) OVERRIDE;

	bool action_triggered(gui_action_creator_t*, value_t) OVERRIDE;

	/**
	 * Zeichnet die Komponente
	 * @author Hj. Malthaner
	 */
	void zeichnen(koord offset);

	/**
	 * add element to droplist
	 * @author hsiegeln
	 */
	void append_element(scrollitem_t *item);

	/**
	 * remove all elements from droplist
	 * @author hsiegeln
	 */
	void clear_elements();

	/**
	 * remove all elements from droplist
	 * @author hsiegeln
	 */
	int count_elements() const;

	/**
	 * remove all elements from droplist
	 * @author hsiegeln
	 */
	scrollitem_t * get_element(sint32 idx) const;

	/**
	 * sets the highlight color for the droplist
	 * @author hsiegeln
	 */
	void set_highlight_color(int color);

	/**
	 * set maximum size for control
	 * @author hsiegeln
	 */
	void set_max_size(koord max);

	/**
	 * returns the selection id
	 * @author hsiegeln
	 */
	int get_selection();

	/**
	 * sets the selection
	 * @author hsiegeln
	 */
	void set_selection(int s);

	void set_groesse(koord groesse) OVERRIDE;

	/**
	 * called when the focus should be released
	 * does some cleanup before releasing
	 * @author hsiegeln
	 */
	void close_box();
};

#endif
