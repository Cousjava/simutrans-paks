/*
 * Copyright (c) 1997 - 2003 Hansjörg Malthaner
 *
 * This file is part of the Simutrans project under the artistic licence.
 * (see licence.txt)
 */

#include "factorylist_frame_t.h"

#include "components/gui_scrollpane.h"
#include "components/gui_label.h"
#include "components/gui_button.h"
#include "factorylist_stats_t.h"

#include "../dataobj/translator.h"
#include "../font.h"

/**
 * GUI data container for "total insulation" pattern.
 * @author Hj, Malthaner
 */
class factory_list_frame_data_t : private action_listener_t
{
private:
	static const char *sort_text[factorylist::SORT_MODES];
	
public:
	gui_label_t sort_label;
	button_t	sortedby;
	button_t	sorteddir;
	factorylist_stats_t stats;
	gui_scrollpane_t scrolly;

	/*
	 * All filter settings are static, so they are not reset each
	 * time the window closes.
	 */
	static factorylist::sort_mode_t sortby;
	static bool sortreverse;

	static factorylist::sort_mode_t get_sortierung() { return sortby; }
	static void set_sortierung(const factorylist::sort_mode_t& sm) { sortby = sm; }

	static bool get_reverse() { return sortreverse; }
	static void set_reverse(const bool& reverse) { sortreverse = reverse; }

	factory_list_frame_data_t(karte_t * welt);
	virtual ~factory_list_frame_data_t() {};

	bool action_triggered(gui_action_creator_t*, value_t) OVERRIDE;
};

factory_list_frame_data_t::factory_list_frame_data_t(karte_t * welt) :
	sort_label(translator::translate("hl_txt_sort")),
	stats(welt, sortby, sortreverse),
	scrolly(&stats)
{
	const int top = D_MARGIN_TOP;
	
	sort_label.set_pos(koord(D_MARGIN_LEFT, top+3));

	sortedby.init(button_t::roundbox, "", koord(90, top), koord(D_BUTTON_WIDTH, BUTTON_TALL_HEIGHT));
	sortedby.add_listener(this);

	sorteddir.init(button_t::roundbox, "", koord(90 + BUTTON1_X + D_BUTTON_WIDTH, top), koord(D_BUTTON_WIDTH, BUTTON_TALL_HEIGHT));
	sorteddir.add_listener(this);

	sortedby.set_text(sort_text[get_sortierung()]);
	sorteddir.set_text(get_reverse() ? "hl_btn_sort_desc" : "hl_btn_sort_asc");

	scrolly.set_pos(koord(1, D_TITLEBAR_HEIGHT+BUTTON_TALL_HEIGHT+2));
	scrolly.set_scroll_amount_y(large_font_p->line_spacing+1);
}

/**
 * This method is called if an action is triggered
 * @author Markus Weber/Volker Meyer
 */
bool factory_list_frame_data_t::action_triggered(gui_action_creator_t *komp, value_t /* */)
{
	if(komp == &sortedby) {
		set_sortierung((factorylist::sort_mode_t)((get_sortierung() + 1) % factorylist::SORT_MODES));
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
	return true;
}

const char * factory_list_frame_data_t::sort_text[factorylist::SORT_MODES] = 
{
	"Fabrikname",
	"Input",
	"Output",
	"Produktion",
	"Rating",
	"Power"
};

/**
 * This variable defines the sort order (ascending or descending)
 * Values: 1 = ascending, 2 = descending)
 * @author Markus Weber
 */
bool factory_list_frame_data_t::sortreverse = false;

/**
 * This variable defines by which column the table is sorted
 * Values: 0 = Station number
 *         1 = Station name
 *         2 = Waiting goods
 *         3 = Station type
 * @author Markus Weber
 */
factorylist::sort_mode_t factory_list_frame_data_t::sortby = factorylist::by_name;

factorylist_frame_t::factorylist_frame_t(karte_t * welt) :
	gui_frame_t( translator::translate("fl_title") )
{
	ooo = new factory_list_frame_data_t(welt);
	
	add_komponente(&ooo->sort_label);
	add_komponente(&ooo->sortedby);
	add_komponente(&ooo->sorteddir);
	add_komponente(&ooo->scrolly);

	set_fenstergroesse(koord(D_DEFAULT_WIDTH-50, D_TITLEBAR_HEIGHT+18*(large_font_p->line_spacing+1)+14+D_BUTTON_HEIGHT+2+1));
	set_min_windowsize(koord(D_DEFAULT_WIDTH-50, D_TITLEBAR_HEIGHT+4*(large_font_p->line_spacing+1)+14+D_BUTTON_HEIGHT+2+1));

	set_resizemode(diagonal_resize);
	resize(koord(0,0));
}

factorylist_frame_t::~factorylist_frame_t()
{
	delete ooo;
	ooo = 0;
}

/**
 * resize window in response to a resize event
 * @author Hj. Malthaner
 * @date   16-Oct-2003
 */
void factorylist_frame_t::resize(const koord delta)
{
	gui_frame_t::resize(delta);

	koord scrolly_pos = ooo->scrolly.get_pos();
	
	const koord groesse = get_fenstergroesse() - scrolly_pos - koord(2, D_TITLEBAR_HEIGHT+D_MARGIN_BOTTOM);
	ooo->scrolly.set_groesse(groesse);
}
