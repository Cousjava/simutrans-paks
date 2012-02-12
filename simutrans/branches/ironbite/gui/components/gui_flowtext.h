#ifndef GUI_COMPONENTS_FLOWTEXT_H
#define GUI_COMPONENTS_FLOWTEXT_H

#include <string>

#include "gui_action_creator.h"
#include "gui_komponente.h"

struct node_t;
struct hyperlink_t;

template <class T> class slist_tpl;

/**
 * A component for floating text.
 * @author Hj. Malthaner
 */
class gui_flowtext_t :
	public gui_action_creator_t,
	public gui_component_t
{
public:
	gui_flowtext_t();
	~gui_flowtext_t();

	/**
	 * Sets the text to display.
	 * @author Hj. Malthaner
	 */
	void set_text(const char* text);

	const char* get_title() const;

	koord get_preferred_size();

	koord get_text_size();

	/**
	 * Paints the component
	 * @author Hj. Malthaner
	 */
	void zeichnen(koord offset);

	bool infowin_event(event_t const*) OVERRIDE;

	bool dirty;
	koord last_offset;

private:
	koord output(koord pos, bool doit, bool return_max_width=true);

	slist_tpl<node_t *>      * nodes;
	slist_tpl<hyperlink_t *> * links;

	char title[128];
};

#endif
