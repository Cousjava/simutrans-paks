/*
 * Copyright (c) 2002 - 2003 Hj. Malthaner
 *
 * This file is part of the Simutrans project under the artistic licence.
 * (see licence.txt)
 */

#ifndef citylist_frame_t_h
#define citylist_frame_t_h

#include "gui_frame.h"

class karte_t;
class citylist_frame_data_t;

/**
 * City list window
 * @author Hj. Malthaner
 */
class citylist_frame_t : public gui_frame_t
{

private:
	
	/**
	 * "Total insulation" pattern.
	 * @author Hj. Malthaner
	 */
	citylist_frame_data_t * ooo;

public:

    citylist_frame_t(karte_t * welt);
    virtual ~citylist_frame_t();

   /**
     * Komponente neu zeichnen. Die übergebenen Werte beziehen sich auf
     * das Fenster, d.h. es sind die Bildschirkoordinaten des Fensters
     * in dem die Komponente dargestellt wird.
     * @author Hj. Malthaner
     */
    void zeichnen(koord pos, koord gr);

    /**
     * resize window in response to a resize event
     * @author Hj. Malthaner
     */
    void resize(const koord delta);

    /**
     * Manche Fenster haben einen Hilfetext assoziiert.
     * @return den Dateinamen für die Hilfe, oder NULL
     * @author V. Meyer
     */
    const char * get_hilfe_datei() const {return "citylist_filter.txt"; }

};

#endif
