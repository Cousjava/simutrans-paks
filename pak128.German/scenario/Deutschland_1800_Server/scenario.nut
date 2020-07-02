/*
 *  Deutschland ab 1800
 *
 *
 */

map.file		       = "D_1800_s.sve"     		// specify the savegame to load
scenario.short_description = "Deutschland 1800 Server"      // scenario name
scenario.author            = "makie"
scenario.translation       <- "makie"
scenario.api	       <- "120.3"			// scenario relies on this version of the api
scenario.version           = "1.1" 


function get_about_text(pl)
{
	local about = ttextfile("about.txt")
	about.short_description = scenario.short_description
	about.version = scenario.version
	about.author = scenario.author
	about.translation = scenario.translation
	return about
}

function get_info_text(pl)
{
	return ttextfile("info.txt")
}

function get_rule_text(pl)
{
	return ttextfile("rule.txt")
}

function get_goal_text(pl)
{
	return ttextfile("goal.txt")
}

function get_result_text(player)
{
	return ttextfile("result.txt")
}

function start()
{

}

function is_tool_allowed(pl, tool_id, wt)
{
	return true;
}



function is_scenario_completed(player)
{
         if (player != 0) return 0			// only human player
	return 0					// kein Ende
}



