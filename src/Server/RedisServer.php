<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 5/22/15
 * Time: 6:40 PM
 */

namespace Hitmeister\Component\RapidoCache\Server;

class RedisServer extends Server
{
    /** @var int */
    public $port = 6379;

    /** @var float */
    public $timeout = 0.0;

    /** @var string */
    public $unixSocket = null;

    /** @var int */
    public $dbIndex = 0;

    /**
     * @param array $options
     * @return RedisServer
     */
    public static function create(array $options = [])
    {
        return parent::create($options);
    }
}
