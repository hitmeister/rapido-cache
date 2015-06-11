<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 5/19/15
 * Time: 7:29 AM
 */

namespace Hitmeister\Component\RapidoCache\Server;

class MemcacheServer extends Server
{
    /**
     * @inheritdoc
     */
    public $port = 11211;

    /**
     * @var bool Controls the use of a persistent connection
     */
    public $persistent = true;

    /**
     * @var int Value in seconds which will be used for connecting to the daemon.
     */
    public $timeout = 1;

    /**
     * @var int Controls how often a failed server will be retried.
     * Setting this parameter to -1 disables automatic retry.
     */
    public $retryInterval = 15;

    /**
     * @var bool Controls if the server should be flagged as online.
     */
    public $status = true;

    /**
     * @var \Closure Allows the user to specify a callback function to run upon encountering an error.
     * The callback is run before failover is attempted. The function takes two parameters, the hostname
     * and port of the failed server.
     */
    public $failureCallback;

    /**
     * @param array $options
     * @return MemcacheServer
     */
    public static function create(array $options = [])
    {
        return parent::create($options);
    }
}