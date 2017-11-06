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
 * Defines the rule for a specific character of a word.
 *
 * @author Stephen Melrose <me@stephenmelrose.co.uk>
 */
class Character extends AbstractCharacter
{
    /**
     * @var string
     */
    protected $character;

    /**
     * @var integer
     */
    protected $canBeRepeatedFor;

    /**
     * @var boolean
     */
    protected $detectRepetition;

    /**
     * Constructor.
     * 
     * @param string $character The character this config applies to.
     * @param array $alternativeCharacters The alternative characters that can be present instead of the character, e.g. @ for a.
     * @param boolean $detectRepetition Whether character repetition should be detected or not, and the minimum number of consecutive occurrences before detection can be applied.
     */
    public function __construct($character, array $alternativeCharacters = array(), $detectRepetition = false)
    {
        parent::__construct($alternativeCharacters);

        $this->setCharacter($character);
        $this->setDetectRepetition($detectRepetition);
    }

    /**
     * Gets the character this config applies to.
     *
     * @return string
     */
    public function getCharacter()
    {
        return $this->character;
    }

    /**
     * Sets the character this config applies to.
     *
     * @param string $character
     *
     * @return Character
     *
     * @throws \InvalidArgumentException
     */
    public function setCharacter($character)
    {
        if(!(is_string($character) && mb_strlen($character) === 1)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid character "%s". Please provide a single character string.',
                $character
            ));
        }

        $this->character = mb_strtolower($character);
        return $this;
    }

    /**
     * Gets whether character repetition should be detected or not,
     * e.g. detect "aaaaa" for "a", and the minimum number of consecutive
     * occurrences before detection can be applied, e.g. for "s" and "2",
     * detection would be applied to "bass" but not "base".
     * 
     * @return boolean|integer
     */
    public function getDetectRepetition()
    {
        return $this->detectRepetition;
    }

    /**
     * Sets whether character repetition should be detected or not,
     * e.g. detect "aaaaa" for "a", and the minimum number of consecutive
     * occurrences before detection can be applied, e.g. for "s" and "2",
     * detection would be applied to "bass" but not "base".
     *
     * @param boolean|integer $minimumRequired
     *
     * @return Character
     *
     * @throws \InvalidArgumentException
     */
    public function setDetectRepetition($minimumRequired)
    {
        if(!(is_bool($minimumRequired)) &&
           !(is_int($minimumRequired) && $minimumRequired > 0)
        ) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid detect repetition minimum consecutive occurrences value "%s". Please provide a boolean or integer greater than 0.',
                $minimumRequired
            ));
        }

        $minimumRequired = (int) $minimumRequired;
        
        $this->detectRepetition = $minimumRequired ?: false;
        return $this;
    }

    public function apply($regExp, Word $word)
    {
        // If we need to detect this character being repeated
        if($this->getDetectRepetition()) {

            // For each reasonably possible combination of consecutive
            // occurrences greater than the minimum allowed
            for($i = 4; $i >= $this->getDetectRepetition(); $i--) {
                if($i !== 1) {
                    // Add repetition detection and set the
                    // minimum number required to $i
                    $regExp = preg_replace(
                        sprintf('/%s{%s,}/iu', $this->getCharacter(), $i),
                        sprintf('%s{%s,}', $this->getCharacter(), $i),
                        $regExp
                    );
                } else {
                    // Add repetition detection for a single character
                    // occurrence, only where previous detection
                    // hasn't been enforced
                    $regExp = preg_replace(sprintf(
                        '/(%s)([^{+]|$)/iu',
                        $this->getCharacter()),
                        '$1+$2',
                        $regExp
                    );
                }
            }
        }

        // Clean up any consecutive occurrences left over
        for($i = 4; $i >= 2; $i--) {
            $regExp = preg_replace(
                sprintf('/(%s){%s}/iu', $this->getCharacter(), $i),
                sprintf('$1{%s}', $i),
                $regExp
            );
        }

        // If there are alternative characters that could be used in
        // place of this character, add detection for them
        if($this->getAlternativeCharacters()) {
            $alternativeCharacters = array_merge(
                array($this->getCharacter()),
                $this->getAlternativeCharacters()
            );
            $alternativeCharacters = preg_replace(
                '/(\+|\*|\?|\$|\^)/iu',
                '\\\$1',
                implode('|', $alternativeCharacters)
            );
            $regExp = preg_replace(
                sprintf('/%s/ui', $this->getCharacter()),
                '(?:'.$alternativeCharacters.')',
                $regExp
            );
        }

        return $regExp;
    }
}