<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 5/18/15
 * Time: 10:53 PM
 */

namespace Hitmeister\Component\RapidoCache\Tests;

use Hitmeister\Component\RapidoCache\StaticCache;

class StaticCacheTest extends CacheTestCase
{
    private $cache = null;

    /**
     * @return StaticCache
     */
    protected function getCacheInstance()
    {
        if (null === $this->cache) {
            $this->cache = new StaticCache();
        }
        return $this->cache;
    }

    public function testStats()
    {
        $cache = $this->getCacheInstance();
        $this->assertFalse($cache->getStats());
    }
}