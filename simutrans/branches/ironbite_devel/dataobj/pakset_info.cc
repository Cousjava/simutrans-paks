#include "pakset_info.h"
#include "../simdebug.h"
#include "../dataobj/translator.h"
#include "../tpl/vector_tpl.h"

stringhashtable_tpl<checksum_t*> pakset_info_t::info;
checksum_t pakset_info_t::general;

void pakset_info_t::append(const char* name, checksum_t *chk)
{
	chk->finish();

	checksum_t *old = info.set(name, chk);
	if (old) {
		delete old;
	}
}


void pakset_info_t::debug()
{
	stringhashtable_iterator_tpl<checksum_t*> iterator(info);
	while(iterator.next())
	{
		checksum_t * chk = iterator.get_current();
		dbg->message("pakset_info_t::debug", "%.30s -> sha1 = %s",
			     iterator.get_current_key(), 
			     chk->get_str());
	}
}


/**
 * order all pak checksums by name
 */
struct entry_t 
{
	entry_t(const char* n=NULL, const checksum_t* i=NULL) : name(n), chk(i) {}
	const char* name;
	const checksum_t* chk;
};


int entry_cmp(const void * aa, const void * bb)
{
	const entry_t  * a = (const entry_t *) aa;
	const entry_t * b = (const entry_t *) bb;
	return strcmp(a->name, b->name) < 0;
}


void pakset_info_t::calculate_checksum()
{
	general.reset();

	// first sort all the besch's
	vector_tpl<entry_t> sorted(info.get_count());
	stringhashtable_iterator_tpl<checksum_t*> iter(info);
	while(iter.next()) 
	{
		sorted.append(entry_t(iter.get_current_key(), iter.get_current()));
	}
	
	sorted.sort(entry_cmp);
	
	// now loop
	for(uint32 i=0; i<sorted.get_count(); i++) 
	{
		sorted.get(i).chk->calc_checksum(&general);
	}
	general.finish();
}
