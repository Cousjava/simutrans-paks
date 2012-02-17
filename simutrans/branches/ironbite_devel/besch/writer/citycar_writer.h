/*
 *  Copyright (c) 1997 - 2002 by Volker Meyer & Hj. Malthaner
 *
 * This file is part of the Simutrans project under the artistic licence.
 */

#ifndef CITYCAR_WRITER_H
#define CITYCAR_WRITER_H

#include <string>
#include "obj_writer.h"
#include "../objversion.h"


/*
 *  Autor:
 *      Volker Meyer
 *
 *  Beschreibung:
 *      Liest Beschreibunnen der automatisch generierten Autos
 */
class citycar_writer_t : public obj_writer_t {
	private:
		static citycar_writer_t the_instance;

		citycar_writer_t() { register_writer(true); }

	protected:
		virtual std::string get_node_name(FILE* fp) const { return name_from_next_node(fp); }

	public:
		virtual void write_obj(FILE* fp, obj_node_t& parent, tabfileobj_t& obj);

		virtual obj_type get_type() const { return obj_citycar; }
		virtual const char* get_type_name() const { return "citycar"; }
};

#endif
