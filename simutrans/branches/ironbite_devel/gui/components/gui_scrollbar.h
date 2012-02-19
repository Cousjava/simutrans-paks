#ifndef gui_scrollbar_h
#define gui_scrollbar_h

#include "gui_action_creator.h"
#include "gui_komponente.h"
#include "../../dataobj/koord.h"

class event_t;
class gui_scrollbar_data_t;

/**
 * Scrollbar class
 * scrollbar can be horizontal or vertical
 *
 * @author Niels Roest, additions by Hj. Malthaner
 */
class scrollbar_t :
	public gui_action_creator_t,
	public gui_component_t
{
public:
	enum type { vertical, horizontal };

	// width/height of bar part
	static int BAR_SIZE;

private:

	gui_scrollbar_data_t * ooo;

	void reposition_buttons();
	void button_press(sint32 number); // arrow button
	void space_press(sint32 updown); // space in slidebar hit
	sint32 slider_drag(sint32 amount); // drags slider. returns dragged amount.


public:
	/**
	 * type is either scrollbar_t::horizontal or scrollbar_t::vertical
	 */
	scrollbar_t(enum type type);
	
	virtual ~scrollbar_t();

	/**
	 * Vorzugsweise sollte diese Methode zum Setzen der Gr��e benutzt werden,
	 * obwohl groesse public ist.
	 * @author Hj. Malthaner
	 */
	void set_groesse(koord groesse) OVERRIDE;

	void set_scroll_amount(const sint32 sa);
	void set_scroll_discrete(const bool sd);

	/**
	 * size is visible size, area is total size in pixels of _parent_.
	 */
	void set_knob(sint32 knob_size, sint32 knob_area);

	sint32 get_knob_offset() const;

	void set_knob_offset(sint32 v);

	bool infowin_event(event_t const *) OVERRIDE;

	void zeichnen(koord pos);
};

#endif
