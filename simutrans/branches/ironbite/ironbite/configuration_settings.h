#ifndef ironbite_configuration_settings_h
#define ironbite_configuration_settings_h

/**
 * Iron Bite configuration settings
 * 
 * @author Hj. Malthaner
 */
class configuration_settings_t
{
public:

	/**
	 * Sets default values. Default is usually Simutrans Standard behaviour.
	 * @author Hj. Malthaner
	 */
	configuration_settings_t();

	int iron_window_body_color;
	int iron_ticker_body_color;
	int iron_drop_shadow_color;

	/**
	 * Read the configuration settings from specified path.
	 * @author Hj. Malthaner
	 */
	bool read(const char * path);
};

/**
 * Global Iron Bite settings
 * 
 * @author Hj. Malthaner
 */
extern class configuration_settings_t configuration_settings;

#endif
