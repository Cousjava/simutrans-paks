/*
 *  Copyright (c) 1997 - 2002 by Volker Meyer & Hj. Malthaner
 *
 * This file is part of the Simutrans project under the artistic license.
 *
 *  Modulbeschreibung:
 *      searches a folder for a certain extension
 */

#ifndef __SEARCHFOLDER_H
#define __SEARCHFOLDER_H


#include <string>
#include "../tpl/vector_tpl.h"


class searchfolder_t {
public:
	~searchfolder_t();
	int search(const std::string &filepath, const std::string &extension);

	static std::string complete(const std::string &filepath, const std::string &extension);

	vector_tpl<char*> files; // NEVER EVER USE ctring_T here!!!
};

#endif
