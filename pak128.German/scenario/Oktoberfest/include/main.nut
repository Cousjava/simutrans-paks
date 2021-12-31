



class Phases
{
	_phase = null;
	_helper = null;
	_currentPhase = null;
	_optionalGoals = null;
	_optionalGoalIds = null;
	
	constructor()
	{
		_helper = PersistentHelper();
		_phase = _helper.loadVar("currentPhase");
		if (_phase == null)
			_phase = 1;
		_currentPhase = getPhaseClass();
		_helper.saveVar("currentPhase", _phase);
		
		_optionalGoals = [];
		_optionalGoalIds = _helper.loadVar("optGoals");
		if (_optionalGoalIds == null)
			_optionalGoalIds = [];
		
		foreach (goalId in _optionalGoalIds)
		{
			_optionalGoals.push(createOptionalGoal(goalId));
		}
	}
	
	// phase completed
	function complete()
	{
		++_phase;
		_currentPhase = getPhaseClass();
		_helper.saveVar("currentPhase", _phase);
	}
	
	function getPhaseClass()
	{
		switch (_phase)
		{
			case 1: return Phase1B(_helper, complete.bindenv(this));
			case 2: return Phase2(_helper, complete.bindenv(this));
			case 3: return Phase3(_helper, complete.bindenv(this));
			case 4: return Phase4(_helper, complete.bindenv(this));
			case 5: return Phase5(_helper, complete.bindenv(this));
			case 6: return PhaseComplete(_helper);
		}
	}
	
	function addOptionalGoal(goalId)
	{
		_optionalGoalIds.push(goalId);
		_optionalGoals.push(createOptionalGoal(goalId));
		_helper.saveVar("optGoals", _optionalGoalIds);
	}
	
	function stopOptionalGoal(goalId)
	{
		local idx = _optionalGoalIds.find(goalId);
		if (idx == null)
			return; //not found
		
		_optionalGoalIds.remove(idx);
		_helper.saveVar("optGoals", _optionalGoalIds);
		
		local found = false;
		for (idx = 0; idx < _optionalGoals.len(); ++idx)
		{
			if (_optionalGoals[idx].getId() == goalId)
			{
				found = true;
				_optionalGoals[idx].stop();
				break;
			}
		}
		if (found)
			_optionalGoals.remove(idx);
	}
	
	function optionalGoalComplete(goalClass)
	{
		local idx = _optionalGoals.find(goalClass);
		_optionalGoals.remove(idx);
		idx = _optionalGoalIds.find(goalClass.getId());
		_optionalGoalIds.remove(idx);
		_helper.saveVar("optGoals", _optionalGoalIds);
	}
	
	function createOptionalGoal(goalId)
	{
		switch (goalId)
		{
			case 1: return BuildIndustry1(_helper, optionalGoalComplete.bindenv(this));	// build Baustofflager + add pops based on delivered goods
			case 2: return BuildGasthof1800(_helper, optionalGoalComplete.bindenv(this));
			case 3: return BuildGastwirtschaft(_helper, optionalGoalComplete.bindenv(this));
			case 4: return BuildHaushaltsartikel(_helper, optionalGoalComplete.bindenv(this));
			case 5: return BuildApotheke(_helper, optionalGoalComplete.bindenv(this));
			case 6: return BuildAutohaus(_helper, optionalGoalComplete.bindenv(this));
			case 7: return BuildMarkt(_helper, optionalGoalComplete.bindenv(this));
			case 8: return BuildWarenhaus(_helper, optionalGoalComplete.bindenv(this));
			case 9: return BuildStadtverwaltung(_helper, optionalGoalComplete.bindenv(this));
			case 10: return ManageCityGrowth(_helper, optionalGoalComplete.bindenv(this));
			default: return null;
		}
	}
	
	function ruletext()
	{
		if (_phase == 1)
		{
			local p1 = _helper.loadVar("phase1");
			if (p1.tooManyPops == 0)
			{
				local text = ttext("A city can only be selected (by building a headquarter near it), if it has less than {pops} citizens.");
				text.pops = p1.maxAllowedPops;
				return text;
			}
			else
			{
				local text = ttext("The selected city {cityname} has too many citizens. ({pops})<br>Please build your headquarter near a city with less than {maxPops} citizens.");
				text.cityname = getLink(p1.tooManyPopsCityName, p1.tooManyPopsCityCoord);
				text.pops = p1.tooManyPops;
				text.maxPops = p1.maxAllowedPops;
				return text;
			}
		}
		else if (_phase == 2)
		{
			local text = ttext("The city growth can be increased by supplying the factories of the city.");
			return text;
		}
		else
		{
			local text = ttext("No special rules.");
			return text;
		}
	}
	
	function infotext()
	{
		local text = ttext("Build your company headquarter in the vincinity of a city, to 'select' it.<br><br>The goal is to grow the 'selected' city to the biggest city in the region and to celebrate a big party.");
		return text;
	}
	
	function goaltext()
	{
		local maingoal = _currentPhase.goaltext();
		
		local optional = "";
		foreach (optGoal in _optionalGoals)
		{
			local otext = optGoal.goaltext();
			if (otext == "" || otext == null)
				continue;
			optional = optional + otext + "<br>";
		}
		
		if (_optionalGoals.len() == 0)
			return maingoal;
		
		local optDesc = ttext("Optional Goals:");
		
		return maingoal + "<br><br>"+ optDesc.tostring() +"<br>" + optional;
	}
	
	function resulttext()
	{
		local maingoal = _currentPhase.resulttext();
		
		local optional = "";
		foreach (optGoal in _optionalGoals)
		{
			local otext = optGoal.resulttext();
			if (otext == "" || otext == null)
				continue;
			optional = optional + otext + "<br>";
		}
		
		if (optional == "")
			return maingoal;
		
		return maingoal + "<br><br>" + optional;
	}
	
	function completePercent()
	{
		if (_phase <= 2)
			return 0;
			
		if (_phase == 3)
			return 20;
			
		if (_phase == 4)
			return 50;
			
		if (_phase == 5)
			return  80;
			
		if (_phase == 6)
			return 100;
			
		return 0;
	}
	
	function debugtext()
	{
		if (!gDebugMessages)
			return "";
	
		local msgOptGoals = "";
		foreach (id in _optionalGoalIds)
		{
			msgOptGoals = msgOptGoals + id + ",";
		}
		
		local ptext = _currentPhase.debugtext();
	
		local msg = "";
		msg = format("current phase: %d<br>optional goals active: %s<br>phase debug: <br>%s<br>", _phase, msgOptGoals, ptext);
		
	
		foreach (optGoal in _optionalGoals)
		{
			msg = msg + "optgoal " + optGoal.getId() + " debug: <br>";
			local otext = optGoal.debugtext();
			msg = msg + otext + "<br>";
		}
	
		return msg;
	}
}