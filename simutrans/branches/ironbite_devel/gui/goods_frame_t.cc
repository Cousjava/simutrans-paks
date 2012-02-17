/*
 * Copyright (c) 1997 - 2003 Hj. Malthaner
 *
 * This file is part of the Simutrans project under the artistic licence.
 * (see licence.txt)
 */

#include <algorithm>

#include "goods_frame_t.h"
#include "components/gui_scrollpane.h"
#include "components/list_button.h"

#include "../bauer/warenbauer.h"
#include "../besch/ware_besch.h"
#include "../dataobj/translator.h"

#include "../simunits.h"
#include "../simcolor.h"
#include "../simgraph.h"
#include "../font.h"
#include "../simworld.h"

/**
 * This variable defines the current speed for bonus calculation
 * @author prissi
 */
int goods_frame_t::relative_speed_change=100;

/**
 * This variable defines by which column the table is sorted
 * Values: 0 = Unsorted (passengers and mail first)
 *         1 = Alphabetical
 *         2 = Revenue
 * @author prissi
 */
goods_frame_t::sort_mode_t goods_frame_t::sortby = unsortiert;

/**
 * This variable defines the sort order (ascending or descending)
 * Values: 1 = ascending, 2 = descending)
 * @author Markus Weber
 */
bool goods_frame_t::sortreverse = false;

const char *goods_frame_t::sort_text[SORT_MODES] = {
	"gl_btn_unsort",
	"gl_btn_sort_name",
	"gl_btn_sort_revenue",
	"gl_btn_sort_bonus",
	"gl_btn_sort_catg"
};

/**
 * This variable controls whether all goods are displayed, or
 * just the ones relevant to the current game
 * Values: false = all goods shown, true = relevant goods shown
 * @author falconne
 */
bool goods_frame_t::filter_goods = false;


goods_frame_t::goods_frame_t(karte_t *wl) :
	gui_frame_t( translator::translate("gl_title") ),
	sort_label(translator::translate("hl_txt_sort")),
	change_speed_label(speed_bonus,COL_WHITE,gui_label_t::right),
	scrolly(&goods_stats)
{
	this->welt = wl;
	int y=BUTTON_HEIGHT+4-TITLEBAR_HEIGHT;

	speed_bonus[0] = 0;
	change_speed_label.set_pos(koord(BUTTON4_X+5, y));
	add_komponente(&change_speed_label);

	speed_down.init(button_t::repeatarrowleft, "", koord(BUTTON4_X-20, y), koord(10,BUTTON_HEIGHT));
	speed_down.add_listener(this);
	add_komponente(&speed_down);

	speed_up.init(button_t::repeatarrowright, "", koord(BUTTON4_X+10, y), koord(10,BUTTON_HEIGHT));
	speed_up.add_listener(this);
	add_komponente(&speed_up);

	y=BUTTON_HEIGHT+4+5*large_font_p->line_spacing;

	filter_goods_toggle.init(button_t::square_state, "Show only used", koord(BUTTON1_X, y));
	filter_goods_toggle.set_tooltip(translator::translate("Only show goods which are currently handled by factories"));
	filter_goods_toggle.add_listener(this);
	filter_goods_toggle.pressed = filter_goods;
	add_komponente(&filter_goods_toggle);
	y += large_font_p->line_spacing+2;

	sort_label.set_pos(koord(BUTTON1_X, y));
	add_komponente(&sort_label);

	y += large_font_p->line_spacing+1;

	sortedby.init(button_t::roundbox, "", koord(BUTTON1_X, y), koord(BUTTON_WIDTH,BUTTON_HEIGHT));
	sortedby.add_listener(this);
	add_komponente(&sortedby);

	sorteddir.init(button_t::roundbox, "", koord(BUTTON2_X, y), koord(BUTTON_WIDTH,BUTTON_HEIGHT));
	sorteddir.add_listener(this);
	add_komponente(&sorteddir);

	y += BUTTON_HEIGHT+2;

	scrolly.set_pos(koord(1, y));
	scrolly.set_scroll_amount_y(large_font_p->line_spacing+1);
	add_komponente(&scrolly);

	sort_list();

	int h = (freight_builder_t::get_waren_anzahl()+1)*(large_font_p->line_spacing+1)+y;
	if(h>450) {
		h = y+27*(large_font_p->line_spacing+1)+TITLEBAR_HEIGHT+1;
	}
	set_window_size(koord(TOTAL_WIDTH, h));
	set_min_window_size(koord(TOTAL_WIDTH,3*(large_font_p->line_spacing+1)+TITLEBAR_HEIGHT+y+1));

	set_resizemode(vertical_resize);
	resize (koord(0,0));
}


bool goods_frame_t::compare_goods(uint16 const a, uint16 const b)
{
	freight_desc_t const* const w1 = freight_builder_t::get_info(a);
	freight_desc_t const* const w2 = freight_builder_t::get_info(b);

	int order = 0;

	switch (sortby) {
		case 0: // sort by number
			order = a - b;
			break;
		case 2: // sort by revenue
			{
				const sint32 grundwert1281 = w1->get_preis()<<7;
				const sint32 grundwert_bonus1 = w1->get_preis()*(1000l+(relative_speed_change-100l)*w1->get_speed_bonus());
				const sint32 price1 = (grundwert1281>grundwert_bonus1 ? grundwert1281 : grundwert_bonus1);
				const sint32 grundwert1282 = w2->get_preis()<<7;
				const sint32 grundwert_bonus2 = w2->get_preis()*(1000l+(relative_speed_change-100l)*w2->get_speed_bonus());
				const sint32 price2 = (grundwert1282>grundwert_bonus2 ? grundwert1282 : grundwert_bonus2);
				order = price1-price2;
			}
			break;
		case 3: // sort by speed bonus
			order = w1->get_speed_bonus()-w2->get_speed_bonus();
			break;
		case 4: // sort by catg_index
			order = w1->get_catg()-w2->get_catg();
			break;
		default: ; // make compiler happy, order will be determined below anyway
	}
	if(  order==0  ) {
		// sort by name if not sorted or not unique
		order = strcmp(translator::translate(w1->get_name()), translator::translate(w2->get_name()));
	}
	return sortreverse ? order > 0 : order < 0;
}


// creates the list and pass it to the child function good_stats, which does the display stuff ...
void goods_frame_t::sort_list()
{
	sortedby.set_text(sort_text[sortby]);
	sorteddir.set_text(sortreverse ? "hl_btn_sort_desc" : "hl_btn_sort_asc");

	// Fetch the list of goods produced by the factories that exist in the current game
	const vector_tpl<const freight_desc_t*> &goods_in_game = welt->get_goods_list();

	int n=0;
	for(unsigned int i=0; i<freight_builder_t::get_waren_anzahl(); i++) {
		const freight_desc_t * wtyp = freight_builder_t::get_info(i);

		// Hajo: we skip goods that don't generate income
		//       this should only be true for the special good 'None'
		if(  wtyp->get_preis()!=0  &&  (!filter_goods  ||  goods_in_game.contains(wtyp))  ) {
			good_list[n++] = i;
		}
	}

	std::sort(good_list, good_list + n, compare_goods);

	goods_stats.update_goodslist( good_list, relative_speed_change, n );
}


/**
 * resize window in response to a resize event
 * @author Hj. Malthaner
 * @date   16-Oct-2003
 */
void goods_frame_t::resize(const koord delta)
{
	gui_frame_t::resize(delta);
	koord groesse = get_window_size()-scrolly.get_pos()-koord(0,TITLEBAR_HEIGHT);
	scrolly.set_groesse(groesse);
}


/**
 * This method is called if an action is triggered
 * @author Hj. Malthaner
 */
bool goods_frame_t::action_triggered( gui_action_creator_t *komp,value_t /* */)
{
	if(komp == &sortedby) {
		// sort by what
		sortby = (sort_mode_t)((int)(sortby+1)%(int)SORT_MODES);
		sort_list();
	}
	else if(komp == &sorteddir) {
		// order
		sortreverse ^= 1;
		sort_list();
	}
	else if(komp == &speed_down) {
		if(relative_speed_change>1) {
			relative_speed_change --;
			sort_list();
		}
	}
	else if(komp == &speed_up) {
		relative_speed_change ++;
		sort_list();
	}
	else if(komp == &filter_goods_toggle) {
		filter_goods = !filter_goods;
		filter_goods_toggle.pressed = filter_goods;
		sort_list();
	}

	return true;
}


/**
 * Zeichnet die Komponente
 * @author Hj. Malthaner
 */
void goods_frame_t::zeichnen(koord pos, koord gr)
{
	gui_frame_t::zeichnen(pos, gr);

	sprintf(speed_bonus,"%i",relative_speed_change-100);

	speed_message.clear();
	speed_message.printf(translator::translate("Speedbonus\nroad %i km/h, rail %i km/h\nships %i km/h, planes %i km/h."),
		(welt->get_average_speed(road_wt)*relative_speed_change)/100,
		(welt->get_average_speed(track_wt)*relative_speed_change)/100,
		(welt->get_average_speed(water_wt)*relative_speed_change)/100,
		(welt->get_average_speed(air_wt)*relative_speed_change)/100
	);
	display_multiline_text(pos.x+11, pos.y+BUTTON_HEIGHT+4, speed_message, COL_WHITE);

	speed_message.clear();
	speed_message.printf(translator::translate("tram %i km/h, monorail %i km/h\nmaglev %i km/h, narrowgauge %i km/h."),
		(welt->get_average_speed(tram_wt)*relative_speed_change)/100,
		(welt->get_average_speed(monorail_wt)*relative_speed_change)/100,
		(welt->get_average_speed(maglev_wt)*relative_speed_change)/100,
		(welt->get_average_speed(narrowgauge_wt)*relative_speed_change)/100
	);
	display_multiline_text(pos.x+11, pos.y+BUTTON_HEIGHT+4+3*large_font_p->line_spacing, speed_message, COL_WHITE);

	speed_message.clear();
	speed_message.printf(translator::translate("100 km/h = %i tiles/month"),
		welt->speed_to_tiles_per_month(kmh_to_speed(100))
	);
	display_multiline_text(pos.x+11, pos.y+BUTTON_HEIGHT+4+5*large_font_p->line_spacing, speed_message, COL_WHITE);
}
