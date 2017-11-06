<?php

/*
 * This file is part of the Badwords PHP package.
 *
 * (c) Stephen Melrose <me@stephenmelrose.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Badwords\Filter\Config;

use Badwords\Filter\Config;
use Badwords\Filter\Config\Rule\Character;
use Badwords\Filter\Config\Rule\MustStartEndWord;
use Badwords\Filter\Config\Rule\Whitespace;

/**
 * Defines a default config to use for the filter.
 *
 * @author Stephen Melrose <me@stephenmelrose.co.uk>
 */
class Standard extends Config
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        // Parent
        parent::__construct();

        // Vowels
        $this->addRules(array(
            new Character('a', array('@', '*'), true),
            new Character('e', array('3', '*'), true),
            new Character('i', array('1', '!', '*'), true),
            new Character('o', array('0', '*'), true),
            new Character('u', array('*'), true)
        ));

        // Consonants
        $this->addRules(array(
            new Character('b', array('8')),
            new Character('c', array('*')),
            new Character('h', array('*')),
            new Character('l', array('1')),
            new Character('s', array('5', '$'), 2),
            new Character('t', array('4', '+'))
        ));

        // Whitespace
        $this->addRule(new Whitespace(array('!', '?')));

        // Start/End Word
        $this->addPostRule(new MustStartEndWord());
    }
}