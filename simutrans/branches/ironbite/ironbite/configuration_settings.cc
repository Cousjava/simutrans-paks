#include "configuration_settings.h"

#include "../simdebug.h"
#include "../dataobj/tabfile.h"
#include "../utils/cbuffer_t.h"

/**
 * Global Iron Bite settings
 * 
 * @author Hj. Malthaner
 */
class configuration_settings_t configuration_settings;



configuration_settings_t::configuration_settings_t()
{
	iron_window_body_color = -1;
	iron_ticker_body_color = -1;
	iron_drop_shadow_color = -1;
}

/**
 * Read the configuration settings from specified path.
 * @author Hj. Malthaner
 */
bool configuration_settings_t::read(const char * path)
{
	const char * simuconf = "simuconf.tab";
	cbuffer_t buf;
	
	buf.append(path);
	
	if(buf.contains(simuconf))
	{
		// Hajo: we need to clean the path.
		buf.truncate(buf.len() - strlen(simuconf));
	}

	buf.append("iron_bite.ini");
	
	tabfile_t tabfile;
	tabfileobj_t data;
	
	if(tabfile.open(buf))
	{
		dbg->warning("configuration_settings_t::read()", "Reading %s\n", buf.to_string());
		
		tabfile.read(data);
		tabfile.close();
		
		if(data.get("iron_window_body_color"))
		{
			iron_window_body_color = data.get_hex("iron_window_body_color", -1);
		} 

		if(data.get("iron_ticker_body_color"))
		{
			iron_ticker_body_color = data.get_hex("iron_ticker_body_color", -1);
		} 
		
		if(data.get("iron_drop_shadow_color"))
		{
			iron_drop_shadow_color = data.get_hex("iron_drop_shadow_color", -1);
		} 
		
		return true;
	}
	else
	{
		dbg->warning("configuration_settings_t::read()", "Could not read %s\n", buf.to_string());
		return false;
	}
		
}
