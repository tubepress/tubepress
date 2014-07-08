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

/**
 * A collection of strings that need translations.
 */
abstract class tubepress_test_app_translation_support_AbstractTranslation
{
    /**
     * @var string
     */
    private $_name;

    /**
     * @string[]
     */
    private $_stringCache;

    public function __construct($name)
    {
        $this->_name = $name;
    }

    public final function getStrings()
    {
        if (!isset($this->_stringCache)) {

            $this->_stringCache = $this->fetchStrings();
        }

        return $this->_stringCache;
    }

    protected abstract function fetchStrings();

    public function getName()
    {
        return $this->_name;
    }
}
