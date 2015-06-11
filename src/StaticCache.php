<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 5/18/15
 * Time: 10:40 PM
 */

namespace Hitmeister\Component\RapidoCache;

class StaticCache extends Cache
{
    private $storage = [];

    /**
     * @inheritdoc
     */
    protected function getValue($key)
    {
        if (isset($this->storage[$key])) {
            // Check expired
            if ($this->storage[$key][1] === 0 || $this->storage[$key][1] > microtime(true)) {
                return $this->storage[$key][0];
            }
            // Expired
            unset($this->storage[$key]);
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    protected function setValue($key, $value, $duration)
    {
        $this->storage[$key] = [$value, $duration === 0 ? 0 : microtime(true) + $duration];
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function addValue($key, $value, $duration)
    {
        if (isset($this->storage[$key])) {
            if ($this->storage[$key][1] === 0 || $this->storage[$key][1] > microtime(true)) {
                return false;
            }
        }
        $this->storage[$key] = [$value, $duration === 0 ? 0 : microtime(true) + $duration];
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function deleteValue($key)
    {
        if (isset($this->storage[$key])) {
            unset($this->storage[$key]);
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    protected function flushValues()
    {
        $this->storage = [];
        return true;
    }
}