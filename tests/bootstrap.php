<?php

/*
 * This file is part of the Badwords PHP package.
 *
 * (c) Stephen Melrose <me@stephenmelrose.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

spl_autoload_register(function($class)
{
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $folders = array('src', 'tests');
    foreach($folders as $folder)
    {
        $fullPath = __DIR__.'/../'.$folder.'/'.$path.'.php';
        if(file_exists($fullPath))
        {
            require_once($fullPath);
            return true;
        }
    }
});