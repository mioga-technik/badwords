<?php

/*
 * This file is part of the Badwords PHP package.
 *
 * (c) Stephen Melrose <me@stephenmelrose.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Badwords;

/**
 * Defines the interface for reading and storing data from a cache.
 *
 * @author Stephen Melrose <me@stephenmelrose.co.uk>
 */
interface Cache
{
    /**
     * Gets data from the cache.
     *
     * @param string $key Unique identifier for the cached data.
     * @param mixed $default The default value to return if there is no valid cache.
     *
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Determines if there is valid cached data for a given key.
     *
     * @param string $key Unique identifier for the cached data.
     *
     * @return boolean
     */
    public function has($key);

    /**
     * Adds data to the cache.
     *
     * @param string $key Unique identifier for the cached data.
     * @param mixed $data The data to store.
     * @param integer $lifetime The amount of time the data should be stored.
     *
     * @return boolean
     */
    public function set($key, $data, $lifetime = null);

    /**
     * Removes data from the cache.
     *
     * @param string $key Unique identifier for the cached data.
     *
     * @return boolean
     */
    public function remove($key);
}