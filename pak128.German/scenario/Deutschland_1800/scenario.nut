/*
 *  Deutschland ab 1800
 *
 *
 */

map.file		       = "D_1800.sve"     		// specify the savegame to load
scenario.short_description = "Deutschland 1800"              // scenario name
scenario.author            = "makie"
scenario.translation       <- "makie"
scenario.api	       <- "120.3"			// scenario relies on this version of the api
scenario.version           = "1.0" 

function is_tool_allowed(pl, tool_id, wt)
{
	return true;
}


function get_rule_text(pl)
{
	return translate("Keine speziellen Regeln. ")
}


function get_goal_text(pl)
{
	local point = "(1142,1770)"
	local text  = ttext("Deutschland um 1800.<br>Große Umbrüche stehen bevor!<br>Ab 1835 können sie eine Eisenbahn bauen.<br>Im Süden befinden sich reiche Ölvorkommen, bauen sie eine Bahnline über den Brenner. ")
	text.link   = "<a href='" + point + "'>" + point + "</a>"
	return text.tostring()
}


function get_info_text(pl)
{
	local text = ttext("Deutschland um 1800.<br>Die Karte ist möglichst naturgetreu.<br>Lassen sie ihr Transportunternehmen wachsen.")
	text.cityname = "<a href='(83,33)'>" + translate("Steinenbruck") + "</a>"
	return text.tostring()
}


function get_result_text(player)
{
	local text1  = ttext("Sie können bis in die heutige Zeit Spielen.")
	return text1.tostring()
}


function start()
{

}


function is_scenario_completed(player)
{
         if (player != 0) return 0			// only human player
	return 0					// kein Ende
}



