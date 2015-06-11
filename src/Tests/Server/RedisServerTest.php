<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 5/19/15
 * Time: 7:39 AM
 */

namespace Hitmeister\Component\RapidoCache\Tests\Server;

use Hitmeister\Component\RapidoCache\Server\RedisServer;

class RedisServerTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $server = RedisServer::create([
            'host' => 'localhost',
            'dbIndex' => 10,
        ]);

        $this->assertInstanceOf('\Hitmeister\Component\RapidoCache\Server\RedisServer', $server);
        $this->assertEquals('localhost', $server->host);
        $this->assertEquals(6379, $server->port);
        $this->assertEquals(10, $server->dbIndex);
    }

    public function testCreateUnixSocket()
    {
        $server = RedisServer::create([
            'unixSocket' => '/tmp/redis.sock',
        ]);

        $this->assertInstanceOf('\Hitmeister\Component\RapidoCache\Server\RedisServer', $server);
        $this->assertEquals('/tmp/redis.sock', $server->unixSocket);
    }
}