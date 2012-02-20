#ifndef gui_scrolled_item_t_h
#define gui_scrolled_item_t_h

#include "../../simtypes.h"

class plainstring;

/**
 * Container for list entries - consisting of text and color
 */
class scrollitem_t 
{
private:
	int color;
public:
	scrollitem_t( int col ) { color = col; }
	virtual ~scrollitem_t() {}
	virtual uint8 get_color() { return color; }
	virtual void set_color(uint8 col) { color = col; }
	virtual char const* get_text() const = 0;
	virtual void set_text(char const*) = 0;
	virtual bool is_valid() { return true; }	//  can be used to indicate invalid entries
};


// editable text
class var_text_scrollitem_t : public scrollitem_t {
private:
	plainstring * text;

public:

	var_text_scrollitem_t(char const* const t, uint8 const col);
	virtual ~var_text_scrollitem_t();

	char const * get_text() const OVERRIDE;

	void set_text(char const *t) OVERRIDE;
};

// only uses pointer, non-editable
class const_text_scrollitem_t : public scrollitem_t {
private:
	const char * text;
public:
	const_text_scrollitem_t( const char *t, uint8 col ) : scrollitem_t(col) { text = t; }
	char const * get_text() const OVERRIDE { return text; }
	void set_text(char const *) OVERRIDE {}
};


#endif
