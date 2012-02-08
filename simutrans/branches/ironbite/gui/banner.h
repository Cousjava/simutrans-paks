/*
 * Copyright (c) 1997 - 2004 Hj. Malthaner
 *
 * This file is part of the Simutrans project under the artistic licence.
 * (see licence.txt)
 */

#ifndef banner_h
#define banner_h

#include "components/gui_button.h"
#include "gui_frame.h"

class karte_t;

/**
 * Game splash screen (aka banner)
 *
 * @author Hj. Malthaner
 */
class banner_t : public gui_frame_t, action_listener_t
{
private:
	sint32 last_ms;
	int line;
	int xoff, yoff;

	button_t new_map, load_map, join_map, quit;

	karte_t *welt;

public:
	banner_t( karte_t *welt );

	bool has_sticky() const { return false; }

	virtual bool has_title() const { return false; }

	/**
	 * @return Window title
	 * @author Hj. Malthaner
	 */
	const char *get_name() const {return ""; }

        /**
         * Display game splash screen (aka banner)
         *
         * @author Hj. Malthaner
         */
	void zeichnen(koord pos, koord gr);

	bool action_triggered( gui_action_creator_t *komp, value_t extra);
};

#endif
