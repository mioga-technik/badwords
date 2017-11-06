<?php
/**
 * Created by PhpStorm.
 * User: MIOGA_PC
 * Date: 04.04.2017
 * Time: 10:47
 */

namespace Badwords\Dictionary;

use Badwords\Cache;

/**
 * Loads and formats a list of words from a PHP Array.
 *
 * @author Brian Schäffner <brian.s@mioga.de>
 */
class PhpArray extends AbstractDictionary
{
    /**
     * @var array
     */
    protected $array;

    protected function getFileType()
    {
        return 'array';
    }

    /**
     * Constructor.
     *
     * @param array $array The path to the source file.
     * @param integer $riskLevel The level of risk associated with the bad words.
     * @param Cache $cache The caching mechanism to use.
     */
    public function __construct(array $array, $riskLevel = null, Cache $cache = null)
    {
        parent::__construct($riskLevel, $cache);
        $this->setArray($array);
    }

    /**
     * Gets the path to the source file.
     *
     * @return array
     */
    public function getArray()
    {
        return $this->array;
    }

    /**
     * Sets the path to the source file.
     *
     * @param array $array
     *
     * @return AbstractDictionary
     *
     * @throws \InvalidArgumentException When the path is invalid.
     */
    public function setArray(array $array)
    {
        if($array !== $this->getArray()) {
            $this->clearWords();
        }

        $this->array = $array;

        return $this;
    }

    /**
     * Gets the unique ID for the Dictionary.
     *
     * @return string
     */
    public function getId()
    {
        $words = implode(",", $this->getArray());

        return $this->getFileType() . '_' . md5(
                $words . ';' .
                $this->getMustStartWordDefault() . ';' .
                $this->getMustEndWordDefault()
            );
    }

    /**
     * Loads the words data from the PHP file.
     *
     * @return array
     *
     * @throws Exception
     */
    protected function loadWordsDataFromSource()
    {

        $data = $this->getArray();
        $wordsData = array();

        foreach($data as $key => $wordData) {

            try {
                $wordData = $this->validateAndCleanWordData($wordData);
            }
            catch(Exception $e) {
                throw new Exception(sprintf(
                    'Invalid word data detected in PHP file at key "%s". %s',
                    $key,
                    $e->getMessage()
                ));
            }

            array_push($wordsData, $wordData);
        }

        return $wordsData;
    }

    /**
     * Validates and cleans the word data from the PHP file.
     *
     * @param array $wordData
     *
     * @return boolean
     *
     * @throws Exception
     */
    protected function validateAndCleanWordData($wordData)
    {
        if(is_string($wordData)) {
            $wordData = array($wordData);
        }

        if(!is_array($wordData)) {
            throw new Exception('Expected word data be an array or string.');
        }

        $wordData = array_values($wordData);

        if(!(isset($wordData[0]) &&
            is_string($wordData[0]) &&
            mb_strlen(trim($wordData[0])) > 0)
        ) {
            throw new Exception(
                'Expected first value "word" to be non-empty string.'
            );
        }

        if(isset($wordData[1]) && !is_bool($wordData[1])) {
            throw new Exception(
                'Expected second value "must start word" to be a boolean or omitted.'
            );
        }

        if(isset($wordData[2]) && !is_bool($wordData[2])) {
            throw new Exception(
                'Expected third value "must end word" to be a boolean or omitted.'
            );
        }

        return $wordData;
    }
}