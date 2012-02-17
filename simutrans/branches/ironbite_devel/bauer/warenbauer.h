/*
 * Copyright (c) 1997 - 2002 Hj. Malthaner
 *
 * This file is part of the Simutrans project under the artistic licence.
 * (see licence.txt)
 */

#ifndef freight_builder_t_h
#define freight_builder_t_h

#include "../tpl/vector_tpl.h"
#include "../tpl/stringhashtable_tpl.h"

class freight_desc_t;

/**
 * Factory-Klasse fuer Waren.
 *
 * @author Hj. Malthaner
 */
class freight_builder_t
{
private:
	static stringhashtable_tpl<const freight_desc_t *> besch_names;
	static vector_tpl<freight_desc_t *> waren;

	static freight_desc_t *load_passagiere;
	static freight_desc_t *load_post;
	static freight_desc_t *load_nichts;

	// number of different good classes;
	static uint8 max_catg_index;

public:
	enum { INDEX_PAS=0, INDEX_MAIL=1, INDEX_NONE=2 };

	static const freight_desc_t *passagiere;
	static const freight_desc_t *post;
	static const freight_desc_t *nichts;

	static bool alles_geladen();
	static bool register_besch(freight_desc_t *besch);

	static uint8 get_max_catg_index() { return max_catg_index; }

	/**
	* Sucht information zur ware 'name' und gibt die
	* Beschreibung davon zur�ck. Gibt NULL zur�ck wenn die
	* Ware nicht bekannt ist.
	*
	* @param name der nicht-�bersetzte Warenname
	* @author Hj. Malthaner/V. Meyer
	*/
	static const freight_desc_t *get_info(const char* name);

	static const freight_desc_t *get_info(uint16 idx) { return waren.get(idx); }

	static uint16 get_waren_anzahl() { return waren.get_count(); }

	// ware by catg
	static const freight_desc_t *get_info_catg(const uint8 catg);

	// ware by catg_index
	static const freight_desc_t *get_info_catg_index(const uint8 catg_index);

	/*
	 * allow to multiply all prices, 1000=1.0
	 * used for the beginner mode
	 */
	static void set_multiplier(sint32 multiplier);
};

#endif
