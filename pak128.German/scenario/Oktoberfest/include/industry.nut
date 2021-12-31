
class FreeField
{
	x = null;
	y = null;
	last = null;
	
	constructor(xx, yy)
	{
		x = xx;
		y = yy;
		last = false;
	}
}

function partition(arr, low, high, compareFunc)
{
	local temp;
	local pivot = arr[high];

	// index of smaller element
	local i = (low - 1);
	for (local j = low; j <= high - 1; j++) {
		// If current element is smaller
		// than or equal to pivot
		local res = compareFunc(arr[j], pivot);
		if (res <= 0) 
		{
			i++;

			// swap arr[i] and arr[j]
			temp = arr[i];
			arr[i] = arr[j];
			arr[j] = temp;
		}
	}

	// swap arr[i+1] and arr[high]
	// (or pivot)

	temp = arr[i + 1];
	arr[i + 1] = arr[high];
	arr[high] = temp;

	return i + 1;
}
  
/* A[] --> Array to be sorted,
l --> Starting index,
h --> Ending index */
function quickSortIterative(arr, compareFunc)
{
	if (arr.len() <= 1)
		return;
		
	// Create an auxiliary stack
	local l = 0;
	local h = arr.len() - 1;
	
	local stack = array(h - l + 1, 0);

	// initialize top of stack
	local top = -1;

	// push initial values of l and h to
	// stack
	stack[++top] = l;
	stack[++top] = h;

	// Keep popping from stack while
	// is not empty
	while (top >= 0) {
		// Pop h and l
		h = stack[top--];
		l = stack[top--];

		// Set pivot element at its
		// correct position in
		// sorted array
		local p = partition(arr, l, h, compareFunc);

		// If there are elements on
		// left side of pivot, then
		// push left side to stack
		if (p - 1 > l) {
			stack[++top] = l;
			stack[++top] = p - 1;
		}

		// If there are elements on
		// right side of pivot, then
		// push right side to stack
		if (p + 1 < h) {
			stack[++top] = p + 1;
			stack[++top] = h;
		}
	}
}

class IndustryBuilder
{
	_freeCoords = null;
	_industryGen = null;
	_fieldBlacklist = null;	// we already tried to build here, but it failed; for next try blacklist this field so a different one is found.

	constructor()
	{
		_freeCoords = [];
		_fieldBlacklist = [];
	}
	
	function buildInCityAsync(cityCoord, nameOfIndustry,productionVal, cmdType)
	{
		if (_industryGen == null)
			_industryGen = buildInCity(cityCoord, nameOfIndustry, productionVal, cmdType);
			
		local pos = resume _industryGen;
		if (pos != null)	// != null means we are finished.
			_industryGen = null;
		return pos;
	}
	
	// Main function
	function buildInCity(cityCoord, nameOfIndustry, productionVal, cmdType)
	{
		// cmdType: 0 = city_chain
		//			1 = land_chain
	
		local city = city_x(cityCoord.x, cityCoord.y);
		if (!city.is_valid())
		{
			debugmsg("buildInCity - cityCoord not valid: x: " + city.x + " y: " + city.y)
			return "invalid city";
		}
		
		local size = getIndustrySize(nameOfIndustry);
		debugmsg("industrysize: x: " + size.x + " y: " + size.y);
		local actualPos = null;
		do
		{
		
			local findPlaceGen = findPlaceForIndustry(city, size.x, size.y);
			
			local place = null;
			do
			{
				place = resume findPlaceGen; //findPlaceForIndustry(city, size.x, size.y);
				if (place == null)
					yield null;
			}
			while (place == null)
			
			if (typeof(place) == "string")
				return place;	// error
			
			yield null;
			
			local pos = convertToCoord3d(place);
			
			local factoryListBefore = factory_list_x();
			local facListBefore = [];
			foreach (fac in factoryListBefore)	// factoryListBefore changes when something is built; we have to save into separate array to know the "before" state.
			{
				facListBefore.push(fac);
			}
			
			yield null;
			
			buildCommand(1, pos, nameOfIndustry, productionVal, cmdType);
			
			local factoryListAfter = factory_list_x();

			local compatMode = get_raw_name_compatibility(factoryListAfter);
			
			if (compatMode)
			{
				// old simutrans version without "get_raw_name" function
				local nameOfIndustry_translated = translate(nameOfIndustry);
				foreach(fac in factoryListAfter)
				{
					if (containsCoord(facListBefore, fac))
						continue; // factory existed before we built ours
						
					local translatedname = fac.get_name(); // 
					if (translatedname == nameOfIndustry_translated)	// we assume only one industry of same type got built
					{
						actualPos = coord(fac.x, fac.y);
						break;
					}
				}
			}
			else //normal case - we have "get_raw_name" function
			{
				// the build command "ignores" the coord we give it;
				// we have to iterate the factories and see where it was actually built.
				foreach(fac in factoryListAfter)
				{
					if (containsCoord(facListBefore, fac))
						continue; // factory existed before we built ours
						
					local rawname = fac.get_raw_name(); // needs nightly
					if (rawname == nameOfIndustry)	// we assume only one industry of same type got built
					{
						actualPos = coord(fac.x, fac.y);
						break;
					}
				}
			}
			
			if (actualPos == null)
			{
				debugmsg("buildInCity - actualPos was not found!");
				_fieldBlacklist.push(place);
				yield null;
			}
		}
		while(actualPos == null)

		debugmsg("actualPos: " + actualPos.tostring());
		
		return actualPos;
		
	}
	
	// returns 2d point where to build.
	function findPlaceForIndustry(city, sizex, sizey)
	{
		// scan all tiles inside the city area.
		if (!city.is_valid())
		{
			debugmsg("findPlaceForIndustry - cityCoord not valid: x: " + city.x + " y: " + city.y)
			return "invalid city";
		}
		
		local posNw = city.get_pos_nw();
		local posSe = city.get_pos_se();
		
		debugmsg("cityborder: NW: " + posNw + " SE: " + posSe); 
		
		if (posNw.x > posSe.x)
		{
			local temp = posNw.x;
			posNw.x = posSe.x;
			posSe.x = temp;
		}
		
		if (posNw.y > posSe.y)
		{
			local temp = posNw.y;
			posNw.y = posSe.y;
			posSe.y = temp;
		}
		
		do
		{
			for (local x = posNw.x; x <= posSe.x; ++x)
			{
				for (local y = posNw.y; y <= posSe.y; ++y)
				{
					local s = square_x(x,y);
					if (s.is_valid())
					{
						local t = s.get_ground_tile();
						if (t.is_empty() && t.get_slope() == slope.flat)
						{
							_freeCoords.push(FreeField(x,y));
						}
					}
				}
				yield null;
			}		
			
			yield null;
			
			_freeCoords[_freeCoords.len() - 1].last = true; // prevent special case with last item
			
			// remove fields that previously failed.
			foreach (field in _fieldBlacklist)
			{
				for (local i = _freeCoords.len() - 1; i >= 0; --i)
				{
					if (_freeCoords[i].x == field.x && _freeCoords[i].y == field.y)
						_freeCoords.remove(i);
				}
			}

			yield null;
			
			local removeGen = removeNonConsecutiveFields(sizex, sizey);
			
			local removeDone = null;
			while(removeDone == null)
			{
				removeDone = resume removeGen;
				yield null;
			}
			
			// remove the last one, doesn't work with algo
			local idxLast = -1;
			for (local i = 0; i < _freeCoords.len(); ++i)
			{
				if (_freeCoords[i].last)
				{
					idxLast = i;
					break;
				}
			}
			
			if (idxLast != -1)
				_freeCoords.remove(idxLast);
			
			if (_freeCoords.len() == 0)
			{
				debugmsg("findPlaceForIndustry - increasing city border size to find place.");
				// increase the city border and try again
				if (posNw.x > 0)
					--posNw.x;
					
				if (posNw.y > 0)
					--posNw.y;
					
				local worldSize = world.get_size();
				if (posSe.x < worldSize.x)
					++posSe.x;
				
				if (posSe.y < worldSize.y)
					++posSe.y;
			}
		}
		while(_freeCoords.len() == 0)
		
		// i think index in the middle is closest to townhallCoord
		
		quickSortIterative(_freeCoords, function (left, right)
		{
			local res = left.x <=> right.x;
			if (res == 0)
				return left.y <=> right.y;
			return res;
		});
		
		//_freeCoords.sort(function (left, right)
		//{
		//	local res = left.x <=> right.x;
		//	if (res == 0)
		//		return left.y <=> right.y;
		//	return res;
		//});
		
		yield null;
		
		local idx = _freeCoords.len() / 2;
		local selectedField = _freeCoords[idx]
		// try to find the other fields
		// find lowest x, but substract max sizex
		
		for (local i = 0; i < sizex; ++i)
		{
			local nextField = FreeField(selectedField.x - (i + 1), selectedField.y);
			if (hasField(nextField))
			{
				selectedField = nextField;
			}
			else
			{
				break; // we found the lowest x.
			}
		}
		
		for (local i = 0; i < sizey; ++i)
		{
			local nextField = FreeField(selectedField.x, selectedField.y - (i + 1));
			if (hasField(nextField))
			{
				selectedField = nextField;
			}
			else
			{
				break; // we found the lowest y.
			}
		}
	
		local msg = "freeCoords: ";
		foreach (f in _freeCoords)
		{
			msg = msg + f.x + ":" + f.y + ",";
		}
		debugmsg(msg)


		// theoretically we have our build pos now
		debugmsg("findPlaceForIndustry - buildpos found. x: " + selectedField.x + " y: " + selectedField.y)
		return selectedField;

	}
	
	function buildCommand(playerNr, pos, nameOfIndustry, productionVal, cmdType)
	{
		/* builds a (if param=NULL random) industry chain starting here *
		 * the parameter string is a follow:
		 * 1#34,oelfeld
		 * first letter: ignore climates
		 * second letter: rotation (0,1,2,3,#=random)
		 * next number is production value (-1 to use default of building)
		 * finally industry name
		 */
	
		local player = player_x(playerNr);
		// ignore climate and rotation always hardcoded
		local param = "1#" + productionVal + "," + nameOfIndustry;
		local buildInd = null;
		if (cmdType == 0)
		{
			buildInd = command_x(tool_city_chain);
		}
		else if (cmdType == 1)
		{
			buildInd = command_x(tool_land_chain);
		}
		
		debugmsg("buildCommand - param: " + param);
		local retVal = buildInd.work(player, pos, param);
		if (retVal == null)
		{
			// it was built
			debugmsg("buildCommand - after. x: " + pos.x + " y: " + pos.y);
		}
		else 
		{
			debugmsg("buildCommand - retval: " + retVal);
		}
	}
	
	function containsCoord(ar, c)
	{
		foreach (entry in ar)
		{
			if (entry.x == c.x && entry.y == c.y)
				return true;
		}
		return false;
	}
	
	function hasField(f)
	{
		foreach (field in _freeCoords)
		{
			if (field.x == f.x &&
				field.y == f.y)
				return true;
		}
		return false;
	}
	
	function convertToCoord3d(c)
	{
		local sq = square_x(c.x, c.y);
		local tile = sq.get_ground_tile();
		return tile;
	}
	
	function removeNonConsecutiveFields(sizex, sizey)
	{
		local removed = true;
		while(removed)
		{
			removed = false;
			if (sizey > 1)
			{
				debugmsg("removeNonConsecutiveFields: sort - count:" + _freeCoords.len());
				
				//local bubbleGen = bubbleSort(_freeCoords, function (left, right)
				//{
				//	local res = left.x <=> right.x;
				//	if (res == 0)
				//		return left.y <=> right.y;
				//	return res;
				//});
				//
				//local bubbleRes = null;
				//while (bubbleRes == null)
				//{
				//	bubbleRes = resume bubbleGen;
				//	yield null;
				//}
				
				//_freeCoords.sort(function (left, right)
				//{
				//	local res = left.x <=> right.x;
				//	if (res == 0)
				//		return left.y <=> right.y;
				//	return res;
				//})
				
				quickSortIterative(_freeCoords, function (left, right)
				{
					local res = left.x <=> right.x;
					if (res == 0)
						return left.y <=> right.y;
					return res;
				});
				
				yield null;
				
				debugmsg("removeNonConsecutiveFields - start Y");
				
				local removeIndex = [];
				local last = -1;
				local count = 0;
				local consecutiveCount = 0;
				local consecutiveLast = -1;
				for (local i = 0; i < _freeCoords.len(); ++i)
				{
					if (last == -1)
					{
						last = _freeCoords[i].x;
						consecutiveLast = _freeCoords[i].y;
						consecutiveCount = 1
						count = 1
						continue;
					}
					
					if (last == _freeCoords[i].x)
					{
						++count;
						if (consecutiveLast + 1 != _freeCoords[i].y)
						{
							//no longer consecutive - check if size is enough
							if (consecutiveCount < sizey)
							{
								// remove all indexes that where consecutive before
								for (local k = 0; k < consecutiveCount; ++k)
								{
									local idx = (i - 1) - k;
									if (idx < 0)
										continue;
										
									if (removeIndex.find(idx) == null)
										removeIndex.push(idx);
								}
							}
							
							consecutiveCount = 1;
							consecutiveLast = _freeCoords[i].y;
						}
						else // still consecutive
						{
							consecutiveLast = _freeCoords[i].y;
							++consecutiveCount;
						}
						
						continue;
					}
					
					// not enough consecutive - remove previous indexes
					if (count < sizey)
					{
						removed = true;
						for(local j = 1; j < sizey; ++j)
						{
							local idx = i - j;
							if (idx < 0)
								continue;
								
							if (removeIndex.find(idx) == null)
								removeIndex.push(idx);
						}
					}
					
					// set current
					last = _freeCoords[i].x;
					consecutiveLast = _freeCoords[i].y;
					consecutiveCount = 1;
					count = 1;
					
					if (i % 100 == 0 && i != 0)
					{
						yield null;
					}
				}
				
				removeIndex.sort();
				removeIndex.reverse(); // make sure we go from back to front
				
				local msg = "indexes: ";
				foreach (idx in removeIndex)
				{
					msg = msg + idx + ",";
				}
				
				debugmsg(msg);
				
				foreach (idx in removeIndex)
				{
					if (idx < 0)
						continue; // dont want to find why -1 is in the array... just skip if it is...
						
					_freeCoords.remove(idx);
				}
			}
			
			yield null;
			
			if (sizex > 1)
			{
				debugmsg("removeNonConsecutiveFields: sort - count:" + _freeCoords.len());
				//local bubbleGen = bubbleSort(_freeCoords, function (left, right)
				//{
				//	local res = left.y <=> right.y;
				//	if (res == 0)
				//		return left.x <=> right.x;
				//	return res;
				//});
				//
				//local bubbleRes = null;
				//while (bubbleRes == null)
				//{
				//	bubbleRes = resume bubbleGen;
				//	yield null;
				//}
				
				//_freeCoords.sort(function (left, right)
				//{
				//	local res = left.y <=> right.y;
				//	if (res == 0)
				//		return left.x <=> right.x;
				//	return res;
				//})
				
				quickSortIterative(_freeCoords, function (left, right)
				{
					local res = left.y <=> right.y;
					if (res == 0)
						return left.x <=> right.x;
					return res;
				});
				
				yield null;
				
				debugmsg("removeNonConsecutiveFields - start X");
				local removeIndex = [];
				local last = -1;
				local count = 0;
				local consecutiveCount = 0;
				local consecutiveLast = -1;
				for (local i = 0; i < _freeCoords.len(); ++i)
				{
					if (last == -1)
					{
						last = _freeCoords[i].y;
						count = 1
						consecutiveLast = _freeCoords[i].x;
						consecutiveCount = 1
						continue;
					}
					
					if (last == _freeCoords[i].y)
					{
						++count;
						if (consecutiveLast + 1 != _freeCoords[i].x)
						{
							//no longer consecutive - check if size is enough
							if (consecutiveCount < sizex)
							{
								// remove all indexes that where consecutive before
								for (local k = 0; k < consecutiveCount; ++k)
								{
									local idx = (i - 1) - k;
									if (idx < 0)
										continue;
										
									if (removeIndex.find(idx) == null)
										removeIndex.push(idx);
								}
							}
							
							consecutiveCount = 1;
							consecutiveLast = _freeCoords[i].x;
						}
						else  // still consecutive
						{
							consecutiveLast = _freeCoords[i].x;
							++consecutiveCount;
						}
						continue;
					}
					
					// not enough consecutive - remove previous indexes
					if (count < sizex)
					{
						removed = true;
						for(local j = 1; j < sizex; ++j)
						{
							local idx = i - j;
							if (idx < 0)
								continue;
								
							if (removeIndex.find(idx) == null)
								removeIndex.push(idx);
						}
					}
					
					// set current
					last = _freeCoords[i].y;
					count = 1;
					consecutiveLast = _freeCoords[i].x;
					consecutiveCount = 1;
					
					if (i % 100 == 0 && i != 0)
					{
						yield null;
					}
				}
				
				removeIndex.sort();
				removeIndex.reverse(); // make sure we go from back to front
				
				local msg = "indexes: ";
				foreach (idx in removeIndex)
				{
					msg = msg + idx + ",";
				}
				
				debugmsg(msg);
				
				foreach (idx in removeIndex)
				{
					if (idx < 0)
						continue; // dont want to find why -1 is in the array... just skip if it is...
					_freeCoords.remove(idx);
				}
			}
		}
		
		return true;
	}
	
	function getIndustrySize(nameOfIndustry)
	{
		try
		{
			local facDesc = factory_desc_x(nameOfIndustry);
			return facDesc.get_building_desc().get_size(0); // always 0 rotation
		}
		catch(ex)
		{
			// old simutrans versions where factory_desc_x does not exist.
			return getIndustrySize_hardcoded(nameOfIndustry);
		}
	}
	
	function getIndustrySize_hardcoded(nameOfIndustry)
	{
		local s = {};
		switch (nameOfIndustry)
		{
			case "Baustoffhof_1800": 
				s.x <- 2;
				s.y <- 2;
				return s;

			case "Oktoberfest":
				s.x <- 5;
				s.y <- 5;
				return s;

			case "Gasthof_1800":
				s.x <- 2;
				s.y <- 2;
				return s;

			case "Gastwirtschaft_mit_Laden":
				s.x <- 2;
				s.y <- 1;
				return s;

			case "Geraete_und_Haushaltsartikel":
				s.x <- 1;
				s.y <- 2;
				return s;

			case "Apotheke1800_NIC":
				s.x <- 1;
				s.y <- 1;
				return s;

			case "VWAutohaus_NIC":
				s.x <- 2;
				s.y <- 2;
				return s;

			case "AVL_Marktplatz_NIC":
				s.x <- 2;
				s.y <- 2;
				return s;

			case "Kaufhaus_NIC":
				s.x <- 2;
				s.y <- 2;
				return s;

			case "Verwaltung_Muenchen_1965":
				s.x <- 2;
				s.y <- 2;
				return s;
		}
	}
	
	// return true, when old simutrans version without get_raw_name
	// return false otherwise
	function get_raw_name_compatibility(facList)
	{
		if (facList.get_count() == 0)
		{
			// special case: no industries on map to check with
			// just assume compat mode
			return true;
		}
		
		local fac = facList[0];
		try
		{
			local rawname = fac.get_raw_name();
		}
		catch(ex)
		{
			return true;
		}
		return false;
	}
}