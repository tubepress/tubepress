<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_test_integration_mocks_MockCacheDriver implements ehough_stash_interfaces_DriverInterface
{
    private static $_CACHE_MAP = array(

        'http:~~gdata.youtube.com~feeds~api~standardfeeds~most_popular?time=today&v=2&key=ai39si5uuzupiqw9bpzgqzrrhvqf3vbgrql-i_28g1zwozmdnjlskzmdqehpz-l2rqgf_6cnwool96ojzrrqko-ej9qo_qppmg&start-index=1&max-results=20&orderby=viewcount&safesearch=none&format=5' =>
        'youtube/most_popular-1.xml',
        'http:~~gdata.youtube.com~feeds~api~standardfeeds~most_popular?time=today&v=2&key=ai39si5uuzupiqw9bpzgqzrrhvqf3vbgrql-i_28g1zwozmdnjlskzmdqehpz-l2rqgf_6cnwool96ojzrrqko-ej9qo_qppmg&start-index=1&max-results=13&orderby=viewcount&safesearch=none&format=5' =>
        'youtube/most_popular-today-13max.xml'
    );

    /**
     * Takes an array which is used to pass option values to the driver. As this is the only required function that is
     * used specifically by the developer is is where any engine specific options should go. An engine that requires
     * authentication information, as an example, should get them here.
     *
     * @param array $options
     */
    public function setOptions(array $options = array())
    {
        //nada
    }

    /**
     * Returns the previously stored data as well as it's expiration date in an associative array. This array contains
     * two keys- a 'data' key and an 'expiration' key. The 'data' key should be exactly the same as the value passed to
     * storeData.
     *
     * @param  array $key
     *
     * @return array
     */
    public function getData($key)
    {
        if (!isset(self::$_CACHE_MAP[$key[2]])) {

            throw new RuntimeException('Cache key ' . $key[2] . ' not found');
        }

        return array(

            'data'       => array('return' => file_get_contents(__DIR__ . '/../../../../../../../../../network-responses/' . self::$_CACHE_MAP[$key[2]])),
            'expiration' => 999999999999999
        );
    }

    /**
     * Takes in data from the exposed Stash class and stored it for later retrieval.
     *
     * *The first argument is an array which should map to a specific, unique location for that array, This location
     * should also be able to handle recursive deletes, where the removal of an item represented by an identical, but
     * truncated, key causes all of the 'children' keys to be removed.
     *
     * *The second argument is the data itself. This is an array which contains the raw storage as well as meta data
     * about the data. The meta data can be ignored or used by the driver but entire data parameter must be retrievable
     * exactly as it was placed in.
     *
     * *The third parameter is the expiration date of the item as a timestamp. This should also be stored, as it is
     * needed by the getData function.
     *
     * @param  array $key
     * @param  mixed $data
     * @param  int   $expiration
     *
     * @return bool
     */
    public function storeData($key, $data, $expiration)
    {
        return true;
    }

    /**
     * Clears the cache tree using the key array provided as the key. If called with no arguments the entire cache gets
     * cleared.
     *
     * @param  null|array $key
     *
     * @return bool
     */
    public function clear($key = null)
    {
        //nada
    }

    /**
     * Removed any expired code from the cache. For same drivers this can just return true, as their underlying engines
     * automatically take care of time based expiration (apc or memcache for example). This function should also be used
     * for other clean up operations that the specific engine needs to handle. This function is generally called outside
     * of user requests as part of a maintenance check, so it is okay if the code in this function takes some time to
     * run,
     *
     * @return bool
     */
    public function purge()
    {
        //nada
    }

    /**
     * Returns whether the driver is able to run in the current environment or not. Any system checks - such as making
     * sure any required extensions are missing - should be done here. This is a general check; if any instance of this
     * driver can be used in the current environment it should return true.
     *
     * @return bool
     */
    public static function isAvailable()
    {
        return true;
    }
}