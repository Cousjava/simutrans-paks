#ifndef dings_thing_no_info_h
#define dings_thing_no_info_h

#include "../simdings.h"

/**
 * Game objects that do not have description windows (for instance zeiger_t, wolke_t)
 */
class ding_no_info_t : public ding_t
{
public:
	ding_no_info_t(karte_t* welt, loadsave_t* file) : ding_t(welt, file) {}

	ding_no_info_t(karte_t* welt, koord3d pos) : ding_t(welt, pos) {}

	void zeige_info() {}

protected:
	ding_no_info_t(karte_t* welt) : ding_t(welt) {}
};

#endif
