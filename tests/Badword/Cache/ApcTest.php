<?php

/*
 * This file is part of the Badwords PHP package.
 *
 * (c) Stephen Melrose <me@stephenmelrose.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Badword\Cache;

class ApcTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Apc
     */
    protected $cacheStub;
    
    protected function setUp()
    {
        if(!function_exists('apc_store') || !ini_get('apc.enabled') || !ini_get('apc.enable_cli'))
        {
            $this->markTestSkipped('APC is either not installed, running, or enabled in CLI mode.');
        }

        $this->cacheStub = new Apc('badwordtest_');
    }
    
    public function testConstruct()
    {
        $cache = new Apc();
        $this->assertEquals('badwords-php_', $cache->getPrefix());
        $this->assertNull($cache->getDefaultLifetime());

        $cache = new Apc('foobar');
        $this->assertEquals('foobar', $cache->getPrefix());
        $this->assertNull($cache->getDefaultLifetime());

        $cache = new Apc('foobar', 123);
        $this->assertEquals('foobar', $cache->getPrefix());
        $this->assertEquals(123, $cache->getDefaultLifetime());
    }

    public function testCaching()
    {
        $this->assertFalse($this->cacheStub->has('foo'));
        $this->assertNull($this->cacheStub->get('foo'));
        $this->assertEquals(array(123), $this->cacheStub->get('foo', array(123)));
        $this->assertFalse($this->cacheStub->remove('foo'));

        try
        {
            $this->cacheStub->set('foo', array(456), 'invalid');
            $this->fail('Expected \InvalidArgumentException not thrown.');
        }
        catch(\InvalidArgumentException $e) {}

        $this->assertTrue($this->cacheStub->set('foo', array(456)));

        $this->assertTrue($this->cacheStub->has('foo'));
        $this->assertEquals(array(456), $this->cacheStub->get('foo'));
        $this->assertEquals(array(456), $this->cacheStub->get('foo', array(123)));

        $this->assertTrue($this->cacheStub->remove('foo'));

        $this->assertFalse($this->cacheStub->has('foo'));
        $this->assertNull($this->cacheStub->get('foo'));
        $this->assertEquals(array(123), $this->cacheStub->get('foo', array(123)));
        $this->assertFalse($this->cacheStub->remove('foo'));
    }
}