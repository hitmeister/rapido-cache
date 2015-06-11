<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 19/05/15
 * Time: 09:14
 */

namespace Hitmeister\Component\RapidoCache\Tests;

use Hitmeister\Component\RapidoCache\MemcacheCache;

class MemcacheCacheTest extends CacheTestCase
{
	private $cache = null;

	/**
	 * @return MemcacheCache
	 */
	protected function getCacheInstance()
	{
		if (!extension_loaded('memcache')) {
			$this->markTestSkipped('`memcache` extension is not installed. Skipping.');
			return null;
		}

		if (!@stream_socket_client('127.0.0.1:11211', $errorNumber, $errorDescription, 0.5)) {
			$this->markTestSkipped(
				'No memcached server running at ' . '127.0.0.1:11211' . ' : ' . $errorNumber . ' -' . $errorDescription);
		}

		if (null === $this->cache) {
			$this->cache = new MemcacheCache();
			$this->cache->setServers([
				['host' => '127.0.0.1']
			]);
		}
		return $this->cache;
	}

    public function testInstance()
    {
        $cache = $this->getCacheInstance();
        $this->assertInstanceOf('\Memcache', $cache->getMemcache());
    }

	public function testExpire()
	{
		if (getenv('TRAVIS') == 'true') {
			$this->markTestSkipped('Can not reliably test memcache expiry on travis-ci.');
		}
		parent::testExpire();
	}

	public function testExpireAdd()
	{
		if (getenv('TRAVIS') == 'true') {
			$this->markTestSkipped('Can not reliably test memcache expiry on travis-ci.');
		}
		parent::testExpireAdd();
	}
}