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

class NoneTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var None
     */
    protected $cacheStub;

    protected function setUp()
    {
        $this->cacheStub = new None();
    }
    
    public function testConstruct()
    {
        $cache = new None();
        $this->assertEquals('badwords-php_', $cache->getPrefix());
        $this->assertNull($cache->getDefaultLifetime());

        $cache = new None('foobar');
        $this->assertEquals('foobar', $cache->getPrefix());
        $this->assertNull($cache->getDefaultLifetime());

        $cache = new None('foobar', 123);
        $this->assertEquals('foobar', $cache->getPrefix());
        $this->assertEquals(123, $cache->getDefaultLifetime());
    }

    public function testCaching()
    {
        $this->assertFalse($this->cacheStub->has('foo'));
        $this->assertNull($this->cacheStub->get('foo'));
        $this->assertEquals(array(123), $this->cacheStub->get('foo', array(123)));
        $this->assertTrue($this->cacheStub->remove('foo'));

        try
        {
            $this->cacheStub->set('foo', array(456), 'invalid');
        }
        catch(\InvalidArgumentException $e)
        {
            $this->fail('Unexpected \InvalidArgumentException thrown.');
        }

        $this->assertTrue($this->cacheStub->set('foo', array(456)));

        $this->assertFalse($this->cacheStub->has('foo'));
        $this->assertNull($this->cacheStub->get('foo'));
        $this->assertEquals(array(123), $this->cacheStub->get('foo', array(123)));

        $this->assertTrue($this->cacheStub->remove('foo'));

        $this->assertFalse($this->cacheStub->has('foo'));
        $this->assertNull($this->cacheStub->get('foo'));
        $this->assertEquals(array(123), $this->cacheStub->get('foo', array(123)));
        $this->assertTrue($this->cacheStub->remove('foo'));
    }
}