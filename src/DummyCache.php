<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 5/18/15
 * Time: 11:09 PM
 */

namespace Hitmeister\Component\RapidoCache;

class DummyCache extends Cache
{
    /**
     * @inheritdoc
     */
    protected function getValue($key)
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    protected function setValue($key, $value, $duration)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function addValue($key, $value, $duration)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function deleteValue($key)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function flushValues()
    {
        return true;
    }
}