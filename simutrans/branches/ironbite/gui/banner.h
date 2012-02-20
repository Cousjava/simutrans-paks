/*
 * Copyright (c) 1997 - 2004 Hj. Malthaner
 *
 * This file is part of the Simutrans project under the artistic licence.
 * (see licence.txt)
 */

#ifndef banner_h
#define banner_h

#include "gui_frame.h"

class karte_t;
class banner_data_t;

/**
 * Game splash screen (aka banner)
 *
 * @author Hj. Malthaner
 */
class banner_t : public gui_frame_t
{
private:
	banner_data_t * ooo;

	sint32 last_ms;
	int line;
	int xoff, yoff;

public:
	banner_t(karte_t *welt);
	virtual ~banner_t();

	virtual bool has_title() const { return false; }

        /**
         * Display game splash screen (aka banner)
         *
 	 * komponente neu zeichnen. Die übergebenen Werte beziehen sich auf
	 * das Fenster, d.h. es sind die Bildschirkoordinaten des Fensters
	 * in dem die Komponente dargestellt wird.
	 * @author Hj. Malthaner
	 */
	virtual void zeichnen(koord pos, koord gr);
};

#endif
