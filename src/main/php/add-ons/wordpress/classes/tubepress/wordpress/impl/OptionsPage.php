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

class tubepress_wordpress_impl_OptionsPage
{
    /**
     * @var tubepress_core_api_options_ui_FormInterface
     */
    private $_form;

    /**
     * @var tubepress_core_api_http_RequestParametersInterface
     */
    private $_httpRequestParams;

    public function __construct(tubepress_core_api_options_ui_FormInterface        $form,
                                tubepress_core_api_http_RequestParametersInterface $requestParams)
    {
        $this->_form              = $form;
        $this->_httpRequestParams = $requestParams;
    }

    /**
     * Filter the content (which may be empty).
     */
    public function run()
    {
        $errors        = array();
        $justSubmitted = false;

        /* are we updating? */
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->_httpRequestParams->hasParam('tubepress_save')) {

            $justSubmitted = true;

            $errors = $this->_form->onSubmit();
        }

        print $this->_form->getHtml($errors, $justSubmitted);
    }
}
