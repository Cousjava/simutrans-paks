/*
 *  Oktoberfest scenario
 */

map.file		       		= "<attach>"    // specify the savegame to load
scenario.short_description 	= "Oktoberfest"    // scenario name
scenario.author            	= "iGi"
scenario.api	       		<- "120.3"		// scenario relies on this version of the api
scenario.version           	= "1.0" 
scenario.translation		= ""

/*
city industry: Für Jahr 1920
Apotheke - 1886 - 1965	- everything else  ; Apotheke1800_NIC
Apotheke (Süddt) - ab 1911 - alpenvorland
Apotheke (alpin) - ab 1906 - alpines Klima
Klinik - probably skipped

Baustoffhof - 1871 - 1950 - Baustoffhof_1800
Brennstoffhandel 1859 - 1990 - 2x2
Baustofflager - 1930	2x2	- possible to upgrade?

Buchhandlung (1800) - both are more or less same
Buchladen (alpin) 

Exporteur 1905-1970 - probably skipped

Gasthof			- 2x2 Gasthof_1800
Gastwirtschaft 	- both similar 1x3

Gastwirtschaft mit Laden - has different goods, might also be nice to have  2x1 Gastwirtschaft_mit_Laden
Kaufhaus(1912) - even more - "ugprade" of above imo. 2x2

Geräte und Haushaltsartikel 1x2

Markt (1920)
Markt (Mittelgeb. 1920)
Markt (nortdt 1920)
Markthalle 1891
Marktplatz (Süddt 1800)

Autohaus 1952 - VWAutohaus_NIC - 2x2

Warenhaus - Kaufhaus_NIC 2x2

Stadtverwaltung - Verwaltung_Muenchen_1965 2x2

1 month = 1048576 ticks
5000 ticks ~ alle 4 stunden

*/

/**
 * Table that contains data that will be saved in savegame
 * only plain data is saved: no classes / instances / functions, no cyclic references
 */

include("include/ticker")
include("include/main")
include("include/optional")
include("include/industry")
include("include/paksetdata")


gTickRatio <- null
gPhases <- null
gDebug <- false
gDebugMessages <- false

function get_rule_text(pl)
{
	if (gPhases == null)
		return "";
	return gPhases.ruletext();
}

function get_goal_text(pl)
{
	if (gPhases == null)
		return "";
	return gPhases.goaltext();
}

function get_info_text(pl)
{
	if (gPhases == null)
		return "";
	return gPhases.infotext();
}

function get_result_text(player)
{
	if (gPhases == null)
		return "";
	return gPhases.resulttext();
}

function get_debug_text(player)
{
	if (gPhases == null)
		return "";
	return gPhases.debugtext();
}


// wait that user founds a new city
class Phase1A
{
	_allCityCoords = null;
	_timerId = null;
	_helper = null;
	_completeCb = null;
	_IWAId = null;
	_pers = null;
	
	constructor(helper, cb)
	{
		_allCityCoords = []
		_timerId = 0;
		_helper = helper;
		_completeCb = cb;
		
		_pers = _helper.loadVar("phase1");
		if (_pers == null)
		{
			_pers = {};
			_pers.cityCoord <- null;
			
			_helper.saveVar("phase1", _pers);
		}
		
		init()
		
		if (!isCityAlreadyFounded())
		{
			_IWAId = gTickRatio.addIsWorkAllowedCallback(checkForNewCity.bindenv(this));
		}
	}
	
	function isCityAlreadyFounded()
	{
		return _pers.cityCoord != null;
	}
	
	function checkForNewCity(pl, tool_id, pos)
	{
		if (tool_id == tool_add_city && pl == 0)
		{
			if (!isCityAlreadyFounded())
			{
				if (_timerId == 0) // otherwise we already have a callback active
				{
					_timerId = gTickRatio.addCallback(checkNewCitySpawned.bindenv(this), 10); // check after a short time
				}
				gTickRatio.removeCallback(_IWAId);
				_IWAId = 0;
			}
		}
		return null;
	}

	function init()
	{
		local list = city_list_x()
		foreach (city in list)
		{
			local townhallCoord = city.get_pos()
			_allCityCoords.append(townhallCoord)
		}
	}
	
	function checkNewCitySpawned()
	{
		local list = city_list_x()
		foreach (city in list)
		{
			local townhallCoord = city.get_pos()
			if (!isExistingCity(townhallCoord))
			{
				// this is a new city -> player founded a new city
				_pers.cityCoord = _helper.coordToTable(townhallCoord);
				_helper.saveVar("phase1", _pers);
				
				// we do no longer need to check periodically 
				if (_timerId > 0)
				{
					gTickRatio.removeCallback(_timerId);
					_timerId = 0;
				}
				
				gPhases.addOptionalGoal(1);	
				gPhases.addOptionalGoal(10);
				_completeCb();

			}	
		}
	}
	
	function isExistingCity(c)
	{
		foreach (existingCityCoord in _allCityCoords)
		{
			if (existingCityCoord.x == c.x &&
				existingCityCoord.y == c.y)
				return true
		}
		
		return false
	}
	
	function debugtext()
	{
		return "";
	}
	
	function goaltext()
	{
		return "";
	}
	
	function resulttext()
	{
		return "";
	}
}

// wait that user builds his HQ in a city.
class Phase1B
{
	_helper = null;
	_completeCb = null;
	_pers = null;
	_checkHQTimerId = null;
	
	constructor(helper, cb)
	{
		_helper = helper;
		_completeCb = cb;
		
		_pers = _helper.loadVar("phase1");
		if (_pers == null)
		{
			_pers = {};
			_pers.cityCoord <- null;
			_pers.maxAllowedPops <- getMaxAllowedPopsInCity();
			_pers.tooManyPops <- 0;
			_pers.tooManyPopsCityName <- null;
			_pers.tooManyPopsCityCoord <- null;
			_helper.saveVar("phase1", _pers);
		}
		
		_checkHQTimerId = gTickRatio.addCallback(checkForHQBuilt.bindenv(this), 100);
	}
	
	function checkForHQBuilt()
	{
		local player = player_x(0);
		if (player.get_headquarter_level() == 0)
			return; // HQ not built yet
			
		local hqpos = player.get_headquarter_pos();
		local cityPos = world.find_nearest_city(hqpos);
		local pops = getPopsOfCity(cityPos)
		pops = pops - 378; // the HQ adds 378 citizens to the city when built.
		if (pops >= _pers.maxAllowedPops)
		{
			local showUI = _pers.tooManyPops == 0;
			_pers.tooManyPops = pops;
			_pers.tooManyPopsCityName = cityPos.get_name();
			_pers.tooManyPopsCityCoord = _helper.coordToTable(cityPos);
			_helper.saveVar("phase1", _pers);
			
			if (showUI)
				gui.open_info_win_client("rules", 0);
			
			return;
		}
		_pers.tooManyPops = 0;
		_pers.tooManyPopsCityName = null;
		_pers.tooManyPopsCityCoord = null;
		
		_pers.cityCoord = _helper.coordToTable(cityPos);
		_helper.saveVar("phase1", _pers);
		
		gTickRatio.removeCallback(_checkHQTimerId);
		_checkHQTimerId = 0;
		debugmsg("phase1 complete");
		
		gPhases.addOptionalGoal(1);	
		gPhases.addOptionalGoal(10);
		_completeCb();
	}
	
	function getMaxAllowedPopsInCity()
	{
		local allPops = [];
		local list = city_list_x();
		foreach (city in list)
		{
			local pops = city.get_citizens()[0];
			allPops.push(pops);
		}
		
		allPops.sort();
		return allPops[allPops.len() / 3] + 100 // all cities that are under 1/3 median are allowed.
												// just randomly +100 to include some more cities
	}
	
	function debugtext()
	{
		return "";
	}
	
	function goaltext()
	{
		local text = ttext("Build your headquarter near a city with less than {pops} citizens.");
		text.pops = _pers.maxAllowedPops;
		return text.tostring();
	}
	
	function resulttext()
	{
		if (_pers.tooManyPops == 0)
			return ttext("Your headquarter is not built yet.");
		else
		{
			local text = ttext("The selected city {cityname} has too many citizens. ({pops})<br>Please build your headquarter near a city with less than {maxPops} citizens.");
			text.cityname = getLink(_pers.tooManyPopsCityName, _pers.tooManyPopsCityCoord);
			text.pops = _pers.tooManyPops;
			text.maxPops = _pers.maxAllowedPops;
			return text;
		}
	}
}


class PersistentHelper
{
	constructor()
	{
	
	}
	
	function getCityCoords()
	{
		local pers = loadVar("phase1");
		if (pers == null)
			return null;
			
		return pers.cityCoord;
	}
	
	function coordToTable(c)
	{
		local t = {};
		t.x <- c.x;
		t.y <- c.y;
		return t;
	}
	
	function saveVar(name, table)
	{
		if (name in ::persistent)
			::persistent[name] = table;
		else
			::persistent[name] <- table;
	}
	
	function loadVar(name)
	{
		if (name in ::persistent)
			return ::persistent[name];
		return null;
	}
	
}


class Phase2
{
	_helper = null;
	_completeCb = null;
	_cityCoord = null;
	_pers = null;
	_checkPopTimerId = null;
	_destinationPops = null;
	_cityName = null;

	// phase is finished when city is biggest city with at least 15000 pops

	constructor(helper, cb)
	{
		_helper = helper;
		_completeCb = cb;
		_cityCoord = _helper.getCityCoords();
		_destinationPops = 15000;
		
		local c = city_x(_cityCoord.x, _cityCoord.y);
		if (c.is_valid())
			_cityName = c.get_name();
		
		_pers = _helper.loadVar("phase2");
		if (_pers == null)
		{
			_pers = {};
			_pers.maxOptGoal <- 0;
			_helper.saveVar("phase2", _pers);
			
			// save the current date - this marks the "official" start of the scenario.
			local starttime = world.get_time();
			_helper.saveVar("startTime_Year", starttime.year);
			_helper.saveVar("startTime_Month", starttime.month); // month 0..11
			
			debugmsg(format("starttime year: %d month: %d", starttime.year, starttime.month));
		}
		
		_checkPopTimerId = gTickRatio.addMonthCallback(checkPops.bindenv(this), 120); // make sure its after the growth increase goal
		
		debugmsg("phase2 - start");
	}
	
	function checkPops()
	{
		// get biggest city - increase destination pops if biggest city is > 20000;
		// we must be 1000 pops more than biggest city.
		local biggest = getBiggestCity();
		if (_destinationPops < biggest + 1000)
			_destinationPops = biggest + 1000;
	
		local pops = getPopsOfCity(_cityCoord);
		if (pops >= _destinationPops)
		{
			gTickRatio.removeMonthCallback(_checkPopTimerId);
			_checkPopTimerId = 0;
		
			// remove all optional goals related to city growth
			gPhases.stopOptionalGoal(1);
			gPhases.stopOptionalGoal(2);
			gPhases.stopOptionalGoal(3);
			gPhases.stopOptionalGoal(4);
			gPhases.stopOptionalGoal(5);
			gPhases.stopOptionalGoal(6);
			gPhases.stopOptionalGoal(7);
			gPhases.stopOptionalGoal(8);
			gPhases.stopOptionalGoal(9);
			gPhases.stopOptionalGoal(10);
		
			debugmsg("phase2 - complete: pops: " + pops);
			_completeCb(); // phase finished
		}
		
		if (pops >= 1500 && _pers.maxOptGoal < 1)
		{
			gPhases.addOptionalGoal(2);	//Gasthof - increase growth
			_pers.maxOptGoal = 1;
			_helper.saveVar("phase2", _pers);
		}
		
		if (pops >= 2200 && _pers.maxOptGoal < 2)
		{
			gPhases.addOptionalGoal(5);	//Apotheke - increase growth factor
			_pers.maxOptGoal = 2;
			_helper.saveVar("phase2", _pers);
		}
		
		if (pops >= 3500 && _pers.maxOptGoal < 3)
		{
			gPhases.addOptionalGoal(3);	//Gastwirtschaft - increase max growth
			_pers.maxOptGoal = 3;
			_helper.saveVar("phase2", _pers);
		}
		
		if (pops >= 5000 && _pers.maxOptGoal < 4)
		{
			gPhases.addOptionalGoal(7);	// Markt - increase growth + max growth
			_pers.maxOptGoal = 4;
			_helper.saveVar("phase2", _pers);
		}
		
		if (pops >= 6500 && _pers.maxOptGoal < 5)
		{
			gPhases.addOptionalGoal(4);	// Haushaltsartikel - increase growth factor
			_pers.maxOptGoal = 5;
			_helper.saveVar("phase2", _pers);
		}
		
		if (pops >= 8500 && _pers.maxOptGoal < 6)
		{
			gPhases.addOptionalGoal(6);	//Autohaus - increase max growth
			_pers.maxOptGoal = 6;
			_helper.saveVar("phase2", _pers);
		}
		
		// only reachable if another city is still bigger.
		
		if (pops >= 16500 && _pers.maxOptGoal < 7)
		{
			gPhases.addOptionalGoal(8); // Warenhaus - increase growth
			_pers.maxOptGoal = 7;
			_helper.saveVar("phase2", _pers);
		}
		
		if (pops >= 22500 && _pers.maxOptGoal < 8)
		{
			gPhases.addOptionalGoal(9); // Stadtverwaltung - increase growth factor
			_pers.maxOptGoal = 8;
			_helper.saveVar("phase2", _pers);
		}
	}
	
	function getBiggestCity()
	{
		local biggest = 0;
		local list = city_list_x();
		foreach (city in list)
		{
			if (city.x == _cityCoord.x && city.y ==_cityCoord.y)
				continue;
		
			local pops = city.get_citizens()[0];
			if (biggest < pops)
				biggest = pops;
		}
		return biggest;
	}
	
	function debugtext()
	{
		local msg = format("cityCoord: x: %d y: %d <br>destination pops: %d", _cityCoord.x, _cityCoord.y, _destinationPops);
		return msg;
	}
	
	function goaltext()
	{
		local text = ttext("Increase the citizens of {cityname} to {destPops}.");
		text.cityname = getLink(_cityName, _cityCoord);
		text.destPops = _destinationPops;
		return text.tostring();
	}
	
	function resulttext()
	{
		local t = ttext("The city {cityname} has currently {pops} citizens of the required {destPops} citizens.<br>(Checked every first day of the month)");
		t.cityname = getLink(_cityName, _cityCoord);
		t.pops = getPopsOfCity(_cityCoord);
		t.destPops = _destinationPops;
		return t.tostring();
	}
}

class Phase3
{
	_helper = null;
	_completeCb = null;
	_cityCoord = null;
	_pers = null;
	_buildTimerId = null;
	_checkStorageTimerId = null;
	_checkStorageAfterTicks = null;
	_checkLastMonthTimerId = null;
	_industryBuilder = null;
	_oktoberfestName = null;
	_consecutiveGoal = 6;
	_showHelpAfterCalls = 30000; // show help that we need a 5x5 slot for building; 50000 is around one month
	_buildCallsCount = null;
	
	//goal: bier versorgung für 6 monate

	constructor(helper, cb)
	{
		_helper = helper;
		_completeCb = cb;
		_cityCoord = _helper.getCityCoords();
		_checkStorageAfterTicks = 200;	// ticks between storage checks
		_buildCallsCount = 0;
		
		_pers = _helper.loadVar("phase3");
		if (_pers == null)
		{
			_pers = {};
			_pers.oktoberfestBuilt <- false;
			_pers.oktoberfestCoord <- null;
			_pers.thisMonthSupply <- false;		// was Bier supplied the whole month
			_pers.consecutiveMonths <- 0;
			_pers.showBuildingHelp <- false;
			_helper.saveVar("phase3", _pers);
		}
		
		if (!_pers.oktoberfestBuilt)
		{
			_buildTimerId = gTickRatio.addCallback(spawnOktoberfest.bindenv(this), 20);
		}
		else
		{
			local fac = factory_x(_pers.oktoberfestCoord.x, _pers.oktoberfestCoord.y);
			_oktoberfestName = fac.get_name();
			_checkStorageTimerId = gTickRatio.addCallback(checkOktoberfestStorage.bindenv(this), _checkStorageAfterTicks);
			_checkLastMonthTimerId = gTickRatio.addMonthCallback(checkLastMonth.bindenv(this));
		}
	}
	
	function spawnOktoberfest()
	{
		if(_industryBuilder == null)
			_industryBuilder = IndustryBuilder();
			
		++_buildCallsCount;
		if (_buildCallsCount > _showHelpAfterCalls)
			_pers.showBuildingHelp = true;
			
		local pos = _industryBuilder.buildInCityAsync(_cityCoord, "Oktoberfest", -1, 1);
		if (pos == null)
			return;
			
		if (typeof(pos) == "string")
		{
			debugmsg(pos);
			_industryBuilder = null;
			return;
		}
		
		_industryBuilder = null;
		
		local fac = factory_x(pos.x, pos.y);
		_oktoberfestName = fac.get_name();
		_pers.oktoberfestCoord = _helper.coordToTable(pos);
		_pers.oktoberfestBuilt = true;
		_helper.saveVar("phase3", _pers);
		
		gTickRatio.removeCallback(_buildTimerId);
		_buildTimerId = 0;
		_checkStorageTimerId = gTickRatio.addCallback(checkOktoberfestStorage.bindenv(this), _checkStorageAfterTicks);
		_checkLastMonthTimerId = gTickRatio.addMonthCallback(checkLastMonth.bindenv(this));
	}
	
	function checkOktoberfestStorage()
	{
		// Bierversorgung sicherstellen für 6 aufeinanderfolgende Monate.
		local fac = factory_x(_pers.oktoberfestCoord.x, _pers.oktoberfestCoord.y);
		local bier = fac.input["Bier"].get_storage()[0];
		if (bier == 0 && !gDebug)
		{
			_pers.thisMonthSupply = false;
			_pers.consecutiveMonths = 0;
			_helper.saveVar("phase3", _pers);
		}
	}
	
	function checkLastMonth()
	{
		if (_pers.thisMonthSupply)
		{
			++_pers.consecutiveMonths;
		}
		
		_pers.thisMonthSupply = true;
		
		_helper.saveVar("phase3", _pers);
		
		if (_pers.consecutiveMonths >= _consecutiveGoal)
		{
			// goal reached
			gTickRatio.removeCallback(_checkStorageTimerId);
			_checkStorageTimerId = 0;
			gTickRatio.removeMonthCallback(_checkLastMonthTimerId);
			_checkLastMonthTimerId = 0;
			
			debugmsg("phase3 complete");
			_completeCb();
		}
	}
	
	function debugtext()
	{
		return format("built: %d, consecMonths: %d, thisMonth: %d", _pers.oktoberfestBuilt, _pers.consecutiveMonths, _pers.thisMonthSupply);
	}
	
	function goaltext()
	{
		local t = null;
		if(_pers.oktoberfestBuilt)
		{
			t = ttext("Supply the {okt} with the most essential ware for {consecGoal} consecutive months, without it going out of stock.<br>(The most essential ware is {bier}.)");
			t.okt = getLink(_oktoberfestName, _pers.oktoberfestCoord);
			t.consecGoal = _consecutiveGoal;
			t.bier = translate("Bier");
		}
		else
		{
			// not built yet, we have to wait
			t = ttext("The mayor is taking a decision... (Please wait)");
			
			if (_pers.showBuildingHelp)
			{
				local help = ttext("The mayor is trying to find a suitable spot for a new factory.<br>You can help him by terraforming 5x5 flat land tiles inside the city borders.");
				local temp = t.tostring() + "<br><br>" + help.tostring();
				return temp;
			}
		}
	
		return t.tostring();
	}
	
	function resulttext()
	{
		if (!_pers.oktoberfestBuilt)
			return "";
		
		local t = ttext("Supplied wares for {current} out of {consecDest} consecutive months.");
		t.current = _pers.consecutiveMonths;
		t.consecDest = _consecutiveGoal;
		return t.tostring();
	}
}

class Phase4
{
	_helper = null;
	_completeCb = null;
	_pers = null;
	_oktCoord = null;
	_checkStorageTimerId = null;
	_checkStorageAfterTicks = null;
	_checkLastMonthTimerId = null;
	_consecutiveGoal = 12;
	_oktoberfestName = null;
	_paxBoostGoal = 800;

	// goal: bier, backwaren und 800% passagier boost für 12 monate

	constructor(helper, cb)
	{
		_helper = helper;
		_completeCb = cb;
		
		local phase3pers = _helper.loadVar("phase3");
		_oktCoord = phase3pers.oktoberfestCoord;
		
		_checkStorageAfterTicks = 200;
		
		_pers = _helper.loadVar("phase4");
		if (_pers == null)
		{
			_pers = {};
			_pers.thisMonthSupply <- false;		// was Bier supplied the whole month
			_pers.consecutiveMonths <- 0;
			_helper.saveVar("phase4", _pers);
		}
		
		local fac = factory_x(_oktCoord.x, _oktCoord.y);
		_oktoberfestName = fac.get_name();
		
		_checkStorageTimerId = gTickRatio.addCallback(checkOktoberfestStorageP4.bindenv(this), _checkStorageAfterTicks);
		_checkLastMonthTimerId = gTickRatio.addMonthCallback(checkLastMonth.bindenv(this));
	}
	
	function checkOktoberfestStorageP4()
	{		
		local fac = factory_x(_oktCoord.x, _oktCoord.y);
		local bier = fac.input["Bier"].get_storage()[0];
		local backwaren = fac.input["Backwaren"].get_storage()[0];
		local mandeln = fac.input["Mandeln"].get_storage()[0];
		local paxBoost = fac.get_boost_pax()[0];
		if ((bier == 0 || backwaren == 0 || mandeln == 0 || paxBoost < _paxBoostGoal) && !gDebug)
		{
			_pers.thisMonthSupply = false;
			_pers.consecutiveMonths = 0;
			_helper.saveVar("phase4", _pers);
		}
	}
	
	function checkLastMonth()
	{
		if (_pers.thisMonthSupply)
		{
			++_pers.consecutiveMonths;
		}
		
		_pers.thisMonthSupply = true;
		
		_helper.saveVar("phase4", _pers);
		
		if (_pers.consecutiveMonths >= _consecutiveGoal)
		{
			gTickRatio.removeCallback(_checkStorageTimerId);
			_checkStorageTimerId = 0;
			gTickRatio.removeMonthCallback(_checkLastMonthTimerId);
			_checkLastMonthTimerId = 0;
			
			debugmsg("phase4 complete");
			_completeCb();
		}
	}
	
	function debugtext()
	{
		return format("consecMonths: %d, thisMonth: %d", _pers.consecutiveMonths, _pers.thisMonthSupply);
	}
	
	function goaltext()
	{
		local t = ttext("Supply the {okt} with these wares for {months} consecutive months, without it going out of stock:<br>{bier}, {backwaren}, {mandeln}<br>Additionally the passenger boost has to be kept above {paxBoost}% during that time.");
		t.okt = getLink(_oktoberfestName, _oktCoord);
		t.months = _consecutiveGoal;
		t.bier = translate("Bier");
		t.backwaren = translate("Backwaren");
		t.mandeln = translate("Mandeln");
		t.paxBoost = _paxBoostGoal;
		return t.tostring();
	}
	
	function resulttext()
	{
		local t = ttext("Supplied wares for {current} out of {consecDest} consecutive months.");
		t.current = _pers.consecutiveMonths;
		t.consecDest = _consecutiveGoal;
		
		local fac = factory_x(_oktCoord.x, _oktCoord.y);
		local paxBoost = fac.get_boost_pax()[0];
		
		local t2 = ttext("Passenger boost: {paxBoost}% of required {maxPax}%");
		t2.paxBoost = paxBoost
		t2.maxPax = _paxBoostGoal;
		
		return t.tostring() + "<br>" + t2.tostring();
	}
}

class Phase5
{
	_helper = null;
	_completeCb = null;
	_oktCoord = null;
	_checkStorageAfterTicks = null;
	_pers = null;
	_checkMonthTimerId = null;
	_checkStorageTimerId = null;
	_consecutiveGoal = 3;
	_oktoberfestName = null;
	
	// goal: in october, oktoberfest needs to be supplied with ALL wares for 3 consecutive years
	
	constructor(helper, cb)
	{
		_helper = helper;
		_completeCb = cb;
		_checkStorageTimerId = 0;
		_checkStorageAfterTicks = 200;
		
		local phase3pers = _helper.loadVar("phase3");
		_oktCoord = phase3pers.oktoberfestCoord;
		
		_pers = _helper.loadVar("phase5");
		if (_pers == null)
		{
			_pers = {};
			_pers.supplied <- false; // was everything supplied in Oktober
			_pers.consecutiveYears <- 0;
			_helper.saveVar("phase5", _pers);
		}
		
		local fac = factory_x(_oktCoord.x, _oktCoord.y);
		_oktoberfestName = fac.get_name();
		
		_checkMonthTimerId = gTickRatio.addMonthCallback(checkMonth.bindenv(this));
		local t = world.get_time();
		if (t.month == 9)
		{
			_checkStorageTimerId = gTickRatio.addCallback(checkStorage.bindenv(this), _checkStorageAfterTicks);
		}
	}
	
	function checkMonth()
	{
		local t = world.get_time();
		//t.month   month 0..11
		if (t.month == 9) // if Oktober
		{
			_pers.supplied = true;
			// start timer to watch the storage
			_checkStorageTimerId = gTickRatio.addCallback(checkStorage.bindenv(this), _checkStorageAfterTicks);
		}
		else
		{
			// no need to check in other months - goal is only for Oktober
			if (_checkStorageTimerId > 0)
			{
				gTickRatio.removeCallback(_checkStorageTimerId);
				_checkStorageTimerId = 0;
			}
		}
		
		if (t.month == 10)
		{
			// check goal
			if (_pers.supplied)
			{
				++_pers.consecutiveYears;
			}
			
			_pers.supplied = true;
			
			_helper.saveVar("phase5", _pers);
			
			if (_pers.consecutiveYears >= _consecutiveGoal)
			{
				//goal reached
				if (_checkStorageTimerId > 0)
				{
					gTickRatio.removeCallback(_checkStorageTimerId);
					_checkStorageTimerId = 0;
				}
				
				gTickRatio.removeMonthCallback(_checkMonthTimerId);
				_checkMonthTimerId = 0;
				
				_completeCb();
			}
		}
	}
	
	function checkStorage()
	{
		local fac = factory_x(_oktCoord.x, _oktCoord.y);
		// pax boost not checked anymore
		local bier = fac.input["Bier"].get_storage()[0];
		local backwaren = fac.input["Backwaren"].get_storage()[0];
		local fisch = fac.input["Fisch"].get_storage()[0];
		local gefluegel = fac.input["Gefluegel"].get_storage()[0];
		local vieh = fac.input["Vieh"].get_storage()[0];
		local fleisch = fac.input["Fleisch"].get_storage()[0];
		local zucker = fac.input["Zucker"].get_storage()[0];
		local mandeln = fac.input["Mandeln"].get_storage()[0];
		
		if ((bier == 0 || backwaren == 0 || fisch == 0 || gefluegel == 0 ||
			vieh == 0 || fleisch == 0 || zucker == 0 || mandeln == 0) && !gDebug)
		{
			_pers.supplied = false;
			_pers.consecutiveYears = 0;
			
			_helper.saveVar("phase5", _pers);
		}
	}
	
	function debugtext()
	{
		return format("consecutive years: %d, supplied: %d", _pers.consecutiveYears, _pers.supplied);
	}
	
	function goaltext()
	{
		local t = ttext("Supply the {okt} with ALL wares in <i>October</i> for {years} consecutive years.");
		t.okt = getLink(_oktoberfestName, _oktCoord);
		t.years = _consecutiveGoal;
		
		return t.tostring();
	}
	
	function resulttext()
	{
		local t = ttext("Fully supplied wares in October for {years} out of {maxYears} consecutive years.");
		t.years = _pers.consecutiveYears;
		t.maxYears = _consecutiveGoal;
		return t.tostring();
	}
}

class PhaseComplete
{
	_helper = null;
	constructor(helper)
	{
		_helper = helper;
		local endtime = world.get_time();
		_helper.saveVar("endTime_Month", endtime.month);
		_helper.saveVar("endTime_Year", endtime.year);
	}
	
	function debugtext()
	{
		return "scenario finished";
	}
	
	function goaltext()
	{
		
		local t = ttext("Congratulations.<br>You have finished this scenario.<br>You started in {month} {year} and finished in {endmonth} {endyear}.");
		local month = _helper.loadVar("startTime_Month");
		if (month == 12)
			month = 11; //first version had 1..12 months instead of 0..11
		t.year = _helper.loadVar("startTime_Year");
		t.month = get_month_name(month);
		t.endyear = _helper.loadVar("endTime_Year");
		local endmonth = _helper.loadVar("endTime_Month");
		if (endmonth == 12)
			endmonth = 11; //first version had 1..12 months instead of 0..11
		t.endmonth = get_month_name(endmonth);
		return t.tostring();
	}
	
	function resulttext()
	{
		return goaltext();
	}
}

function getPopsOfCity(cityCoord)
{
	local c = city_x(cityCoord.x, cityCoord.y);
	if (c.is_valid())
	{
		local ar = c.get_citizens();
		return ar[0];
	}
	else
	{
		debugmsg("getPopsOfCity - invalid city. x: " + cityCoord.x + " y: " + cityCoord.y);
	}
	return 0;
}

function getLink(cityname, cityCoord)
{
	return format("<a href='(%d,%d)'>%s</a>", cityCoord.x, cityCoord.y, cityname);
}

function debugmsg(msg)
{
	if (gDebugMessages)
	{
		local me = player_x(0);
		gui.add_message(me, msg);
	}
}

function start()
{
	debug.set_pause_on_error(true);
	gTickRatio <- TickRatio();
	gPhases <- Phases();
	
	local idx = scenario.forbidden_tools.find(tool_add_city)
	scenario.forbidden_tools.remove(idx)
	idx = scenario.forbidden_tools.find(tool_land_chain)
	scenario.forbidden_tools.remove(idx)
	idx = scenario.forbidden_tools.find(tool_link_factory)
	scenario.forbidden_tools.remove(idx)
	idx = scenario.forbidden_tools.find(tool_city_chain)
	scenario.forbidden_tools.remove(idx)
	idx = scenario.forbidden_tools.find(tool_build_factory)
	scenario.forbidden_tools.remove(idx)
	idx = scenario.forbidden_tools.find(tool_increase_industry)
	scenario.forbidden_tools.remove(idx)
	idx = scenario.forbidden_tools.find(dialog_enlarge_map)
	scenario.forbidden_tools.remove(idx)
	idx = scenario.forbidden_tools.find(dialog_edit_factory)
	scenario.forbidden_tools.remove(idx)
	
	rules.allow_tool(player_all, tool_build_factory);
	rules.allow_tool(player_all, tool_increase_industry);
	
	rules.gui_needs_update();
}

/**
 * Called after loading a savegame of a played scenario
 */
function resume_game()
{
	start()
}

function is_scenario_completed(player)
{
	if (gPhases == null || gTickRatio == null)
		return 0;

	gTickRatio.tick();

	return gPhases.completePercent();
}

/**
 * Called when user clicks to build etc.
 * Error messages are sent back over network to clients.
 * Does not work with waybuilding etc use the rules.forbid_* functions in this case.
 *
 * @param pos is a table with coordinate { x=, y=, z=}
 * @return null if allowed, an error message otherwise
 */
function is_work_allowed_here(pl, tool_id, pos)
{
	if (gTickRatio == null)
		return null;
	return gTickRatio.is_work_allowed_here(pl, tool_id, pos);
}

function new_month()
{
	if (gTickRatio == null)
		return;
	gTickRatio.month();
}

function new_year()
{

}







