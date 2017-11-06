<?php

namespace Badwords;

/**
 * Class Badwords
 * @package Badword
 */
class Badwords extends AbstractBadwords implements BadWordsInterface
{
    public function __construct(array $dictionaries, $config=null)
    {
        parent::__construct($dictionaries, $config);
    }

    /**
     * @return Filter
     */
    public function Filter() {
        return $this->Filter;
    }

    /**
     * @return Dictionary
     */
    public function Collection() {
        return $this->getCollections();
    }

    /**
     * @return array
     */
    public function Rules() {
        return $this->getRules();
    }
}