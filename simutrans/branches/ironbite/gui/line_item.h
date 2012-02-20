#ifndef line_scrollitem_t_h
#define line_scrollitem_t_h

#include "components/gui_scrolled_item.h"
#include "../linehandle_t.h"

/**
 * Container for list entries - consisting of text and color
 */
class line_scrollitem_t : public scrollitem_t
{
private:
	linehandle_t line;
public:
	line_scrollitem_t( linehandle_t l );
	COLOR_VAL get_color() OVERRIDE;
	linehandle_t get_line() const { return line; }
	void set_color(COLOR_VAL) OVERRIDE { assert(false); }
	char const* get_text() const OVERRIDE;
	void set_text(char const*) OVERRIDE;
	bool is_valid() OVERRIDE { return line.is_bound(); }	//  can be used to indicate invalid entries
};

#endif
