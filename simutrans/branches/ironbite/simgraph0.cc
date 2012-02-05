/*
 * Copyright (c) 2001 Hj. Malthaner
 * h_malthaner@users.sourceforge.net
 *
 * This file is part of the Simugraph engine and may not be used
 * in other projects without written permission of the author.
 *
 * Usage for Iso-Angband is granted.
 */

#include "simconst.h"
#include "simsys.h"
#include "besch/bild_besch.h"

#include "simgraph.h"

typedef uint16 PIXVAL;

KOORD_VAL tile_raster_width = 16; // zoomed
KOORD_VAL base_tile_raster_width = 16; // original


KOORD_VAL display_set_base_raster_width(KOORD_VAL)
{
	return 0;
}

void set_zoom_factor(int)
{
}

int display_zoom_in()
{
	return false;
}

int display_zoom_out()
{
	return false;
}

static inline void mark_tile_dirty(const int, const int)
{
}

static inline void mark_tiles_dirty(const int, const int, const int)
{
}

static inline int is_tile_dirty(const int, const int)
{
	return false;
}

void mark_rect_dirty_wc(KOORD_VAL, KOORD_VAL, KOORD_VAL, KOORD_VAL)
{
}

int display_set_unicode(int)
{
	return false;
}

bool display_load_font(const char*)
{
        large_font_p->line_spacing = 13;
	return true;
}

sint16 display_get_width(void)
{
	return 0;
}

sint16 display_get_height(void)
{
	return 0;
}

void display_set_height(KOORD_VAL)
{
}

void display_set_actual_width(KOORD_VAL)
{
}

int display_get_light(void)
{
	return 0;
}

void display_set_light(int)
{
}

void display_day_night_shift(int)
{
}

void display_set_player_color_scheme(const int, const COLOR_VAL, const COLOR_VAL)
{
}

void display_register_image(struct bild_t* bild)
{
	bild->bild_nr = 1;
}

void display_get_image_offset(unsigned, KOORD_VAL *, KOORD_VAL *, KOORD_VAL *, KOORD_VAL *)
{
}

void display_get_base_image_offset(unsigned, KOORD_VAL *, KOORD_VAL *, KOORD_VAL *, KOORD_VAL *)
{
}

void display_set_base_image_offset(unsigned, KOORD_VAL, KOORD_VAL)
{
}

int get_mouse_x(void)
{
	return sys_event.mx;
}

int get_mouse_y(void)
{
	return sys_event.my;
}

struct clip_dimension display_get_clip_wh(void)
{
	struct clip_dimension clip_rect;
	return clip_rect;
}

void display_set_clip_wh(KOORD_VAL, KOORD_VAL, KOORD_VAL, KOORD_VAL)
{
}

void display_scroll_band(const KOORD_VAL, const KOORD_VAL, const KOORD_VAL)
{
}

static inline void pixcopy(PIXVAL *, const PIXVAL *, const unsigned int)
{
}

static inline void colorpixcopy(PIXVAL *, const PIXVAL *, const PIXVAL * const)
{
}

void display_img_aux(const unsigned, KOORD_VAL, KOORD_VAL, const signed char, const int, const int)
{
}

void display_color_img(const unsigned, KOORD_VAL, KOORD_VAL, const signed char, const int, const int)
{
}

void display_base_img(const unsigned, KOORD_VAL, KOORD_VAL, const signed char, const int, const int)
{
}

void display_rezoomed_img_blend(const unsigned, KOORD_VAL, KOORD_VAL, const signed char, const PLAYER_COLOR_VAL, const int, const int)
{
}

void display_base_img_blend(const unsigned, KOORD_VAL, KOORD_VAL, const signed char, const PLAYER_COLOR_VAL, const int, const int)
{
}

// Knightly : variables for storing currently used image procedure set and tile raster width
display_image_proc display_normal = display_base_img;
display_image_proc display_color = display_base_img;
display_blend_proc display_blend = display_base_img_blend;
signed short current_tile_raster_width = 0;

void display_mark_img_dirty(unsigned, KOORD_VAL, KOORD_VAL)
{
}

void display_fillbox_wh(KOORD_VAL, KOORD_VAL, KOORD_VAL, KOORD_VAL, PLAYER_COLOR_VAL, bool)
{
}

void display_fillbox_wh_clip(KOORD_VAL, KOORD_VAL, KOORD_VAL, KOORD_VAL, PLAYER_COLOR_VAL, bool)
{
}

void display_vline_wh(KOORD_VAL, KOORD_VAL, KOORD_VAL, PLAYER_COLOR_VAL, bool)
{
}

void display_vline_wh_clip(KOORD_VAL, KOORD_VAL, KOORD_VAL, PLAYER_COLOR_VAL, bool)
{
}

void display_array_wh(KOORD_VAL, KOORD_VAL, KOORD_VAL, KOORD_VAL, const COLOR_VAL *)
{
}

size_t get_next_char(const char*, size_t pos)
{
	return pos + 1;
}

long get_prev_char(const char*, long pos)
{
	if (pos <= 0) {
		return 0;
	}
	return pos - 1;
}

KOORD_VAL display_get_char_width(utf16)
{
	return 0;
}

unsigned short get_next_char_with_metrics(const char* &, unsigned char &, unsigned char &)
{
	return 0;
}

unsigned short get_prev_char_with_metrics(const char* &, const char *const, unsigned char &, unsigned char &)
{
	return 0;
}

int display_calc_proportional_string_len_width(const char*, size_t)
{
	return 0;
}

int display_text_proportional_len_clip(KOORD_VAL, KOORD_VAL, const char*, int, const PLAYER_COLOR_VAL, long)
{
	return 0;
}

void display_outline_proportional(KOORD_VAL, KOORD_VAL, PLAYER_COLOR_VAL, PLAYER_COLOR_VAL, const char *, int)
{
}

void display_shadow_proportional(KOORD_VAL, KOORD_VAL, PLAYER_COLOR_VAL, PLAYER_COLOR_VAL, const char *, int)
{
}

void display_ddd_box(KOORD_VAL, KOORD_VAL, KOORD_VAL, KOORD_VAL, PLAYER_COLOR_VAL, PLAYER_COLOR_VAL)
{
}

void display_ddd_box_clip(KOORD_VAL, KOORD_VAL, KOORD_VAL, KOORD_VAL, PLAYER_COLOR_VAL, PLAYER_COLOR_VAL)
{
}

void display_ddd_proportional(KOORD_VAL, KOORD_VAL, KOORD_VAL, KOORD_VAL, PLAYER_COLOR_VAL, PLAYER_COLOR_VAL, const char *, int)
{
}

void display_ddd_proportional_clip(KOORD_VAL, KOORD_VAL, KOORD_VAL, KOORD_VAL, PLAYER_COLOR_VAL, PLAYER_COLOR_VAL, const char *, int)
{
}

int display_multiline_text(KOORD_VAL, KOORD_VAL, const char *, PLAYER_COLOR_VAL)
{
	return 0;
}

void display_flush_buffer(void)
{
}

void display_system_move_pointer(KOORD_VAL, KOORD_VAL)
{
}

void display_system_show_pointer(int)
{
}

void display_system_set_pointer(int)
{
}

void display_show_load_pointer(int)
{
}

void simgraph_init(KOORD_VAL, KOORD_VAL, int)
{
}

int is_display_init(void)
{
	return false;
}

void display_free_all_images_above( unsigned)
{
}

void simgraph_exit()
{
<<<<<<< .mine
	return system_close();
=======
	dr_os_close();
>>>>>>> .r5221
}

void simgraph_resize(KOORD_VAL, KOORD_VAL)
{
}

void display_snapshot()
{
}

void display_direct_line(const KOORD_VAL, const KOORD_VAL, const KOORD_VAL, const KOORD_VAL, const PLAYER_COLOR_VAL)
{
}

void display_set_progress_text(const char *)
{
}

void display_progress(int, int)
{
}

void add_poly_clip(int, int, int, int, int)
{
}

void clear_all_poly_clip()
{
}

void activate_ribi_clip(int)
{
}
