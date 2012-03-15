#ifndef factorylist_frame_t_h
#define factorylist_frame_t_h

#include "gui_frame.h"


class karte_t;
class factory_list_frame_data_t;

/**
 * Factory list window
 * @author Hj. Malthaner
 */
class factorylist_frame_t : public gui_frame_t
{
private:

	factory_list_frame_data_t * ooo;


public:
	factorylist_frame_t(karte_t * welt);
	virtual ~factorylist_frame_t();

	/**
	 * resize window in response to a resize event
	 * @author Hj. Malthaner
	 */
	void resize(const koord delta);

	/**
	 * Manche Fenster haben einen Hilfetext assoziiert.
	 * @return den Dateinamen für die Hilfe, oder NULL
	 * @author V. Meyer
	 */
	const char * get_hilfe_datei() const {return "factorylist_filter.txt"; }
};

#endif
