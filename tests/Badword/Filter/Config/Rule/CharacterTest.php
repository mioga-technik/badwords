<?php

/*
 * This file is part of the Badwords PHP package.
 *
 * (c) Stephen Melrose <me@stephenmelrose.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Badword\Filter\Config\Rule;

use Badword\Word;

class CharacterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Character
     */
    protected $ruleStub;

    protected function setUp()
    {
        $this->ruleStub = new Character('a');
    }

    public function testConstruct()
    {
        $character = new Character('b');
        $this->assertEquals('b', $character->getCharacter());
        $this->assertEquals(array(), $character->getAlternativeCharacters());
        $this->assertFalse($character->getDetectRepetition());

        $character = new Character('b', array('c', 'd', 'e'));
        $this->assertEquals('b', $character->getCharacter());
        $this->assertEquals(array('c', 'd', 'e'), $character->getAlternativeCharacters());
        $this->assertFalse($character->getDetectRepetition());

        $character = new Character('b', array('c', 'd'), true);
        $this->assertEquals('b', $character->getCharacter());
        $this->assertEquals(array('c', 'd'), $character->getAlternativeCharacters());
        $this->assertEquals(1, $character->getDetectRepetition());

        $character = new Character('b', array('c', 'd'), 2);
        $this->assertEquals('b', $character->getCharacter());
        $this->assertEquals(array('c', 'd'), $character->getAlternativeCharacters());
        $this->assertEquals(2, $character->getDetectRepetition());
    }

    public function dataProviderSettingCharacter()
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
            array(true, 'fd'),
            array(false, 'f'),
            array(false, ' ')
        );
    }

    /**
     * @dataProvider dataProviderSettingCharacter
     */
    public function testSettingCharacter($expectError, $data)
    {
        $this->setExpectedException($expectError ? '\InvalidArgumentException' : null);
        $this->assertInstanceOf('Badword\Filter\Config\Rule\Character', $this->ruleStub->setCharacter($data));
        $this->assertEquals($data, $this->ruleStub->getCharacter());
    }

    public function dataProviderSettingDetectRepetition()
    {
        return array(
            array(true, array('foo')),
            array(true, null),
            array(true, ''),
            array(true, '    '),
            array(true, 'foobar'),
            array(true, 0),
            array(true, -1),
            array(false, true),
            array(false, false),
            array(false, 1),
            array(false, 2),
        );
    }

    /**
     * @dataProvider dataProviderSettingDetectRepetition
     */
    public function testSettingDetectRepetition($expectError, $data)
    {
        $this->setExpectedException($expectError ? '\InvalidArgumentException' : null);
        $this->assertInstanceOf('Badword\Filter\Config\Rule\Character', $this->ruleStub->setDetectRepetition($data));
        $this->assertEquals($data ? (int) $data : false, $this->ruleStub->getDetectRepetition());
    }

    public function dataProviderApply()
    {
        $wordStub1 = new Word('bazaars');

        return array(
            array($wordStub1, 'baza{2}rs', 'a'),
            array($wordStub1, 'ba(?:z|@)aars', 'z', array('@')),
            array($wordStub1, 'ba(?:z|@|\*)aars', 'z', array('@', '*')),
            array($wordStub1, 'ba+za{2,}rs', 'a', array(), true),
            array($wordStub1, 'baza{2,}rs', 'a', array(), 2),
            array($wordStub1, 'baza{2}rs', 'a', array(), 3),
            array($wordStub1, 'b(?:a|@)+z(?:a|@){2,}rs', 'a', array('@'), true),
            array($wordStub1, 'b(?:a|@|\*)+z(?:a|@|\*){2,}rs', 'a', array('@', '*'), true),
            array($wordStub1, 'b(?:a|@)z(?:a|@){2,}rs', 'a', array('@'), 2),
            array($wordStub1, 'b(?:a|@|\*)z(?:a|@|\*){2,}rs', 'a', array('@', '*'), 2),
            array($wordStub1, 'b(?:a|@)z(?:a|@){2}rs', 'a', array('@'), 3),
            array($wordStub1, 'b(?:a|@|\*)z(?:a|@|\*){2}rs', 'a', array('@', '*'), 3),
        );
    }

    /**
     * @dataProvider dataProviderApply
     */
    public function testApply(Word $word, $expectedResult, $character, $alternativeCharacters = array(), $detectRepetition = false)
    {
        $this->ruleStub->setCharacter($character);
        $this->ruleStub->setAlternativeCharacters($alternativeCharacters);
        $this->ruleStub->setDetectRepetition($detectRepetition);

        $this->assertEquals($expectedResult, $this->ruleStub->apply($word->getWord(), $word));
        $this->assertEquals(1, preg_match_all('/'.$expectedResult.'/iu', $word->getWord(), $matches));
    }
}