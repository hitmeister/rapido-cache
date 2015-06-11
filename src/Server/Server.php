<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 5/19/15
 * Time: 7:27 AM
 */

namespace Hitmeister\Component\RapidoCache\Server;

abstract class Server
{
    /**
     * @var string server hostname or IP address
     */
    public $host;

    /**
     * @var integer server port
     */
    public $port;

    /**
     * @var integer probability of using this server among all servers.
     */
    public $weight = 1;

    /**
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        foreach ($options as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * @param array $options
     * @return Server
     */
    public static function create(array $options = [])
    {
        $server =  new static($options);
        return $server;
    }
}