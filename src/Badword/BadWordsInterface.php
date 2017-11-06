<?php

namespace Badwords;

use Badwords\Filter\Config;

/**
 * Connection BadWordsInterface
 * @package Badword
 */
interface BadWordsInterface
{
    /**
     * BadWordsInterface constructor.
     * @param array $dictionaries
     * @param Config $config
     */
    public function __construct(array $dictionaries, $config=null);

    /**
     * @return array
     */
    public function getRules();

    /**
     * @return Filter
     */
    public function Filter();
}