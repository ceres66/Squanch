<?php
namespace Squanch\AbstractCommand;


use Squanch\Enum\Bucket;
use Squanch\Enum\Callbacks;
use Squanch\Base\ICallback;
use Squanch\Base\Command\ICmdHas;
use Squanch\Base\Boot\ICallbacksLoader;


abstract class AbstractHas implements ICmdHas
{
	private $connector;
	private $callbacksLoader;
	private $key;
	private $bucket = Bucket::DEFAULT_BUCKET_NAME;
	
	
	protected function getCallbacksLoader(): ICallbacksLoader
	{
		return $this->callbacksLoader;
	}
	
	protected function getConnector()
	{
		return $this->connector;
	}	
	
	protected function reset()
	{
		unset($this->key);
		$this->bucket = Bucket::DEFAULT_BUCKET_NAME;
	}	
	
	protected function getBucket(): string
	{
		return $this->bucket;
	}
	
	protected function getKey()
	{
		return $this->key;
	}
	
	
	/**
	 * @return static
	 */
	public function setup($connector, ICallbacksLoader $callbacksLoader)
	{
		$this->connector = $connector;
		$this->callbacksLoader = $callbacksLoader;
		return $this;
	}
	
	/**
	 * @return static
	 */
	public function byBucket(string $bucket)
	{
		$this->bucket = $bucket;
		return $this;
	}
	
	/**
	 * @return static
	 */
	public function byKey(string $key)
	{
		$this->key = $key;
		return $this;
	}
	
	/**
	 * @param \Closure|ICallback $onSuccess
	 * @return static
	 */
	public function onSuccess($onSuccess)
	{
		$this->getCallbacksLoader()->addCallback(Callbacks::SUCCESS_ON_HAS, $onSuccess);
		return $this;
	}
	
	/**
	 * @param \Closure|ICallback $onFail
	 * @return static
	 */
	public function onFail($onFail)
	{
		$this->getCallbacksLoader()->addCallback(Callbacks::FAIL_ON_HAS, $onFail);
		return $this;
	}
	
	/**
	 * @param \Closure|ICallback $onComplete
	 * @return static
	 */
	public function onComplete($onComplete)
	{
		$this->getCallbacksLoader()->addCallback(Callbacks::ON_HAS, $onComplete);
		return $this;
	}
}