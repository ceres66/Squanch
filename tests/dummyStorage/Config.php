<?php
namespace dummyStorage;


use Squanch;
use Squanch\Base\Boot\IBoot;
use Squanch\Base\Boot\IConfigLoader;
use Squanch\Base\ICachePlugin;
use Squanch\Enum\InstancePriority;
use Squanch\Enum\InstanceType;
use Squanch\Objects\Data;
use Squanch\Objects\Instance;
use Squanch\Plugins\Squid\SquanchSquidConnector;
use Squanch\Plugins\Squid\SquidPlugin;
use Squid\MySql;



require_once __DIR__ . '/../../vendor/autoload.php';


class Config
{
	/** @var ICachePlugin */
	private $plugin;
	
	
	private function initSquidInstance()
	{
		$mysql = new MySql();
		
		$mysql
			->config()
			->addConfig(
				'main', [
				'host' => 'localhost',
				'port' => 3306,
				'user' => 'root',
				'password' => '',
				'database' => 'squanch_cache'
			]);
		
		
		$connector = new SquanchSquidConnector();
		
		$connector
			->setConnector($mysql->getConnector())
			->setDomain(Data::class)
			->setTable('HardCache')
			->setIgnoreFields(['Created', 'Modified']);
		
		
		$plugin = new SquidPlugin($connector);
		
		$instance = new Instance();
		$instance->Name = 'squid';
		$instance->Type = InstanceType::HARD;
		$instance->Plugin = $plugin;
		
		$garbageCollector = new Squanch\Plugins\Squid\SquidGarbageCollector($connector);
		$forever = Data::FOREVER_IN_SEC;
		$garbageCollector->setEndDate((new \DateTime())->modify("+ {$forever} seconds"));
		$garbageCollector->run();
		
		return $instance;
	}
	
	private function initDummyInstance()
	{
		$instance = new Instance();
		$instance->Name = 'dummy';
		$instance->Type = InstanceType::SOFT;
		$instance->Priority = InstancePriority::MEDIUM;
		$instance->Plugin = new DummyStoragePlugin();
		
		return $instance;
	}
	
	public function __construct()
	{
		$instanceName = getenv('instance');
	
		if (!$instanceName)
		{
			$instanceName = 'dummy';
		}

		/** @var IConfigLoader $configLoader */
		$configLoader = Squanch::skeleton(IConfigLoader::class);
		
		$configLoader->addInstance($this->initDummyInstance());
		$configLoader->addInstance($this->initSquidInstance());
		
		/** @var IBoot $squanch */
		$squanch = Squanch::skeleton(IBoot::class);
		$squanch->setConfigLoader($configLoader);
		
		$this->plugin = $squanch->filterInstancesByName($instanceName)->getPlugin();
	}
	
	/**
	 * @return ICachePlugin;
	 */
	public function getPlugin()
	{
		return $this->plugin;
	}
}