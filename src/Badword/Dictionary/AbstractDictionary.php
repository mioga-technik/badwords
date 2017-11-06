<?php

/*
 * This file is part of the Badwords PHP package.
 *
 * (c) Stephen Melrose <me@stephenmelrose.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Badwords\Dictionary;

use Badwords\Cache;
use Badwords\Cache\None;
use Badwords\Dictionary;
use Badwords\Word;

/**
 * Base class for all Dictionary classes.
 *
 * @author Stephen Melrose <me@stephenmelrose.co.uk>
 */
abstract class AbstractDictionary implements Dictionary
{
    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var boolean
     */
    protected $mustEndWordDefault = false;

    /**
     * @var boolean
     */
    protected $mustStartWordDefault = false;

    /**
     * @var integer
     */
    protected $riskLevel;

    /**
     * @var array
     */
    protected $words;

    /**
     * Constructor.
     *
     * @param integer $riskLevel The level of risk associated with the words.
     * @param Cache $cache The cache to use.
     */
    public function __construct($riskLevel = null, Cache $cache = null)
    {
        $this->setRiskLevel($riskLevel);
        $this->setCache($cache ?: new None());
    }

    /**
     * Gets the cache.
     *
     * @return Cache
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * Sets the cache.
     * 
     * @param Cache $cache
     * 
     * @return AbstractDictionary
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
        return $this;
    }

    /**
     * Gets the "must end word" default.
     *
     * @return boolean
     */
    public function getMustEndWordDefault()
    {
        return $this->mustEndWordDefault;
    }

    /**
     * Sets the "must end word" default.
     *
     * @param boolean $mustEndWordDefault
     *
     * @return AbstractDictionary
     *
     * @throws \InvalidArgumentException
     */
    public function setMustEndWordDefault($mustEndWordDefault = true)
    {
        if(!is_bool($mustEndWordDefault)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid "must end word" default "%s". Expected boolean.',
                $mustEndWordDefault
            ));
        }

        if($mustEndWordDefault !== $this->getMustEndWordDefault()) {
            $this->clearWords();
        }

        $this->mustEndWordDefault = $mustEndWordDefault;
        return $this;
    }

    /**
     * Gets the "must start word" default.
     *
     * @return boolean
     */
    public function getMustStartWordDefault()
    {
        return $this->mustStartWordDefault;
    }

    /**
     * Sets the "must start word" default.
     *
     * @param boolean $mustStartWordDefault
     *
     * @return AbstractDictionary
     *
     * @throws \InvalidArgumentException
     */
    public function setMustStartWordDefault($mustStartWordDefault = true)
    {
        if(!is_bool($mustStartWordDefault)) {
            throw new \InvalidArgumentException(sprintf('
                Invalid "must start word" default "%s". Expected boolean.',
                $mustStartWordDefault
            ));
        }

        if($mustStartWordDefault !== $this->getMustStartWordDefault()) {
            $this->clearWords();
        }

        $this->mustStartWordDefault = $mustStartWordDefault;
        return $this;
    }

    public function getRiskLevel()
    {
        return $this->riskLevel;
    }

    /**
     * Sets the risk level associated with the words.
     *
     * The greater the number, the greater the risk, i.e. 0 is considered
     * no risk. For example, a Dictionary containing words that only require
     * moderation could have a risk level of 1. Where as a Dictionary
     * containing words that should be instantly rejected could have
     * a risk level of 2. The scale past 0 is completely arbitrary.
     *
     * @param integer $riskLevel
     *
     * @return AbstractDictionary
     *
     * @throws \InvalidArgumentException
     */
    public function setRiskLevel($riskLevel)
    {
        if(!($riskLevel === null || (is_int($riskLevel) && $riskLevel > 0))) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid risk level "%s". Please provide an integer greater than 0, or null.',
                $riskLevel
            ));
        }

        $this->riskLevel = $riskLevel;
        return $this;
    }

    /**
     * Clears the local cache of words.
     *
     * @return AbstractDictionary
     */
    protected function clearWords()
    {
        $this->words = null;
        return $this;
    }

    public function getWords()
    {
        if($this->words === null)
        {
            $this->words = $this->loadWords();
        }

        return $this->words;
    }

    /**
     * Loads the words either from the cache or directly from the source.
     *
     * @return array
     *
     * @throws Exception
     */
    protected function loadWords()
    {
        $fromCache = true;
        $wordsData = $this->loadWordsDataFromCache();
        if(!$wordsData) {
            $fromCache = false;
            $wordsData = $this->loadWordsDataFromSource();
        }

        if(!(is_array($wordsData) && count($wordsData) > 0)) {
            throw new Exception('Words could not be loaded. Load failed or source was empty.');
        }

        if(!$fromCache) {
            $this->saveWordsDataToCache($wordsData);
        }

        $words = array();
        foreach($wordsData as $wordData) {
            $word = $this->convertWordDataToObject($wordData);
            if(!in_array($word, $words)) {
                array_push($words, $word);
            } else {
                unset($word);
            }
        }

        return $words;
    }

    /**
     * Loads the words data from the cache.
     *
     * @return array
     */
    protected function loadWordsDataFromCache()
    {
        $cache = $this->getCache();
        $cacheKey = $this->getCacheKey();
        return $cache->has($cacheKey) ? $cache->get($cacheKey) : null;
    }

    /**
     * Loads the words data from the source.
     *
     * @return array
     */
    abstract protected function loadWordsDataFromSource();

    /**
     * Saves the words data to the cache.
     *
     * @param array $wordsData
     *
     * @return boolean
     */
    protected function saveWordsDataToCache(array $wordsData)
    {
        return $this->getCache()->set($this->getCacheKey(), $wordsData);
    }

    /**
     * Gets the key used to read/store the words data from the cache.
     *
     * @return string
     */
    protected function getCacheKey()
    {
        return $this->getId() . '_words_data';
    }

    /**
     * Converts a valid array of word data in a new Word object.
     *
     * @param array $wordData
     *
     * @return Word
     */
    protected function convertWordDataToObject(array $wordData)
    {
        return new Word(
            (string) $wordData[0],
            (isset($wordData[1]) ? (bool) $wordData[1] : $this->getMustStartWordDefault()),
            (isset($wordData[2]) ? (bool) $wordData[2] : $this->getMustEndWordDefault())
        );
    }
}