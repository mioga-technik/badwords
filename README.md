Badwords PHP
============

Badwords PHP is **small lightweight PHP library** for detecting "bad" words, e.g. profanity, in content.

Aside from the obvious matching if a word is present in a string, the filter also tries to detect words similar to those in the list, e.g. `gl@d` and `glad`.

The library is designed to be **highly configurable**, from the word lists used to the character replacement configuration at the heart of the filter.

**Note:** At present the default configuration provided is **not** a bulletproof/catch-all solution, but it will catch most variations. This will become more robust over time.

Requirements
------------

* The library is only supported on PHP 5.3.0 and up.
* Composer is required.

Installation
------------

To install include it in your projects's `composer.json`.

```bash
    $ composer require mioga-brian/badwords-php
    $ composer update
```

There are no additional dependencies required for this package to work.

Usage
-----
### File Method:

The simplest way to use the library is as follows,

```PHP
    $dictionary = new \Badword\Dictionary\Php('path/to/dictionary_list.php');
    $config = new \Badword\Filter\Config\Standard();
    $filter = new \Badword\Filter($dictionary, $config);

    $result = $filter->filter('My content...');
    $result->getRiskLevel();
    $result->getMatches();
    $result->getMatchesAndRiskLevels();
    $result->getHighlightedContent();
```

Explained,

* First load your list of "bad" words using the `Dictionary` objects, or create your own and implement the `Dictionary` interface.
* Define a configuration for the filter to use (a default `Standard` configuration is provided).
* Create the `Filter` passing your dictionary(s) and config.
* Filter your content using the `filter()` method.
* Use the `Result` object to analyse your content.

### Array Method:

```PHP
    // An example moderate dictionary.
    $dictionaryWords = array(
        'some',
        'bad',
        'words'
    );

    $dictionary = new \Badword\Dictionary\PhpArray($dictionaryWords, 1);
    $config = new \Badword\Filter\Config\Standard();
    $filter = new \Badword\Filter($dictionary, $config);

    $result = $filter->filter('My content...');
    $result->getRiskLevel();
    $result->getMatches();
    $result->getMatchesAndRiskLevels();
    $result->getHighlightedContent();
```

### Object Layer Method [new]:

```PHP
   $dictionaryWords = array(
        'reject' => array(
            'maecenas',
            'mauris',
            'luctus'
        ),
        'moderate' => array(
            'consectetur',
            'neque',
            'velit'
        )
   );
    
   $Badwords = new \Badword\Badwords($dictionaryWords);
    
   $result = $Badwords->Filter()->filter('My content...'));
   $result->getRiskLevel();
   $result->getMatches();
   $result->getMatchesAndRiskLevels();
   $result->getHighlightedContent();
```

Testing
-------
To run the unit tests on this package, simply run `vendor/bin/phpunit` from the package directory.

Credits
-------
* Update Repository and push it to packagist by [Brian Schäffner](https://github.com/mioga-brian).
* Written and developed by [Stephen Melrose](http://twitter.com/stephenmelrose).
* Original concept by [Paul Lemon](http://twitter.com/anthonylime).
