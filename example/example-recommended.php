<?php

/*
 * This file is part of the Badwords PHP package.
 *
 * (c) Stephen Melrose <me@stephenmelrose.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}
$defaultContent = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque congue luctus enim, porttitor rhoncus risus tempor at. Maecenas volutpat congue neque, eu placerat sapien rutrum sed. Pellentesque adipiscing, sem nec dictum ultrices, mauris metus malesuada risus, ac sagittis metus velit a nunc.';


if(isset($_POST['content']))
{
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

    $content = $_POST['content'];
    $result = $Badwords->Filter()->filter($content);

}

?>
<!DOCTYPE html> 
<html> 
    <head> 
        <meta charset='utf-8' />
        <title>Badwords PHP Example</title>
        <link href="http://fonts.googleapis.com/css?family=Ubuntu:regular,bold&v1" rel="stylesheet" type="text/css" />
        <link href="example.css" rel="stylesheet" type="text/css" media="screen" /> 
    </head>
    <body>
        
        <h1>Badwords PHP Example</h1>
        
        <form action="" method="post">
            <dl>
                <dt><label for="content">Content</label></dt>
                <dd><textarea id="content" name="content"><?php echo isset($content) ? htmlentities($content, null, 'utf-8') : $defaultContent; ?></textarea></dd>
            </dl>
            <input type="submit" value="Filter" />
        </form>
        
        <?php if(isset($result)): ?>
        
            <h2>Result</h2>
            
            <?php if(!$result->isClean()): ?>
                <p>
                    Status: <strong>NOT Clean</strong><br />
                    Risk Level: <strong><?php echo $result->getRiskLevel() === 2 ? 'Reject' : 'Moderate'; ?></strong><br />
                    No. of Matches: <strong><?php echo count($result->getMatches()); ?></strong>
                </p>
            <?php else: ?>
                <p>Status: <strong>Clean</strong></p>
            <?php endif; ?>

            <div class="result">
                <?php echo nl2br($result->getHighlightedContent()); ?>
                <?php echo "<pre>"; print_r($result->getMatchesAndRiskLevels()); echo "</pre>"; ?>
            </div>
        
        <?php endif; ?>
        
    </body>
</html>