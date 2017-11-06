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
 * Loads and formats a list of words from a CSV file.
 *
 * @author Stephen Melrose <me@stephenmelrose.co.uk>
 */
class Csv extends AbstractFile
{
    protected function getFileType()
    {
        return 'csv';
    }

    /**
     * Loads the words data from the source CSV file.
     *
     * @return array
     *
     * @throws Exception
     */
    protected function loadWordsDataFromSource()
    {
        $handle = fopen($this->getPath(), 'r');
        if($handle === false) {
            throw new Exception('Error. CSV file not could be opened.');
        }

        $row = 0;
        $wordsData = array();

        while(($rowData = fgetcsv($handle, 1024, ',')) !== false) {

            $row++;

            try {
                $rowData = $this->validateAndCleanWordData($rowData);
            }
            catch(Exception $e) {
                throw new Exception(sprintf(
                    'Invalid word data detected in CSV file on row %s. %s',
                    $row,
                    $e->getMessage()
                ));
            }

            array_push($wordsData, $rowData);
        }

        return $wordsData ?: null;
    }
    
    /**
     * Validates and cleans the word data from the CSV file.
     * 
     * @param array $wordData
     * 
     * @return boolean
     *
     * @throws Exception
     */
    protected function validateAndCleanWordData(array $wordData)
    {
        $wordData = array_values($wordData);

        if(!(isset($wordData[0]) &&
            is_string($wordData[0]) &&
            mb_strlen(trim($wordData[0])) > 0)
        ) {
            throw new Exception('Expected word in column 1 to be non-empty string.');
        }

        $allowedBooleanValues = array(true, false, 1, 0, '1', '0');

        if(isset($wordData[1])) {
            if(!in_array($wordData[1], $allowedBooleanValues, true)) {
                throw new Exception(sprintf(
                    'Expected must start word "%s" in column 2 to be either 1, 0, or to be omitted.',
                    $wordData[1]
                ));
            } else {
                $wordData[1] = (bool) $wordData[1];
            }
        }

        if(isset($wordData[2])) {
            if(!in_array($wordData[2], $allowedBooleanValues, true)) {
                throw new Exception(sprintf(
                    'Expected must end word "%s" in column 2 to be either 1, 0, or to be omitted.',
                    $wordData[2]
                ));
            } else {
                $wordData[2] = (bool) $wordData[2];
            }
        }

        return $wordData;
    }
}