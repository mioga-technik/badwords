<?php

/*
 * This file is part of the Badwords PHP package.
 *
 * (c) Stephen Melrose <me@stephenmelrose.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Badwords;

/**
 * Represents a single word and its settings.
 *
 * @author Stephen Melrose <me@stephenmelrose.co.uk>
 */
class Word
{
    /**
     * @var boolean
     */
    protected $mustStartWord;
    
    /**
     * @var boolean
     */
    protected $mustEndWord;
    
    /**
     * @var string
     */
    protected $word;

    /**
     * Constructor.
     * 
     * @param string $word The bad word.
     * @param boolean $mustStartWord Whether the bad word must exist at the start of a word only.
     * @param boolean $mustEndWord Whether the bad word must exist at the end of a word only.
     */
    public function __construct($word, $mustStartWord = false, $mustEndWord = false)
    {
        $this->setWord($word);
        $this->setMustStartWord($mustStartWord);
        $this->setMustEndWord($mustEndWord);
    }

    /**
     * Gets whether the bad word must exist at the start of a word only.
     *
     * @return boolean
     */
    public function getMustStartWord()
    {
        return $this->mustStartWord;
    }

    /**
     * Sets whether the bad word must exist at the start of a word only.
     *
     * @param boolean $mustEndWord
     *
     * @return Word
     *
     * @throws \InvalidArgumentException
     */
    public function setMustStartWord($mustStartWord)
    {
        if(!(is_bool($mustStartWord))) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid must start word "%s". Expected boolean.',
                $mustStartWord
            ));
        }

        $this->mustStartWord = $mustStartWord;
        return $this;
    }
    
    /**
     * Gets whether the bad word must exist at the end of a word only.
     * 
     * @return boolean
     */
    public function getMustEndWord()
    {
        return $this->mustEndWord;
    }

    /**
     * Sets whether the bad word must exist at the end of a word only.
     *
     * @param boolean $mustEndWord
     *
     * @return Word
     *
     * @throws \InvalidArgumentException
     */
    public function setMustEndWord($mustEndWord)
    {
        if(!(is_bool($mustEndWord))) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid must end word "%s". Expected boolean.',
                $mustEndWord
            ));
        }

        $this->mustEndWord = $mustEndWord;
        return $this;
    }

    /**
     * Gets the bad word.
     *
     * @return string
     */
    public function getWord()
    {
        return $this->word;
    }

    /**
     * Sets the bad word.
     *
     * @param string $word
     *
     * @return Word
     *
     * @throws \InvalidArgumentException
     */
    public function setWord($word)
    {
        if(!(is_string($word) && mb_strlen(trim($word)) > 0)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid word "%s". Expected non-empty string.',
                $word
            ));
        }

        $this->word = mb_strtolower(trim($word));
        return $this;
    }
}