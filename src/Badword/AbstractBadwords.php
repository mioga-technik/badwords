<?php

namespace Badwords;

use Badwords\Dictionary\PhpArray;
use Badwords\Filter\Config;
use Badwords\Filter\Config\Standard;

/**
 * Class AbstractBadwords
 * @package Badword
 */
abstract class AbstractBadwords
{
    /**
     * Rule Identifier
     *
     * @var int
     */
    private static $ID = 1;

    /**
     * @var array
     */
    protected $rules;

    /**
     * @var Dictionary
     */
    protected $dictionaries;

    /**
     * @var Config
     */
    protected $Config;

    /**
     * @var Filter
     */
    protected $Filter;

    public function __construct(array $dictionaries, $config=null)
    {
        $this->create($config);
        $this->registerCollections($dictionaries);
        $this->registerFilter();
    }

    private function create($config) {
        if ($config !== null) {
            $this->Config = $config;
        }

        $this->Config = new Standard();

        return true;
    }

    /**
     * @param $dictionaries
     * @return bool
     */
    private function registerCollections($dictionaries) {
        foreach ($dictionaries as $rule => $dictionary) {
            $this->rules[$rule] = self::$ID;
            $this->dictionaries[self::$ID] = new PhpArray($dictionary, self::$ID);
            self::$ID++;
        }

        return true;
    }

    /**
     * @return Dictionary
     */
    protected function getCollections() {
        return $this->dictionaries;
    }

    /**
     * Register Filter for Class
     */
    private function registerFilter() {
        $this->Filter = new Filter($this->getCollections(), $this->Config);
    }

    /**
     * @return array
     */
    public function getRules() {
        return $this->rules;
    }

}