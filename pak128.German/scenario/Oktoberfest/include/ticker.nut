class TickCallback
{
	callback = null;
	afterTicks = null;
	current = null
	id = null
	order = null;
	
	constructor(id2, cb, ticks, order2)
	{
		callback = cb;
		afterTicks = ticks;
		current = 0;
		id = id2;
		order = order2;
	}
	
	function tick()
	{
		++current;
		if (current >= afterTicks)
		{
			current = 0;
			return true;
		}
		
		return false;
	}
	
	function fire()
	{
		callback();
	}
}

class IsWorkAllowedCallback
{
	id = null;
	callback = null;
	
	constructor(id2, cb)
	{
		id = id2;
		callback = cb;
	}
	
	function fire(pl, tool_id, pos)
	{
		_callback(pl, tool_id, pos);
	}
}

class TickRatio
{
	tickCallbacks = null;
	monthCallbacks = null;
	isWorkAllowedCallbacks = null;
	runningId = null;
	expired = null;		// timers that should execute; only one callback is executed per tick, if more callbacks are registered.
	
	// runningId: first 4 bits, used for callback type (would prefer to not use most significant bit, so we are not negative)
	// >0000< 0000 00000000 00000000 00000000
	static Tick = 1;
	static Month = 2;
	static IWA = 3;
	
	constructor()
	{
		tickCallbacks = [];
		monthCallbacks = [];
		isWorkAllowedCallbacks = [];
		runningId = 1;
		expired = [];
	}
	
	function getType(id)
	{
		return id >> 28;
	}
	
	function getId(callbackType)
	{
		local id = callbackType << 28;
		id = id + runningId++;
		return id;
	}
	
	function addCallback(cb, ticks)
	{
		local c = TickCallback(getId(Tick), cb, ticks, 50);
		tickCallbacks.push(c);
		return c.id;
	}
	
	function addMonthCallback(cb, order = 50)
	{
		local c = TickCallback(getId(Month), cb, 0, order);
		monthCallbacks.push(c);
		
		monthCallbacks.sort(function (left, right) 
		{
			return left.order <=> right.order;
		});
		
		return c.id;
	}
	
	function addIsWorkAllowedCallback(cb)
	{
		local c = IsWorkAllowedCallback(getId(IWA), cb);
		isWorkAllowedCallbacks.push(c);
		return c.id;
	}
	
	function removeCallback(id)
	{
		if (id == 0 || id == null)
			return;
	
		local t = getType(id);
		if (t == Tick)
		{
			for (local i = 0; i < tickCallbacks.len(); ++i)
			{
				if (tickCallbacks[i].id == id)
				{
					tickCallbacks.remove(i);
					break;
				}
			}
		}
		else if (t == Month)
		{
			removeMonthCallback(id);
		}
		else if (t == IWA)
		{
			removeIsWorkAllowedCallback(id);
		}
	}
	
	function removeMonthCallback(id)
	{
		if (id == 0 || id == null)
			return;
	
		for (local i = 0; i < monthCallbacks.len(); ++i)
		{
			if (monthCallbacks[i].id == id)
			{
				monthCallbacks.remove(i);
				break;
			}
		}
	}
	
	function removeIsWorkAllowedCallback(id)
	{
		if (id == 0 || id == null)
			return;
	
		for (local i = 0; i < isWorkAllowedCallbacks.len(); ++i)
		{
			if (isWorkAllowedCallbacks[i].id == id)
			{
				isWorkAllowedCallbacks.remove(i);
				break;
			}
		}
	}
	
	function tick()
	{
		foreach(cb in tickCallbacks)
		{
			if (cb.tick())
			{
				expired.insert(0, cb);
			}
		}
		
		// execute one callback per tick
		if (expired.len() > 0)
		{
			local cb = expired.pop();
			cb.fire();
		}
	}
	
	function month()
	{
		foreach(cb in monthCallbacks)
		{
			expired.insert(0, cb);
		}
	}
	
	function is_work_allowed_here(pl, tool_id, pos)
	{
		local res = null; 
		foreach (cb in isWorkAllowedCallbacks)
		{
			if (res == null)
			{
				res = cb.fire(pl, tool_id, pos);
			}
			else 
			{
				cb.fire(pl, tool_id, pos); // ignore return value; if error already set, we just use the first error reported.
			}								// we just make sure every callback is called;
		}
		return res;
	}
}