/*
 * Copyright (c) 1997 - 2003 Hj. Malthaner
 *
 * This file is part of the Simutrans project under the artistic licence.
 * (see licence.txt)
 */

#include "citylist_stats_t.h"
#include "stadt_info.h"

#include "../simcity.h"
#include "../simevent.h"
#include "../simgraph.h"
#include "../simskin.h"
#include "../simwin.h"
#include "../simworld.h"
#include "components/gui_component_colors.h"

#include "../besch/skin_besch.h"

#include "../dataobj/translator.h"

#include "../utils/cbuffer_t.h"
#include "../utils/simstring.h"

#include "../ironbite/configuration_settings.h"

static const char* total_bev_translation = NULL;
char citylist_stats_t::total_bev_string[128];


citylist_stats_t::citylist_stats_t(karte_t* w, citylist::sort_mode_t sortby, bool sortreverse) :
	welt(w)
{
	total_bev_translation = translator::translate("Total inhabitants:");
	sort(sortby, sortreverse);
	recalc_size();
	line_selected = 0xFFFFFFFFu;
}


class compare_cities
{
	public:
		compare_cities(citylist::sort_mode_t sortby_, bool reverse_) :
			sortby(sortby_),
			reverse(reverse_)
		{}

		bool operator ()(const stadt_t* a, const stadt_t* b)
		{
			int cmp;
			switch (sortby) {
				default: NOT_REACHED
				case citylist::by_name:   cmp = strcmp(a->get_name(), b->get_name());    break;
				case citylist::by_size:   cmp = a->get_einwohner() - b->get_einwohner(); break;
				case citylist::by_growth: cmp = a->get_wachstum()  - b->get_wachstum();  break;
			}
			return reverse ? cmp > 0 : cmp < 0;
		}

	private:
		citylist::sort_mode_t sortby;
		bool reverse;
};


void citylist_stats_t::sort(citylist::sort_mode_t sb, bool sr)
{
	const weighted_vector_tpl<stadt_t*>& cities = welt->get_staedte();

	sortby = sb;
	sortreverse = sr;

	city_list.clear();
	city_list.resize(cities.get_count());

	FOR(weighted_vector_tpl<stadt_t*>, const i, cities) {
		city_list.insert_ordered(i, compare_cities(sortby, sortreverse));
	}
}


bool citylist_stats_t::infowin_event(const event_t * ev)
{
	const uint line = ev->cy / (LINESPACE + 1);

	line_selected = 0xFFFFFFFFu;
	if (line >= city_list.get_count()) {
		return false;
	}

	stadt_t* stadt = city_list[line];
	if(  ev->button_state>0  &&  ev->cx>0  &&  ev->cx<15  ) {
		line_selected = line;
	}

	if (IS_LEFTRELEASE(ev) && ev->cy>0) {
		if(ev->cx>0  &&  ev->cx<15) {
			const koord pos = stadt->get_pos();
			welt->change_world_position( koord3d(pos, welt->min_hgt(pos)) );
		}
		else {
			stadt->zeige_info();
		}
	} else if (IS_RIGHTRELEASE(ev) && ev->cy > 0) {
		const koord pos = stadt->get_pos();
		welt->change_world_position( koord3d(pos, welt->min_hgt(pos)) );
	}
	return false;
}


void citylist_stats_t::recalc_size()
{
	set_size(210, welt->get_staedte().get_count() * (LINESPACE + 4) - 10);
}


void citylist_stats_t::zeichnen(koord offset)
{
	const clip_dimension cd = display_get_clip_wh();

	// Hajo: try a different background color for lists
	display_fillbox_wh(cd.x, cd.y, cd.xx-cd.x+1, cd.yy-cd.y+1, COLOR_LIST_BACKGROUND, true);

	cbuffer_t buf;

	sint32 total_bev = 0;
	sint32 total_growth = 0;

	if(  welt->get_staedte().get_count()!=city_list.get_count()  ) {
		// some deleted/ added => resort
		sort( sortby, sortreverse );
		recalc_size();
	}

	int zebra = 0;
	int yoff = offset.y +1;
	uint32 sel = line_selected;
	FORX(vector_tpl<stadt_t*>, const stadt, city_list, yoff += LINESPACE + 4) 
	{
		if((zebra & 1) && configuration_settings.iron_zebra_lists)
		{
			display_fillbox_wh_clip(cd.x, yoff-1, cd.xx-cd.x+1, LINESPACE+4, COLOR_LIST_BACKGROUND_ZEBRA, true);
		}
		
		sint32 bev = stadt->get_einwohner();
		sint32 growth = stadt->get_wachstum();

		buf.clear();
		buf.append( stadt->get_name() );
		buf.append( ": ");
		buf.append( bev, 0 );
		buf.append( " (" );
		buf.append( growth/10.0, 1 );
		buf.append( ")" );
		display_proportional_clip(offset.x + 16, yoff+1, buf, ALIGN_LEFT, COL_BLACK, true);

		// goto button
		image_id const img = sel-- != 0 ? button_t::arrow_right_normal : button_t::arrow_right_pushed;
		display_color_img(img, offset.x + 2, yoff+1, 0, false, true);

		total_bev += bev;
		total_growth += growth;
		zebra ++;
	}

	total_bev_string[0] = 0;

	// some cities there?
	if(  total_bev > 0  ) 
	{
		buf.clear();
		buf.printf( "%s %u", total_bev_translation, total_bev );
		buf.append( " (" );
		buf.append( total_growth/10.0, 1 );
		buf.append( ")" );
		tstrncpy(total_bev_string, buf, lengthof(total_bev_string));
	}
}
