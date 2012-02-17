/*
 * Copyright (c) 1997 - 2001 Hj. Malthaner
 *
 * This file is part of the Simutrans project under the artistic licence.
 * (see licence.txt)
 */

#ifndef gui_kennfarbe_h
#define gui_kennfarbe_h

#include "gui_frame.h"
#include "components/action_listener.h"
#include "components/gui_button.h"
#include "components/gui_image.h"
#include "components/gui_label.h"

/**
 * This dialog allows the player to choose their preferred color for
 * vehicles and buildings.
 *
 * @author Hj. Malthaner
 */
class farbengui_t : public gui_frame_t, action_listener_t
{
private:
	spieler_t *sp;
	gui_label_t txt;
	gui_label_t primary;
	gui_label_t secondary;
	gui_image_t bild;

	button_t player_color_1[28];
	button_t player_color_2[28];

public:
	farbengui_t(spieler_t *sp);

	/**
	 * Manche Fenster haben einen Hilfetext assoziiert.
	 * @return den Dateinamen f�r die Hilfe, oder NULL
	 * @author Hj. Malthaner
	 */
	const char * get_help_file() const { return "color.txt"; }

	bool action_triggered(gui_action_creator_t*, value_t) OVERRIDE;
};

#endif
