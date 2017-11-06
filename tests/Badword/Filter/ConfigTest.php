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

use Badword\Filter\Config\Rule;
use Badword\Word;

class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Config
     */
    protected $configStub;

    protected function setUp()
    {
        $this->configStub = new Config();
    }

    public function testConstruct()
    {
        $config = new Config();
        $this->assertEquals(array(), $config->getRules());
        $this->assertEquals(array(), $config->getPreRules());
        $this->assertEquals(array(), $config->getPostRules());

        $ruleStub1 = new RuleStub('a');
        $ruleStub2 = new RuleStub('b');
        $ruleStub3 = new RuleStub('c');

        $config = new Config(array($ruleStub1));
        $this->assertEquals(array($ruleStub1), $config->getRules());
        $this->assertEquals(array(), $config->getPreRules());
        $this->assertEquals(array(), $config->getPostRules());

        $config = new Config(array($ruleStub1), array($ruleStub2));
        $this->assertEquals(array($ruleStub1), $config->getRules());
        $this->assertEquals(array($ruleStub2), $config->getPreRules());
        $this->assertEquals(array(), $config->getPostRules());

        $config = new Config(array($ruleStub1), array($ruleStub2), array($ruleStub3));
        $this->assertEquals(array($ruleStub1), $config->getRules());
        $this->assertEquals(array($ruleStub2), $config->getPreRules());
        $this->assertEquals(array($ruleStub3), $config->getPostRules());
    }

    public function testAddingRules()
    {
        $ruleStub1 = new RuleStub('a');
        $ruleStub2 = new RuleStub('b');
        $ruleStub3 = new RuleStub('c');
        $ruleStub4 = new RuleStub('d');
        $ruleStub5 = new RuleStub('e');
        $ruleStub6 = new RuleStub('f');
        $ruleStub7 = new RuleStub('g');

        $this->assertInstanceOf('Badword\Filter\Config', $this->configStub->addRule($ruleStub1));
        $this->assertEquals(array($ruleStub1), $this->configStub->getRules());

        $this->assertInstanceOf('Badword\Filter\Config', $this->configStub->addRule($ruleStub1));
        $this->assertEquals(array($ruleStub1), $this->configStub->getRules());

        try
        {
            $this->configStub->addRules(array($ruleStub2, 'invalid'));
            $this->fail('Expected \InvalidArgumentException not thrown.');
        }
        catch(\InvalidArgumentException $e) {}

        $this->assertEquals(array($ruleStub1), $this->configStub->getRules());

        $this->assertInstanceOf('Badword\Filter\Config', $this->configStub->addRules(array($ruleStub1, $ruleStub2, $ruleStub3)));
        $this->assertEquals(array($ruleStub1, $ruleStub2, $ruleStub3), $this->configStub->getRules());

        $this->assertInstanceOf('Badword\Filter\Config', $this->configStub->addRules(array('test' => $ruleStub3, $ruleStub4, 'test2' => $ruleStub5, 'test3' => $ruleStub5)));
        $this->assertEquals(array($ruleStub1, $ruleStub2, $ruleStub3, $ruleStub4, $ruleStub5), $this->configStub->getRules());

        try
        {
            $this->configStub->setRules(array($ruleStub6, 'invalid'));
            $this->fail('Expected \InvalidArgumentException not thrown.');
        }
        catch(\InvalidArgumentException $e) {}

        $this->assertEquals(array($ruleStub1, $ruleStub2, $ruleStub3, $ruleStub4, $ruleStub5), $this->configStub->getRules());

        $this->assertInstanceOf('Badword\Filter\Config', $this->configStub->setRules(array($ruleStub7, $ruleStub6, $ruleStub7)));
        $this->assertEquals(array($ruleStub7, $ruleStub6), $this->configStub->getRules());
    }

    public function testAddingPreRules()
    {
        $ruleStub1 = new RuleStub('a');
        $ruleStub2 = new RuleStub('b');
        $ruleStub3 = new RuleStub('c');
        $ruleStub4 = new RuleStub('d');
        $ruleStub5 = new RuleStub('e');
        $ruleStub6 = new RuleStub('f');
        $ruleStub7 = new RuleStub('g');

        $this->assertInstanceOf('Badword\Filter\Config', $this->configStub->addPreRule($ruleStub1));
        $this->assertEquals(array($ruleStub1), $this->configStub->getPreRules());

        $this->assertInstanceOf('Badword\Filter\Config', $this->configStub->addPreRule($ruleStub1));
        $this->assertEquals(array($ruleStub1), $this->configStub->getPreRules());

        try
        {
            $this->configStub->addPreRules(array($ruleStub2, 'invalid'));
            $this->fail('Expected \InvalidArgumentException not thrown.');
        }
        catch(\InvalidArgumentException $e) {}

        $this->assertEquals(array($ruleStub1), $this->configStub->getPreRules());

        $this->assertInstanceOf('Badword\Filter\Config', $this->configStub->addPreRules(array($ruleStub1, $ruleStub2, $ruleStub3)));
        $this->assertEquals(array($ruleStub1, $ruleStub2, $ruleStub3), $this->configStub->getPreRules());

        $this->assertInstanceOf('Badword\Filter\Config', $this->configStub->addPreRules(array('test' => $ruleStub3, $ruleStub4, 'test2' => $ruleStub5, 'test3' => $ruleStub5)));
        $this->assertEquals(array($ruleStub1, $ruleStub2, $ruleStub3, $ruleStub4, $ruleStub5), $this->configStub->getPreRules());

        try
        {
            $this->configStub->setPreRules(array($ruleStub6, 'invalid'));
            $this->fail('Expected \InvalidArgumentException not thrown.');
        }
        catch(\InvalidArgumentException $e) {}

        $this->assertEquals(array($ruleStub1, $ruleStub2, $ruleStub3, $ruleStub4, $ruleStub5), $this->configStub->getPreRules());

        $this->assertInstanceOf('Badword\Filter\Config', $this->configStub->setPreRules(array($ruleStub7, $ruleStub6, $ruleStub7)));
        $this->assertEquals(array($ruleStub7, $ruleStub6), $this->configStub->getPreRules());
    }

    public function testAddingPostRules()
    {
        $ruleStub1 = new RuleStub('a');
        $ruleStub2 = new RuleStub('b');
        $ruleStub3 = new RuleStub('c');
        $ruleStub4 = new RuleStub('d');
        $ruleStub5 = new RuleStub('e');
        $ruleStub6 = new RuleStub('f');
        $ruleStub7 = new RuleStub('g');

        $this->assertInstanceOf('Badword\Filter\Config', $this->configStub->addPostRule($ruleStub1));
        $this->assertEquals(array($ruleStub1), $this->configStub->getPostRules());

        $this->assertInstanceOf('Badword\Filter\Config', $this->configStub->addPostRule($ruleStub1));
        $this->assertEquals(array($ruleStub1), $this->configStub->getPostRules());

        try
        {
            $this->configStub->addPostRules(array($ruleStub2, 'invalid'));
            $this->fail('Expected \InvalidArgumentException not thrown.');
        }
        catch(\InvalidArgumentException $e) {}

        $this->assertEquals(array($ruleStub1), $this->configStub->getPostRules());

        $this->assertInstanceOf('Badword\Filter\Config', $this->configStub->addPostRules(array($ruleStub1, $ruleStub2, $ruleStub3)));
        $this->assertEquals(array($ruleStub1, $ruleStub2, $ruleStub3), $this->configStub->getPostRules());

        $this->assertInstanceOf('Badword\Filter\Config', $this->configStub->addPostRules(array('test' => $ruleStub3, $ruleStub4, 'test2' => $ruleStub5, 'test3' => $ruleStub5)));
        $this->assertEquals(array($ruleStub1, $ruleStub2, $ruleStub3, $ruleStub4, $ruleStub5), $this->configStub->getPostRules());

        try
        {
            $this->configStub->setPostRules(array($ruleStub6, 'invalid'));
            $this->fail('Expected \InvalidArgumentException not thrown.');
        }
        catch(\InvalidArgumentException $e) {}

        $this->assertEquals(array($ruleStub1, $ruleStub2, $ruleStub3, $ruleStub4, $ruleStub5), $this->configStub->getPostRules());

        $this->assertInstanceOf('Badword\Filter\Config', $this->configStub->setPostRules(array($ruleStub7, $ruleStub6, $ruleStub7)));
        $this->assertEquals(array($ruleStub7, $ruleStub6), $this->configStub->getPostRules());
    }

    public function testAddingWhitelistedWords()
    {
        $wordStub = new Word('foo');

        $this->assertInstanceOf('Badword\Filter\Config', $this->configStub->addWhitelistedWord($wordStub));
        $this->assertEquals(array('foo'), $this->configStub->getWhitelistedWords());

        $this->assertInstanceOf('Badword\Filter\Config', $this->configStub->addWhitelistedWord('foo'));
        $this->assertEquals(array('foo'), $this->configStub->getWhitelistedWords());

        try
        {
            $this->configStub->addWhitelistedWords(array('bar', 123456));
            $this->fail('Expected \InvalidArgumentException not thrown.');
        }
        catch(\InvalidArgumentException $e) {}

        $this->assertEquals(array('foo'), $this->configStub->getWhitelistedWords());

        $this->assertInstanceOf('Badword\Filter\Config', $this->configStub->addWhitelistedWords(array('foo', 'BAR ', 'shu')));
        $this->assertEquals(array('foo', 'bar', 'shu'), $this->configStub->getWhitelistedWords());

        $this->assertInstanceOf('Badword\Filter\Config', $this->configStub->addWhitelistedWords(array('test' => 'shu', 'koo', 'test2' => 'yii', 'test3' => 'yii')));
        $this->assertEquals(array('foo', 'bar', 'shu', 'koo', 'yii'), $this->configStub->getWhitelistedWords());

        try
        {
            $this->configStub->setWhitelistedWords(array('gaa', 123456));
            $this->fail('Expected \InvalidArgumentException not thrown.');
        }
        catch(\InvalidArgumentException $e) {}

        $this->assertEquals(array('foo', 'bar', 'shu', 'koo', 'yii'), $this->configStub->getWhitelistedWords());

        $this->assertInstanceOf('Badword\Filter\Config', $this->configStub->setWhitelistedWords(array('ler', 'gaa', 'ler')));
        $this->assertEquals(array('ler', 'gaa'), $this->configStub->getWhitelistedWords());
    }

    public function dataProviderApplyRulesToWord()
    {
        $ruleStub1 = new RuleStub('a');
        $ruleStub2 = new RuleStub('b');
        $ruleStub3 = new RuleStub('c');

        $wordStub = new Word('mock_');

        return array(
            array($wordStub, 'mock_'),
            array($wordStub, 'mock_a', array($ruleStub1)),
            array($wordStub, 'mock_ab', array($ruleStub1, $ruleStub2)),
            array($wordStub, 'mock_a', array(), array($ruleStub1)),
            array($wordStub, 'mock_ab', array(), array($ruleStub1, $ruleStub2)),
            array($wordStub, 'mock_a', array(), array(), array($ruleStub1)),
            array($wordStub, 'mock_ab', array(), array(), array($ruleStub1, $ruleStub2)),
            array($wordStub, 'mock_ba', array($ruleStub1), array($ruleStub2)),
            array($wordStub, 'mock_ac', array($ruleStub1), array(), array($ruleStub3)),
            array($wordStub, 'mock_cab', array($ruleStub1), array($ruleStub3), array($ruleStub2)),
        );
    }

    /**
     * @dataProvider dataProviderApplyRulesToWord
     */
    public function testApplyRulesToWord($word, $expectedResult, array $rules = array(), array $preRules = array(), array $postRules = array())
    {
        $this->configStub->setRules($rules);
        $this->configStub->setPreRules($preRules);
        $this->configStub->setPostRules($postRules);

        $this->assertEquals($expectedResult, $this->configStub->applyRulesToWord($word));
    }
}

class RuleStub implements Rule
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function apply($regExp, Word $word)
    {
        return $regExp.$this->data;
    }
}