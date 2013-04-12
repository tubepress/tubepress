<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Basic cache functionality.
 */
class tubepress_impl_cache_PoolDecorator implements ehough_stash_PoolInterface
{
    /**
     * @var ehough_stash_PoolInterface
     */
    private $_delegate;

    public function __construct(ehough_stash_PoolInterface $delegate)
    {
        $this->_delegate = $delegate;
    }

    /**
     * Returns objects which implement the Cache\Item interface.
     *
     * Provided key must be unique for each item in the cache. Implementing
     * Libraries are responsible for any encoding or escaping required by their
     * backends, but must be able to supply the original key if needed. Keys
     * should not contain the special characters listed:
     *  {}()/\@
     *
     * @param string $key
     *
     * @return ehough_stash_ItemInterface
     */
    public function getItem($key)
    {
        return $this->_toDecoratedItem($this->_delegate->getItem($key));
    }

    /**
     * Returns a group of cache objects as an Iterator
     *
     * Bulk lookups can often by streamlined by backend cache systems. The
     * returned iterator will contain a Cache\Item for each key passed.
     *
     * @param array $keys
     * @return Iterator
     */
    public function getItemIterator($keys)
    {
        $items = array();

        foreach ($keys as $key) {

            $items[] = $this->_toDecoratedItem($this->getItem($key));
        }

        return new ArrayIterator($items);
    }

    /**
     * Clears the cache pool of all items.
     *
     * @return bool
     */
    public function clear()
    {
        return $this->_delegate->clear();
    }

    private function _toDecoratedItem(ehough_stash_ItemInterface $item)
    {
        return new tubepress_impl_cache_ItemDecorator($item, $this);
    }
}