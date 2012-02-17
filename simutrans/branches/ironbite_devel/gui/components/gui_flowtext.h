#ifndef GUI_COMPONENTS_FLOWTEXT_H
#define GUI_COMPONENTS_FLOWTEXT_H

#include "gui_action_creator.h"
#include "gui_komponente.h"

class gui_flowtext_data_t;

/**
 * A component for floating text.
 *
 * @author Hj. Malthaner
 */
class gui_flowtext_t :
	public gui_action_creator_t,
	public gui_component_t
{
public:
	gui_flowtext_t();
	virtual ~gui_flowtext_t();

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

private:
	koord output(koord pos, bool doit, bool return_max_width=true);

	gui_flowtext_data_t * ooo;
};

#endif
