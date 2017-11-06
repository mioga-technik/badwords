<?php

/*
 * This file is part of the Badwords PHP package.
 *
 * (c) Stephen Melrose <me@stephenmelrose.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Badword\Dictionary;

class AbstractDictionaryTest extends \PHPUnit_Framework_TestCase
{
    protected static $wordsData = array(
        array('foo', 0, 0),
        array('bar', 0, 1),
        array('moo', 1, 0),
        array('boo', 1, 1),
        array('shu', 1),
        array('kew'),
        array('boo', 1, 1),
    );

    /**
     * @var AbstractDictionary
     */
    protected $dictionaryStub;

    protected function setUp()
    {
        $this->dictionaryStub = $this->getMock('\Badword\Dictionary\AbstractDictionary', array('loadWordsDataFromSource', 'getId'));

        $this->dictionaryStub->expects($this->any())
                             ->method('getId')
                             ->will($this->returnValue('mock_dictionary'));

        $this->dictionaryStub->expects($this->any())
                             ->method('loadWordsDataFromSource')
                             ->will($this->returnValue(static::$wordsData));
    }
    
    public function testGetCache()
    {
        $this->assertInstanceOf('\Badword\Cache', $this->dictionaryStub->getCache());
        $this->assertInstanceOf('\Badword\Cache\None', $this->dictionaryStub->getCache());
    }

    public function dataProviderSettingMustEndWordDefault()
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
            array(false, false),
        );
    }

    /**
     * @dataProvider dataProviderSettingMustEndWordDefault
     */
    public function testSettingMustEndWordDefault($expectError, $data)
    {
        $this->setExpectedException($expectError ? '\InvalidArgumentException' : null);
        $this->assertInstanceOf('\Badword\Dictionary\AbstractDictionary', $this->dictionaryStub->setMustEndWordDefault($data));
        $this->assertEquals($data, $this->dictionaryStub->getMustEndWordDefault());
    }

    public function dataProviderSettingMustStartWordDefault()
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
            array(false, false),
        );
    }

    /**
     * @dataProvider dataProviderSettingMustStartWordDefault
     */
    public function testSettingMustStartWordDefault($expectError, $data)
    {
        $this->setExpectedException($expectError ? '\InvalidArgumentException' : null);
        $this->assertInstanceOf('\Badword\Dictionary\AbstractDictionary', $this->dictionaryStub->setMustStartWordDefault($data));
        $this->assertEquals($data, $this->dictionaryStub->getMustStartWordDefault());
    }

    public function dataProviderSettingRiskLevel()
    {
        return array(
            array(true, array('foo')),
            array(true, true),
            array(true, false),
            array(true, ''),
            array(true, '    '),
            array(true, 'foobar'),
            array(true, -1),
            array(true, 1.5),
            array(true, 0),
            array(false, null),
            array(false, 1),
            array(false, 2),
        );
    }

    /**
     * @dataProvider dataProviderSettingRiskLevel
     */
    public function testSettingRiskLevel($expectError, $data)
    {
        $this->setExpectedException($expectError ? '\InvalidArgumentException' : null);
        $this->assertInstanceOf('\Badword\Dictionary\AbstractDictionary', $this->dictionaryStub->setRiskLevel($data));
        $this->assertEquals($data, $this->dictionaryStub->getRiskLevel());
    }

    public function testGetWords()
    {
        $words = $this->dictionaryStub->getWords();
        $this->assertInternalType('array', $words);
        $this->assertEquals(count(static::$wordsData)-1, count($words));

        foreach($words as $key => $word)
        {
            $this->assertInstanceOf('\Badword\Word', $word);
            $this->assertEquals(static::$wordsData[$key][0], $word->getWord());
            $this->assertEquals(isset(static::$wordsData[$key][1]) && static::$wordsData[$key][1] ? true : false, $word->getMustStartWord());
            $this->assertEquals(isset(static::$wordsData[$key][2]) && static::$wordsData[$key][2] ? true : false, $word->getMustEndWord());
        }
    }
}