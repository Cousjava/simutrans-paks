/*
 * Copyright (c) 2001 Hj. Malthaner
 *
 * This file is part of the Simutrans project under the artistic license.
 */

/*
 * Versuch einer Graphic fuer Simulationsspiele
 * Hj. Malthaner, Aug. 1997
 *
 *
 * 3D, isometrische Darstellung
 */
#ifndef simgraph_h
#define simgraph_h

#define LINESPACE 11

struct clip_dimension 
{
    int x, xx, w, y, yy, h;
};


// helper macros

// save the current clipping and set a new one
#define PUSH_CLIP(x,y,w,h) \
{\
const struct clip_dimension p_cr = display_get_clip_wh(); \
display_set_clip_wh(x, y, w, h);

// restore a saved clipping rect
#define POP_CLIP() \
display_set_clip_wh(p_cr.x, p_cr.y, p_cr.w, p_cr.h); \
}

/**
 * helper functions for clipping along tile borders
 * @author Dwachs
 */
void add_poly_clip(int x0_,int y0_, int x1, int y1, int ribi=15);
void clear_all_poly_clip();
void activate_ribi_clip(int ribi=15);

/* Do no access directly, use the get_tile_raster_width()
 * macro instead.
 * @author Hj. Malthaner
 */
#define get_tile_raster_width()    (tile_raster_width)
extern int tile_raster_width;

#define get_base_tile_raster_width() (base_tile_raster_width)
extern int base_tile_raster_width;

/* changes the raster width after loading */
int display_set_base_raster_width(int new_raster);


int display_zoom_in(void);
int display_zoom_out(void);


/**
 * Initialises the graphics module
 * @author Hj. Malthaner
 */
void simgraph_init(int width, int height, int fullscreen);
int is_display_init(void);
void simgraph_exit();
void simgraph_resize(int w, int h);

/*
 * uncomment to enable unicode
 */
#define UNICODE_SUPPORT

int	display_set_unicode(int use_unicode);

/* Loads the font
 * @author prissi
 */
bool display_load_font(const char* fname);

void display_register_image(struct bild_t*);

// delete all images above a certain number ...
void display_free_all_images_above( unsigned above );

// unzoomed offsets
void display_set_base_image_offset( unsigned bild, int xoff, int yoff );
void display_get_base_image_offset( unsigned bild, int *xoff, int *yoff, int *xw, int *yw );
// zoomed offsets
void display_get_image_offset( unsigned bild, int *xoff, int *yoff, int *xw, int *yw );
void display_get_base_image_offset( unsigned bild, int *xoff, int *yoff, int *xw, int *yw );
void display_mark_img_dirty( unsigned bild, int x, int y );

int get_mouse_x(void);
int get_mouse_y(void);

void mark_rect_dirty_wc(int x1, int y1, int x2, int y2);

int display_get_width(void);
int display_get_height(void);
void      display_set_height(int);
void      display_set_actual_width(int);


int display_get_light(void);
void display_set_light(int new_light_level);

void display_day_night_shift(int night);


// scrolls horizontally, will ignore clipping etc.
void display_scroll_band( const int start_y, const int x_offset, const int h );

// set first and second company color for player
void display_set_player_color_scheme(const int player, const int col1, const int col2 );

// display image with day and night change
void display_img_aux(const unsigned n, int xp, int yp, const signed char player_nr, const int daynight, const int dirty);
#define display_img( n, x, y, d ) display_img_aux( (n), (x), (y), 0, true, (d) )

/**
 * draws the images with alpha, either blended or as outline
 * @author kierongreen
 */
void display_rezoomed_img_blend(const unsigned n, int xp, int yp, const signed char player_nr, const int color_index, const int daynight, const int dirty);
#define display_img_blend( n, x, y, c, dn, d ) display_rezoomed_img_blend( (n), (x), (y), 0, (c), (dn), (d) )

// display image with color (if there) and optinal day and nightchange
void display_color_img(const unsigned n, int xp, int yp, const signed char player_nr, const int daynight, const int dirty);

// display unzoomed image
void display_base_img(const unsigned n, int xp, int yp, const signed char player_nr, const int daynight, const int dirty);

// Knightly : display unzoomed image with alpha, either blended or as outline
void display_base_img_blend(const unsigned n, int xp, int yp, const signed char player_nr, const int color_index, const int daynight, const int dirty);

// Knightly : pointer to image display procedures
typedef void (*display_image_proc)(const unsigned n, int xp, int yp, const signed char player_nr, const int daynight, const int dirty);
typedef void (*display_blend_proc)(const unsigned n, int xp, int yp, const signed char player_nr, const int color_index, const int daynight, const int dirty);

// Knightly : variables for storing currently used image procedure set and tile raster width
extern display_image_proc display_normal;
extern display_image_proc display_color;
extern display_blend_proc display_blend;
extern signed short current_tile_raster_width;

// Knightly : call this instead of referring to current_tile_raster_width directly
#define get_current_tile_raster_width() (current_tile_raster_width)

// Knightly : for switching between image procedure sets and setting current tile raster width
#define display_set_image_proc( is_global ) \
{ \
	if(  is_global  ) { \
		display_normal = display_img_aux; \
		display_color = display_color_img; \
		display_blend = display_rezoomed_img_blend; \
		current_tile_raster_width = get_tile_raster_width(); \
	} \
	else { \
		display_normal = display_base_img; \
		display_color = display_base_img; \
		display_blend = display_base_img_blend; \
		current_tile_raster_width = get_base_tile_raster_width(); \
	} \
}


void display_fillbox_wh(int xp, int yp, int w, int h, int color, bool dirty);
void display_fillbox_wh_clip(int xp, int yp, int w, int h, int color, bool dirty);
void display_vline_wh(int xp, int yp, int h, int color, bool dirty);
void display_vline_wh_clip(int xp, int yp, int h, int c, bool dirty);
void display_clear(void);

void display_flush_buffer(void);

void display_system_move_pointer(int dx, int dy);
void display_system_show_pointer(int yesno);
void display_system_set_pointer(int pointer);
void display_show_load_pointer(int loading);


void display_array_wh(int xp, int yp, int w, int h, const unsigned char *arr);

// compound painting routines
void display_outline_proportional(int xpos, int ypos, int text_color, int shadow_color, const char *text, int dirty);
void display_shadow_proportional(int xpos, int ypos, int text_color, int shadow_color, const char *text, int dirty);
void display_ddd_box(int x1, int y1, int w, int h, int tl_color, int rd_color);
void display_ddd_box_clip(int x1, int y1, int w, int h, int tl_color, int rd_color);


// unicode save moving in strings
int get_next_char(const char* text, int pos);
int get_prev_char(const char* text, int pos);

/**
 * For the next logical character in the text, returns the character code
 * as well as retrieves the char byte count and the screen pixel width
 * CAUTION : The text pointer advances to point to the next logical character
 * @author Knightly
 */
unsigned short get_next_char_with_metrics(const char* &text, unsigned char &byte_length, unsigned char &pixel_width);

/**
 * For the previous logical character in the text, returns the character code
 * as well as retrieves the char byte count and the screen pixel width
 * CAUTION : The text pointer recedes to point to the previous logical character
 * @author Knightly
 */
unsigned short get_prev_char_with_metrics(const char* &text, const char *const text_start, unsigned char &byte_length, unsigned char &pixel_width);

/* routines for string len (macros for compatibility with old calls) */
#define proportional_string_width(text)          display_calc_proportional_string_len_width(text, 0x7FFF)
#define proportional_string_len_width(text, len) display_calc_proportional_string_len_width(text, len)
// length of a string in pixel
int display_calc_proportional_string_len_width(const char* text, int len);

/*
 * len parameter added - use -1 for previous behaviour.
 * completely renovated for unicode and 10 bit width and variable height
 * @author Volker Meyer, prissi
 * @date  15.06.2003, 2.1.2005
 */
enum
{
	ALIGN_LEFT   = 0 << 0,
	ALIGN_MIDDLE = 1 << 0,
	ALIGN_RIGHT  = 2 << 0,
	ALIGN_MASK   = 3 << 0,
	DT_DIRTY     = 1 << 2,
	DT_CLIP      = 1 << 3
};

int display_text_proportional_len_clip(int x, int y, const char* txt, int flags, int color_index, long len);
/* macro are for compatibility */
#define display_proportional(     x,  y, txt, align, color, dirty) display_text_proportional_len_clip(x, y, txt, align | (dirty ? DT_DIRTY : 0),           color,  -1)
#define display_proportional_clip(x,  y, txt, align, color, dirty) display_text_proportional_len_clip(x, y, txt, align | (dirty ? DT_DIRTY : 0) | DT_CLIP, color,  -1)

void display_ddd_proportional(int xpos, int ypos, int width, int hgt,int ddd_farbe, int text_farbe,const char *text, int dirty);
void display_ddd_proportional_clip(int xpos, int ypos, int width, int hgt,int ddd_farbe, int text_farbe, const char *text, int dirty);

int display_multiline_text(int x, int y, const char *inbuf, int color);

void display_direct_line(const int x, const int y, const int xx, const int yy, const int color);

void display_set_clip_wh(int x, int y, int w, int h);
struct clip_dimension display_get_clip_wh(void);

void display_snapshot(void);

void display_set_progress_text(const char *text);
void display_progress(int part, int total);

#if COLOUR_DEPTH != 0
extern int * display_day_lights_p;
extern int * display_night_lights_p;
#endif

#endif
