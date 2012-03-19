/*
 * Copyright (c) 1997 - 2003 Hj. Malthaner
 *
 * This file is part of the Simutrans project under the artistic licence.
 * (see licence.txt)
 */

#include "citylist_frame_t.h"
#include "citylist_stats_t.h"

#include "components/action_listener.h"
#include "components/gui_button.h"
#include "components/gui_label.h"
#include "components/gui_chart.h"
#include "components/gui_scrollpane.h"
#include "components/gui_tab_panel.h"
#include "components/gui_component_colors.h"

#include "../dataobj/translator.h"
#include "../simcolor.h"
#include "../dataobj/umgebung.h"

#include "../simworld.h"

#define CHART_HEIGHT (168)
#define TOTAL_HEIGHT (D_TITLEBAR_HEIGHT+3*(LINESPACE+1)+50+D_MARGIN_BOTTOM)

/**
 * "Total insulation" pattern.
 * @author Hj. Malthaner
 */
class citylist_frame_data_t : private action_listener_t
{
public:
	
	static const char * sort_text[citylist::SORT_MODES];

	static const char hist_type[karte_t::MAX_WORLD_COST][20];
	static const uint8 hist_type_color[karte_t::MAX_WORLD_COST];
	static const uint8 hist_type_type[karte_t::MAX_WORLD_COST];

	static karte_t * welt;

	citylist_frame_t * gui;

	gui_label_t sort_label;

	button_t	sortedby;
	button_t	sorteddir;

	citylist_stats_t stats;
	gui_scrollpane_t scrolly;

	button_t	show_stats;
	gui_chart_t chart, mchart;
	button_t	filterButtons[karte_t::MAX_WORLD_COST];
	gui_tab_panel_t year_month_tabs;

	/*
	 * All filter settings are static, so they are not reset each
	 * time the window closes.
	 */
	static citylist::sort_mode_t sortby;
	static bool sortreverse;
	
	static citylist::sort_mode_t get_sortierung() { return sortby; }
	static void set_sortierung(const citylist::sort_mode_t& sm) { sortby = sm; }

	static bool get_reverse() { return sortreverse; }
	static void set_reverse(const bool& reverse) { sortreverse = reverse; }

	citylist_frame_data_t(citylist_frame_t * gui, karte_t * welt);
	
	bool action_triggered(gui_action_creator_t*, value_t) OVERRIDE;
};


citylist_frame_data_t::citylist_frame_data_t(citylist_frame_t * gui, karte_t * welt) :
	sort_label(translator::translate("hl_txt_sort")),
	stats(welt, sortby, sortreverse),
	scrolly(&stats)
{
	this->gui = gui;
	this->welt = welt;
		
	// Hajo: skip "total inhabitants" text line
	const int top = D_MARGIN_TOP + D_TITLEBAR_HEIGHT;

	sort_label.set_pos(koord(D_MARGIN_LEFT, top+3));

	sortedby.init(button_t::roundbox, "", koord(90, top), koord(D_BUTTON_WIDTH, BUTTON_TALL_HEIGHT));
	sortedby.add_listener(this);

	sorteddir.init(button_t::roundbox, "", koord(90 + BUTTON2_X-2, top), koord(D_BUTTON_WIDTH, BUTTON_TALL_HEIGHT));
	sorteddir.add_listener(this);

	show_stats.init(button_t::roundbox_state, "Chart", koord(90 + BUTTON3_X + 8, top), koord(D_BUTTON_WIDTH, BUTTON_TALL_HEIGHT));
	show_stats.set_tooltip("Show/hide statistics");
	show_stats.add_listener(this);

	sortedby.set_text(sort_text[get_sortierung()]);
	sorteddir.set_text(get_reverse() ? "hl_btn_sort_desc" : "hl_btn_sort_asc");

	year_month_tabs.add_tab(&chart, translator::translate("Years"));
	year_month_tabs.add_tab(&mchart, translator::translate("Months"));
	year_month_tabs.set_pos(koord(0,42));
	year_month_tabs.set_groesse(koord(D_DEFAULT_WIDTH, CHART_HEIGHT-D_BUTTON_HEIGHT*3-D_TITLEBAR_HEIGHT));
	year_month_tabs.set_visible(false);

	const sint16 yb = 42+CHART_HEIGHT-D_BUTTON_HEIGHT*3-8;
	chart.set_pos(koord(60,8+gui_tab_panel_t::HEADER_VSIZE));
	chart.set_groesse(koord(D_DEFAULT_WIDTH-60-8,yb-16-42-10-gui_tab_panel_t::HEADER_VSIZE));
	chart.set_dimension(12, karte_t::MAX_WORLD_COST*MAX_WORLD_HISTORY_YEARS);
	chart.set_visible(false);
	chart.set_background(MN_GREY1);
	for (int cost = 0; cost<karte_t::MAX_WORLD_COST; cost++) {
		chart.add_curve(hist_type_color[cost], welt->get_finance_history_year(), karte_t::MAX_WORLD_COST, cost, MAX_WORLD_HISTORY_YEARS, hist_type_type[cost], false, true, (cost==1) ? 1 : 0 );
	}

	mchart.set_pos(koord(60,8+gui_tab_panel_t::HEADER_VSIZE));
	mchart.set_groesse(koord(D_DEFAULT_WIDTH-60-8,yb-16-42-10-gui_tab_panel_t::HEADER_VSIZE));
	mchart.set_dimension(12, karte_t::MAX_WORLD_COST*MAX_WORLD_HISTORY_MONTHS);
	mchart.set_visible(false);
	mchart.set_background(MN_GREY1);
	for (int cost = 0; cost<karte_t::MAX_WORLD_COST; cost++) {
		mchart.add_curve(hist_type_color[cost], welt->get_finance_history_month(), karte_t::MAX_WORLD_COST, cost, MAX_WORLD_HISTORY_MONTHS, hist_type_type[cost], false, true, (cost==1) ? 1 : 0 );
	}

	for (int cost = 0; cost<karte_t::MAX_WORLD_COST; cost++) {
		filterButtons[cost].init(button_t::box_state, hist_type[cost], koord(BUTTON1_X+(D_BUTTON_WIDTH+D_H_SPACE)*(cost%4), yb+(D_BUTTON_HEIGHT+2)*(cost/4)), koord(D_BUTTON_WIDTH, D_BUTTON_HEIGHT));
		filterButtons[cost].add_listener(this);
		filterButtons[cost].background = hist_type_color[cost];
		filterButtons[cost].set_visible(false);
		filterButtons[cost].pressed = false;
	}

	scrolly.set_pos(koord(1, 50));
	scrolly.set_scroll_amount_y(LINESPACE+1);
}


/**
 * This variable defines the sort order (ascending or descending)
 * Values: 1 = ascending, 2 = descending)
 * @author Markus Weber
 */
bool citylist_frame_data_t::sortreverse = false;

karte_t * citylist_frame_data_t::welt = NULL;

/**
 * This variable defines by which column the table is sorted
 * Values: 0 = Station number
 *         1 = Station name
 *         2 = Waiting goods
 *         3 = Station type
 * @author Markus Weber
 */
citylist::sort_mode_t citylist_frame_data_t::sortby = citylist::by_name;

const char *citylist_frame_data_t::sort_text[citylist::SORT_MODES] = {
	"Name",
	"citicens",
	"Growth"
};

const char citylist_frame_data_t::hist_type[karte_t::MAX_WORLD_COST][20] =
{
	"citicens",
	"Growth",
	"Towns",
	"Factories",
	"Convoys",
	"Verkehrsteilnehmer",
	"ratio_pax",
	"Passagiere",
	"sended",
	"Post",
	"Arrived",
	"Goods"
};

const uint8 citylist_frame_data_t::hist_type_color[karte_t::MAX_WORLD_COST] =
{
	COL_WHITE,
	COL_DARK_GREEN,
	COL_LIGHT_PURPLE,
	71 /*COL_GREEN*/,
	COL_TURQUOISE,
	87,
	COL_LIGHT_BLUE,
	100,
	COL_LIGHT_YELLOW,
	COL_YELLOW,
	COL_LIGHT_BROWN,
	COL_BROWN
};

const uint8 citylist_frame_data_t::hist_type_type[karte_t::MAX_WORLD_COST] =
{
	STANDARD,
	STANDARD,
	STANDARD,
	STANDARD,
	STANDARD,
	STANDARD,
	MONEY,
	STANDARD,
	MONEY,
	STANDARD,
	MONEY,
	STANDARD
};


citylist_frame_t::citylist_frame_t(karte_t * welt) :
	gui_frame_t(translator::translate("City list"))
{
	ooo = new citylist_frame_data_t(this, welt);
	
	add_komponente(&ooo->sort_label);
	add_komponente(&ooo->sortedby);
	add_komponente(&ooo->sorteddir);
	add_komponente(&ooo->show_stats);
	add_komponente(&ooo->year_month_tabs);

	for (int cost = 0; cost<karte_t::MAX_WORLD_COST; cost++) 
	{
		add_komponente(&ooo->filterButtons[cost]);
	}

	add_komponente(&ooo->scrolly);

	set_fenstergroesse(koord(D_DEFAULT_WIDTH, TOTAL_HEIGHT+CHART_HEIGHT));
	set_min_windowsize(koord(D_DEFAULT_WIDTH, TOTAL_HEIGHT));

	set_resizemode(diagonal_resize);
	resize(koord(0,0));
}

citylist_frame_t::~citylist_frame_t()
{
	delete ooo;
	ooo = 0;
}

bool citylist_frame_data_t::action_triggered( gui_action_creator_t *komp,value_t /* */)
{
	if(komp == &sortedby) {
		set_sortierung((citylist::sort_mode_t)((get_sortierung() + 1) % citylist::SORT_MODES));
		sortedby.set_text(sort_text[get_sortierung()]);
		stats.sort(get_sortierung(),get_reverse());
		stats.recalc_size();
	}
	else if(komp == &sorteddir) {
		set_reverse(!get_reverse());
		sorteddir.set_text(get_reverse() ? "hl_btn_sort_desc" : "hl_btn_sort_asc");
		stats.sort(get_sortierung(),get_reverse());
		stats.recalc_size();
	}
	else if(komp == &show_stats) {
		show_stats.pressed = !show_stats.pressed;
		chart.set_visible( show_stats.pressed );
		year_month_tabs.set_visible( show_stats.pressed );
		const int height = show_stats.pressed ? TOTAL_HEIGHT+CHART_HEIGHT : TOTAL_HEIGHT;
		gui->set_min_windowsize( koord(D_DEFAULT_WIDTH, height));
		
		for(  int i=0;  i<karte_t::MAX_WORLD_COST;  i++ ) {
			filterButtons[i].set_visible(show_stats.pressed);
		}
		
		gui->resize( koord(0,0) );
	}
	else {
		for(  int i=0;  i<karte_t::MAX_WORLD_COST;  i++ ) {
			if(  komp == filterButtons+i  ) {
				filterButtons[i].pressed = !filterButtons[i].pressed;
				if (filterButtons[i].pressed) {
					chart.show_curve(i);
					mchart.show_curve(i);
				}
				else {
					chart.hide_curve(i);
					mchart.hide_curve(i);
				}
			}
		}
	}
	return true;
}


void citylist_frame_t::resize(const koord delta)
{
	gui_frame_t::resize(delta);

	const int yoff = 50+(ooo->show_stats.pressed*CHART_HEIGHT);
	
	koord groesse = get_fenstergroesse();
	groesse -= koord(2, yoff + D_TITLEBAR_HEIGHT + D_MARGIN_BOTTOM);
	ooo->scrolly.set_pos(koord(1, yoff));
	ooo->scrolly.set_groesse(groesse);
	set_dirty();
}


void citylist_frame_t::zeichnen(koord pos, koord gr)
{
	if(ooo->show_stats.pressed) {
		ooo->welt->update_history();
	}
	gui_frame_t::zeichnen(pos, gr);

	display_proportional(pos.x+D_MARGIN_LEFT, pos.y+D_MARGIN_TOP+D_TITLEBAR_HEIGHT, 
			     citylist_stats_t::total_bev_string, 
			     ALIGN_LEFT, COLOR_TEXT, true);
}
