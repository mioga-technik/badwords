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

class WhitespaceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Whitespace
     */
    protected $ruleStub;

    protected function setUp()
    {
        $this->ruleStub = new Whitespace();
    }

    public function testConstruct()
    {
        $whitespace = new Whitespace();
        $this->assertEquals(array(), $whitespace->getAlternativeCharacters());

        $whitespace = new Whitespace(array('c', 'd', 'e'));
        $this->assertEquals(array('c', 'd', 'e'), $whitespace->getAlternativeCharacters());
    }

    public function dataProviderApply()
    {
        $wordStub1 = new Word('some phrase');
        $wordStub2 = new Word('some other          phrase');

        return array(
            array($wordStub1, 'some\s*phrase'),
            array($wordStub2, 'some\s*other\s*phrase'),
            array($wordStub1, 'some(?:\s|!)*phrase', array('!')),
            array($wordStub2, 'some(?:\s|!)*other(?:\s|!)*phrase', array('!')),
            array($wordStub1, 'some(?:\s|!|\*)*phrase', array('!', '*')),
            array($wordStub2, 'some(?:\s|!|\*)*other(?:\s|!|\*)*phrase', array('!', '*'))
        );
    }

    /**
     * @dataProvider dataProviderApply
     */
    public function testApply(Word $word, $expectedResult, $alternativeCharacters = array())
    {
        $this->ruleStub->setAlternativeCharacters($alternativeCharacters);

        $this->assertEquals($expectedResult, $this->ruleStub->apply($word->getWord(), $word));
        $this->assertEquals(1, preg_match_all('/'.$expectedResult.'/iu', $word->getWord(), $matches));
    }
}