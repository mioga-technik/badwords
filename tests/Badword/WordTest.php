<?php

/*
 * This file is part of the Badwords PHP package.
 *
 * (c) Stephen Melrose <me@stephenmelrose.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Badword;

class WordTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Word
     */
    protected $wordStub;

    protected function setUp()
    {
        $this->wordStub = new Word('foobar');
    }

    public function testConstruct()
    {
        $word = new Word('foobar');
        $this->assertEquals('foobar', $word->getWord());
        $this->assertFalse($word->getMustStartWord());
        $this->assertFalse($word->getMustEndWord());

        $word = new Word('foobar', true);
        $this->assertEquals('foobar', $word->getWord());
        $this->assertTrue($word->getMustStartWord());
        $this->assertFalse($word->getMustEndWord());

        $word = new Word('foobar', false, true);
        $this->assertEquals('foobar', $word->getWord());
        $this->assertFalse($word->getMustStartWord());
        $this->assertTrue($word->getMustEndWord());
    }

    public function dataProviderSettingWord()
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
     * @dataProvider dataProviderSettingWord
     */
    public function testSettingWord($expectError, $data)
    {
        $this->setExpectedException($expectError ? '\InvalidArgumentException' : null);
        $this->assertInstanceOf('\Badword\Word', $this->wordStub->setWord($data));
        $this->assertEquals($data, $this->wordStub->getWord());
    }

    public function dataProviderSettingMustStartWord()
    {
        return array(
            array(true, array('foo')),
            array(true, null),
            array(true, 0),
            array(true, 1),
            array(true, ''),
            array(true, '    '),
            array(true, 'foobar'),
            array(false, true),
            array(false, false)
        );
    }

    /**
     * @dataProvider dataProviderSettingMustStartWord
     */
    public function testSettingMustStartWord($expectError, $data)
    {
        $this->setExpectedException($expectError ? '\InvalidArgumentException' : null);
        $this->assertInstanceOf('\Badword\Word', $this->wordStub->setMustStartWord($data));
        $this->assertEquals($data, $this->wordStub->getMustStartWord());
    }

    public function dataProviderSettingMustEndWord()
    {
        return array(
            array(true, array('foo')),
            array(true, null),
            array(true, 0),
            array(true, 1),
            array(true, ''),
            array(true, '    '),
            array(true, 'foobar'),
            array(false, true),
            array(false, false)
        );
    }

    /**
     * @dataProvider dataProviderSettingMustEndWord
     */
    public function testSettingMustEndWord($expectError, $data)
    {
        $this->setExpectedException($expectError ? '\InvalidArgumentException' : null);
        $this->assertInstanceOf('\Badword\Word', $this->wordStub->setMustEndWord($data));
        $this->assertEquals($data, $this->wordStub->getMustEndWord());
    }
}