<?php
/**
 * User: Maksim Naumov <maksim.naumov@hitmeister.de>
 * Date: 5/19/15
 * Time: 7:39 AM
 */

namespace Hitmeister\Component\RapidoCache\Tests\Server;

use Hitmeister\Component\RapidoCache\Server\MemcacheServer;

class MemcacheServerTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $server = MemcacheServer::create([
            'host' => 'localhost',
        ]);

        $this->assertInstanceOf('\Hitmeister\Component\RapidoCache\Server\MemcacheServer', $server);
        $this->assertEquals('localhost', $server->host);
        $this->assertEquals(11211, $server->port);
        $this->assertEquals(1, $server->weight);
    }
}