<?php

/*
 * This file is part of the Badwords PHP package.
 *
 * (c) Stephen Melrose <me@stephenmelrose.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Badword\Filter;

use Badword\Dictionary;

class ResultTest extends \PHPUnit_Framework_TestCase
{
    const CONTENT = 'Lorem ipsum dolor.';

    public function testGetContent()
    {
        $result = new Result(self::CONTENT, array(), array());
        $this->assertEquals(self::CONTENT, $result->getContent());
    }

    public function testGetMatches()
    {
        $result = new Result(self::CONTENT, array(array('amet')), array());
        $this->assertEquals(array('amet'), $result->getMatches());
        
        $result = new Result(self::CONTENT, array(array('amet'), array('amet', 'adipiscing')), array());
        $this->assertEquals(array('amet', 'adipiscing'), $result->getMatches());
    }

    public function testGetMatchesAndRiskLevels()
    {
        $result = new Result(self::CONTENT, array(array('amet')), array());
        $this->assertEquals(array('amet' => null), $result->getMatchesAndRiskLevels());

        $result = new Result(self::CONTENT, array(array('amet')), array(2));
        $this->assertEquals(array('amet' => 2), $result->getMatchesAndRiskLevels());

        $result = new Result(self::CONTENT, array(array('amet'), array('amet', 'adipiscing')), array(null, 3));
        $this->assertEquals(array('amet' => 3, 'adipiscing' => 3), $result->getMatchesAndRiskLevels());

        $result = new Result(self::CONTENT, array(array('amet'), array('amet', 'adipiscing')), array(4, null));
        $this->assertEquals(array('amet' => 4, 'adipiscing' => null), $result->getMatchesAndRiskLevels());
    }

    public function testGetDictionaryMatches()
    {
        $dictionaryStub = new DictionaryStub(1);

        $result = new Result(self::CONTENT, array(array('amet')), array());
        $this->assertEquals(array(), $result->getDictionaryMatches($dictionaryStub));

        $result = new Result(self::CONTENT, array(array('amet'), array('amet', 'adipiscing')), array());
        $this->assertEquals(array('amet', 'adipiscing'), $result->getDictionaryMatches($dictionaryStub));
    }

    public function testGetRiskLevel()
    {
        $result = new Result(self::CONTENT, array(array('amet')), array());
        $this->assertNull($result->getRiskLevel());

        $result = new Result(self::CONTENT, array(array('amet')), array(null, 3));
        $this->assertNull($result->getRiskLevel());

        $result = new Result(self::CONTENT, array(array('amet')), array(2, 4));
        $this->assertEquals(2, $result->getRiskLevel());

        $result = new Result(self::CONTENT, array(array('amet'), array('amet')), array(7, 6));
        $this->assertEquals(7, $result->getRiskLevel());
    }

    public function testIsClean()
    {
        $result = new Result(self::CONTENT, array(array('amet')), array());
        $this->assertFalse($result->isClean());

        $result = new Result(self::CONTENT, array(), array());
        $this->assertTrue($result->isClean());
    }

    public function dataProviderSetHighlightedContentBadwordClass()
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
     * @dataProvider dataProviderSetHighlightedContentBadwordClass
     */
    public function testSetHighlightedContentBadwordClass($expectError, $data)
    {
        $this->setExpectedException($expectError ? '\InvalidArgumentException' : null);
        $result = new Result(self::CONTENT, array(), array());
        $this->assertInstanceOf('\Badword\Filter\Result', $result->setHighlightedContentBadwordClass($data));
    }

    public function dataProviderSetHighlightedContentRiskLevelClassSuffix()
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
     * @dataProvider dataProviderSetHighlightedContentRiskLevelClassSuffix
     */
    public function testSetHighlightedContentRiskLevelClassSuffix($expectError, $data)
    {
        $this->setExpectedException($expectError ? '\InvalidArgumentException' : null);
        $result = new Result(self::CONTENT, array(), array());
        $this->assertInstanceOf('\Badword\Filter\Result', $result->setHighlightedContentRiskLevelClassSuffix($data));
    }

    public function testGetHighlightedContent()
    {
        $result = new Result(self::CONTENT, array(), array());
        $this->assertEquals(self::CONTENT, $result->getHighlightedContent());
        
        $result = new Result(self::CONTENT, array(array('ipsum')), array());
        $this->assertEquals('Lorem <span class="badword">ipsum</span> dolor.', $result->getHighlightedContent());

        $result->setHighlightedContentBadwordClass('foo');
        $this->assertEquals('Lorem <span class="foo">ipsum</span> dolor.', $result->getHighlightedContent());

        $result = new Result(self::CONTENT, array(array('ipsum')), array(2));
        $this->assertEquals('Lorem <span class="badword risk-level-2">ipsum</span> dolor.', $result->getHighlightedContent());

        $result->setHighlightedContentBadwordClass('foo');
        $this->assertEquals('Lorem <span class="foo risk-level-2">ipsum</span> dolor.', $result->getHighlightedContent());

        $result->setHighlightedContentRiskLevelClassSuffix('bar');
        $this->assertEquals('Lorem <span class="foo bar2">ipsum</span> dolor.', $result->getHighlightedContent());
    }
}

class DictionaryStub implements Dictionary
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getRiskLevel() {}

    public function getWords() {}
}