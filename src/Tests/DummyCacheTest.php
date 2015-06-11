<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 5/18/15
 * Time: 10:53 PM
 */

namespace Hitmeister\Component\RapidoCache\Tests;

use Hitmeister\Component\RapidoCache\DummyCache;

class DummyCacheTest extends CacheTestCase
{
    private $cache = null;

    /**
     * @return DummyCache
     */
    protected function getCacheInstance()
    {
        if (null === $this->cache) {
            $this->cache = new DummyCache();
        }
        return $this->cache;
    }


    public function testSet()
    {
        $cache = $this->getCacheInstance();
        $this->assertTrue($cache->set('string_test', 'string_test'));
    }

    /**
     * @dataProvider multiSetExpiry
     * @param integer
     */
    public function testMultiSet($expiry)
    {
        $cache = $this->getCacheInstance();
        $this->assertTrue($cache->mSet(['string_test' => 'string_test', 'number_test' => 42], $expiry));
    }

    public function testAdd()
    {
        $cache = $this->getCacheInstance();
        $this->assertTrue($cache->add('number_test', 13));
        $this->assertFalse($cache->get('number_test'));
    }

    public function testMultiAdd()
    {
        $cache = $this->getCacheInstance();
        $this->assertTrue($cache->mAdd(['number_test' => 13, 'add_test' => 13]));
        $this->assertFalse($cache->get('number_test'));
    }

    public function testExists()
    {
        $cache = $this->getCacheInstance();
        $this->assertFalse($cache->exists('string_test'));
    }

    public function testGet()
    {
        $cache = $this->getCacheInstance();
        $this->assertFalse($cache->get('string_test'));
    }

    public function testArrayAccess()
    {
        $cache = $this->getCacheInstance();
        $cache['array_access_test'] = new \stdClass();
        $this->assertFalse($cache['array_access_test']);
    }

    public function testMultiGet()
    {
        $cache = $this->getCacheInstance();
        $this->assertEquals(['string_test' => false, 'number_test' => false], $cache->mGet(['string_test', 'number_test']));
    }

    public function testDelete()
    {
        $cache = $this->getCacheInstance();
        $this->assertTrue($cache->delete('number_test'));
    }

    public function testExpire()
    {
        $this->markTestSkipped('Have no sense to test Dummy for expiration.');
    }

    public function testExpireAdd()
    {
        $this->markTestSkipped('Have no sense to test Dummy for expiration.');
    }

    public function testStats()
    {
        $cache = $this->getCacheInstance();
        $this->assertFalse($cache->getStats());
    }

	public function testLongKeySetGet()
	{
		$this->markTestSkipped('Have no sense to test Dummy for key length.');
	}
}