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

class PhpTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Php
     */
    protected $dictionaryStub;

    protected function getFixtureDir()
    {
        return __DIR__.'/Fixtures/Php';
    }

    protected function setUp()
    {
        $this->dictionaryStub = new Php($this->getFixtureDir().'/words.php');
    }

    public function testGetId()
    {
        $dictionaryStub = $this->getMock(
            '\Badword\Dictionary\Php',
            array('getPath'),
            array($this->getFixtureDir().'/words.php')
        );

        $dictionaryStub->expects($this->any())
                       ->method('getPath')
                       ->will($this->returnValue('/i/am/a/php/static/path'));
        
        $dictionaryStub->setMustStartWordDefault(true);
        $dictionaryStub->setMustEndWordDefault(true);

        $this->assertEquals('php_e8f4fa15dc2aeada588130fb270854e0', $dictionaryStub->getId());

        $dictionaryStub->setMustStartWordDefault(false);
        $this->assertEquals('php_f7ad03e1f2fa33a25edd5597c2cd5263', $dictionaryStub->getId());

        $dictionaryStub->setMustEndWordDefault(false);
        $this->assertEquals('php_e9a486d6d5fc2ecee393a8e810962e1e', $dictionaryStub->getId());
    }

    public function dataProviderGetWords()
    {
        return array(
            array(true, $this->getFixtureDir().'/no_words_variable.php'),
            array(true, $this->getFixtureDir().'/invalid_format.php'),
            array(true, $this->getFixtureDir().'/invalid_word_data.php'),
            array(true, $this->getFixtureDir().'/invalid_word.php'),
            array(true, $this->getFixtureDir().'/invalid_must_start_word.php'),
            array(true, $this->getFixtureDir().'/invalid_must_end_word.php'),
            array(false, $this->getFixtureDir().'/words.php'),
        );
    }

    /**
     * @dataProvider dataProviderGetWords
     */
    public function testGetWords($expectError, $data)
    {
        $this->setExpectedException($expectError ? '\Badword\Dictionary\Exception' : null);
        
        $dictionary = new Php($data);

        $words = $dictionary->getWords();
        $this->assertInternalType('array', $words);
        $this->assertEquals(8, count($words));
        
        foreach($words as $key => $word)
        {
            $this->assertInstanceOf('\Badword\Word', $word);
        }
    }
}