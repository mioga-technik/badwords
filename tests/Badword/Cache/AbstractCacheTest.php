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

class AbstractCacheTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractCache
     */
    protected $cacheStub;

    protected function setUp()
    {
        $this->cacheStub = $this->getMock('\Badword\Cache\AbstractCache', array('get', 'has', 'set', 'remove'));

        $this->cacheStub->expects($this->any())
                        ->method('get')
                        ->will($this->returnValue(false));

        $this->cacheStub->expects($this->any())
                        ->method('has')
                        ->will($this->returnValue(false));

        $this->cacheStub->expects($this->any())
                        ->method('set')
                        ->will($this->returnValue(true));

        $this->cacheStub->expects($this->any())
                        ->method('remove')
                        ->will($this->returnValue(true));
    }
    
    public function dataProviderSettingPrefix()
    {
        return array(
            array(true, array('foo')),
            array(true, true),
            array(true, false),
            array(true, null),
            array(true, 0),
            array(true, 1),
            array(true, ''),
            array(true, '    '),
            array(false, 'foobar')
        );
    }

    /**
     * @dataProvider dataProviderSettingPrefix
     */
    public function testSettingPrefix($expectError, $data)
    {
        $this->setExpectedException($expectError ? '\InvalidArgumentException' : null);
        $this->assertInstanceOf('\Badword\Cache\AbstractCache', $this->cacheStub->setPrefix($data));
        $this->assertEquals($data, $this->cacheStub->getPrefix());
    }

    public function dataProviderSettingDefaultLifetime()
    {
        return array(
            array(true, array('foo')),
            array(true, true),
            array(true, false),
            array(true, ''),
            array(true, '    '),
            array(true, 'foobar'),
            array(true, -1.5),
            array(true, -1),
            array(true, 0),
            array(true, 1.5),
            array(false, null),
            array(false, 1),
            array(false, 123),
        );
    }

    /**
     * @dataProvider dataProviderSettingDefaultLifetime
     */
    public function testSettingDefaultLifetime($expectError, $data)
    {
        $this->setExpectedException($expectError ? '\InvalidArgumentException' : null);
        $this->assertInstanceOf('\Badword\Cache\AbstractCache', $this->cacheStub->setDefaultLifetime($data));
        $this->assertEquals($data, $this->cacheStub->getDefaultLifetime());
    }
}