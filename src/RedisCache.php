<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 5/22/15
 * Time: 6:32 PM
 */

namespace Hitmeister\Component\RapidoCache;

use Hitmeister\Component\RapidoCache\Exception\InvalidConfigException;
use Hitmeister\Component\RapidoCache\Server\RedisServer;

class RedisCache extends Cache
{
    /**
     * @var array An associative array of options where the key is the option
     * to set and the value is the new value for the option.
     * @see https://github.com/phpredis/phpredis#setoption
     */
    public $options = [];

    /**
     * @var bool
     */
    protected $serializer = null;

    /**
     * @var \Redis
     */
    private $instance;

    /**
     * @inheritdoc
     */
    public function setSerializer($serializer)
    {
	    if (
		    !is_array($serializer) ||
		    count($serializer) < 2 ||
		    !is_callable($serializer[0]) ||
		    !is_callable($serializer[1])
	    ) {
		    throw new InvalidConfigException(
			    'Serializer value should be an array of 2 members. Each member should be a callable function.');
	    }
	    $this->serializer = $serializer;
    }

    /**
     * @return \Redis
     * @throws InvalidConfigException
     */
    public function getRedis()
    {
        if (null === $this->instance) {
            if (!extension_loaded('redis')) {
                throw new InvalidConfigException(__CLASS__ . ' requires PHP `redis` extension to be loaded.');
            }
            $this->instance = new \Redis();

            if (!empty($this->options)) {
                foreach ($this->options as $key => $value) {
                    $this->instance->setOption($key, $value);
                }
            }
        }
        return $this->instance;
    }

    /**
     * @param array $server
     * @throws InvalidConfigException
     */
    public function setServer($server)
    {
        if (!($server instanceof RedisServer)) {
            $server = RedisServer::create($server);
        }

        if (null !== $server->unixSocket) {
            $result = $this->getRedis()->connect($server->unixSocket);
        } else {
            $result = $this->getRedis()->connect($server->host, $server->port, $server->timeout);
        }

        if (!$result) {
            throw new InvalidConfigException(__CLASS__ . ' unable to connect to redis server.');
        }

        $this->getRedis()->select($server->dbIndex);
    }

    /**
     * @inheritdoc
     */
    protected function getValue($key)
    {
	    $result = $this->getRedis()->get($key);
	    if (false !== $result) {
		    $result = $this->unserialize($result);
	    }
        return $result;
    }

    /**
     * @inheritdoc
     */
    protected function getValues($keys)
    {
        $results = $this->getRedis()->mget($keys);
	    foreach ($results as &$value) {
		    if (false !== $value) {
			    $value = $this->unserialize($value);
		    }
	    }
        return array_combine($keys, $results);
    }

    /**
     * @inheritdoc
     */
    protected function setValue($key, $value, $duration)
    {
	    $value = $this->serialize($value);
        if ($duration > 0) {
            return $this->getRedis()->setex($key, $duration, $value);
        }
        return $this->getRedis()->set($key, $value);
    }

    /**
     * @inheritdoc
     */
    protected function setValues($data, $duration)
    {
	    // With duration
	    if ($duration > 0) {
		    $redis = $this->getRedis()->multi(\Redis::PIPELINE);
		    foreach ($data as $key => $value) {
			    $value = $this->serialize($value);
			    $redis->setex($key, $duration, $value);
		    }
		    $redis->exec();
		    return true;
	    }

	    // Without duration
	    foreach($data as $key => &$value) {
		    $value = $this->serialize($value);
	    }
        return $this->getRedis()->mset($data);
    }

    /**
     * @inheritdoc
     */
    protected function addValue($key, $value, $duration)
    {
	    $value = $this->serialize($value);
        $result = $this->getRedis()->setnx($key, $value);
        if ($result && $duration > 0) {
            return $this->getRedis()->expire($key, $duration);
        }
        return $result;
    }

    /**
     * @inheritdoc
     */
    protected function deleteValue($key)
    {
        return 1 == $this->getRedis()->delete($key);
    }

	/**
	 * @inheritdoc
	 */
	protected function deleteValues($keys)
	{
		return count($keys) == $this->getRedis()->delete($keys);
	}

    /**
     * @inheritdoc
     */
    protected function flushValues()
    {
        return $this->getRedis()->flushDB();
    }

    /**
     * @inheritdoc
     */
    public function getStats()
    {
        $stats = $this->getRedis()->info();
        return [
            static::STATS_HITS              => $stats['keyspace_hits'],
            static::STATS_MISSES            => $stats['keyspace_misses'],
            static::STATS_MEMORY_USAGE      => $stats['used_memory'],
            static::STATS_MEMORY_AVAILABLE  => 0,
            static::STATS_EVICTIONS         => $stats['evicted_keys'],
        ];
    }

	/**
	 * @param mixed $value
	 * @return string
	 */
	protected function serialize($value)
	{
		return $this->serializer ? call_user_func($this->serializer[0], $value) : serialize($value);
	}

	/**
	 * @param string $string
	 * @return mixed
	 */
	protected function unserialize($string)
	{
		return $this->serializer ? call_user_func($this->serializer[1], $string) : unserialize($string);
	}
}
