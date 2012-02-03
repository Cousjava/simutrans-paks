#ifndef freight_list_sorter_h
#define freight_list_sorter_h

// same sorting for stations and vehicle/convoi freight ...

#include "simtypes.h"

template<class T> class slist_tpl;
template<class T> class vector_tpl;
class freight_t;
class cbuffer_t;
class karte_t;


class freight_list_sorter_t
{
public:
	enum sort_mode_t { by_name=0, by_via=1, by_via_sum=2, by_amount=3};

	static void sort_freight(const vector_tpl<freight_t>* warray, cbuffer_t& buf, sort_mode_t sort_mode, const slist_tpl<freight_t>* full_list, const char* what_doing, karte_t *world);

private:
	static karte_t *welt;

	static sort_mode_t sortby;

	static bool compare_ware(freight_t const& w1, freight_t const& w2);

	static void add_ware_heading( cbuffer_t &buf, uint32 sum, uint32 max, const freight_t *ware, const char *what_doing );
};


#endif
