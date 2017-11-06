<?php

/*
 * This file is part of the Badwords PHP package.
 *
 * (c) Stephen Melrose <me@stephenmelrose.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Badwords\Cache;

/**
 * Cache class that mimics cache interaction, but does nothing.
 *
 * @author Stephen Melrose <me@stephenmelrose.co.uk>
 */
class None extends AbstractCache
{
    public function get($key, $default = null)
    {
        return $default;
    }

    public function has($key)
    {
        return false;
    }

    public function set($key, $data, $lifetime = null)
    {
        return true;
    }

    public function remove($key)
    {
        return true;
    }
}