#include <string.h>

#include "gui_flowtext.h"

#include "../../simcolor.h"
#include "../../simevent.h"
#include "../../simgraph.h"
#include "../../utils/simstring.h"
#include "../../utils/cbuffer_t.h"


#include "../../tpl/slist_tpl.h"

enum attributes
{
	ATT_NONE,
	ATT_NEWLINE,
	ATT_A_START,      ATT_A_END,
	ATT_H1_START,     ATT_H1_END,
	ATT_EM_START,     ATT_EM_END,
	ATT_IT_START,     ATT_IT_END,
	ATT_STRONG_START, ATT_STRONG_END,
	ATT_UNKNOWN
};

struct node_t
{
	node_t(const char * string, attributes att_) : text(string), att(att_) {}

	cbuffer_t text;
	attributes att;
};

/**
 * Hyperlink position container
 * @author Hj. Malthaner
 */
struct hyperlink_t
{
	hyperlink_t(const char * string) : text(string) {}

	koord        tl;    // top left display position
	koord        br;    // bottom right display position
	cbuffer_t text;
};

class gui_flowtext_data_t
{
public:
	
	slist_tpl <node_t *>      nodes;
	slist_tpl <hyperlink_t *> links;

	bool dirty;
	koord last_offset;

	char title[128];

	~gui_flowtext_data_t()
	{
		slist_iterator_tpl <node_t *> node_i (nodes);
		while(node_i.next()) delete node_i.access_current();

		slist_iterator_tpl <hyperlink_t *> links_i (links);
		while(links_i.next()) delete links_i.access_current();
	}
};


gui_flowtext_t::gui_flowtext_t()
{
	ooo = new gui_flowtext_data_t();
	
	ooo->title[0] = '\0';
	ooo->last_offset = koord::invalid;
	ooo->dirty = true;
}

gui_flowtext_t::~gui_flowtext_t()
{
	delete ooo;
	ooo = 0;
}

void gui_flowtext_t::set_text(const char *text)
{
	// purge all old texts
	ooo->nodes.clear();
	ooo->links.clear();

	// Hajo: danger here, longest word in text
	// must not exceed 511 chars!
	char word[512];
	attributes att = ATT_NONE;

	const unsigned char* tail = (const unsigned char*)text;
	const unsigned char* lead = (const unsigned char*)text;

	// hyperref param
	// std::string param;

	while (*tail) 
	{
		if (*lead == '<') 
		{
			bool endtag = false;
			if (lead[1] == '/') {
				endtag = true;
				lead++;
				tail++;
			}

			// parse a tag (not allowed to exceed 511 letters)
			for (int i = 0; *lead != '>' && *lead > 0 && i < 511; i++) {
				lead++;
			}

			strncpy(word, (const char*)tail + 1, lead - tail - 1);
			word[lead - tail - 1] = '\0';
			lead++;

			if (word[0] == 'p' || (word[0] == 'b' && word[1] == 'r')) {
				att = ATT_NEWLINE;
			} else if (word[0] == 'a') {
				if (!endtag) 
				{
					att = ATT_A_START;
					word [strlen(word) - 9] = 0;
					ooo->links.append(new hyperlink_t(word + 8));
				}
				else 
				{
					att = ATT_A_END;
				}
			} else if (word[0] == 'h' && word[1] == '1') {
				att = endtag ? ATT_H1_END : ATT_H1_START;
			} else if (word[0] == 'i') {
				att = endtag ? ATT_IT_END : ATT_IT_START;
			} else if (word[0] == 'e' && word[1] == 'm') {
				att = endtag ? ATT_EM_END : ATT_EM_START;
			} else if (word[0] == 's' && word[1] == 't') {
				att = endtag ? ATT_STRONG_END : ATT_STRONG_START;
			} else if (!endtag && strcmp(word, "title") == 0) {
				// title tag
				const unsigned char* title_start = lead;

				// parse title tag (again, enforce 511 limit)
				for (int i = 0; *lead != '<' && *lead > 0 && i < 511; i++) {
					lead++;
				}

				tstrncpy(ooo->title, (const char*)title_start, lead - title_start+1);

				// close title tag (again, enforce 511 limit)
				for (int i = 0; *lead != '>' && *lead > 0 && i < 511; i++) {
					lead++;
				}
				if (*lead == '>') {
					lead++;
				}
				att = ATT_UNKNOWN;
			}
			else {
				// ignore all unknown
				att = ATT_UNKNOWN;
			}
			// end of commands
		}
		else if(  lead[0]=='&'  ) {
			if(  lead[2]=='t'  &&  lead[3]==';'  ) {
				// either gt or lt
				strcpy( word, lead[1]=='l' ? "<" : ">" );
				lead += 4;
			}
			else if(  lead[1]=='#'  ) {
				// decimal number
				word[0] = atoi( (const char *)lead+2 );
				word[1] = 0;
				while( *lead++!=';'  ) {
				}
			}
			else {
				// only copy ampersand
				strcpy( word, "&" );
				lead ++;
			}
			att = ATT_NONE;
		}
		else {

			// parse a word (and obey limits)
			for (int i = 0;  *lead != '<'  &&  *lead > 32  &&  i < 511  &&  *lead != '&'; i++) {
				lead++;
			}
			tstrncpy(word, (const char*)tail, lead - tail + 1);

			att = ATT_NONE;
		}

		if (att != ATT_UNKNOWN) { // only add know commands
			ooo->nodes.append(new node_t(word, att));
		}

		// skip white spaces
		while (*lead <= 32 && *lead > 0) {
			lead++;
		}
		tail = lead;
	}
	ooo->dirty = true;
}


const char* gui_flowtext_t::get_title() const
{
	return ooo->title;
}


koord gui_flowtext_t::get_preferred_size()
{
	return output(koord(0, 0), false);
}

koord gui_flowtext_t::get_text_size()
{
	return output(koord(0, 0), false, false);
}

void gui_flowtext_t::zeichnen(koord offset)
{
	offset += pos;
	if(offset!=ooo->last_offset) {
		ooo->dirty = true;
		ooo->last_offset = offset;
	}
	output(offset, true);
}


koord gui_flowtext_t::output(koord offset, bool doit, bool return_max_width)
{
	const int width = groesse.x;

	int xpos         = 0;
	int ypos         = 0;
	int color        = COL_BLACK;
	int double_color = COL_BLACK;
	bool double_it   = false;
	int max_width    = width;
	int text_width   = width;

	hyperlink_t * link = 0;
	
	slist_iterator_tpl <node_t *> iter (ooo->nodes); 
	slist_iterator_tpl <hyperlink_t *> link_iter (ooo->links);
	
	while(iter.next())
	{
		node_t * node = iter.get_current();
		
		switch (node->att) 
		{
			case ATT_NONE: 
				{
				int nxpos = xpos + proportional_string_width(node->text) + 4;

				if (nxpos >= width) {
					if (nxpos - xpos > max_width) {
						// word too long
						max_width = nxpos;
					}
					nxpos -= xpos;
					xpos = 0;
					ypos += LINESPACE;
				}
				if (nxpos >= text_width) {
					text_width = nxpos;
				}

				if (doit) 
				{
					if (double_it) 
						{
						display_proportional_clip(offset.x + xpos + 1, offset.y + ypos + 1, node->text, 0, double_color, false);
					}
					display_proportional_clip(offset.x + xpos, offset.y + ypos, node->text, 0, color, false);
				}

				xpos = nxpos;
				break;
			}

			case ATT_NEWLINE:
				xpos = 0;
				ypos += LINESPACE;
				break;

			case ATT_A_START:
				color = COL_BLUE;
			
				if(link_iter.next())
				{
					link = link_iter.get_current();
					link->tl.x = xpos;
					link->tl.y = ypos;
				}
				
				break;

			case ATT_A_END:
				
				// see if there was a start tag ...
				if(link)
				{				
					link->br.x = xpos - 4;
					link->br.y = ypos + LINESPACE;

					if (link->br.x < link->tl.x) {
						link->tl.x = 0;
						link->tl.y = ypos;
					}

					if (doit) 
					{
						display_fillbox_wh_clip(link->tl.x + offset.x, link->tl.y + offset.y + 10, link->br.x - link->tl.x, 1, color, false);
					}
					
					link = 0;
				}
				
				color = COL_BLACK;
				break;

			case ATT_H1_START:
				color        = COL_ORANGE;
				double_color = COL_BLACK;
				double_it    = true;
				break;

			case ATT_H1_END:
				color     = COL_BLACK;
				double_it = false;
				if (doit) {
					display_fillbox_wh_clip(offset.x + 1, offset.y + ypos + 10 + 1, xpos - 4, 1, COL_WHITE, false);
					display_fillbox_wh_clip(offset.x,     offset.y + ypos + 10,     xpos - 4, 1, color,     false);
				}
				xpos = 0;
				ypos += LINESPACE;
				break;

			case ATT_EM_START:
				color = COL_WHITE;
				break;

			case ATT_EM_END:
				color = COL_BLACK;
				break;

			case ATT_IT_START:
				color        = COL_BLACK;
				double_color = COL_YELLOW;
				double_it    = true;
				break;

			case ATT_IT_END:
				double_it = false;
				break;

			case ATT_STRONG_START:
				if(  !double_it  ) {
					color = COL_RED;
				}
				break;

			case ATT_STRONG_END:
				if(  !double_it  ) {
					color = COL_BLACK;
				}
				break;

			default: break;
		}
	}
	if(ooo->dirty) {
		mark_rect_dirty_wc( offset.x, offset.y, offset.x+max_width, offset.y+ypos+LINESPACE );
		ooo->dirty = false;
	}
	return koord( return_max_width ? max_width : text_width, ypos + LINESPACE);
}


bool gui_flowtext_t::infowin_event(const event_t* ev)
{
	if (IS_LEFTCLICK(ev)) 
	{
		// scan links for hit
		slist_iterator_tpl <hyperlink_t *> iter (ooo->links);

		while(iter.next())
		{
			hyperlink_t * link = iter.get_current();
			
			if (link->tl.x <= ev->cx && ev->cx < link->br.x &&
			    link->tl.y <= ev->cy && ev->cy < link->br.y) 
			{
				const char * text = link->text;
				call_listeners((const void*)text);
			}
		}
	}
	return true;
}
