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
class tubepress_app_options_ui_impl_fields_GallerySourceField extends tubepress_app_options_ui_impl_fields_AbstractOptionsPageField
{
    public function __construct(tubepress_lib_translation_api_TranslatorInterface $translator,
                                tubepress_app_options_api_PersistenceInterface    $persistence,
                                tubepress_app_http_api_RequestParametersInterface $requestParams)
    {
        parent::__construct(tubepress_app_media_provider_api_Constants::OPTION_GALLERY_SOURCE, $translator, $persistence, $requestParams);
    }

    /**
     * Invoked when the element is submitted by the user.
     *
     * @return string|null A string error message to be displayed to the user, or null if no problem.
     */
    public function onSubmit()
    {
        $fieldName = tubepress_app_media_provider_api_Constants::OPTION_GALLERY_SOURCE;

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