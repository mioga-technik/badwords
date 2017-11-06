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

class MustStartEndWordTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var MustStartEndWord
     */
    protected $ruleStub;

    protected function setUp()
    {
        $this->ruleStub = new MustStartEndWord();
    }

    public function dataProviderApply()
    {
        $wordStub1 = new Word('bazaars');
        $wordStub2 = new Word('bazaars');
        $wordStub2->setMustStartWord(true);
        $wordStub3 = new Word('bazaars');
        $wordStub3->setMustEndWord(true);
        $wordStub4 = new Word('bazaars');
        $wordStub4->setMustStartWord(true);
        $wordStub4->setMustEndWord(true);

        return array(
            array($wordStub1, 'bazaars'),
            array($wordStub2, '(?<=^|'.MustStartEndWord::REGEXP.')bazaars'),
            array($wordStub3, 'bazaars(?=$|'.MustStartEndWord::REGEXP.')'),
            array($wordStub4, '(?<=^|'.MustStartEndWord::REGEXP.')bazaars(?=$|'.MustStartEndWord::REGEXP.')'),
        );
    }

    /**
     * @dataProvider dataProviderApply
     */
    public function testApply(Word $word, $expectedResult)
    {
        $this->assertEquals($expectedResult, $this->ruleStub->apply($word->getWord(), $word));
        $this->assertEquals(1, preg_match_all('/'.$expectedResult.'/iu', $word->getWord(), $matches));
    }
    
    public function dataProviderRegExp()
    {
        $wordStub1 = new Word('@nimal');
        $wordStub1->setMustStartWord(true);
        
        $wordStub2 = new Word('magn@');
        $wordStub2->setMustEndWord(true);
        
        $wordStub3 = new Word('@sd@');
        $wordStub3->setMustStartWord(true);
        $wordStub3->setMustEndWord(true);
        
        $boundaries = array(
            ' ',
            '.',
            '-',
            '_',
            '!',
            '"',
            '\'',
            '^',
            '&',
            '*',
            '(',
            ')',
            '=',
            '+',
            '@'
        );
        
        $data = array();
        
        array_push($data, array($wordStub1, 'start'.$wordStub1->getWord()));
        array_push($data, array($wordStub1, $wordStub1->getWord(), $wordStub1->getWord()));
       
        foreach($boundaries as $boundary)
        {
            array_push($data, array(
                $wordStub1, 
                'lorem'.$boundary.$wordStub1->getWord(), 
                $wordStub1->getWord()
            ));
        }
        
        array_push($data, array($wordStub2, $wordStub2->getWord().'end'));
        array_push($data, array($wordStub2, $wordStub2->getWord(), $wordStub2->getWord()));
       
        foreach($boundaries as $boundary)
        {
            array_push($data, array(
                $wordStub2, 
                $wordStub2->getWord().$boundary.'ipsum', 
                $wordStub2->getWord()
            ));
        }
        
        array_push($data, array($wordStub3, 'start'.$wordStub3->getWord().'end'));
        array_push($data, array($wordStub3, $wordStub3->getWord(), $wordStub3->getWord()));
       
        foreach($boundaries as $boundary)
        {
            array_push($data, array(
                $wordStub3, 
                'lorem'.$boundary.$wordStub3->getWord().$boundary.'ipsum', 
                $wordStub3->getWord()
            ));
        }
        
        return $data;
    }
    
    /**
     * @dataProvider dataProviderRegExp
     */
    public function testRegExp(Word $word, $string, $expectedResult = null)
    {
        $regExp = $this->ruleStub->apply($word->getWord(), $word);
        
        $this->assertEquals(($expectedResult !== null ? 1 : 0), preg_match_all('/'.$regExp.'/iu', $string, $matches));
        if($expectedResult !== null)
        {
            $this->assertEquals(1, count($matches));
            $this->assertEquals(array($expectedResult), $matches[0]);
        }
    }
}