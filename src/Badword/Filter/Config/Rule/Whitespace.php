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

use Badwords\Word;

/**
 * Defines the rule for whitespace in a word.
 *
 * @author Stephen Melrose <me@stephenmelrose.co.uk>
 */
class Whitespace extends AbstractCharacter
{
    public function apply($regExp, Word $word)
    {
        // Add repetition detection
        $regExp = preg_replace('/\s+/iu', '\\s*', $regExp);

        // If there are alternative characters that could be used
        // in place of the whitespace, add detection for them
        if($this->getAlternativeCharacters()) {
            $alternativeCharacters = array_merge(
                array('\s'),
                $this->getAlternativeCharacters()
            );
            $alternativeCharacters = preg_replace(
                '/(\+|\*|\?|\$|\^)/iu',
                '\\\$1',
                implode('|', $alternativeCharacters)
            );
            $regExp = preg_replace(
                '/\\\s/ui',
                '(?:'.$alternativeCharacters.')',
                $regExp
            );
        }

        return $regExp;
    }
}