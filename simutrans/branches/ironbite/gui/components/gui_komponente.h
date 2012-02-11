/*
 * Copyright (c) 1997 - 2001 Hj. Malthaner
 *
 * This file is part of the Simutrans project under the artistic licence.
 * (see licence.txt)
 */

#ifndef ifc_gui_komponente_h
#define ifc_gui_komponente_h

#include "../../dataobj/koord.h"

struct event_t;

/**
 * Komponenten von Fenstern sollten von dieser Klassse abgeleitet werden.
 *
 * @autor Hj. Malthaner
 */
class gui_component_t
{
private:
	/**
	* allow component to show/hide itself
	* @author hsiegeln
	*/
	bool visible:1;

	/**
	* some components might not be allowed to gain focus
	* for example: gui_textarea_t
	* this flag can be set to true to deny focus request for a gui_component always
	* @author hsiegeln
	*/
	bool focusable:1;

protected:
	/**
	 * Position der Komponente. Eintraege sind relativ zu links/oben der
	 * umgebenden Komponente.
	 * @author Hj. Malthaner
	 */
	koord pos;

public:
	/**
	* Basic contructor, initialises member variables
	* @author Hj. Malthaner
	*/
	gui_component_t(bool _focusable = false) : visible(true), focusable(_focusable) {}

	/**
	* Virtueller Destruktor, damit Klassen sauber abgeleitet werden k�nnen
	* @author Hj. Malthaner
	*/
	virtual ~gui_component_t() {}

	void set_focusable(bool yesno) { focusable = yesno; }

	// Knightly : a component can only be focusable when it is visible
	virtual bool is_focusable() { return visible && focusable; }

	/**
	* Sets component to be shown/hidden
	* @author Hj. Malthaner
	*/
	void set_visible(bool yesno) {
		visible = yesno;
	}


	/**
	* Checks if component should be displayed
	* @author Hj. Malthaner
	*/
	bool is_visible() const {return visible;}

	/**
	* Vorzugsweise sollte diese Methode zum Setzen der Position benutzt werden,
	* obwohl pos public ist.
	* @author Hj. Malthaner
	*/
	void set_pos(koord pos) {
		this->pos = pos;
	}

	/**
	* Vorzugsweise sollte diese Methode zum Abfragen der Position benutzt werden,
	* obwohl pos public ist.
	* @author Hj. Malthaner
	*/
	koord get_pos() const {
		return pos;
	}

	/**
	 * Component size
	 * @author Hj. Malthaner
	 */
	koord groesse;

	/**
	 * Vorzugsweise sollte diese Methode zum Setzen der Gr��e benutzt werden,
	 * obwohl groesse public ist.
	 * @author Hj. Malthaner
	 */
	virtual void set_groesse(const koord groesse) {this->groesse = groesse;}
	
	/**
 	 * Instead of accessing the "groesse" member, use this method to set a component's size
 	 * 
	 * @author Hj. Malthaner
	 */
	virtual void set_size(const int w, const int h) {set_groesse(koord(w, h));}

	/**
	* Vorzugsweise sollte diese Methode zum Abfragen der Gr��e benutzt werden,
	* obwohl groesse public ist.
	* @author Hj. Malthaner
	*/
	koord get_groesse() const {
		return groesse;
	}

	/**
	* Pr�ft, ob eine Position innerhalb der Komponente liegt.
	* @author Hj. Malthaner
	*/
	virtual bool getroffen(int x, int y) {
		return (pos.x <= x && pos.y <= y && (pos.x+groesse.x) > x && (pos.y+groesse.y) > y);
	}

	/**
	* deliver event to a component if
	* - component has focus
	* - mouse is over this component
	* - event for all components
	* @return: true for swalloing this event
	* @author Hj. Malthaner
	* prissi: default -> do nothing
	*/
	virtual bool infowin_event(const event_t *) { return false; }

	/**
	* Zeichnet die Komponente
	* @author Hj. Malthaner
	*/
	virtual void zeichnen(koord offset) = 0;

	/**
	 * returns element that has the focus
	 * other derivates like scrolled list of tabs want to
	 * return a component out of their selection
	 */
	virtual gui_component_t *get_focus() { return is_focusable() ? this : 0; }

	/**
	 * Get the relative position of the focused component.
	 * Used for auto-scrolling inside a scroll pane.
	 * @author Knightly
	 */
	virtual koord get_focus_pos() { return pos; }
};

#endif
