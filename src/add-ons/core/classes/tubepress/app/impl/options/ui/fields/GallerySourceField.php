<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
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
class tubepress_app_impl_options_ui_fields_GallerySourceField extends tubepress_app_impl_options_ui_fields_AbstractField
{
    public function __construct(tubepress_app_api_options_PersistenceInterface    $persistence,
                                tubepress_lib_api_http_RequestParametersInterface $requestParams)
    {
        parent::__construct(tubepress_app_api_options_Names::GALLERY_SOURCE, $persistence, $requestParams);
    }

    /**
     * Invoked when the element is submitted by the user.
     *
     * @return string|null A string error message to be displayed to the user, or null if no problem.
     */
    public function onSubmit()
    {
        $fieldName = tubepress_app_api_options_Names::GALLERY_SOURCE;

        if (!$this->getHttpRequestParameters()->hasParam($fieldName)) {

            return null;
        }

        return $this->sendToStorage($fieldName, $this->getHttpRequestParameters()->getParamValue($fieldName));
    }

    public function getWidgetHTML()
    {
        return '';
    }

    /**
     * Gets whether or not this field is TubePress Pro only.
     *
     * @return boolean True if this field is TubePress Pro only. False otherwise.
     *
     * @api
     * @since 4.0.0
     */
    public function isProOnly()
    {
        return false;
    }
}