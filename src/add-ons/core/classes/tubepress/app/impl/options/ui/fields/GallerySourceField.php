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
class tubepress_app_impl_options_ui_fields_GallerySourceField extends tubepress_app_impl_options_ui_fields_AbstractField implements tubepress_app_api_options_ui_MultiSourceFieldInterface
{
    /**
     * @var string
     */
    private $_multiSourcePrefix = '';

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
        $optionName    = tubepress_app_api_options_Names::GALLERY_SOURCE;
        $paramName     = $this->_multiSourcePrefix . $optionName;
        $requestParams = $this->getHttpRequestParameters();

        if (!$requestParams->hasParam($paramName)) {

            return null;
        }

        return $this->sendToStorage($optionName, $requestParams->getParamValue($paramName));
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

    public function setMultiSourcePrefix($prefix)
    {
        $this->_multiSourcePrefix = $prefix;
    }

    /**
     * @return string The page-unique identifier for this item.
     *
     * @api
     * @since 4.0.0
     */
    public function getId()
    {
        return $this->_multiSourcePrefix . tubepress_app_api_options_Names::GALLERY_SOURCE;
    }

    /**
     * @param $prefix
     * @param tubepress_app_api_options_PersistenceInterface $persistence
     *
     * @return tubepress_app_api_options_ui_FieldInterface
     */
    public function cloneForMultiSource($prefix, tubepress_app_api_options_PersistenceInterface $persistence)
    {
        $toReturn = new self($persistence, $this->getHttpRequestParameters());

        $toReturn->setMultiSourcePrefix($prefix);

        return $toReturn;
    }
}