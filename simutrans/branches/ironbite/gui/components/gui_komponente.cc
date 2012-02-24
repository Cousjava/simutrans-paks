#include "gui_komponente.h"

/**
 * A component can only be focusable when it is visible
 * @author Knightly
 */
bool gui_komponente_t::is_focusable() 
{
	return visible && focusable; 
}

/**
* Vorzugsweise sollte diese Methode zum Setzen der Größe benutzt werden,
* obwohl groesse public ist.
* @author Hj. Malthaner
*/
void gui_komponente_t::set_groesse(koord groesse) 
{
	this->groesse = groesse;
}

/**
 * Instead of accessing the "groesse" member, use this method to set a component's size
 * 
 * @author Hj. Malthaner
 */
void gui_komponente_t::set_size(const int w, const int h) 
{
	set_groesse(koord(w, h));
}


/**
* Prüft, ob eine Position innerhalb der Komponente liegt.
* @author Hj. Malthaner
*/
bool gui_komponente_t::getroffen(int x, int y) 
{
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
bool gui_komponente_t::infowin_event(const event_t *) { return false; }

/**
 * returns element that has the focus
 * other derivates like scrolled list of tabs want to
 * return a component out of their selection
 */
gui_komponente_t * gui_komponente_t::get_focus() { return is_focusable() ? this : 0; }

/**
 * Get the relative position of the focused component.
 * Used for auto-scrolling inside a scroll pane.
 * @author Knightly
 */
koord gui_komponente_t::get_focus_pos() { return pos; }
