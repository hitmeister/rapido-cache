<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 19/05/15
 * Time: 09:52
 */

namespace Hitmeister\Component\RapidoCache;

use Hitmeister\Component\RapidoCache\Exception\InvalidConfigException;
use Hitmeister\Component\RapidoCache\Server\MemcachedServer;

class MemcachedCache extends Cache
{
	/**
	 * @var string By default the Memcached instances are destroyed at the end of the request. To create an instance
	 * that persists between requests, use persistentId to specify a unique ID for the instance. All instances
	 * created with the same persistentId will share the same connection.
	 * @see http://php.net/manual/en/memcached.construct.php
	 */
	public $persistentId;

	/**
	 * @var array An associative array of options where the key is the option to set and the value is the new value for
	 * the option.
	 * @see http://php.net/manual/en/memcached.constants.php
	 */
	public $options = [];

	/**
	 * @var string The username to use for authentication.
	 */
	public $username;

	/**
	 * @var string The password to use for authentication.
	 */
	public $password;

	/**
	 * @var \Memcached
	 */
	private $instance;

	/**
	 * @return \Memcached
	 * @throws InvalidConfigException
	 */
	public function getMemcached()
	{
		if (null === $this->instance) {
			if (!extension_loaded('memcached')) {
				throw new InvalidConfigException(__CLASS__ . ' requires PHP `memcached` extension to be loaded.');
			}
			$this->instance = new \Memcached($this->persistentId);

			// SASL authentication
			// @see http://php.net/manual/en/memcached.setsaslauthdata.php
			if (ini_get('memcached.use_sasl') && (null !== $this->username || null !== $this->password)) {
				if (method_exists($this->instance, 'setSaslAuthData')) {
					$this->instance->setSaslAuthData($this->username, $this->password);
				}
			}

			if (!empty($this->options)) {
				$this->instance->setOptions($this->options);
			}
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
				new MemcachedServer(['host' => '127.0.0.1']),
			];
		}

		$existingServers = [];
		if (null !== $this->persistentId) {
			foreach ($this->getMemcached()->getServerList() as $item) {
				$existingServers[$item['host'] . ':' . $item['port']] = true;
			}
		}

		foreach ($servers as $server) {
			if (!($server instanceof MemcachedServer)) {
				$server = new MemcachedServer($server);
			}

			if (empty($existingServers) || !isset($existingServers[$server->host . ':' . $server->port])) {
				$this->getMemcached()->addServer($server->host, $server->port, $server->weight);
			}
		}
	}

	/**
	 * @inheritdoc
	 */
	protected function getValue($key)
	{
		return $this->getMemcached()->get($key);
	}

	/**
	 * @inheritdoc
	 */
	protected function getValues($keys)
	{
		return $this->getMemcached()->getMulti($keys);
	}

	/**
	 * @inheritdoc
	 */
	protected function setValue($key, $value, $duration)
	{
		if ($duration > 2592000) { // 30 * 24 * 3600 = 30 days
			$duration = time() + $duration;
		}
		return $this->getMemcached()->set($key, $value, $duration);
	}

	/**
	 * @inheritdoc
	 */
	protected function setValues($data, $duration)
	{
		if ($duration > 2592000) { // 30 * 24 * 3600 = 30 days
			$duration = time() + $duration;
		}
		return $this->getMemcached()->setMulti($data, $duration);
	}

	/**
	 * @inheritdoc
	 */
	protected function addValue($key, $value, $duration)
	{
		if ($duration > 2592000) { // 30 * 24 * 3600 = 30 days
			$duration = time() + $duration;
		}
		return $this->getMemcached()->add($key, $value, $duration);
	}

	/**
	 * @inheritdoc
	 */
	protected function deleteValue($key)
	{
		return $this->getMemcached()->delete($key);
	}

	/**
	 * @inheritdoc
	 */
	protected function deleteValues($keys)
	{
		// Some versions of memcached driver does not support this method
		if (method_exists($this->getMemcached(), 'deleteMulti')) {
			return $this->getMemcached()->deleteMulti($keys);
		}
		return parent::deleteValues($keys);
	}

	/**
	 * @inheritdoc
	 */
	protected function flushValues()
	{
		return $this->getMemcached()->flush();
	}

    /**
     * @inheritdoc
     */
    public function getStats()
    {
        $serversStats = $this->getMemcached()->getStats();
        if (false === $serversStats) {
            return false;
        }

        $hits = 0;
        $misses = 0;
        $usage = 0;
        $available = 0;
        $evictions = 0;

        foreach ($serversStats as $stats) {
            if (false === $stats) {
                continue;
            }

            $hits += $stats['get_hits'];
            $misses += $stats['get_misses'];
            $usage += $stats['bytes'];
            $available += $stats['limit_maxbytes'];
            $evictions += $stats['evictions'];
        }

        return [
            static::STATS_HITS              => $hits,
            static::STATS_MISSES            => $misses,
            static::STATS_MEMORY_USAGE      => $usage,
            static::STATS_MEMORY_AVAILABLE  => $available,
            static::STATS_EVICTIONS         => $evictions,
        ];
    }
}