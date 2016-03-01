<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @api
 * @since 4.0.0
 */
class tubepress_api_options_listeners_PatternValidatingListener extends tubepress_api_options_listeners_AbstractValidatingListener
{
    /**
     * @var string
     */
    private $_pattern;

    /**
     * @var string
     */
    private $_errorTemplate;

    public function __construct($pattern, $errorTemplate,
                                tubepress_api_options_ReferenceInterface      $reference,
                                tubepress_api_translation_TranslatorInterface $translator)
    {
        parent::__construct($reference, $translator);

        $this->_pattern       = (string) $pattern;
        $this->_errorTemplate = (string) $errorTemplate;
    }

    /**
     * @param $optionName
     * @param $optionValue
     *
     * @return boolean
     */
    protected function isValid($optionName, $optionValue)
    {
        if (!is_scalar($optionValue)) {

            return false;
        }

        return preg_match_all($this->_pattern, (string) $optionValue, $matches) >= 1 &&
            $matches[0][0] === (string) $optionValue;
    }

    protected function getErrorMessageTemplate()
    {
        return $this->_errorTemplate;
    }
}