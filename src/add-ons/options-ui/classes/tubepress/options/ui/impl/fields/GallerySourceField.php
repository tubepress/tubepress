<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
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
class tubepress_options_ui_impl_fields_GallerySourceField extends tubepress_options_ui_impl_fields_AbstractField implements tubepress_api_options_ui_MultiSourceFieldInterface
{
    /**
     * @var string
     */
    private $_multiSourcePrefix = '';

    public function __construct(tubepress_api_options_PersistenceInterface    $persistence,
                                tubepress_api_http_RequestParametersInterface $requestParams)
    {
        parent::__construct(tubepress_api_options_Names::GALLERY_SOURCE, $persistence, $requestParams);
    }

    /**
     * Invoked when the element is submitted by the user.
     *
     * @return string|null A string error message to be displayed to the user, or null if no problem.
     */
    public function onSubmit()
    {
        $optionName    = tubepress_api_options_Names::GALLERY_SOURCE;
        $paramName     = $this->_multiSourcePrefix . $optionName;
        $requestParams = $this->getHttpRequestParameters();

        if (!$requestParams->hasParam($paramName)) {

            return null;
        }

        return $this->sendToStorage($optionName, $requestParams->getParamValue($paramName));
    }

    /**
     * {@inheritdoc}
     */
    public function getWidgetHTML()
    {
        return '';
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->_multiSourcePrefix . tubepress_api_options_Names::GALLERY_SOURCE;
    }

    /**
     * {@inheritdoc}
     */
    public function cloneForMultiSource($prefix, tubepress_api_options_PersistenceInterface $persistence)
    {
        $toReturn = new self($persistence, $this->getHttpRequestParameters());

        $toReturn->setMultiSourcePrefix($prefix);

        return $toReturn;
    }
}
