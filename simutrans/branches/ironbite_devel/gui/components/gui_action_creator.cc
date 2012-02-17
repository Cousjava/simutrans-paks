/*
 * Copyright (c) 1997 - 2001 Hj. Malthaner
 *
 * This file is part of the Simutrans project under the artistic licence.
 * (see licence.txt)
 */


#include "gui_action_creator.h"
#include "../../tpl/slist_tpl.h"


gui_action_creator_t::gui_action_creator_t()
{
	listeners = new slist_tpl <action_listener_t *>();
}

gui_action_creator_t::~gui_action_creator_t()
{
	delete listeners;
}

/**
 * Inform all listeners that an action was triggered.
 * @author Hj. Malthaner
 */
void gui_action_creator_t::call_listeners(value_t v)
{
	slist_iterator_tpl<action_listener_t *> iter (listeners);
	while (iter.next() && !iter.get_current()->action_triggered(this, v)) {}
}

/**
 * Add a new listener to this text input field.
 * @author Hj. Malthaner
 */
void gui_action_creator_t::add_listener(action_listener_t * l) 
{
	listeners->insert(l); 
}
