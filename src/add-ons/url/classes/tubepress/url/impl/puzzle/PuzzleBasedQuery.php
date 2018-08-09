<?php
/*
 * Copyright 2006 - 2018 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_url_impl_puzzle_PuzzleBasedQuery implements tubepress_api_url_QueryInterface
{
    /**
     * @var puzzle_Query
     */
    private $_delegate;

    /**
     * @var bool
     */
    private $_isFrozen = false;

    public function __construct(puzzle_Query $delegate)
    {
        $this->_delegate = $delegate;
    }

    /**
     * {@inheritdoc}
     */
    public function add($key, $value)
    {
        $this->_assertNotFrozen();

        $this->_delegate->add($key, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->_assertNotFrozen();

        $this->_delegate->clear();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function filter($closure)
    {
        return $this->_delegate->filter($closure);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return $this->_delegate->get($key);
    }

    /**
     * {@inheritdoc}
     */
    public function getKeys()
    {
        return $this->_delegate->getKeys();
    }

    /**
     * {@inheritdoc}
     */
    public function hasKey($key)
    {
        return $this->_delegate->hasKey($key);
    }

    /**
     * {@inheritdoc}
     */
    public function hasValue($value)
    {
        return $this->_delegate->hasValue($value);
    }

    /**
     * {@inheritdoc}
     */
    public function map($closure, array $context = array())
    {
        return $this->_delegate->map($closure, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function merge($data)
    {
        $this->_assertNotFrozen();

        if ($data instanceof tubepress_api_url_QueryInterface) {

            $data = $data->toArray();
        }

        $this->_delegate->merge($data);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function overwriteWith($data)
    {
        $this->_assertNotFrozen();

        $this->_delegate->overwriteWith($data);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($key)
    {
        $this->_assertNotFrozen();

        $this->_delegate->remove($key);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function replace(array $data)
    {
        $this->_assertNotFrozen();

        $this->_delegate->replace($data);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        $this->_assertNotFrozen();

        $this->_delegate->set($key, $value);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setEncodingType($type)
    {
        $this->_assertNotFrozen();

        $this->_delegate->setEncodingType($type);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return $this->_delegate->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function toString()
    {
        return $this->__toString();
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->_delegate->__toString();
    }

    /**
     * {@inheritdoc}
     */
    public function freeze()
    {
        $this->_isFrozen = true;
    }

    /**
     * {@inheritdoc}
     */
    public function isFrozen()
    {
        return $this->_isFrozen;
    }

    private function _assertNotFrozen()
    {
        if ($this->_isFrozen) {

            throw new BadMethodCallException('Query is frozen');
        }
    }
}
