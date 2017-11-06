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

use Badwords\Word;

/**
 * Defines the interface for a specific regular expression generation
 * rule for the config to implement
 * 
 * @author Stephen Melrose <me@stephenmelrose.co.uk>
 */
interface Rule
{
    /**
     * Applies the rule to the data using the provided word.
     *
     * @param string $regExp
     * @param Word $word
     *
     * @return string
     */
    public function apply($regExp, Word $word);
}