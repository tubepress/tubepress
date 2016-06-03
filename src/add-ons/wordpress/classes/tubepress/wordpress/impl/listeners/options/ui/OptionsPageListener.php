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
class tubepress_wordpress_impl_listeners_options_ui_OptionsPageListener
{
    /**
     * @var tubepress_api_options_ui_FormInterface
     */
    private $_form;

    /**
     * @var tubepress_api_http_RequestParametersInterface
     */
    private $_httpRequestParams;

    public function __construct(tubepress_api_options_ui_FormInterface        $form,
                                tubepress_api_http_RequestParametersInterface $requestParams)
    {
        $this->_form              = $form;
        $this->_httpRequestParams = $requestParams;
    }

    public function run(tubepress_api_event_EventInterface $event)
    {
        $errors        = array();
        $justSubmitted = false;

        /* are we updating? */
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->_httpRequestParams->hasParam('tubepress_save')) {

            $justSubmitted = true;

            $errors = $this->_form->onSubmit();
        }

        echo $this->_form->getHtml($errors, $justSubmitted);
    }

    public function onTemplateSelect(tubepress_api_event_EventInterface $event)
    {
        $event->setSubject('options-ui/wp-settings-page');
    }
}
