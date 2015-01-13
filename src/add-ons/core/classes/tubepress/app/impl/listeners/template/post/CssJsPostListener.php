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
 *
 */
class tubepress_app_impl_listeners_template_post_CssJsPostListener
{
    /**
     * @var tubepress_lib_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var tubepress_lib_api_http_RequestParametersInterface
     */
    private $_requestParameters;

    public function __construct(tubepress_lib_api_event_EventDispatcherInterface  $eventDispatcher,
                                tubepress_lib_api_http_RequestParametersInterface $requestParams)
    {
        $this->_eventDispatcher   = $eventDispatcher;
        $this->_requestParameters = $requestParams;
    }

    public function onPostScriptsTemplateRender(tubepress_lib_api_event_EventInterface $event)
    {
        $jsEvent = $this->_eventDispatcher->newEventInstance(array());

        $this->_eventDispatcher->dispatch(tubepress_app_api_event_Events::HTML_GLOBAL_JS_CONFIG, $jsEvent);

        $args   = $jsEvent->getSubject();
        $asJson = json_encode($args);
        $html   = $event->getSubject();

        $toPrepend = <<<EOT
<script type="text/javascript">var TubePressJsConfig = $asJson;</script>
EOT;

        $event->setSubject($toPrepend . $html);
    }

    public function onPostStylesTemplateRender(tubepress_lib_api_event_EventInterface $event)
    {
        $html = $event->getSubject();

        $page = $this->_requestParameters->getParamValueAsInt('tubepress_page', 1);

        if ($page > 1) {

            $html .= "\n" . '<meta name="robots" content="noindex, nofollow" />';
        }

        $event->setSubject($html);
    }
}