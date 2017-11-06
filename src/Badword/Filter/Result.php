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

use Badwords\Dictionary;

/**
 * Contains result data from a filter execution.
 *
 * @author Stephen Melrose <me@stephenmelrose.co.uk>
 */
class Result
{
    /**
     * @var string
     */
    protected $content;

    /**
     * @var string
     */
    protected $highlightedContentBadwordClass = 'badword';

    /**
     * @var string
     */
    protected $highlightedContentRiskLevelClassSuffix = 'risk-level-';

    /**
     * @var array
     */
    protected $matches;

    /**
     * @var array
     */
    protected $riskLevels;

    /**
     * Constructor.
     *
     * @param string $content The content that was filtered.
     * @param array $matches The matches found in the content suspected of being bad words.
     * @param array $riskLevels The risk levels for each Dictionary used to filter the content with.
     */
    public function __construct($content, array $matches, array $riskLevels)
    {
        $this->content = $content;
        $this->matches = $matches;
        $this->riskLevels = $riskLevels;
    }

    /**
     * Gets the content that was filtered.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Gets the matches found in the content suspected of being bad words.
     *
     * @return array
     */
    public function getMatches()
    {
        $matches = array();

        foreach($this->matches as $dictionaryMatches) {
            $matches = array_merge($matches, $dictionaryMatches);
        }

        return array_values(array_unique($matches));
    }

    /**
     * Gets the matches found in the content suspected of being
     * bad words and their specific risk levels.
     * 
     * @return array
     */
    public function getMatchesAndRiskLevels()
    {
        $matchesAndRisk = array();

        foreach($this->matches as $dictionaryId => $dictionaryMatches) {
            foreach($dictionaryMatches as $match) {
                if(!isset($matchesAndRisk[$match])) {
                    $matchesAndRisk[$match] = null;
                }
                if(isset($this->riskLevels[$dictionaryId]) &&
                   $this->riskLevels[$dictionaryId] !== null
                ) {
                    $matchesAndRisk[$match] =
                        $this->riskLevels[$dictionaryId] > $matchesAndRisk[$match] ?
                            $this->riskLevels[$dictionaryId] :
                            $matchesAndRisk[$match];
                }
            }
        }

        return $matchesAndRisk;
    }

    /**
     * Gets the matches for a specific dictionary found in the
     * content suspected of being bad words.
     *
     * @param Dictionary $dictionary
     * 
     * @return array
     */
    public function getDictionaryMatches(Dictionary $dictionary)
    {
        return isset($this->matches[$dictionary->getId()]) ?
            $this->matches[$dictionary->getId()] : array();
    }

    /**
     * Gets the maximum level of risk the content poses. The greater
     * the number, the greater the risk. 0 is considered no risk.
     *
     * @return integer
     */
    public function getRiskLevel()
    {
        $riskLevel = null;

        foreach($this->matches as $dictionaryId => $dictionaryMatches) {
            if(isset($this->riskLevels[$dictionaryId]) &&
                $this->riskLevels[$dictionaryId] !== null
            ){
                $riskLevel = $this->riskLevels[$dictionaryId] > $riskLevel ?
                    $this->riskLevels[$dictionaryId] : $riskLevel;
            }
        }

        return $riskLevel;
    }

    /**
     * Determines if the content is clean or not, a.k.a. has any matches.
     *
     * @return boolean
     */
    public function isClean()
    {
        return count($this->getMatches()) === 0;
    }
    
    /**
     * Gets the CSS class used when highlighting suspected bad words in content.
     *
     * @return string
     */
    protected function getHighlightedContentBadwordClass()
    {
        return $this->highlightedContentBadwordClass;
    }

    /**
     * Sets the CSS class used when highlighting suspected bad words in content.
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function setHighlightedContentBadwordClass($class)
    {
        if(!(is_string($class) && mb_strlen(trim($class)) > 0)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid highlight CSS class "%s". Expected non-empty string.',
                $class
            ));
        }

        $this->highlightedContentBadwordClass = trim($class);
        return $this;
    }

    /**
     * Gets the CSS class suffix used to highlight the risk level
     * when highlighting suspected bad words in content.
     *
     * @return string
     */
    protected function getHighlightedContentRiskLevelClassSuffix()
    {
        return $this->highlightedContentRiskLevelClassSuffix;
    }

    /**
     * Sets the CSS class suffix used to highlight the risk level
     * when highlighting suspected bad words in content.
     *
     * @param string $class
     *
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    public function setHighlightedContentRiskLevelClassSuffix($class)
    {
        if(!(is_string($class) && mb_strlen(trim($class)) > 0)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid highlight risk level CSS class suffix "%s". Expected non-empty string.',
                $class
            ));
        }

        $this->highlightedContentRiskLevelClassSuffix = trim($class);
        return $this;
    }

    /**
     * Gets the content that was filtered with suspected bad words
     * highlighted using <span>'s.
     *
     * @return string
     */
    public function getHighlightedContent()
    {
        $content = htmlentities($this->getContent());
        $replacements = array();

        foreach($this->getMatchesAndRiskLevels() as $match => $riskLevel) {

            $replacement = sprintf(
                '<span class="%s%s">%s</span>',
                $this->getHighlightedContentBadwordClass(),
                ($riskLevel !== null ? ' ' . $this->getHighlightedContentRiskLevelClassSuffix() . $riskLevel : null),
                $match
            );
            
            $placeholder = '{#{#{'.count($replacements).'}#}#}';
            
            $replacements[$placeholder] = $replacement;
            $content = preg_replace('/' . $match . '/iu', $placeholder, $content);
        }
        
        foreach($replacements as $placeholder => $replacement) {
            $content = str_replace($placeholder, $replacement, $content);
        }

        return $content;
    }
}