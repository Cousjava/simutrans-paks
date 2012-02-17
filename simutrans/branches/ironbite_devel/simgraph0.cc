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
#include "simcolor.h"

typedef uint16 PIXVAL;

int tile_raster_width = 16; // zoomed
int base_tile_raster_width = 16; // original


int display_set_base_raster_width(int)
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

void mark_rect_dirty_wc(int, int, int, int)
{
}

int display_set_unicode(int)
{
	return false;
}

bool display_load_font(const char*)
{
  	return true;
}

int display_get_width(void)
{
	return 0;
}

int display_get_height(void)
{
	return 0;
}

void display_set_height(int)
{
}

void display_set_actual_width(int)
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

void display_set_player_color_scheme(const int, const int, const int)
{
}

void display_register_image(struct bild_t* bild)
{
	bild->bild_nr = 1;
}

void display_get_image_offset(unsigned, int *, int *, int *, int *)
{
}

void display_get_base_image_offset(unsigned, int *, int *, int *, int *)
{
}

void display_set_base_image_offset(unsigned, int, int)
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

void display_set_clip_wh(int, int, int, int)
{
}

void display_scroll_band(const int, const int, const int)
{
}

static inline void pixcopy(PIXVAL *, const PIXVAL *, const unsigned int)
{
}

static inline void colorpixcopy(PIXVAL *, const PIXVAL *, const PIXVAL * const)
{
}

void display_img_aux(const unsigned, int, int, const signed char, const int, const int)
{
}

void display_color_img(const unsigned, int, int, const signed char, const int, const int)
{
}

void display_base_img(const unsigned, int, int, const signed char, const int, const int)
{
}

void display_rezoomed_img_blend(const unsigned, int, int, const signed char, const int, const int, const int)
{
}

void display_base_img_blend(const unsigned, int, int, const signed char, const int, const int, const int)
{
}

// Knightly : variables for storing currently used image procedure set and tile raster width
display_image_proc display_normal = display_base_img;
display_image_proc display_color = display_base_img;
display_blend_proc display_blend = display_base_img_blend;
signed short current_tile_raster_width = 0;

void display_mark_img_dirty(unsigned, int, int)
{
}

void display_fillbox_wh(int, int, int, int, int, bool)
{
}

void display_fillbox_wh_clip(int, int, int, int, int, bool)
{
}

void display_vline_wh(int, int, int, int, bool)
{
}

void display_vline_wh_clip(int, int, int, int, bool)
{
}

void display_array_wh(int, int, int, int, const unsigned char *)
{
}

int get_next_char(const char*, int pos)
{
	return pos + 1;
}

int get_prev_char(const char*, int pos)
{
	if (pos <= 0) {
		return 0;
	}
	return pos - 1;
}

unsigned short get_next_char_with_metrics(const char* &, unsigned char &, unsigned char &)
{
	return 0;
}

unsigned short get_prev_char_with_metrics(const char* &, const char *const, unsigned char &, unsigned char &)
{
	return 0;
}

int display_calc_proportional_string_len_width(const char*, int)
{
	return 0;
}

int display_text_proportional_len_clip(int, int, const char*, int, const int, long)
{
	return 0;
}

void display_outline_proportional(int, int, int, int, const char *, int)
{
}

void display_shadow_proportional(int, int, int, int, const char *, int)
{
}

void display_ddd_box(int, int, int, int, int, int)
{
}

void display_ddd_box_clip(int, int, int, int, int, int)
{
}

void display_ddd_proportional(int, int, int, int, int, int, const char *, int)
{
}

void display_ddd_proportional_clip(int, int, int, int, int, int, const char *, int)
{
}

int display_multiline_text(int, int, const char *, int)
{
	return 0;
}

void display_flush_buffer(void)
{
}

void display_system_move_pointer(int, int)
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

void simgraph_init(int, int, int)
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
	system_close();
}

void simgraph_resize(int, int)
{
}

void display_snapshot()
{
}

void display_direct_line(const int, const int, const int, const int, const int)
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
