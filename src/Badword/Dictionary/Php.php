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

/**
 * Loads and formats a list of words from a PHP file.
 *
 * @author Stephen Melrose <me@stephenmelrose.co.uk>
 */
class Php extends AbstractFile
{
    protected function getFileType()
    {
        return 'php';
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
        $includeFile = function($path) {

            ob_start();
            require($path);
            ob_end_clean();

            if(!(isset($words) && is_array($words))) {
                throw new Exception(
                    '"$words" variable could not be found or is not an array in the PHP file.'
                );
            }

            return $words;
        };

        $data = $includeFile($this->getPath());
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