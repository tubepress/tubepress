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
 * Displays a drop-down input for the TubePress theme.
 */
class tubepress_core_impl_options_ui_fields_GallerySourceField extends tubepress_core_impl_options_ui_fields_AbstractOptionsPageField
{
    public function __construct(tubepress_core_api_translation_TranslatorInterface $translator,
                                tubepress_core_api_options_PersistenceInterface    $persistence,
                                tubepress_core_api_http_RequestParametersInterface $requestParams)
    {
        parent::__construct(tubepress_core_api_const_options_Names::GALLERY_SOURCE, $translator, $persistence, $requestParams);
    }

    /**
     * Invoked when the element is submitted by the user.
     *
     * @return string|null A string error message to be displayed to the user, or null if no problem.
     */
    public function onSubmit()
    {
        $fieldName = tubepress_core_api_const_options_Names::GALLERY_SOURCE;

        if (!$this->getHttpRequestParameters()->hasParam($fieldName)) {

            return null;
        }

        return $this->sendToStorage($fieldName, $this->getHttpRequestParameters()->getParamValue($fieldName));
    }

    /**
     * Gets whether or not this field is TubePress Pro only.
     *
     * @return boolean True if this field is TubePress Pro only. False otherwise.
     */
    public function isProOnly()
    {
        return false;
    }

    public function getWidgetHTML()
    {
        return '';
    }
}