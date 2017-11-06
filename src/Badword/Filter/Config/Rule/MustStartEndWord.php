<?php

/*
 * This file is part of the Badwords PHP package.
 *
 * (c) Stephen Melrose <me@stephenmelrose.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Badwords\Filter\Config\Rule;

use Badwords\Filter\Config\Rule;
use Badwords\Word;

/**
 * Defines the rule for whether a word must exist at the
 * start and/or end of a word only.
 *
 * @author Stephen Melrose <me@stephenmelrose.co.uk>
 */
class MustStartEndWord implements Rule
{
    const REGEXP = '[^a-z0-9]';
    
    public function apply($regExp, Word $word)
    {
        // If the Word must exist at the start of a word only,
        // add word boundary detection
        if($word->getMustStartWord()) {
            $regExp = '(?<=^|' . static::REGEXP . ')' . $regExp;
        }
        
        // If the Word must exist at the end of a word only,
        // add word boundary detection
        if($word->getMustEndWord()) {
            $regExp .= '(?=$|' . static::REGEXP . ')';
        }
        
        return $regExp;
    }
}