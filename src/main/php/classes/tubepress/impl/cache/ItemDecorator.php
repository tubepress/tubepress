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
 * Decorates ehough_stash_Item to handle cache cleaning and TTL.
 */
class tubepress_impl_cache_ItemDecorator implements ehough_stash_ItemInterface
{
    /**
     * @var ehough_stash_ItemInterface
     */
    private $_delegate;

    /**
     * @var ehough_stash_PoolInterface
     */
    private $_parentCache;

    public function __construct(ehough_stash_ItemInterface $delegate, ehough_stash_PoolInterface $parentCache)
    {
        $this->_delegate    = $delegate;
        $this->_parentCache = $parentCache;
    }

    /**
     * Returns the key for the current cache item.
     *
     * The key is loaded by the Implementing Library, but should be available to
     * the higher level callers when needed.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->_delegate->getKey();
    }

    /**
     * Retrieves the item from the cache associated with this objects key.
     *
     * Value returned must be identical to the value original stored by set().
     *
     * If the cache is empty then null should be returned. However, null is also
     * a valid cache item, so the isMiss function should be used to check
     * validity.
     *
     * @return mixed
     */
    public function get()
    {
        return $this->_delegate->get();
    }

    /**
     * Stores a value into the cache.
     *
     * The $value argument can be any item that can be serialized by PHP,
     * although the method of serialization is left up to the Implementing
     * Library.
     *
     * The $ttl can be defined in a number of ways. As an integer or
     * DateInverval object the argument defines how long before the cache should
     * expire. As a DateTime object the argument defines the actual expiration
     * time of the object. Implementations are allowed to use a lower time than
     * passed, but should not use a longer one.
     *
     * If no $ttl is passed then the item can be stored indefinitely or a
     * default value can be set by the Implementing Library.
     *
     * Returns true if the item was successfully stored.
     *
     * @param mixed                     $value
     * @param int|DateInterval|DateTime $ttl
     *
     * @return bool
     */
    public function set($value = null, $ttl = null)
    {
        $context        = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $cleaningFactor = $context->get(tubepress_api_const_options_names_Cache::CACHE_CLEAN_FACTOR);
        $cleaningFactor = intval($cleaningFactor);

        /**
         * Handle cleaning factor.
         */
        if ($cleaningFactor > 0 && rand(1, $cleaningFactor) === 1) {

            $this->_parentCache->clear();
        }

        if ($ttl === null) {

            $ttl = intval($context->get(tubepress_api_const_options_names_Cache::CACHE_LIFETIME_SECONDS));
        }

        return $this->_delegate->set($value, $ttl);
    }

    /**
     * Validates the current state of the item in the cache.
     *
     * Checks the validity of a cache result. If the object is good (is not a
     * miss, and meets all the standards set by the Implementing Library) then
     * this function returns true.
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->_delegate->isValid();
    }

    /**
     * Removes the current key from the cache.
     *
     * Returns true if the item is no longer present (either because it was
     * removed or was not present to begin with).
     *
     * @return bool
     */
    public function remove()
    {
        return $this->_delegate->remove();
    }
}