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

class AbstractCharacterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Character
     */
    protected $ruleStub;

    protected function setUp()
    {
        $this->ruleStub = $this->getMock(
            '\Badword\Filter\Config\Rule\AbstractCharacter',
            array('apply')
        );
    }

    public function dataProviderAddAlternativeCharacter()
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
            array(false, 'f')
        );
    }

    /**
     * @dataProvider dataProviderAddAlternativeCharacter
     */
    public function testAddAlternativeCharacter($expectError, $data)
    {
        $this->setExpectedException($expectError ? '\InvalidArgumentException' : null);
        $this->assertInstanceOf('Badword\Filter\Config\Rule\AbstractCharacter', $this->ruleStub->addAlternativeCharacter($data));
        $this->assertEquals(array($data), $this->ruleStub->getAlternativeCharacters());
    }

    public function testAddingAlternativeCharacter()
    {
        $this->ruleStub->addAlternativeCharacter('a');
        $this->assertEquals(array('a'), $this->ruleStub->getAlternativeCharacters());

        $this->ruleStub->addAlternativeCharacter('a');
        $this->assertEquals(array('a'), $this->ruleStub->getAlternativeCharacters());

        try
        {
            $this->ruleStub->addAlternativeCharacters(array('a', 'foo', 'c'));
            $this->fail('Expected \InvalidArgumentException not thrown.');
        }
        catch(\InvalidArgumentException $e) {}

        $this->assertEquals(array('a'), $this->ruleStub->getAlternativeCharacters());

        $this->ruleStub->addAlternativeCharacters(array('a', 'b', 'c'));
        $this->assertEquals(array('a', 'b', 'c'), $this->ruleStub->getAlternativeCharacters());

        $this->ruleStub->addAlternativeCharacters(array('test' => 'c', 'd', 'test2' => 'e', 'test3' => 'e'));
        $this->assertEquals(array('a', 'b', 'c', 'd', 'e'), $this->ruleStub->getAlternativeCharacters());

        $this->ruleStub->addAlternativeCharacter('D');
        $this->assertEquals(array('a', 'b', 'c', 'd', 'e'), $this->ruleStub->getAlternativeCharacters());

        $this->ruleStub->addAlternativeCharacters(array('test' => 'G', 'F', 'test2' => 'D'));
        $this->assertEquals(array('a', 'b', 'c', 'd', 'e', 'g', 'f'), $this->ruleStub->getAlternativeCharacters());

        try
        {
            $this->ruleStub->setAlternativeCharacters(array('a', 'foo', 'c'));
            $this->fail('Expected \InvalidArgumentException not thrown.');
        }
        catch(\InvalidArgumentException $e) {}

        $this->assertEquals(array('a', 'b', 'c', 'd', 'e', 'g', 'f'), $this->ruleStub->getAlternativeCharacters());

        $this->ruleStub->setAlternativeCharacters(array('x', 'Y', 'z', 'Z'));
        $this->assertEquals(array('x', 'y', 'z'), $this->ruleStub->getAlternativeCharacters());
    }
}