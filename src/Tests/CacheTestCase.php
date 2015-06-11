<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 5/18/15
 * Time: 10:54 PM
 */

namespace Hitmeister\Component\RapidoCache\Tests;

use Hitmeister\Component\RapidoCache\Cache;

abstract class CacheTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @return Cache
     */
    abstract protected function getCacheInstance();

    protected function setUp()
    {
        $cache = $this->getCacheInstance();
        $cache->set('string_test', 'string_test');
        $cache->set('number_test', 42);
        $cache->set('array_test', ['array_test' => 'array_test']);
    }

    protected function tearDown()
    {
        $cache = $this->getCacheInstance();
        $cache->flush();
    }

    public function testSet()
    {
        $cache = $this->getCacheInstance();
        $this->assertTrue($cache->set('string_test', 'string_test'));
        $this->assertTrue($cache->set('number_test', 42));
        $this->assertTrue($cache->set('array_test', ['array_test' => 'array_test']));
    }

    /**
     * @return array
     */
    public function multiSetExpiry()
    {
        return [[0], [2]];
    }

    /**
     * @dataProvider multiSetExpiry
     * @param integer
     */
    public function testMultiSet($expiry)
    {
        $cache = $this->getCacheInstance();
        $cache->flush();
        $cache->mSet(['string_test' => 'string_test', 'number_test' => 42, 'array_test' => ['array_test' => 'array_test']], $expiry);
        $this->assertEquals('string_test', $cache->get('string_test'));
        $this->assertEquals(42, $cache->get('number_test'));
        $array = $cache->get('array_test');
        $this->assertArrayHasKey('array_test', $array);
        $this->assertEquals('array_test', $array['array_test']);
    }

    public function testAdd()
    {
        $cache = $this->getCacheInstance();
        $this->assertFalse($cache->add('number_test', 13));
        $this->assertEquals(42, $cache->get('number_test'));
        $this->assertFalse($cache->get('add_test'));
        $this->assertTrue($cache->add('add_test', 13));
        $this->assertEquals(13, $cache->get('add_test'));
    }

    public function testMultiAdd()
    {
        $cache = $this->getCacheInstance();
        $this->assertFalse($cache->get('add_test'));
        $cache->mAdd(['number_test' => 13, 'add_test' => 13]);
        $this->assertEquals(42, $cache->get('number_test'));
        $this->assertEquals(13, $cache->get('add_test'));
    }

    public function testExists()
    {
        $cache = $this->getCacheInstance();
        $this->assertTrue($cache->exists('string_test'));
        $this->assertTrue($cache->exists('number_test'));
        $this->assertFalse($cache->exists('not_exists'));
    }

    public function testGet()
    {
        $cache = $this->getCacheInstance();
        $this->assertEquals('string_test', $cache->get('string_test'));
        $this->assertEquals(42, $cache->get('number_test'));
        $array = $cache->get('array_test');
        $this->assertArrayHasKey('array_test', $array);
        $this->assertEquals('array_test', $array['array_test']);
    }

    public function testGetNonExistent()
    {
        $cache = $this->getCacheInstance();
        $this->assertFalse($cache->get('non_existent_key'));
    }

    public function testArrayAccess()
    {
        $cache = $this->getCacheInstance();
        $cache['array_access_test'] = new \stdClass();
        $this->assertInstanceOf('stdClass', $cache['array_access_test']);
    }

    public function testMultiGet()
    {
        $cache = $this->getCacheInstance();
        $this->assertEquals(['string_test' => 'string_test', 'number_test' => 42], $cache->mGet(['string_test', 'number_test']));
        $this->assertEquals(['number_test' => 42, 'string_test' => 'string_test'], $cache->mGet(['number_test', 'string_test']));
        $this->assertEquals(['number_test' => 42, 'non_existent_key' => false], $cache->mGet(['number_test', 'non_existent_key']));
    }

    public function testDelete()
    {
        $cache = $this->getCacheInstance();
        $this->assertNotNull($cache->get('number_test'));
        $this->assertTrue($cache->delete('number_test'));
        $this->assertFalse($cache->get('number_test'));
    }

    public function testFlush()
    {
        $cache = $this->getCacheInstance();
        $this->assertTrue($cache->flush());
        $this->assertFalse($cache->get('number_test'));
    }

    public function testExpire()
    {
        $cache = $this->getCacheInstance();
        $this->assertTrue($cache->set('expire_test', 'expire_test', 2));
        usleep(500000);
        $this->assertEquals('expire_test', $cache->get('expire_test'));
        usleep(2500000);
        $this->assertFalse($cache->get('expire_test'));
    }

    public function testExpireAdd()
    {
        $cache = $this->getCacheInstance();
        $this->assertTrue($cache->add('expire_test_add', 'expire_test_add', 2));
        usleep(500000);
        $this->assertEquals('expire_test_add', $cache->get('expire_test_add'));
        usleep(2500000);
        $this->assertFalse($cache->get('expire_test_add'));
    }

    public function testStats()
    {
        $cache = $this->getCacheInstance();
        $this->assertArrayHasKey(Cache::STATS_HITS, $cache->getStats());
    }

	public function testLongKeySetGet()
	{
		$longKey = '1234567890123456789012345678901234567890123456789012345678901234567890';
		$cache = $this->getCacheInstance();
		$cache->keyLength = 50;
		$this->assertTrue($cache->set($longKey, 'value'));
		$this->assertEquals('value', $cache->get($longKey));
		$cache->keyLength = null;
	}
}
