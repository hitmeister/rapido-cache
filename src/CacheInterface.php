<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 5/18/15
 * Time: 9:24 PM
 */

namespace Hitmeister\Component\RapidoCache;

interface CacheInterface
{
    const STATS_HITS             = 'hits';
    const STATS_MISSES           = 'misses';
    const STATS_MEMORY_USAGE     = 'memory_usage';
    const STATS_MEMORY_AVAILABLE = 'memory_available';
    const STATS_EVICTIONS        = 'evictions';

    /**
     * Checks whether a specified key exists in the cache.
     *
     * @param string    $key
     * @return boolean
     */
    public function exists($key);

    /**
     * Returns the value of cache entry with specified key.
     *
     * @param string    $key        The key if the value to be received.
     * @return mixed
     */
    public function get($key);

    /**
     * @param array     $keys
     * @return array
     */
    public function mGet($keys);

    /**
     * Puts the value into the cache with specified key. If value already exists it will be replaced.
     *
     * @param string    $key        The key of the value to be stored.
     * @param mixed     $value      The value to be stored in the cache,
     * @param integer   $duration   The number of seconds in which the cached value will expire. 0 means never expire.
     * @return boolean
     */
    public function set($key, $value, $duration = 0);

    /**
     * @param array     $items
     * @param integer $duration
     * @return boolean
     */
    public function mSet($items, $duration = 0);

    /**
     * Puts a value with specified key into cache if the cache does not contain this key.
     *
     * @param string    $key        The key of the value to be stored.
     * @param mixed     $value      The value to be stored in the cache,
     * @param integer   $duration   The number of seconds in which the cached value will expire. 0 means never expire.
     * @return boolean
     */
    public function add($key, $value, $duration = 0);

    /**
     * @param array     $items
     * @param integer   $duration
     * @return boolean
     */
    public function mAdd($items, $duration = 0);

    /**
     * Deletes a value with the specified key from cache.
     *
     * @param string    $key        The key of the value to be deleted.
     * @return boolean
     */
    public function delete($key);

	/**
	 * @param array     $items
	 * @return boolean
	 */
	public function mDelete($items);

    /**
     * Deletes all values from cache.
     *
     * @return boolean
     */
    public function flush();

    /**
     * Retrieves cached information from the data store.
     *
     * @return array|false
     */
    public function getStats();
}
