<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 19/05/15
 * Time: 09:14
 */

namespace Hitmeister\Component\RapidoCache\Tests;

use Hitmeister\Component\RapidoCache\RedisCache;

class RedisCacheTest extends CacheTestCase
{
	private $cache = null;

	/**
	 * @return RedisCache
	 */
	protected function getCacheInstance()
	{
		if (!extension_loaded('redis')) {
			$this->markTestSkipped('`redis` extension is not installed. Skipping.');
			return null;
		}

		if (!@stream_socket_client('127.0.0.1:6379', $errorNumber, $errorDescription, 0.5)) {
			$this->markTestSkipped(
				'No redis server running at ' . '127.0.0.1:6379' . ' : ' . $errorNumber . ' -' . $errorDescription);
		}

		if (null === $this->cache) {
			$this->cache = new RedisCache();
            $this->cache->setServer([
                'host' => '127.0.0.1',
            ]);
		}
		return $this->cache;
	}

    public function testInstance()
    {
        $cache = $this->getCacheInstance();
        $this->assertInstanceOf('\Redis', $cache->getRedis());
    }

	public function testExpire()
	{
		if (getenv('TRAVIS') == 'true') {
			$this->markTestSkipped('Can not reliably test redis expiry on travis-ci.');
		}
		parent::testExpire();
	}

	public function testExpireAdd()
	{
		if (getenv('TRAVIS') == 'true') {
			$this->markTestSkipped('Can not reliably test redis expiry on travis-ci.');
		}
		parent::testExpireAdd();
	}
}