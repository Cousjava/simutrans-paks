#ifndef FONT_H
#define FONT_H

#include "simtypes.h"

class font_t
{
public:
	sint16	height;
	sint16	descent;
	uint16 num_chars;
	uint8 *screen_width;
	uint8 *char_data;
        uint8 line_spacing;

	/**
	 * Calculate width of a character
	 */
	int get_char_width(const int c);
};

// Hajo: don't know where this is currently used ...
// I guess it should be replaced some day.
extern int large_font_height;

// Hajo: at the moment this is our only font.
extern struct font_t * large_font_p;


/*
 * characters are stored dense in a array
 * first 12 bytes are the first row
 * then come nibbles with the second part (6 bytes)
 * then the start offset for drawing
 * then a byte with the real width
 */
#define CHARACTER_LEN (20)


/**
 * Loads a font
 */
bool load_font(font_t* font, const char* fname);

#endif
