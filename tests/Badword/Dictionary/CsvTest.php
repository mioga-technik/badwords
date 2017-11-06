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

class CsvTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Csv
     */
    protected $dictionaryStub;

    protected function getFixtureDir()
    {
        return __DIR__.'/Fixtures/Csv';
    }

    protected function setUp()
    {
        $this->dictionaryStub = new Csv($this->getFixtureDir().'/words.csv');
    }

    public function testGetId()
    {
        $dictionaryStub = $this->getMock(
            '\Badword\Dictionary\Csv',
            array('getPath'),
            array($this->getFixtureDir().'/words.csv')
        );

        $dictionaryStub->expects($this->any())
                       ->method('getPath')
                       ->will($this->returnValue('/i/am/a/csv/static/path'));
        
        $dictionaryStub->setMustStartWordDefault(true);
        $dictionaryStub->setMustEndWordDefault(true);

        $this->assertEquals('csv_d0a1f2faed8ce6f4586315b32e8e4755', $dictionaryStub->getId());

        $dictionaryStub->setMustStartWordDefault(false);
        $this->assertEquals('csv_c41aff8463fb2194147532208a25a596', $dictionaryStub->getId());

        $dictionaryStub->setMustEndWordDefault(false);
        $this->assertEquals('csv_56c37148695fefc44c177cd4551d78cf', $dictionaryStub->getId());
    }

    public function dataProviderGetWords()
    {
        return array(
            array(true, $this->getFixtureDir().'/empty.csv'),
            array(true, $this->getFixtureDir().'/invalid_word.csv'),
            array(true, $this->getFixtureDir().'/invalid_must_start_word.csv'),
            array(true, $this->getFixtureDir().'/invalid_must_end_word.csv'),
            array(false, $this->getFixtureDir().'/words.csv'),
        );
    }

    /**
     * @dataProvider dataProviderGetWords
     */
    public function testGetWords($expectError, $data)
    {
        $this->setExpectedException($expectError ? '\Badword\Dictionary\Exception' : null);
        
        $dictionary = new Csv($data);

        $words = $dictionary->getWords();
        $this->assertInternalType('array', $words);
        $this->assertEquals(8, count($words));
        
        foreach($words as $key => $word)
        {
            $this->assertInstanceOf('\Badword\Word', $word);
        }
    }
}