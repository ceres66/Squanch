<?php
namespace Squanch\Plugins\PhpCache\Command;


use Squanch\Objects\Data;
use Squanch\Objects\CallbackData;
use Squanch\Plugins\PhpCache\Connector\IPhpCacheConnector;
use Squanch\Commands\AbstractGet;

use Objection\Mapper;
use Cache\Namespaced\NamespacedCachePool;


class Get extends AbstractGet implements IPhpCacheConnector
{
	use \Squanch\Plugins\PhpCache\Connector\TPhpCacheConnector;
	
	
	protected function onUpdateTTL(CallbackData $data, int $ttl)
	{
		$bucket = new NamespacedCachePool($this->getConnector(), $data->Bucket);
		$bucket->getItem($data->Key)->expiresAfter($ttl);
	}
	
	/**
	 * @param CallbackData $data
	 * @return Data|null
	 */
	protected function onGet(CallbackData $data)
	{
		$result = null;
		$bucket = new NamespacedCachePool($this->getConnector(), $data->Bucket);
		$item	= $bucket->getItem($data->Key);
		
		if ($item->isHit())
		{
			$mapper = Mapper::createFor(Data::class);
			
			/** @var Data $result */
			$result = $mapper->getObject($item->get());
			
			if ($result->TTL > 0)
			{
				$result->TTL = $result->EndDate->diff(new \DateTime())->format('%s');
			}
			
			$data->setData($result);
		}
		
		return $result;
	}
}