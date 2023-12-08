<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 5/18/15
 * Time: 9:21 PM
 */

namespace Hitmeister\Component\RapidoCache;

abstract class Cache implements CacheInterface, \ArrayAccess
{
    /**
     * Prefixed string to every cache kay.
     *
     * @var string|null
     */
    public $keyPrefix = null;

	/**
	 * @var int
	 */
	public $keyLength = null;

    /**
     * Checks whether a specified key exists in the cache.
     *
     * @param string $key
     * @return bool
     */
    public function exists($key)
    {
        return false !== $this->get($key);
    }

    /**
     * Returns the value of cache entry with specified key.
     *
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        $key = $this->buildKey($key);
        $value = $this->getValue($key);

        return $value;
    }

    /**
     * @param array $keys
     * @return array
     */
    public function mGet($keys)
    {
        // Prepare butch of keys
        $preparedKeys = [];
        foreach ($keys as $key) {
            $preparedKeys[$key] = $this->buildKey($key);
        }

        // Retrieve values
        $values = $this->getValues(array_values($preparedKeys));

        $results = array_fill_keys($keys, false);
        foreach ($preparedKeys as $originalKey => $newKey) {
            if (isset($values[$newKey])) {
	            $results[$originalKey] = $values[$newKey];
            }
        }

        return $results;
    }

    /**
     * Puts the value into the cache with specified key. If value already exists it will be replaced.
     *
     * @param string    $key        The key of the value to be stored.
     * @param mixed     $value      The value to be stored in the cache,
     * @param integer   $duration   The number of seconds in which the cached value will expire. 0 means never expire.
     * @return boolean
     */
    public function set($key, $value, $duration = 0)
    {
        $key = $this->buildKey($key);
        return $this->setValue($key, $value, $duration);
    }

    /**
     * @param array   $items
     * @param integer $duration
     * @return bool
     */
    public function mSet($items, $duration = 0)
    {
        $data = [];
        foreach ($items as $key => $value) {
            $key = $this->buildKey($key);
            $data[$key] = $value;
        }
        return $this->setValues($data, $duration);
    }

    /**
     * Puts a value with specified key into cache if the cache does not contain this key.
     *
     * @param string  $key        The key of the value to be stored.
     * @param mixed   $value      The value to be stored in the cache,
     * @param integer $duration   The number of seconds in which the cached value will expire. 0 means never expire.
     * @return boolean
     */
    public function add($key, $value, $duration = 0)
    {
        $key = $this->buildKey($key);
        return $this->addValue($key, $value, $duration);
    }

    /**
     * @param array   $items
     * @param integer $duration
     * @return bool
     */
    public function mAdd($items, $duration = 0)
    {
        $data = [];
        foreach ($items as $key => $value) {
            $key = $this->buildKey($key);
            $data[$key] = $value;
        }
        return $this->addValues($data, $duration);
    }

    /**
     * Deletes a value with the specified key from cache.
     *
     * @param string $key        The key of the value to be deleted.
     * @return boolean
     */
    public function delete($key)
    {
        $key = $this->buildKey($key);
        return $this->deleteValue($key);
    }

	/**
	 * @param array $keys
	 * @return bool
	 */
	public function mDelete($keys)
	{
		// Prepare butch of keys
		$preparedKeys = [];
		foreach ($keys as $key) {
			$preparedKeys[] = $this->buildKey($key);
		}

		// Retrieve values
		return $this->deleteValues($preparedKeys);
	}

    /**
     * Deletes all values from cache.
     *
     * @return boolean
     */
    public function flush()
    {
        return $this->flushValues();
    }

	/**
	 * Normalizes the key.
	 *
	 * @param string $key
	 * @return string
	 */
	protected function buildKey($key)
	{
		if (null !== $this->keyPrefix) {
			$key = $this->keyPrefix . $key;
		}
		if (null !== $this->keyLength && $this->keyLength > 32 && strlen($key) > $this->keyLength) {
			$key = substr($key, 0, -32) . md5($key);
		}
		return $key;
	}

	/**
     * Returns the value of cache entry with specified key.
     *
     * @param string    $key        The key if the value to be received.
     * @return mixed
     */
    abstract protected function getValue($key);

    /**
     * Puts a value with specified key into cache.
     *
     * @param string    $key        The key of the value to be stored.
     * @param mixed     $value      The value to be stored in the cache,
     * @param integer   $duration   The number of seconds in which the cached value will expire. 0 means never expire.
     * @return bool
     */
    abstract protected function setValue($key, $value, $duration);

    /**
     * Puts a value with specified key into cache if the cache does not contain this key.
     *
     * @param string    $key        The key of the value to be received.
     * @param mixed     $value      The value to be stored in the cache,
     * @param integer   $duration   The number of seconds in which the cached value will expire. 0 means never expire.
     * @return boolean
     */
    abstract protected function addValue($key, $value, $duration);

    /**
     * Deletes a value with the specified key from cache.
     *
     * @param string    $key The key of the value to be deleted.
     * @return boolean
     */
    abstract protected function deleteValue($key);

    /**
     * Deletes all values from cache.
     *
     * @return boolean
     */
    abstract protected function flushValues();

    /**
     * Gets multiple values from cache with the specified keys.
     * If cache storage supports multi-get function this method should be overridden!
     *
     * @param array     $keys The list of keys of cached values
     * @return array
     */
    protected function getValues($keys)
    {
        $results = [];
        foreach ($keys as $key) {
            $results[$key] = $this->getValue($key);
        }
        return $results;
    }

    /**
     * Sets multiple values into the cache with the specified keys.
     * If cache storage supports multi-set function this method should be overridden!
     *
     * @param array     $data       The array of data, where keys of array - there are keys and values - there are data.
     * @param integer   $duration   The number of seconds in which the cached value will expire. 0 means never expire.
     * @return bool
     */
    protected function setValues($data, $duration)
    {
        $result = true;
        foreach ($data as $key => $value) {
	        $result = $this->setValue($key, $value, $duration) && $result;
        }
        return $result;
    }

    /**
     * Sets multiple values into the cache with the specified keys if keys are not exists.
     * If cache storage supports multi-add function this method should be overridden!
     *
     * @param array     $data       The array of data, where keys of array - there are keys and values - there are data.
     * @param integer   $duration   The number of seconds in which the cached value will expire. 0 means never expire.
     * @return bool
     */
    protected function addValues($data, $duration)
    {
	    $result = true;
	    foreach ($data as $key => $value) {
		    $result = $this->addValue($key, $value, $duration) && $result;
	    }
	    return $result;
    }

	/**
	 * Gets multiple values from cache with the specified keys.
	 * If cache storage supports multi-get function this method should be overridden!
	 *
	 * @param array     $keys The list of keys of cached values
	 * @return bool
	 */
	protected function deleteValues($keys)
	{
		$result = true;
		foreach ($keys as $key) {
			$result = $this->deleteValue($key) && $result;
		}
		return $result;
	}

    /**
     * Returns true if cache entry with specified key exists.
     * This method is required by the interface ArrayAccess.
     *
     * @param mixed     $offset
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return $this->exists($offset);
    }

    /**
     * Returns the value of cache entry with specified key.
     * This method is required by the interface ArrayAccess.
     *
     * @param mixed     $offset
     * @return mixed
     */
    public function offsetGet($offset): mixed
    {
        return $this->get($offset);
    }

    /**
     * Puts the value into the cache with specified key. If value already exists it will be replaced.
     * To add expiration parameter, please, use [[set()]] method. Otherwise the data wll not be expired.
     * This method is required by the interface ArrayAccess.
     *
     * @param mixed     $offset
     * @param mixed     $value
     */
    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }

    /**
     * Deletes the value from cache by specified key.
     * This method is required by the interface ArrayAccess.
     *
     * @param mixed     $offset
     */
    public function offsetUnset($offset): void
    {
        $this->delete($offset);
    }

    /**
     * @inheritdoc
     */
    public function getStats()
    {
        return false;
    }
}
