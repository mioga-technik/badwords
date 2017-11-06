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
 * Defines the base settings for all single character based rules.
 *
 * @author Stephen Melrose <me@stephenmelrose.co.uk>
 */
abstract class AbstractCharacter implements Rule
{
    /**
     * @var array
     */
    protected $alternativeCharacters;

    /**
     * Constructor.
     *
     * @param array $alternativeCharacters The alternative characters that can be present instead of the character, e.g. @ for a.
     */
    public function __construct(array $alternativeCharacters = array())
    {
        $this->setAlternativeCharacters($alternativeCharacters);
    }

    /**
     * Adds an alternative character that can be present instead of
     * the character, e.g. @ for a.
     *
     * @param string $alternativeCharacter
     *
     * @return AbstractCharacter
     *
     * @throws \InvalidArgumentException
     */
    public function addAlternativeCharacter($alternativeCharacter)
    {
        if(!$this->validateAlternativeCharacter($alternativeCharacter)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid alternative character "%s". Please provide a single string character.',
                $alternativeCharacter
            ));
        }

        $alternativeCharacter = $this->cleanAlternativeCharacter($alternativeCharacter);

        if(!in_array($alternativeCharacter, $this->alternativeCharacters)) {
            array_push($this->alternativeCharacters, $alternativeCharacter);
        }

        return $this;
    }

    /**
     * Adds alternative characters that can be present instead of
     * the character, e.g. @ for a.
     *
     * @param array $alternativeCharacters
     *
     * @return AbstractCharacter
     *
     * @throws \InvalidArgumentException
     */
    public function addAlternativeCharacters(array $alternativeCharacters)
    {
        foreach($alternativeCharacters as $key => $alternativeCharacter) {
            if(!$this->validateAlternativeCharacter($alternativeCharacter)) {
                throw new \InvalidArgumentException(sprintf(
                    'Invalid alternative character "%s". Please provide a single character string.',
                    $alternativeCharacter
                ));
            }

            $alternativeCharacters[$key] = $this->cleanAlternativeCharacter($alternativeCharacter);
        }

        $this->alternativeCharacters = array_values(array_unique(array_merge(
            $this->alternativeCharacters,
            array_values($alternativeCharacters)
        )));

        return $this;
    }

    /**
     * Gets the alternative characters that can be present instead of
     * the character, e.g. @ for a.
     *
     * @return array
     */
    public function getAlternativeCharacters()
    {
        return $this->alternativeCharacters;
    }

    /**
     * Sets the alternative characters that can be present instead of
     * the character, e.g. @ for a.
     *
     * @param array $alternativeCharacters
     *
     * @return AbstractCharacter
     *
     * @throws \InvalidArgumentException
     */
    public function setAlternativeCharacters(array $alternativeCharacters)
    {
        foreach($alternativeCharacters as $key => $alternativeCharacter) {
            if(!$this->validateAlternativeCharacter($alternativeCharacter)) {
                throw new \InvalidArgumentException(sprintf(
                    'Invalid alternative character "%s". Please provide a single character string',
                    $alternativeCharacter
                ));
            }

            $alternativeCharacters[$key] = $this->cleanAlternativeCharacter($alternativeCharacter);
        }

        $this->alternativeCharacters = array_values(array_unique($alternativeCharacters));
        return $this;
    }

    /**
     * Validates an alternative character.
     *
     * @param string $alternativeCharacter
     *
     * @return boolean
     */
    protected function validateAlternativeCharacter($alternativeCharacter)
    {
        return is_string($alternativeCharacter) &&
            mb_strlen(trim($alternativeCharacter)) === 1;
    }

    /**
     * Cleans an alternative character into the correct format.
     *
     * @param string $alternativeCharacter
     *
     * @return string
     */
    protected function cleanAlternativeCharacter($alternativeCharacter)
    {
        return mb_strtolower(trim($alternativeCharacter));
    }
}