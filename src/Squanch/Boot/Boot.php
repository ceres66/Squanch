<?php
namespace Squanch\Boot;


use Squanch\Base\ICachePlugin;
use Squanch\Base\Boot\IBoot;
use Squanch\Base\Boot\IConfigLoader;

use Squanch\Objects\Instance;
use Squanch\Exceptions\SquanchInstanceException;


/**
 * @autoload
 */
class Boot implements IBoot
{
	/** @var IConfigLoader $config */
	private $config;
	
	/** @var Instance[] $filteredInstances */
	private $filteredInstances = [];
	
	
	private function filterInstances(\Closure $compare)
	{
		if (is_null($this->filteredInstances))
		{
			$this->resetFilters();
		}
		
		$instances = [];
		
		foreach ($this->filteredInstances as $instance)
		{
			if ($compare($instance))
			{
				$instances[] = $instance;
			}
		}
		
		$this->filteredInstances = $instances;
	}
	
	
	public function resetFilters()
	{
		$this->filteredInstances = $this->config->getInstances();
		return $this;
	}
	
	public function setConfigLoader(IConfigLoader $configLoader): IBoot
	{
		$this->config = $configLoader;
		return $this;
	}
	
	public function filterInstancesByType(string $type): IBoot
	{
		$this->filterInstances(
			function($instance) use ($type)
			{
				return $instance->Type == $type ? true : false;
			}
		);
		
		return $this;
	}
	
	public function filterInstancesByName(string $name): IBoot
	{
		$this->filterInstances(
			function($instance) use ($name)
			{
				return $instance->Name == $name ? true : false;
			}
		);
		
		return $this;
	}
	
	public function filterInstancesByPriorityLessOrEqual(int $priority): IBoot
	{
		$this->filterInstances(
			function($instance) use ($priority)
			{
				return $instance->Priority <= $priority ? true : false;
			}
		);
		
		return $this;
	}
	
	public function filterInstancesByPriorityGreaterOrEqual(int $priority): IBoot
	{
		$this->filterInstances(
			function($instance) use ($priority)
			{
				return $instance->Priority >= $priority ? true : false;
			}
		);
		
		return $this;
	}
	
	public function filterInstancesByPriority(int $priority): IBoot
	{
		$this->filterInstances(
			function($instance) use ($priority)
			{
				return $instance->Priority == $priority ? true : false;
			}
		);
		
		return $this;
	}
	
	public function getPlugin(): ICachePlugin
	{
		$total = count($this->filteredInstances);
		
		if ($total == 0)
		{
			throw new SquanchInstanceException('Required instance not found');
		}
		else if ($total > 1)
		{
			throw new SquanchInstanceException('Got multiple instances with same properties');
		}
		
		/** @var Instance $instance */
		$instance = array_values($this->filteredInstances)[0];
		
		return $instance->Plugin;
	}
}