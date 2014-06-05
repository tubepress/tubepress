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
 * Base class for HTML fields.
 */
abstract class tubepress_core_options_ui_impl_fields_AbstractOptionsPageField extends tubepress_core_options_ui_impl_BaseElement implements tubepress_core_options_ui_api_FieldInterface
{
    /**
     * @var string Translated description.
     */
    private $_untranslatedDescription;

    /**
     * @var tubepress_core_options_api_PersistenceInterface
     */
    private $_persistence;

    /**
     * @var tubepress_core_http_api_RequestParametersInterface
     */
    private $_httpRequestParameters;

    public function __construct($id,
                                tubepress_core_translation_api_TranslatorInterface $translator,
                                tubepress_core_options_api_PersistenceInterface    $persistence,
                                tubepress_core_http_api_RequestParametersInterface $requestParameters,
                                $untranslatedDisplayName = null,
                                $untranslatedDescription = null)
    {
        parent::__construct($id, $translator, $untranslatedDisplayName);

        $this->_persistence             = $persistence;
        $this->_httpRequestParameters   = $requestParameters;
        $this->_untranslatedDescription = $untranslatedDescription;
    }

    /**
     * @return string The optional description of this element that is displayed to the user. May be empty or null.
     *
     * @api
     * @since 4.0.0
     */
    public function getTranslatedDescription()
    {
        if (!isset($this->_untranslatedDescription)) {

            return '';
        }

        return $this->getModifiedDescription($this->translate($this->_untranslatedDescription));
    }

    /**
     * @param string $name  The option name.
     * @param string $value The option value.
     *
     * @return string|null Null if stored successfully, otherwise a string error message.
     */
    protected function sendToStorage($name, $value)
    {
        return $this->_persistence->queueForSave($name, $value);
    }

    /**
     * Override point.
     *
     * Allows subclasses to further modify the description for this field.
     *
     * @param $originalDescription string The original description as calculated by AbstractField.php.
     *
     * @return string The (possibly) modified description for this field.
     */
    protected function getModifiedDescription($originalDescription)
    {
        //override point
        return $originalDescription;
    }

    /**
     * @return tubepress_core_options_api_PersistenceInterface
     */
    protected function getOptionPersistence()
    {
        return $this->_persistence;
    }

    /**
     * @return tubepress_core_http_api_RequestParametersInterface
     */
    protected function getHttpRequestParameters()
    {
        return $this->_httpRequestParameters;
    }
}