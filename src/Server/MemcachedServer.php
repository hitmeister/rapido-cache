<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 5/19/15
 * Time: 7:29 AM
 */

namespace Hitmeister\Component\RapidoCache\Server;

class MemcachedServer extends Server
{
    /**
     * @inheritdoc
     */
    public $port = 11211;

    /**
     * @param array $options
     * @return MemcachedServer
     */
    public static function create(array $options = [])
    {
        return parent::create($options);
    }
}