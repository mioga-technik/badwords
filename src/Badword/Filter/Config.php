<?php

/*
 * This file is part of the Badwords PHP package.
 *
 * (c) Stephen Melrose <me@stephenmelrose.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Badwords\Filter;

use Badwords\Filter\Config\Rule;
use Badwords\Filter\Config\Rule\MustStartEndWord;
use Badwords\Word;

/**
 * Defines settings and regular expression generation rules
 * the filter adheres to when executing.
 *
 * @author Stephen Melrose <me@stephenmelrose.co.uk>
 */
class Config
{
    /**
     * @var array
     */
    protected $rules = array();

    /**
     * @var array
     */
    protected $preRules = array();

    /**
     * @var array
     */
    protected $postRules = array();

    /**
     * @var array
     */
    protected $whitelist = array();

    /**
     * Constructor.
     * 
     * @param array $rules The regular expression generation Rules.
     * @param array $preRules The "pre" regular expression generation Rules (executed before the standard Rules).
     * @param array $postRules The "post" regular expression generation Rules (executed after the standard Rules).
     */
    public function __construct(array $rules = array(), array $preRules = array(), array $postRules = array())
    {
        $this->setRules($rules);
        $this->setPreRules($preRules);
        $this->setPostRules($postRules);
    }

    /**
     * Adds a regular expression generation rule.
     * 
     * @param Rule $rule
     * 
     * @return Config
     */
    public function addRule(Rule $rule)
    {
        return $this->addRuleToStack($rule, $this->rules);
    }

    /**
     * Adds regular expression generation rules.
     *
     * @param array $rules
     *
     * @return Config
     */
    public function addRules(array $rules)
    {
        return $this->addRulesToStack($rules, $this->rules);
    }

    /**
     * Gets the regular expression generation rules.
     *
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * Sets the regular expression generation rules.
     *
     * @param array $rules
     *
     * @return Config
     */
    public function setRules(array $rules)
    {
        return $this->setRulesToStack($rules, $this->rules);
    }

    /**
     * Adds a "pre" regular expression generation rule
     * (executed before the standard rules).
     *
     * @param Rule $rule
     *
     * @return Config
     */
    public function addPreRule(Rule $rule)
    {
        return $this->addRuleToStack($rule, $this->preRules);
    }

    /**
     * Adds "pre" regular expression generation rules
     * (executed before the standard rules).
     *
     * @param array $rules
     *
     * @return Config
     */
    public function addPreRules(array $rules)
    {
        return $this->addRulesToStack($rules, $this->preRules);
    }

    /**
     * Gets the "pre" regular expression generation rules
     * (executed before the standard rules).
     *
     * @return array
     */
    public function getPreRules()
    {
        return $this->preRules;
    }

    /**
     * Sets the "pre" regular expression generation rules
     * (executed before the standard rules).
     *
     * @param array $rules
     *
     * @return Config
     */
    public function setPreRules(array $rules)
    {
        return $this->setRulesToStack($rules, $this->preRules);
    }

    /**
     * Adds a "post" regular expression generation rule
     * (executed after the standard rules).
     *
     * @param Rule $rule
     *
     * @return Config
     */
    public function addPostRule(Rule $rule)
    {
        return $this->addRuleToStack($rule, $this->postRules);
    }

    /**
     * Adds "post" regular expression generation rules
     * (executed after the standard rules).
     *
     * @param array $rules
     *
     * @return Config
     */
    public function addPostRules(array $rules)
    {
        return $this->addRulesToStack($rules, $this->postRules);
    }

    /**
     * Gets the "post" regular expression generation rules
     * (executed after the standard rules).
     *
     * @return array
     */
    public function getPostRules()
    {
        return $this->postRules;
    }

    /**
     * Sets the "post" regular expression generation rules
     * (executed after the standard rules).
     *
     * @param array $rules
     *
     * @return Config
     */
    public function setPostRules(array $rules)
    {
        return $this->setRulesToStack($rules, $this->postRules);
    }

    /**
     * Adds a regular expression generation rule to the specified stack.
     *
     * @param Rule $rule
     * @param array &$stack
     *
     * @return Config
     */
    protected function addRuleToStack(Rule $rule, array &$stack)
    {
        if(!in_array($rule, $stack)) {
            array_push($stack, $rule);
        }

        return $this;
    }

    /**
     * Adds regular expression generation rules to the specified stack.
     *
     * @param array $rules
     * @param array &$stack
     *
     * @return Config
     *
     * @throws \InvalidArgumentException
     */
    protected function addRulesToStack(array $rules, array &$stack)
    {
        foreach($rules as $key => $rule) {
            if(!($rule instanceof Rule)) {
                throw new \InvalidArgumentException(sprintf(
                    'Invalid rule at key "%s". Expected instance of Rule.',
                    $key
                ));
            }
        }

        foreach($rules as $rule) {
            $this->addRuleToStack($rule, $stack);
        }

        return $this;
    }

    /**
     * Sets the regular expression generation rules for the specified stack.
     *
     * @param array $rules
     * @param array &$stack
     *
     * @return Config
     *
     * @throws \InvalidArgumentException
     */
    protected function setRulesToStack(array $rules, array &$stack)
    {
        foreach($rules as $key => $rule) {
            if(!($rule instanceof Rule)) {
                throw new \InvalidArgumentException(sprintf(
                    'Invalid rule at key "%s". Expected instance of Rule.',
                    $key
                ));
            }
        }

        $stack = array();

        foreach($rules as $rule) {
            $this->addRuleToStack($rule, $stack);
        }

        return $this;
    }

    /**
     * Adds a word to the list of safe/whitelisted words.
     *
     * @param string|Word $word
     * 
     * @return Config
     */
    public function addWhitelistedWord($word)
    {
        if($word instanceof Word) {
            $word = $word->getWord();
        }

        if(!(is_string($word) && mb_strlen(trim($word)) > 0)) {
            throw new \InvalidArgumentException(
                'Invalid whitelist word. Expected non-empty string or instance of Word.'
            );
        }

        if(!in_array($word, $this->whitelist)) {
            array_push($this->whitelist, mb_strtolower(trim($word)));
        }

        return $this;
    }

    /**
     * Adds words to the list of safe/whitelisted words.
     *
     * @param array $words
     *
     * @return Config
     *
     * @throws \InvalidArgumentException
     */
    public function addWhitelistedWords(array $words)
    {
        $currentWhitelist = $this->whitelist;

        try {
            foreach($words as $key => $word) {
                $this->addWhitelistedWord($word);
            }
        }
        catch(\InvalidArgumentException $e) {
            $this->whitelist = $currentWhitelist;
            throw new \InvalidArgumentException(sprintf(
                'Invalid whitelist word at key "%s". Expected non-empty string or instance of Word.',
                $key
            ));
        }

        return $this;
    }

    /**
     * Gets the list of safe/whitelisted words.
     *
     * @return array
     */
    public function getWhitelistedWords()
    {
        return $this->whitelist;
    }

    /**
     * Sets the list of safe/whitelisted words.
     *
     * @param array $words
     *
     * @return Config
     *
     * @throws \InvalidArgumentException
     */
    public function setWhitelistedWords(array $words)
    {
        $currentWhitelist = $this->whitelist;
        $this->whitelist = array();

        try {
            foreach($words as $key => $word) {
                $this->addWhitelistedWord($word);
            }
        }
        catch(\InvalidArgumentException $e) {
            $this->whitelist = $currentWhitelist;
            throw new \InvalidArgumentException(sprintf(
                'Invalid whitelist word at key "%s". Expected non-empty string or instance of Word.',
                $key
            ));
        }

        return $this;
    }

    /**
     * Applies the regular expression generation rules to the word.
     *
     * @param Word $word
     * 
     * @return string
     */
    public function applyRulesToWord(Word $word)
    {
        $regExp = addslashes($word->getWord());
        $rules = array_merge(
            $this->getPreRules(),
            $this->getRules(),
            $this->getPostRules()
        );

        foreach($rules as $rule) {
            $regExp = $rule->apply($regExp, $word);
        }

        return $regExp;
    }
}