<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 5/19/15
 * Time: 7:44 AM
 */

namespace Hitmeister\Component\RapidoCache;

use Hitmeister\Component\RapidoCache\Exception\InvalidConfigException;
use Hitmeister\Component\RapidoCache\Server\MemcacheServer;

class MemcacheCache extends Cache
{
	/**
	 * @var bool Store the item compressed (uses zlib).
	 */
	public $compression = false;

	/**
	 * @var \Memcache
	 */
    private $instance;

    /**
     * @return \Memcache
     * @throws InvalidConfigException
     */
    public function getMemcache()
    {
        if (null === $this->instance) {
            if (!extension_loaded('memcache')) {
                throw new InvalidConfigException(__CLASS__ . ' requires PHP `memcache` extension to be loaded.');
            }
            $this->instance = new \Memcache();
        }
        return $this->instance;
    }

    /**
     * @param array $servers
     */
    public function setServers(array $servers)
    {
        if (empty($servers)) {
            $servers = [
                new MemcacheServer(['host' => '127.0.0.1']),
            ];
        }

        foreach ($servers as $server) {
            if (!($server instanceof MemcacheServer)) {
                $server = new MemcacheServer($server);
            }

	        $this->getMemcache()->addServer(
		        $server->host, $server->port, $server->persistent, $server->timeout, $server->retryInterval,
		        $server->status, $server->failureCallback
	        );
        }
    }

	/**
	 * @inheritdoc
	 */
	protected function getValue($key)
	{
		return $this->getMemcache()->get($key);
	}

	/**
	 * @inheritdoc
	 */
	protected function getValues($keys)
	{
		return $this->getMemcache()->get($keys);
	}

	/**
	 * @inheritdoc
	 */
	protected function setValue($key, $value, $duration)
	{
		if ($duration > 2592000) { // 30 * 24 * 3600 = 30 days
			$duration = time() + $duration;
		}
		return $this->getMemcache()->set($key, $value, ($this->compression ? MEMCACHE_COMPRESSED : 0), $duration);
	}

	/**
	 * @inheritdoc
	 */
	protected function addValue($key, $value, $duration)
	{
		if ($duration > 2592000) { // 30 * 24 * 3600 = 30 days
			$duration = time() + $duration;
		}
		return $this->getMemcache()->add($key, $value, ($this->compression ? MEMCACHE_COMPRESSED : 0), $duration);
	}

	/**
	 * @inheritdoc
	 */
	protected function deleteValue($key)
	{
		return $this->getMemcache()->delete($key);
	}

	/**
	 * @inheritdoc
	 */
	protected function flushValues()
	{
		return $this->getMemcache()->flush();
	}

    /**
     * @inheritdoc
     */
    public function getStats()
    {
        $serversStats = $this->getMemcache()->getExtendedStats();
        if (false === $serversStats) {
            return false;
        }

        $hits = 0;
        $misses = 0;
        $usage = 0;
        $available = 0;

        foreach ($serversStats as $stats) {
            if (false === $stats) {
                continue;
            }

            $hits += $stats['get_hits'];
            $misses += $stats['get_misses'];
            $usage += $stats['bytes'];
            $available += $stats['limit_maxbytes'];
        }

        return [
            static::STATS_HITS              => $hits,
            static::STATS_MISSES            => $misses,
            static::STATS_MEMORY_USAGE      => $usage,
            static::STATS_MEMORY_AVAILABLE  => $available,
            static::STATS_EVICTIONS         => 0,
        ];
    }
}