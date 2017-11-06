<?php

/*
 * This file is part of the Badfilters PHP package.
 *
 * (c) Stephen Melrose <me@stephenmelrose.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Badword;

use Badword\Cache\Apc;
use Badword\Filter\Config;
use Badword\Word;

class FilterTest extends \PHPUnit_Framework_TestCase
{
    const CONTENT = 'Lorem ipsum dolor.';
    
    /**
     * @var Filter
     */
    protected $filterStub;

    protected function setUp()
    {
        $this->filterStub = new Filter(array(), new Config());
    }

    public function testConstruct()
    {
        $config = new Config();

        $filter = new Filter(array(), new Config());
        $this->assertEquals(array(), $filter->getDictionaries());
        $this->assertEquals($config, $filter->getConfig());
        $this->assertInstanceOf('\Badword\Cache\None', $filter->getCache());

        $dictionary = new DictionaryStub('a');

        $filter = new Filter(array($dictionary), new Config());
        $this->assertEquals(array($dictionary), $filter->getDictionaries());
        $this->assertEquals($config, $filter->getConfig());
        $this->assertInstanceOf('\Badword\Cache\None', $filter->getCache());
    }

    public function testGetCache()
    {
        $this->assertInstanceOf('\Badword\Cache', $this->filterStub->getCache());
        $this->assertInstanceOf('\Badword\Cache\None', $this->filterStub->getCache());
    }

    public function testAddingDictionaries()
    {
        $dictionaryStub1 = new DictionaryStub('a');
        $dictionaryStub2 = new DictionaryStub('b');
        $dictionaryStub3 = new DictionaryStub('c');
        $dictionaryStub4 = new DictionaryStub('d');
        $dictionaryStub5 = new DictionaryStub('e');
        $dictionaryStub6 = new DictionaryStub('f');
        $dictionaryStub7 = new DictionaryStub('g');

        $this->assertInstanceOf('Badword\Filter', $this->filterStub->addDictionary($dictionaryStub1));
        $this->assertEquals(array($dictionaryStub1), $this->filterStub->getDictionaries());

        $this->assertInstanceOf('Badword\Filter', $this->filterStub->addDictionary($dictionaryStub1));
        $this->assertEquals(array($dictionaryStub1), $this->filterStub->getDictionaries());

        try
        {
            $this->filterStub->addDictionaries(array($dictionaryStub2, 'invalid'));
            $this->fail('Expected \InvalidArgumentException not thrown.');
        }
        catch(\InvalidArgumentException $e) {}

        $this->assertEquals(array($dictionaryStub1), $this->filterStub->getDictionaries());

        $this->assertInstanceOf('Badword\Filter', $this->filterStub->addDictionaries(array($dictionaryStub1, $dictionaryStub2, $dictionaryStub3)));
        $this->assertEquals(array($dictionaryStub1, $dictionaryStub2, $dictionaryStub3), $this->filterStub->getDictionaries());

        $this->assertInstanceOf('Badword\Filter', $this->filterStub->addDictionaries(array('test' => $dictionaryStub3, $dictionaryStub4, 'test2' => $dictionaryStub5, 'test3' => $dictionaryStub5)));
        $this->assertEquals(array($dictionaryStub1, $dictionaryStub2, $dictionaryStub3, $dictionaryStub4, $dictionaryStub5), $this->filterStub->getDictionaries());

        try
        {
            $this->filterStub->setDictionaries(array($dictionaryStub6, 'invalid'));
            $this->fail('Expected \InvalidArgumentException not thrown.');
        }
        catch(\InvalidArgumentException $e) {}

        $this->assertEquals(array($dictionaryStub1, $dictionaryStub2, $dictionaryStub3, $dictionaryStub4, $dictionaryStub5), $this->filterStub->getDictionaries());

        $this->assertInstanceOf('Badword\Filter', $this->filterStub->setDictionaries(array($dictionaryStub7, $dictionaryStub6, $dictionaryStub7)));
        $this->assertEquals(array($dictionaryStub7, $dictionaryStub6), $this->filterStub->getDictionaries());
    }

    public function testFilter()
    {
        try
        {
            $this->filterStub->filter(true);
            $this->fail('Expected \InvalidArgumentException not thrown.');
        }
        catch(\InvalidArgumentException $e) {}

        $result = $this->filterStub->filter(self::CONTENT);

        $this->assertInstanceOf('\Badword\Filter\Result', $result);
        $this->assertEquals(self::CONTENT, $result->getContent());
        $this->assertEquals(array(), $result->getMatches());
        $this->assertTrue($result->isClean());

        $results = $this->filterStub->filter(array(self::CONTENT.'0', self::CONTENT.'1'));

        $this->assertInternalType('array', $results);
        foreach($results as $key => $result)
        {
            $this->assertInstanceOf('\Badword\Filter\Result', $result);
            $this->assertEquals(self::CONTENT.$key, $result->getContent());
            $this->assertEquals(array(), $result->getMatches());
            $this->assertTrue($result->isClean());
        }
    }

    public function testFilterMatches()
    {
        $filter = new Filter(new DictionaryStub('a'), new Config());
        $result = $filter->filter(self::CONTENT);

        $this->assertInstanceOf('\Badword\Filter\Result', $result);
        $this->assertEquals(self::CONTENT, $result->getContent());
        $this->assertEquals(array('ipsum', 'dolor'), $result->getMatches());
        $this->assertFalse($result->isClean());
        $this->assertEquals(2, $result->getRiskLevel());
    }

    public function testFilterWhitelist()
    {
        $config = new Config();
        $config->addWhitelistedWord('ipsum');

        $filter = new Filter(new DictionaryStub('a'), $config);
        $result = $filter->filter(self::CONTENT);

        $this->assertInstanceOf('\Badword\Filter\Result', $result);
        $this->assertEquals(self::CONTENT, $result->getContent());
        $this->assertEquals(array('dolor'), $result->getMatches());
        $this->assertFalse($result->isClean());
        $this->assertEquals(2, $result->getRiskLevel());
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

    public function getRiskLevel()
    {
        return 2;
    }

    public function getWords()
    {
        return array(new Word('ipsum'), new Word('dolor'));
    }
}