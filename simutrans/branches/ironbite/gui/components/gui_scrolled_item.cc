#include "gui_scrolled_item.h"
#include "../../utils/plainstring.h"


// editable text


var_text_scrollitem_t::var_text_scrollitem_t(char const* const t, uint8 const col) : scrollitem_t(col)
{
	text = new plainstring(t);
}

var_text_scrollitem_t::~var_text_scrollitem_t()
{
	delete text;
	text = 0;
}

char const * var_text_scrollitem_t::get_text() const OVERRIDE 
{
	return *text; 
}

void var_text_scrollitem_t::set_text(char const *t) OVERRIDE 
{
	delete text;
	text = new plainstring(t); 
}
