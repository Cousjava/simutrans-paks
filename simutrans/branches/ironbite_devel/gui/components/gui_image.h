/*
 * just displays an image
 *
 * Copyright (c) 1997 - 2001 Hj. Malthaner
 *
 * This file is part of the Simutrans project under the artistic licence.
 * (see licence.txt)
 */

#ifndef gui_image_h
#define gui_image_h

#include "../../simimg.h"
#include "../../simgraph.h"
#include "gui_komponente.h"


class gui_image_t : public gui_component_t
{
private:
	image_id id;
	uint16 player_nr;

public:
	gui_image_t( const image_id i=IMG_LEER, const uint8 p=0 ) : id(i), player_nr(p) { }

    void set_image( const image_id i ) { id = i; }

    /**
     * Zeichnet die Komponente
     * @author Hj. Malthaner
     */
    void zeichnen( koord offset ) { display_base_img( id, pos.x+offset.x, pos.y+offset.y, (sint8)player_nr, false, true ); }
};

#endif
