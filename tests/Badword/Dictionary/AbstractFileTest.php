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

class AbstractFileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractFile
     */
    protected $dictionaryStub;

    protected function getFixtureDir()
    {
        return __DIR__.'/Fixtures/Csv';
    }

    protected function setUp()
    {
        $this->dictionaryStub = $this->getMock(
            '\Badword\Dictionary\AbstractFile',
            array('getFileType', 'loadWordsDataFromSource'),
            array($this->getFixtureDir().'/words.csv')
        );

        $this->dictionaryStub->expects($this->any())
                             ->method('getFileType')
                             ->will($this->returnValue('mock'));
    }

    public function dataProviderSettingPath()
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
            array(true, 'foobar'),
            array(true, '/i/dont/exist.file'),
            array(true, $this->getFixtureDir()),
            array(false, $this->getFixtureDir().'/words.csv'),
        );
    }

    /**
     * @dataProvider dataProviderSettingPath
     */
    public function testSettingPath($expectError, $data)
    {
        $this->setExpectedException($expectError ? '\InvalidArgumentException' : null);
        $this->assertInstanceOf('\Badword\Dictionary\AbstractFile', $this->dictionaryStub->setPath($data));
        $this->assertEquals(realpath($data), $this->dictionaryStub->getPath());
    }

    public function testGetId()
    {
        $dictionaryStub = $this->getMock(
            '\Badword\Dictionary\AbstractFile',
            array('getFileType', 'getPath', 'loadWordsDataFromSource'),
            array($this->getFixtureDir().'/words.csv')
        );

        $dictionaryStub->expects($this->any())
                        ->method('getFileType')
                        ->will($this->returnValue('mock'));

        $dictionaryStub->expects($this->any())
                        ->method('getPath')
                        ->will($this->returnValue('/i/am/a/static/path'));
        
        $dictionaryStub->setMustStartWordDefault(true);
        $dictionaryStub->setMustEndWordDefault(true);
        
        $this->assertEquals('mock_b7b43ae4d94bbf7a6fa92f96c99377f2', $dictionaryStub->getId());

        $dictionaryStub->setMustStartWordDefault(false);
        $this->assertEquals('mock_db30bfdb62d383a4aef178b8958aac33', $dictionaryStub->getId());

        $dictionaryStub->setMustEndWordDefault(false);
        $this->assertEquals('mock_9d06107d83cdb13045ef5321f493e8ae', $dictionaryStub->getId());
    }
}