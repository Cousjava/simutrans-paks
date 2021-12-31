
/*
	optional goal interface:
	
	constructor (helper, completeCallback)
	
	getId();  // return int constant
	
	
	completeCallback(this);
*/

function messageBuiltIndustry(industryName, coord)
{
	local t = ttext("A new {factory} was built in the city. See scenario goals for more information.");
	t.factory = industryName;
	gui.add_message_at(t.tostring() , coord);
}

function addWaresList(str, waresList)
{
	for (local i = 0; i < waresList.len(); ++i)
	{
		if (i>0)
			str += ", ";
		str += translate(waresList[i]);
	}
	str += "<br>";
	
	return str;
}

// build Baustofflager + add pops based on amount delivered.
class BuildIndustry1
{
	_id = 1;
	_helper = null;
	_completeCB = null;
	_pers = null;
	_buildTimerId = null;
	_addPopsTimerId = null;
	_cityCoord = null;
	_industryBuilder = null;
	_industryName = null;

	constructor(helper, completeCallback)
	{
		_helper = helper;
		_completeCB = completeCallback;
		_cityCoord = _helper.getCityCoords();
		_pers = _helper.loadVar("optGoal" + _id);
		if (_pers == null)
		{
			_pers = {};
			_pers.industryBuilt <- false;
			_pers.industryCoord <- null;
			_helper.saveVar("optGoal" + _id, _pers);
		}
		
		if (!_pers.industryBuilt)
		{
			_buildTimerId = gTickRatio.addCallback(buildIndustry.bindenv(this), 20); //TODO: ticks?
		}
		else
		{
			local fac = factory_x(_pers.industryCoord.x, _pers.industryCoord.y);
			_industryName = fac.get_name();
			_addPopsTimerId = gTickRatio.addMonthCallback(addPopsBasedOnDelivered.bindenv(this));
		}
	}
	
	function stop()
	{
		gTickRatio.removeCallback(_buildTimerId);
		_buildTimerId = 0;
		gTickRatio.removeCallback(_addPopsTimerId);
		_addPopsTimerId = 0;
	}
	
	function getId()
	{
		return _id;
	}
	
	function buildIndustry()
	{
		if (_industryBuilder == null)
		{
			_industryBuilder = IndustryBuilder();
		}
		
		local pos = _industryBuilder.buildInCityAsync(_cityCoord, "Baustoffhof_1800", -1, 0);
		
		if (pos == null)
			return;
		
		if (typeof(pos) == "string")
		{
			debugmsg(pos);
			_industryBuilder = null;
			return;
		}
		
		_industryBuilder = null;
		
		_pers.industryBuilt = true;
		_pers.industryCoord = _helper.coordToTable(pos);
		_helper.saveVar("optGoal" + _id, _pers);
		if (_buildTimerId > 0)
		{
			gTickRatio.removeCallback(_buildTimerId);
			_buildTimerId = 0;
		}
		
		local fac = factory_x(pos.x, pos.y);
		_industryName = fac.get_name();
		
		_addPopsTimerId = gTickRatio.addMonthCallback(addPopsBasedOnDelivered.bindenv(this));
		
		messageBuiltIndustry(_industryName, _pers.industryCoord);
	}
	
	function addPopsBasedOnDelivered()
	{
		if (!_pers.industryBuilt)
			return;
			
		debugmsg("optgoal1 - add pops based on delivered");
			
		local fac = factory_x(_pers.industryCoord.x, _pers.industryCoord.y);
		
		// Zement, Betonfertigteile, Stahl
		// Zement 100%	- 140
		// Betonfertigteile 100% - 140
		// Stahl 25%	- 35
		
		// Production 140 pro Monat
		// 315 möglich - unbuffed.
		
		local consumedTotal = fac.input["Zement"].get_consumed()[1]; // consumed of last month#
		consumedTotal += fac.input["Betonfertigteile"].get_consumed()[1];
		consumedTotal += fac.input["Stahl"].get_consumed()[1];
		
		local popIncrease = consumedTotal / 21;
		if (popIncrease > 20)
			popIncrease = 20; // cap at 20
		
		if (gDebug)
		{
			popIncrease = 200;
		}
		
		// increases growth
		if (popIncrease > 0)
		{
			debugmsg(format("optgoal %d: growth: %d", _id, popIncrease));
			local cityGrowth = _helper.loadVar("cityGrowth");
			if (cityGrowth == null)
				return;
				
			cityGrowth.growth.push(popIncrease);
			//debugmsg("increasing city by " + popIncrease)
			//local publicPlayer = player_x(1);
			//local cmd = command_x(tool_change_city_size);
			//local sq = square_x(_cityCoord.x, _cityCoord.y);
			//local tile = sq.get_ground_tile();
			
			//local retval = cmd.work(publicPlayer, tile, popIncrease.tostring());
			
			//debugmsg("increase city retval: " + retval);
		}
	}
	
	function debugtext()
	{
		return "";
	}
	
	function goaltext()
	{
		if (!_pers.industryBuilt)
			return "";
		
		local text = ttext("{fac}:<br>You can increase the city's <i>growth</i> by supplying at least one of these wares to the factory:<br>");
		text.fac = getLink(_industryName, _pers.industryCoord);
		local wares = ["Zement", "Betonfertigteile", "Stahl"];

		return addWaresList(text.tostring(), wares);
	}
	
	function resulttext()
	{
		return "";
	}
}

class BuildGasthof1800
{
	_id = 2;
	_helper = null;
	_completeCB = null;
	_pers = null;
	_buildTimerId = null;
	_addPopsTimerId = null;
	_cityCoord = null;
	_industryBuilder = null;
	_industryName = null;
	
	constructor(helper, completeCb)
	{
		_helper = helper;
		_completeCB = completeCb;
		_cityCoord = _helper.getCityCoords();
		
		_pers = _helper.loadVar("optGoal" + _id);
		if (_pers == null)
		{
			_pers = {};
			_pers.industryBuilt <- false;
			_pers.industryCoord <- null;
			_helper.saveVar("optGoal" + _id, _pers);
		}
		
		if (!_pers.industryBuilt)
		{
			_buildTimerId = gTickRatio.addCallback(buildIndustry.bindenv(this), 20); //TODO: ticks?
		}
		else
		{
			local fac = factory_x(_pers.industryCoord.x, _pers.industryCoord.y);
			_industryName = fac.get_name();
			_addPopsTimerId = gTickRatio.addMonthCallback(addPopsBasedOnDelivered.bindenv(this));
		}
	}
	
	function stop()
	{
		gTickRatio.removeCallback(_buildTimerId);
		_buildTimerId = 0;
		gTickRatio.removeCallback(_addPopsTimerId);
		_addPopsTimerId = 0;
	}
	
	function getId()
	{
		return _id;
	}
	
	function buildIndustry()
	{
		if (_industryBuilder == null)
		{
			_industryBuilder = IndustryBuilder();
		}
		
		local pos = _industryBuilder.buildInCityAsync(_cityCoord, "Gasthof_1800", -1, 0);
		
		if (pos == null)
			return;
		
		if (typeof(pos) == "string")
		{
			debugmsg(pos);
			_industryBuilder = null;
			return;
		}
		
		_industryBuilder = null;
		
		_pers.industryBuilt = true;
		_pers.industryCoord = _helper.coordToTable(pos);
		_helper.saveVar("optGoal" + _id, _pers);
		if (_buildTimerId > 0)
		{
			gTickRatio.removeCallback(_buildTimerId);
			_buildTimerId = 0;
		}
		
		local fac = factory_x(pos.x, pos.y);
		_industryName = fac.get_name();
		
		_addPopsTimerId = gTickRatio.addMonthCallback(addPopsBasedOnDelivered.bindenv(this));
		
		messageBuiltIndustry(_industryName, _pers.industryCoord);
	}
	
	function addPopsBasedOnDelivered()
	{
		if (!_pers.industryBuilt)
			return;
			
		debugmsg("optgoal2 - add pops based on delivered");
			
		local fac = factory_x(_pers.industryCoord.x, _pers.industryCoord.y);
		
		// Bier, Wein, Backwaren
		// Bier 100%		- 40
		// Wein 50% 		- 20
		// Backwaren 100%	- 40
		
		// Fisch	30%		- 12	*2 24
		// Fleisch	70%		- 28
		
		// Production 40 pro Monat
		// 100 möglich - unbuffed.
		
		// 52 for max growth - unbuffed
		
		// 5 Pops per 100 consumed
		local consumedTotal = fac.input["Bier"].get_consumed()[1]; // consumed of last month#
		consumedTotal += fac.input["Wein"].get_consumed()[1];
		consumedTotal += fac.input["Backwaren"].get_consumed()[1];
		
		local consumed2 = fac.input["Fisch"].get_consumed()[1];
		consumed2 += fac.input["Fleisch"].get_consumed()[1];
		
		local popIncrease = consumedTotal / 10;
		if (popIncrease > 15)
			popIncrease = 15;
		
		local maxGrowth = consumed2 / 2;
		
		if (gDebug)
		{
			popIncrease = 100;
		}
		
		// increases growth
		if (popIncrease > 0)
		{
			debugmsg(format("optgoal %d: growth: %d", _id, popIncrease));
			local cityGrowth = _helper.loadVar("cityGrowth");
			if (cityGrowth == null)
				return;
				
			cityGrowth.growth.push(popIncrease);
		}
		
		// increases max growth
		if (maxGrowth > 0)
		{
			debugmsg(format("optgoal %d: maxgrowth: %d", _id, maxGrowth));
			local cityGrowth = _helper.loadVar("cityGrowth");
			if (cityGrowth == null)
				return;
				
			cityGrowth.maxGrowth.push(maxGrowth);
		}
	}
	
	function debugtext()
	{
		return "";
	}
	
	function goaltext()
	{
		if (!_pers.industryBuilt)
			return "";
		
		local text = ttext("{fac}:<br>You can increase the city's <i>growth</i> by supplying at least one of these wares to the factory:<br>");
		text.fac = getLink(_industryName, _pers.industryCoord);
		local wares = ["Bier", "Wein", "Backwaren"];
		local text = addWaresList(text.tostring(), wares) + "<br>";
		
		local text2 = ttext("You can increase the city's <i>maximum growth</i> by supplying at least one of these wares to the factory:<br>");
		local wares2 = ["Fisch", "Fleisch"];
		text2 = addWaresList(text2.tostring(), wares2);
		
		return text + text2;
	}
	
	function resulttext()
	{
		return "";
	}
}


class BuildGastwirtschaft
{
	_id = 3;
	_helper = null;
	_completeCB = null;
	_pers = null;
	_buildTimerId = null;
	_addPopsTimerId = null;
	_cityCoord = null;
	_industryBuilder = null;
	_industryName = null;
	
	constructor(helper, completeCb)
	{
		_helper = helper;
		_completeCB = completeCb;
		_cityCoord = _helper.getCityCoords()
		
		_pers = _helper.loadVar("optGoal" + _id);
		if (_pers == null)
		{
			_pers = {};
			_pers.industryBuilt <- false;
			_pers.industryCoord <- null;
			_helper.saveVar("optGoal" + _id, _pers);
		}
		
		if (!_pers.industryBuilt)
		{
			_buildTimerId = gTickRatio.addCallback(buildIndustry.bindenv(this), 20); //TODO: ticks?
		}
		else
		{
			local fac = factory_x(_pers.industryCoord.x, _pers.industryCoord.y);
			_industryName = fac.get_name();
		
			_addPopsTimerId = gTickRatio.addMonthCallback(addPopsBasedOnDelivered.bindenv(this));
		}
	}
	
	function stop()
	{
		gTickRatio.removeCallback(_buildTimerId);
		_buildTimerId = 0;
		gTickRatio.removeCallback(_addPopsTimerId);
		_addPopsTimerId = 0;
	}
	
	function getId()
	{
		return _id;
	}
	
	function buildIndustry()
	{
		if (_industryBuilder == null)
		{
			_industryBuilder = IndustryBuilder();
		}
		
		local pos = _industryBuilder.buildInCityAsync(_cityCoord, "Gastwirtschaft_mit_Laden", -1, 0);
		
		if (pos == null)
			return;
		
		if (typeof(pos) == "string")
		{
			debugmsg(pos);
			_industryBuilder = null;
			return;
		}
		
		_industryBuilder = null;
		
		_pers.industryBuilt = true;
		_pers.industryCoord = _helper.coordToTable(pos);
		_helper.saveVar("optGoal" + _id, _pers);
		if (_buildTimerId > 0)
		{
			gTickRatio.removeCallback(_buildTimerId);
			_buildTimerId = 0;
		}
		
		local fac = factory_x(pos.x, pos.y);
		_industryName = fac.get_name();
		
		_addPopsTimerId = gTickRatio.addMonthCallback(addPopsBasedOnDelivered.bindenv(this));
		
		messageBuiltIndustry(_industryName, _pers.industryCoord);
	}
	
	function addPopsBasedOnDelivered()
	{
		if (!_pers.industryBuilt)
			return;
			
		debugmsg("optgoal3 - add pops based on delivered");
			
		local fac = factory_x(_pers.industryCoord.x, _pers.industryCoord.y);
		
		// Bier 			100%		- 80	- 40 (only half effective)
		// Fertiggerichte 	20% 		- 16
		// Konserven 		20%			- 16
		// Marzipan 		10%			- 8
		
		// Production 80 pro Monat
		// 120 möglich - unbuffed.
		
		local consumedTotal = fac.input["Bier"].get_consumed()[1] / 2; // consumed of last month#
		consumedTotal += fac.input["Fertiggerichte"].get_consumed()[1];
		consumedTotal += fac.input["Dosenessen"].get_consumed()[1];
		consumedTotal += fac.input["Marzipan"].get_consumed()[1];
		
		local popIncrease = consumedTotal / 8;
		if (popIncrease > 30)
			popIncrease = 30;
		
		if (gDebug)
		{
			popIncrease = 100;
		}
		
		// increases max growth
		if (popIncrease > 0)
		{
			debugmsg(format("optgoal %d: maxgrowth: %d", _id, popIncrease));
			local cityGrowth = _helper.loadVar("cityGrowth");
			if (cityGrowth == null)
				return;
				
			cityGrowth.maxGrowth.push(popIncrease);
		}
	}
	
	function debugtext()
	{
		return "";
	}
	
	function goaltext()
	{
		if (!_pers.industryBuilt)
			return "";
	
		local text = ttext("{fac}:<br>You can increase the city's <i>maximum growth</i> by supplying at least one of these wares to the factory:<br>");
		text.fac = getLink(_industryName, _pers.industryCoord);
		local wares = ["Bier", "Fertiggerichte", "Dosenessen", "Marzipan"];
		return addWaresList(text.tostring(), wares);
	}
	
	function resulttext()
	{
		return "";
	}
}

class BuildHaushaltsartikel
{
	_id = 4;
	_helper = null;
	_completeCB = null;
	_pers = null;
	_buildTimerId = null;
	_addPopsTimerId = null;
	_cityCoord = null;
	_industryBuilder = null;
	_industryName = null;
	_checkStorageTimerId = null;
	_checkStorageAfterTicks = null;

	constructor(helper, completeCb)
	{
		_helper = helper;
		_completeCB = completeCb;
		_cityCoord = _helper.getCityCoords();
		_checkStorageAfterTicks = 5000;
		
		_pers = _helper.loadVar("optGoal" + _id);
		if (_pers == null)
		{
			_pers = {};
			_pers.supplyOn <- 0;
			_pers.supplyOff <- 0;
			_pers.industryBuilt <- false;
			_pers.industryCoord <- null;
			_helper.saveVar("optGoal" + _id, _pers);
		}
		
		if (!_pers.industryBuilt)
		{
			_buildTimerId = gTickRatio.addCallback(buildIndustry.bindenv(this), 20); //TODO: ticks?
		}
		else
		{
			local fac = factory_x(_pers.industryCoord.x, _pers.industryCoord.y);
			_industryName = fac.get_name();
		
			_addPopsTimerId = gTickRatio.addMonthCallback(addPopsBasedOnDelivered.bindenv(this));
			_checkStorageTimerId = gTickRatio.addCallback(checkStorage.bindenv(this), _checkStorageAfterTicks);
		}
	}
	
	function stop()
	{
		gTickRatio.removeCallback(_buildTimerId);
		_buildTimerId = 0;
		gTickRatio.removeCallback(_addPopsTimerId);
		_addPopsTimerId = 0;
		gTickRatio.removeCallback(_checkStorageTimerId);
		_checkStorageTimerId = 0;
	}
	
	function getId()
	{
		return _id;
	}
	
	function buildIndustry()
	{
		if (_industryBuilder == null)
		{
			_industryBuilder = IndustryBuilder();
		}
		
		local pos = _industryBuilder.buildInCityAsync(_cityCoord, "Geraete_und_Haushaltsartikel", -1, 0);
		
		if (pos == null)
			return;
		
		if (typeof(pos) == "string")
		{
			debugmsg(pos);
			_industryBuilder = null;
			return;
		}
		
		_industryBuilder = null;
		
		_pers.industryBuilt = true;
		_pers.industryCoord = _helper.coordToTable(pos);
		_helper.saveVar("optGoal" + _id, _pers);
		if (_buildTimerId > 0)
		{
			gTickRatio.removeCallback(_buildTimerId);
			_buildTimerId = 0;
		}
		
		local fac = factory_x(pos.x, pos.y);
		_industryName = fac.get_name();
		
		_addPopsTimerId = gTickRatio.addMonthCallback(addPopsBasedOnDelivered.bindenv(this));
		_checkStorageTimerId = gTickRatio.addCallback(checkStorage.bindenv(this), _checkStorageAfterTicks);
		
		messageBuiltIndustry(_industryName, _pers.industryCoord);
	}
	
	function checkStorage()
	{
		local fac = factory_x(_pers.industryCoord.x, _pers.industryCoord.y);
		local maschinen = fac.input["Maschinen"].get_storage()[0];
		local moebel = fac.input["Moebel"].get_storage()[0];
		
		if (maschinen != 0)
		{
			++_pers.supplyOn;
		}
		else 
		{
			++_pers.supplyOff;
		}
		
		if (moebel != 0)
		{
			++_pers.supplyOn;
		}
		else 
		{
			++_pers.supplyOff;
		}
		
		_helper.saveVar("optGoal" + _id, _pers);
	}
	
	function addPopsBasedOnDelivered()
	{
		if (!_pers.industryBuilt)
			return;
			
		// Maschinen		100%		- 80
		// Möbel		 	100% 		- 80
		
		// Production 80 pro Monat
		// 160 möglich - unbuffed.
		
		local total = _pers.supplyOn + _pers.supplyOff;
		local growthFactor = 1 + (_pers.supplyOn / (total * 2.0)); // max 1,5 increase
		
		_pers.supplyOn = 0;
		_pers.supplyOff = 0;
		
		_helper.saveVar("optGoal" + _id, _pers);
		
		if (gDebug)
		{
			growthFactor = 1.5;
		}
		
		//increases growth factor
		debugmsg(format("optgoal %d: growthFactor: %f", _id, growthFactor));
		local cityGrowth = _helper.loadVar("cityGrowth");
		if (cityGrowth == null)
			return;
			
		cityGrowth.growthFactor.push(growthFactor);
	}
	
	function debugtext()
	{
		return "";
	}
	
	function goaltext()
	{
		if (!_pers.industryBuilt)
			return "";
	
		local text = ttext("{fac}:<br>You can increase the city's <i>growth factor</i> by supplying at least one of these wares to the factory:<br>");
		text.fac = getLink(_industryName, _pers.industryCoord);
		local wares =["Maschinen", "Moebel"];
		return addWaresList(text.tostring(), wares);
	}
	
	function resulttext()
	{
		return "";
	}
}

class BuildApotheke
{
	_id = 5;
	_helper = null;
	_completeCB = null;
	_pers = null;
	_buildTimerId = null;
	_addPopsTimerId = null;
	_cityCoord = null;
	_industryBuilder = null;
	_checkStorageTimerId = null;
	_checkStorageAfterTicks = null;
	_industryName = null;
	
	constructor(helper, completeCb)
	{
		_helper = helper;
		_completeCB = completeCb;
		_cityCoord = _helper.getCityCoords();
		_checkStorageAfterTicks = 5000;
		
		_pers = _helper.loadVar("optGoal" + _id);
		if (_pers == null)
		{
			_pers = {};
			_pers.industryBuilt <- false;
			_pers.industryCoord <- null;
			_pers.medsOn <- 0;	// how many checks were meds supplied
			_pers.medsOff <- 0; // how many checks were meds not supplied.
			_helper.saveVar("optGoal" + _id, _pers);
		}
		
		if (!_pers.industryBuilt)
		{
			_buildTimerId = gTickRatio.addCallback(buildIndustry.bindenv(this), 20); //TODO: ticks?
		}
		else
		{
			local fac = factory_x(_pers.industryCoord.x, _pers.industryCoord.y);
			_industryName = fac.get_name();
		
			_addPopsTimerId = gTickRatio.addMonthCallback(addPopsBasedOnDelivered.bindenv(this), 10);
			_checkStorageTimerId = gTickRatio.addCallback(checkStorage.bindenv(this), _checkStorageAfterTicks);
		}
	}
	
	function stop()
	{
		gTickRatio.removeCallback(_buildTimerId);
		_buildTimerId = 0;
		gTickRatio.removeCallback(_addPopsTimerId);
		_addPopsTimerId = 0;
		gTickRatio.removeCallback(_checkStorageTimerId);
		_checkStorageTimerId = 0;
	}
	
	function getId()
	{
		return _id;
	}
	
	function buildIndustry()
	{
		if (_industryBuilder == null)
		{
			_industryBuilder = IndustryBuilder();
		}
		
		local pos = _industryBuilder.buildInCityAsync(_cityCoord, "Apotheke1800_NIC", -1, 0);
		
		if (pos == null)
			return;
		
		if (typeof(pos) == "string")
		{
			debugmsg(pos);
			_industryBuilder = null;
			return;
		}
		
		_industryBuilder = null;
		
		_pers.industryBuilt = true;
		_pers.industryCoord = _helper.coordToTable(pos);
		_helper.saveVar("optGoal" + _id, _pers);
		if (_buildTimerId > 0)
		{
			gTickRatio.removeCallback(_buildTimerId);
			_buildTimerId = 0;
		}
		
		local fac = factory_x(pos.x, pos.y);
		_industryName = fac.get_name();
		
		_addPopsTimerId = gTickRatio.addMonthCallback(addPopsBasedOnDelivered.bindenv(this), 10);
		_checkStorageTimerId = gTickRatio.addCallback(checkStorage.bindenv(this), _checkStorageAfterTicks);
		
		messageBuiltIndustry(_industryName, _pers.industryCoord);
	}
	
	function checkStorage()
	{
		local fac = factory_x(_pers.industryCoord.x, _pers.industryCoord.y);
		local meds = fac.input["Medikamente"].get_storage()[0];
		
		if (meds != 0)
		{
			++_pers.medsOn;
		}
		else 
		{
			++_pers.medsOff;
		}
		
		_helper.saveVar("optGoal" + _id, _pers);
	}
	
	function addPopsBasedOnDelivered()
	{
		if (!_pers.industryBuilt)
			return;
			
		debugmsg("optgoal5 - add pops based on delivered");
			
		local total = _pers.medsOn + _pers.medsOff;
		local growthFactor = 1 + (_pers.medsOn / (total * 2.0)); // max 1,5 increase
		
		if (gDebug)
		{
			growthFactor = 1.5;
		}
		
		// increases growth factor
		local cityGrowth = _helper.loadVar("cityGrowth");
		cityGrowth.growthFactor.push(growthFactor);
		
		_pers.medsOn = 0;
		_pers.medsOff = 0;
		
		_helper.saveVar("optGoal" + _id, _pers);
	}
	
	function debugtext()
	{
		return "";
	}
	
	function goaltext()
	{
		if (!_pers.industryBuilt)
			return "";
	
		local text = ttext("{fac}:<br>You can increase the city's <i>growth factor</i> by supplying at least one of these wares to the factory:<br>");
		text.fac = getLink(_industryName, _pers.industryCoord);
		local wares = ["Medikamente"];
		return addWaresList(text.tostring(), wares);
	}
	
	function resulttext()
	{
		return "";
	}
}

class BuildAutohaus
{
	_id = 6;
	_helper = null;
	_completeCB = null;
	_pers = null;
	_buildTimerId = null;
	_addPopsTimerId = null;
	_cityCoord = null;
	_industryBuilder = null;
	_industryName = null;

	constructor(helper, completeCb)
	{
		_helper = helper;
		_completeCB = completeCb;
		_cityCoord = _helper.getCityCoords();
		
		
		_pers = _helper.loadVar("optGoal" + _id);
		if (_pers == null)
		{
			_pers = {};
			_pers.industryBuilt <- false;
			_pers.industryCoord <- null;
			_helper.saveVar("optGoal" + _id, _pers);
		}
		
		if (!_pers.industryBuilt)
		{
			_buildTimerId = gTickRatio.addCallback(buildIndustry.bindenv(this), 20); //TODO: ticks?
		}
		else
		{
			local fac = factory_x(_pers.industryCoord.x, _pers.industryCoord.y);
			_industryName = fac.get_name();
		
			_addPopsTimerId = gTickRatio.addMonthCallback(addPopsBasedOnDelivered.bindenv(this));
		}
	}
	
	function stop()
	{
		gTickRatio.removeCallback(_buildTimerId);
		_buildTimerId = 0;
		gTickRatio.removeCallback(_addPopsTimerId);
		_addPopsTimerId = 0;
	}
	
	function getId()
	{
		return _id;
	}
	
	function buildIndustry()
	{
		if (_industryBuilder == null)
		{
			_industryBuilder = IndustryBuilder();
		}
		
		local pos = _industryBuilder.buildInCityAsync(_cityCoord, "VWAutohaus_NIC", -1, 0);
		
		if (pos == null)
			return;
		
		if (typeof(pos) == "string")
		{
			debugmsg(pos);
			_industryBuilder = null;
			return;
		}
		
		_industryBuilder = null;
		
		_pers.industryBuilt = true;
		_pers.industryCoord = _helper.coordToTable(pos);
		_helper.saveVar("optGoal" + _id, _pers);
		if (_buildTimerId > 0)
		{
			gTickRatio.removeCallback(_buildTimerId);
			_buildTimerId = 0;
		}
		
		local fac = factory_x(pos.x, pos.y);
		_industryName = fac.get_name();
		
		_addPopsTimerId = gTickRatio.addMonthCallback(addPopsBasedOnDelivered.bindenv(this));
		
		messageBuiltIndustry(_industryName, _pers.industryCoord);
	}
	
	function addPopsBasedOnDelivered()
	{
		if (!_pers.industryBuilt)
			return;
			
		debugmsg("optgoal6 - add pops based on delivered");
			
		local fac = factory_x(_pers.industryCoord.x, _pers.industryCoord.y);
		
		// Autos		100%		- 48
		
		// Production 48 pro Monat
		// 48 möglich - unbuffed.
		
		local consumedTotal = fac.input["Autos"].get_consumed()[1]; // consumed of last month#
		
		local popIncrease = consumedTotal / 4;
		
		if (popIncrease > 30)
			popIncrease = 30;
		
		if (gDebug)
		{
			popIncrease = 100;
		}
		
		// increase max growth
		if (popIncrease > 0)
		{
			debugmsg(format("optgoal %d: growth: %d", _id, popIncrease));
			local cityGrowth = _helper.loadVar("cityGrowth");
			if (cityGrowth == null)
				return;
				
			cityGrowth.maxGrowth.push(popIncrease);
		}
	}
	
	function debugtext()
	{
		return "";
	}
	
	function goaltext()
	{
		if (!_pers.industryBuilt)
			return "";
	
		local text = ttext("{fac}:<br>You can increase the city's <i>maximum growth</i> by supplying at least one of these wares to the factory:<br>");
		text.fac = getLink(_industryName, _pers.industryCoord);
		local wares = ["Autos"]
		return addWaresList(text.tostring(), wares);
	}
	
	function resulttext()
	{
		return "";
	}
}

class BuildMarkt
{
	_id = 7;
	_helper = null;
	_completeCB = null;
	_pers = null;
	_buildTimerId = null;
	_addPopsTimerId = null;
	_cityCoord = null;
	_industryBuilder = null;
	_industryName = null;

	constructor(helper, completeCb)
	{
		_helper = helper;
		_completeCB = completeCb;
		_cityCoord = _helper.getCityCoords();
		
		
		_pers = _helper.loadVar("optGoal" + _id);
		if (_pers == null)
		{
			_pers = {};
			_pers.industryBuilt <- false;
			_pers.industryCoord <- null;
			_helper.saveVar("optGoal" + _id, _pers);
		}
		
		if (!_pers.industryBuilt)
		{
			_buildTimerId = gTickRatio.addCallback(buildIndustry.bindenv(this), 20); //TODO: ticks?
		}
		else
		{
			local fac = factory_x(_pers.industryCoord.x, _pers.industryCoord.y);
			_industryName = fac.get_name();
		
			_addPopsTimerId = gTickRatio.addMonthCallback(addPopsBasedOnDelivered.bindenv(this));
		}
	}
	
	function stop()
	{
		gTickRatio.removeCallback(_buildTimerId);
		_buildTimerId = 0;
		gTickRatio.removeCallback(_addPopsTimerId);
		_addPopsTimerId = 0;
	}
	
	function getId()
	{
		return _id;
	}
	
	function buildIndustry()
	{
		if (_industryBuilder == null)
		{
			_industryBuilder = IndustryBuilder();
		}
		
		local pos = _industryBuilder.buildInCityAsync(_cityCoord, "AVL_Marktplatz_NIC", -1, 0);
		
		if (pos == null)
			return;
		
		if (typeof(pos) == "string")
		{
			debugmsg(pos);
			_industryBuilder = null;
			return;
		}
		
		_industryBuilder = null;
		
		_pers.industryBuilt = true;
		_pers.industryCoord = _helper.coordToTable(pos);
		_helper.saveVar("optGoal" + _id, _pers);
		if (_buildTimerId > 0)
		{
			gTickRatio.removeCallback(_buildTimerId);
			_buildTimerId = 0;
		}
		
		local fac = factory_x(pos.x, pos.y);
		_industryName = fac.get_name();
		
		_addPopsTimerId = gTickRatio.addMonthCallback(addPopsBasedOnDelivered.bindenv(this));
		
		messageBuiltIndustry(_industryName, _pers.industryCoord);
	}
	
	function addPopsBasedOnDelivered()
	{
		if (!_pers.industryBuilt)
			return;
			
		debugmsg("optgoal7 - add pops based on delivered");
			
		local fac = factory_x(_pers.industryCoord.x, _pers.industryCoord.y);
		
		// Bier				100%	210
		// Backwaren		100		210		
		// Eier				100		210
		// Südfrüchte		100		210
		// Milch			50%		105
		// Textilien		20%		50
		// Obst				100		210
		// Geflügel			100		210
		
		// Production 210 pro Monat
		// 840 möglich for growth - unbuffed
		// 575 möglich for max growth - unbuffed
		
		// 1415 möglich - unbuffed.
		
		// for growth 
		local consumedTotal = fac.input["Bier"].get_consumed()[1]; // consumed of last month#
		consumedTotal += fac.input["Backwaren"].get_consumed()[1];
		consumedTotal += fac.input["Eier"].get_consumed()[1];
		consumedTotal += fac.input["Obst"].get_consumed()[1];
		
		// for max growth
		local consumed2 = fac.input["Suedfruechte"].get_consumed()[1];
		consumed2 += fac.input["Milch"].get_consumed()[1];
		consumed2 += fac.input["Textilien"].get_consumed()[1];
		consumed2 += fac.input["Gefluegel"].get_consumed()[1];
		
		local popIncrease = consumedTotal / 60;
		if (popIncrease > 30)
			popIncrease = 30; // cap at 30;
			
		local maxGrowth = consumed2 / 30
		if (maxGrowth > 30)
			maxGrowth = 30;
		
		if (gDebug)
		{
			popIncrease = 100;
			maxGrowth = 100;
		}
		
		// increases growth and max growth
		if (popIncrease > 0)
		{
			debugmsg(format("optgoal %d: growth: %d", _id, popIncrease));
			local cityGrowth = _helper.loadVar("cityGrowth");
			if (cityGrowth == null)
				return;
				
			cityGrowth.growth.push(popIncrease);
		}

		if (maxGrowth > 0)
		{
			debugmsg(format("optgoal %d: maxGrowth: %d", _id, maxGrowth));
			local cityGrowth = _helper.loadVar("cityGrowth");
			if (cityGrowth == null)
				return;
				
			cityGrowth.maxGrowth.push(maxGrowth);
		}
	}
	
	function debugtext()
	{
		return "";
	}
	
	function goaltext()
	{
		if (!_pers.industryBuilt)
			return "";
	
		local text = ttext("{fac}:<br>You can increase the city's <i>growth</i> by supplying at least one of these wares to the factory:<br>");
		text.fac = getLink(_industryName, _pers.industryCoord);
		local wares = ["Bier", "Backwaren", "Eier", "Obst"];
		local text = addWaresList(text.tostring(), wares) + "<br>";
		
		local text2 = ttext("You can increase the city's <i>maximum growth</i> by supplying at least one of these wares to the factory:<br>");
		local wares2 = ["Suedfruechte", "Milch", "Textilien", "Gefluegel"];
		text2 = addWaresList(text2.tostring(), wares2);
		
		return text + text2;
	}
	
	function resulttext()
	{
		return "";
	}
}

class BuildWarenhaus
{
	_id = 8;
	_helper = null;
	_completeCB = null;
	_pers = null;
	_buildTimerId = null;
	_addPopsTimerId = null;
	_cityCoord = null;
	_industryBuilder = null;
	_industryName = null;

	constructor(helper, completeCb)
	{
		_helper = helper;
		_completeCB = completeCb;
		_cityCoord = _helper.getCityCoords();
		
		
		_pers = _helper.loadVar("optGoal" + _id);
		if (_pers == null)
		{
			_pers = {};
			_pers.industryBuilt <- false;
			_pers.industryCoord <- null;
			_helper.saveVar("optGoal" + _id, _pers);
		}
		
		if (!_pers.industryBuilt)
		{
			_buildTimerId = gTickRatio.addCallback(buildIndustry.bindenv(this), 20); //TODO: ticks?
		}
		else
		{
			local fac = factory_x(_pers.industryCoord.x, _pers.industryCoord.y);
			_industryName = fac.get_name();
		
			_addPopsTimerId = gTickRatio.addMonthCallback(addPopsBasedOnDelivered.bindenv(this));
		}
	}
	
	function stop()
	{
		gTickRatio.removeCallback(_buildTimerId);
		_buildTimerId = 0;
		gTickRatio.removeCallback(_addPopsTimerId);
		_addPopsTimerId = 0;
	}
	
	function getId()
	{
		return _id;
	}
	
	function buildIndustry()
	{
		if (_industryBuilder == null)
		{
			_industryBuilder = IndustryBuilder();
		}
		
		local pos = _industryBuilder.buildInCityAsync(_cityCoord, "Kaufhaus_NIC", -1, 0);
		
		if (pos == null)
			return;
		
		if (typeof(pos) == "string")
		{
			debugmsg(pos);
			_industryBuilder = null;
			return;
		}
		
		_industryBuilder = null;
		
		_pers.industryBuilt = true;
		_pers.industryCoord = _helper.coordToTable(pos);
		_helper.saveVar("optGoal" + _id, _pers);
		if (_buildTimerId > 0)
		{
			gTickRatio.removeCallback(_buildTimerId);
			_buildTimerId = 0;
		}
		
		local fac = factory_x(pos.x, pos.y);
		_industryName = fac.get_name();
		
		_addPopsTimerId = gTickRatio.addMonthCallback(addPopsBasedOnDelivered.bindenv(this));
		
		messageBuiltIndustry(_industryName, _pers.industryCoord);
	}
	
	function addPopsBasedOnDelivered()
	{
		if (!_pers.industryBuilt)
			return;
			
		debugmsg("optgoal8 - add pops based on delivered");
			
		local fac = factory_x(_pers.industryCoord.x, _pers.industryCoord.y);
		
		// Bier				100%	220
		// Backwaren		100		220		
		// Bücher			50		110
		// Südfrüchte		100		220
		// Fertiggerichte	100%	220
		// Textilien		100%	220
		// Kameras			20		44		- *4 = 176
		// Konserven		100		220
		// Molkereiprodukte	100		220
		// Möbel			40		88		- *2 ? 176
		// Wein				100		220
		
		
		// Production 220 pro Monat
		// 2000 möglich - unbuffed.
		// 2220 möglich mit factor
		
		local consumedTotal = fac.input["Bier"].get_consumed()[1]; // consumed of last month#
		consumedTotal += fac.input["Backwaren"].get_consumed()[1];
		consumedTotal += fac.input["Buecher"].get_consumed()[1];
		consumedTotal += fac.input["Suedfruechte"].get_consumed()[1];
		consumedTotal += fac.input["Fertiggerichte"].get_consumed()[1];
		consumedTotal += fac.input["Textilien"].get_consumed()[1];
		consumedTotal += fac.input["Kameras"].get_consumed()[1] * 4;
		consumedTotal += fac.input["Dosenessen"].get_consumed()[1];
		consumedTotal += fac.input["Molkereiprodukte"].get_consumed()[1];
		consumedTotal += fac.input["Moebel"].get_consumed()[1] * 2;
		consumedTotal += fac.input["Wein"].get_consumed()[1];
		
		local popIncrease = consumedTotal / 100;
		if (popIncrease > 40)
			popIncrease = 40;
		
		if (gDebug)
		{
			popIncrease = 100;
		}
		
		// increases growth
		if (popIncrease > 0)
		{
			debugmsg(format("optgoal %d: growth: %d", _id, popIncrease));
			local cityGrowth = _helper.loadVar("cityGrowth");
			if (cityGrowth == null)
				return;
				
			cityGrowth.growth.push(popIncrease);
		}
	}
	
	function debugtext()
	{
		return "";
	}
	
	function goaltext()
	{
		if (!_pers.industryBuilt)
			return "";
	
		local text = ttext("{fac}:<br>You can increase the city's <i>growth</i> by supplying at least one of these wares to the factory:<br>");
		text.fac = getLink(_industryName, _pers.industryCoord);
		local wares = ["Bier", "Backwaren", "Buecher", "Suedfruechte", "Fertiggerichte", "Textilien", "Kameras", "Dosenessen", "Molkereiprodukte", "Moebel", "Wein"];
		return addWaresList(text.tostring(), wares);
	}
	
	function resulttext()
	{
		return "";
	}
}

class BuildStadtverwaltung
{
	_id = 9;
	_helper = null;
	_completeCB = null;
	_pers = null;
	_buildTimerId = null;
	_addPopsTimerId = null;
	_cityCoord = null;
	_industryBuilder = null;
	_industryName = null;
	_checkStorageTimerId = null;
	_checkStorageAfterTicks = null;

	constructor(helper, completeCb)
	{
		_helper = helper;
		_completeCB = completeCb;
		_cityCoord = _helper.getCityCoords();
		_checkStorageAfterTicks = 5000;
		
		_pers = _helper.loadVar("optGoal" + _id);
		if (_pers == null)
		{
			_pers = {};
			_pers.supplyOn <- 0;
			_pers.supplyOff <- 0;
			_pers.industryBuilt <- false;
			_pers.industryCoord <- null;
			_helper.saveVar("optGoal" + _id, _pers);
		}
		
		if (!_pers.industryBuilt)
		{
			_buildTimerId = gTickRatio.addCallback(buildIndustry.bindenv(this), 20); 
		}
		else
		{
			local fac = factory_x(_pers.industryCoord.x, _pers.industryCoord.y);
			_industryName = fac.get_name();
		
			_addPopsTimerId = gTickRatio.addMonthCallback(addPopsBasedOnDelivered.bindenv(this), 20);
			_checkStorageTimerId = gTickRatio.addCallback(checkStorage.bindenv(this), _checkStorageAfterTicks);
		}
	}
	
	function stop()
	{
		gTickRatio.removeCallback(_buildTimerId);
		_buildTimerId = 0;
		gTickRatio.removeCallback(_addPopsTimerId);
		_addPopsTimerId = 0;
		gTickRatio.removeCallback(_checkStorageTimerId);
		_checkStorageTimerId = 0;
	}
	
	function getId()
	{
		return _id;
	}
	
	function buildIndustry()
	{
		if (_industryBuilder == null)
		{
			_industryBuilder = IndustryBuilder();
		}
		
		local pos = _industryBuilder.buildInCityAsync(_cityCoord, "Verwaltung_Muenchen_1965", -1, 0);
		
		if (pos == null)
			return;
		
		if (typeof(pos) == "string")
		{
			debugmsg(pos);
			_industryBuilder = null;
			return;
		}
		
		_industryBuilder = null;
		
		_pers.industryBuilt = true;
		_pers.industryCoord = _helper.coordToTable(pos);
		_helper.saveVar("optGoal" + _id, _pers);
		if (_buildTimerId > 0)
		{
			gTickRatio.removeCallback(_buildTimerId);
			_buildTimerId = 0;
		}
		
		local fac = factory_x(pos.x, pos.y);
		_industryName = fac.get_name();
		
		_addPopsTimerId = gTickRatio.addMonthCallback(addPopsBasedOnDelivered.bindenv(this), 20);
		_checkStorageTimerId = gTickRatio.addCallback(checkStorage.bindenv(this), _checkStorageAfterTicks);
		
		messageBuiltIndustry(_industryName, _pers.industryCoord);
	}
	
	function storageCheckWeight (fac, warename, weight)
	{
		local ware = fac.input[warename].get_storage()[0];
		if (ware != 0)
		{
			_pers.supplyOn += weight;
		}
		else
		{
			_pers.supplyOff += weight;
		}
	}
	
	function checkStorage()
	{
		local fac = factory_x(_pers.industryCoord.x, _pers.industryCoord.y);
		storageCheckWeight(fac, "Maschinen", 3);
		storageCheckWeight(fac, "Computer", 4);
		storageCheckWeight(fac, "Papier", 1);
		storageCheckWeight(fac, "Farben", 1);
		
		_helper.saveVar("optGoal" + _id, _pers);
	}
	
	function addPopsBasedOnDelivered()
	{
		if (!_pers.industryBuilt)
			return;
			
		debugmsg("optgoal9 - add pops based on delivered");
			
		// Computer			10%		18
		// Maschinen		10		18		
		// Papier			10		18
		// Farben			10		18
		
		// Production 180 pro Monat
		// 72 möglich - unbuffed.
		
		local total = _pers.supplyOn + _pers.supplyOff;
		local growthFactor = 1 + (_pers.supplyOn / (total * 1.0)); // max 2 increase
		
		if (gDebug)
		{
			growthFactor = 2.0;
		}
		
		// increases growth factor
		local cityGrowth = _helper.loadVar("cityGrowth");
		cityGrowth.growthFactor.push(growthFactor);
		
		_pers.supplyOn = 0;
		_pers.supplyOff = 0;
		
		_helper.saveVar("optGoal" + _id, _pers);
	}
	
	function debugtext()
	{
		return "";
	}
	
	function goaltext()
	{
		if (!_pers.industryBuilt)
			return "";
	
		local text = ttext("{fac}:<br>You can increase the city's <i>growth factor</i> by supplying at least one of these wares to the factory:<br>");
		text.fac = getLink(_industryName, _pers.industryCoord);
		local wares = ["Computer"];
		return addWaresList(text.tostring(), wares);
	}
	
	function resulttext()
	{
		return "";
	}
}

class ManageCityGrowth
{
	_id = 10;
	_helper = null;
	_completeCB = null;
	_pers = null;
	_addPopsTimerId = null;
	_cityCoord = null;

	constructor(helper, completeCb)
	{
		_helper = helper;
		_completeCB = completeCb;
		_cityCoord = _helper.getCityCoords();

		local cityGrowth = _helper.loadVar("cityGrowth");
		if (cityGrowth == null)
		{
			cityGrowth = createCityGrowth();
			_helper.saveVar("cityGrowth", cityGrowth);
		}
		
		_pers = _helper.loadVar("optGoal" + _id);
		if (_pers == null)
		{
			_pers = {};
			_pers.lastCityGrowth <- createCityGrowth();	// city growth of last month
			_pers.calcGrowth <- 0;
			_pers.calcMaxGrowth <- _pers.lastCityGrowth.baseMaxGrowth;
			_helper.saveVar("optGoal" + _id, _pers);
		}

		_addPopsTimerId = gTickRatio.addMonthCallback(addPopsBasedOnDelivered.bindenv(this), 100); // must be last to execute on the month callback (after all other growth goals)
	}
	
	function stop()
	{
		gTickRatio.removeCallback(_addPopsTimerId);
		_addPopsTimerId = 0;
	}
	
	function getId()
	{
		return _id;
	}
	
	function addPopsBasedOnDelivered()
	{
		local cityGrowth = _helper.loadVar("cityGrowth");
		if (cityGrowth == null)
		{
			debugmsg("cityGrowth is null");
			return;
		}
		
		_helper.saveVar("cityGrowth", createCityGrowth()); // create new empty for next month
		
		//if (cityGrowth.growth.len() == 0 && !gDebug) 
		//{
		//	_pers.lastCityGrowth = cityGrowth;
		//	_helper.saveVar("optGoal" + _id, _pers);
		//	return; // we have no growth
		//}
		
		local growth = getGrowth(cityGrowth)
		debugmsg("growth: " + growth);
		local growthFactor = getGrowthFactor(cityGrowth);
		debugmsg("growthfactor: " + growthFactor);
			
		growth = growth * growthFactor;
		growth = floor(growth);
		
		local maxGrowthFactor = getMaxGrowthFactor(cityGrowth);
		debugmsg("maxGrowthFactor: " + maxGrowthFactor);
		local maxGrowth = cityGrowth.baseMaxGrowth + getMaxGrowth(cityGrowth);
		debugmsg("maxGrowth: " + maxGrowth);
		
		maxGrowth = maxGrowth * maxGrowthFactor;
		maxGrowth = floor(maxGrowth);
		
		if (growth > maxGrowth)
			growth = maxGrowth;
			
		_pers.lastCityGrowth = cityGrowth;
		_pers.calcGrowth = growth;
		_pers.calcMaxGrowth = maxGrowth;
		_helper.saveVar("optGoal" + _id, _pers);
		
		debugmsg("growing city by: " + growth);
		
		if (gDebug)
		{
			if (growth < 100)
				growth = 100;
		}
		
		if (growth > 0)
		{
			local city = city_x(_cityCoord.x, _cityCoord.y)
			city.change_size(growth.tointeger());
		}
	}
	
	function createCityGrowth()
	{	
		local cityGrowth = {};
		cityGrowth.baseMaxGrowth <- 25; // 25 citizens maximum in one month
		cityGrowth.growth <- [];	// increases growth in one month
		cityGrowth.maxGrowth <- [];	// increases maximum growth in one month
		cityGrowth.growthFactor <- [];	// growth factor -> growth is modified by this.
		//cityGrowth.growthFactorMax <- []; // maximum for growth factor
		cityGrowth.maxGrowthFactor <- []; // max growth factor -> max growth is modified by this.
		//cityGrowth.maxGrowthFactorMax <- []; //maximum for max growth factor
		return cityGrowth;
	}
	
	function getGrowth(cityGrowth)
	{
		local growth = 0;
		foreach (g in cityGrowth.growth)
		{
			growth += g;
		}
		return growth;
	}
	
	function getMaxGrowth(cityGrowth)
	{
		local maxGrowth = 0;
		foreach (maxG in cityGrowth.maxGrowth)
		{
			maxGrowth += maxG;
		}
		return maxGrowth;
	}
	
	function getGrowthFactor(cityGrowth)
	{
		local growthFactor = 1.0;
		foreach (factor in cityGrowth.growthFactor)
		{
			growthFactor = growthFactor * factor;
		}
		return growthFactor;
	}
	
	function getMaxGrowthFactor(cityGrowth)
	{
		local maxGrowthFactor = 1.0;
		foreach (factor in cityGrowth.maxGrowthFactor)
		{
			maxGrowthFactor = maxGrowthFactor * factor;
		}
		return maxGrowthFactor;
	}
	
	function debugtext()
	{
		local gr = "growth: ";
		local temp = "";
		foreach (x in _pers.lastCityGrowth.growth)
		{
			temp = temp + x + ",";
		}
		
		local maxgr = "max growth: ";
		foreach (x in _pers.lastCityGrowth.maxGrowth)
		{
			temp = temp + x + ",";
		}
		
		local grfac = "growth factor: ";
		foreach (x in _pers.lastCityGrowth.growthFactor)
		{
			temp = temp + x + ",";
		}
		
		local maxgrfac = "max growth factor: ";
		foreach (x in _pers.lastCityGrowth.maxGrowthFactor)
		{
			temp = temp + x + ",";
		}
	
		return gr + "<br>" + maxgr + "<br>" + grfac + "<br>" + maxgrfac + "<br>";
	}
	
	function goaltext()
	{
		return "";
	}
	
	function resulttext()
	{
		local cityGrowth = _pers.lastCityGrowth;
		local t = ttext("Due to supplied wares to factories in the city");
		t.growth = getGrowth(cityGrowth);
		t.baseMaxGrowth = cityGrowth.baseMaxGrowth;
		t.maxGrowth = getMaxGrowth(cityGrowth);
		t.growthFactor = getGrowthFactor(cityGrowth);
		t.maxGrowthFactor = getMaxGrowthFactor(cityGrowth);
		t.calcMaxGrowth = _pers.calcMaxGrowth
		t.calcGrowth = _pers.calcGrowth;
		return t.tostring();
	}
}
