#ifndef gui_optionen_h
#define gui_optionen_h

#include "gui_frame.h"

class optionen_gui_data_t;

/**
 * Settings in the game.
 *
 * @author Hj. Malthaner
 */
class optionen_gui_t : public gui_frame_t
{
private:
	
	optionen_gui_data_t * ooo;

public:

	optionen_gui_t(karte_t *welt);
	virtual ~optionen_gui_t();

	/**
	 * Manche Fenster haben einen Hilfetext assoziiert.
	 * @return den Dateinamen f�r die Hilfe, oder NULL
	 * @author Hj. Malthaner
	 */
	const char * get_help_file() const {return "options.txt";}

};

#endif
