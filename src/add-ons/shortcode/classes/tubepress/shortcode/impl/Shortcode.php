<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 *
 *
 * Based on some work from https://github.com/thunderer/Shortcode.
 *
 * Original copyright follows:
 *
 * Copyright (c) 2015 Tomasz Kowalczyk
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

class tubepress_shortcode_impl_Shortcode implements tubepress_api_shortcode_ShortcodeInterface
{
    /**
     * @var string
     */
    private $_name;

    /**
     * @var array
     */
    private $_attributes;

    /**
     * @var string
     */
    private $_innerContent;

    public function __construct($name, $attributes = array(), $innerContent = null)
    {
        if (!is_string($name)) {

            throw new InvalidArgumentException('Shortcode name must be a string');
        }

        /*
         * Ensure no leading or trailing spaces around the name.
         */
        $name = trim($name);

        if (preg_match_all('/^[0-9a-zA-Z_-]{1,50}$/', $name, $matches) !== 1) {

            throw new InvalidArgumentException('Invalid shortcode name');
        }

        if (!is_array($attributes)) {

            throw new InvalidArgumentException('Shortcode attributes must be an array');
        }

        if (!empty($attributes) && count(array_filter(array_keys($attributes), 'is_string')) !== count($attributes)) {

            throw new InvalidArgumentException('Shortcode attributes must be an associative array');
        }

        if ($innerContent !== null && !is_string($innerContent)) {

            throw new InvalidArgumentException('Inner content must either be null or a string');
        }

        $this->_name         = $name;
        $this->_attributes   = $attributes;
        $this->_innerContent = $innerContent;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return $this->_attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function getInnerContent()
    {
        return $this->_innerContent;
    }
}
