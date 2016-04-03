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

class tubepress_html_impl_listeners_HtmlListener
{
    /**
     * @var tubepress_api_environment_EnvironmentInterface
     */
    private $_environment;

    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var tubepress_api_http_RequestParametersInterface
     */
    private $_requestParameters;

    public function __construct(tubepress_api_log_LoggerInterface              $logger,
                                tubepress_api_environment_EnvironmentInterface $environment,
                                tubepress_api_event_EventDispatcherInterface  $eventDispatcher,
                                tubepress_api_http_RequestParametersInterface $requestParams)
    {
        $this->_logger            = $logger;
        $this->_environment       = $environment;
        $this->_eventDispatcher   = $eventDispatcher;
        $this->_requestParameters = $requestParams;
    }

    public function onGlobalJsConfig(tubepress_api_event_EventInterface $event)
    {
        /*
         * @var array
         */
        $config = $event->getSubject();

        if (!isset($config['urls'])) {

            $config['urls'] = array();
        }

        $baseUrl         = $this->_environment->getBaseUrl()->getClone();
        $userContentUrl  = $this->_environment->getUserContentUrl()->getClone();
        $ajaxEndpointUrl = $this->_environment->getAjaxEndpointUrl()->getClone();

        $baseUrl->removeSchemeAndAuthority();
        $userContentUrl->removeSchemeAndAuthority();
        $ajaxEndpointUrl->removeSchemeAndAuthority();

        $config['urls']['base'] = "$baseUrl";
        $config['urls']['usr']  = "$userContentUrl";
        $config['urls']['ajax'] = "$ajaxEndpointUrl";

        $event->setSubject($config);
    }

    public function onException(tubepress_api_event_EventInterface $event)
    {
        if (!$this->_logger->isEnabled()) {

            return;
        }

        /*
         * @var Exception
         */
        $exception = $event->getSubject();
        $traceData = $exception->getTraceAsString();
        $traceData = explode("\n", $traceData);

        foreach ($traceData as $line) {

            $line = htmlspecialchars($line);
            $this->_logger->error("<code>$line</code><br />");
        }
    }

    public function onPostScriptsTemplateRender(tubepress_api_event_EventInterface $event)
    {
        $jsEvent = $this->_eventDispatcher->newEventInstance(array());

        $this->_eventDispatcher->dispatch(tubepress_api_event_Events::HTML_GLOBAL_JS_CONFIG, $jsEvent);

        $args   = $jsEvent->getSubject();
        $asJson = json_encode($args);
        $html   = $event->getSubject();

        $toPrepend = <<<EOT
<script type="text/javascript">var TubePressJsConfig = $asJson;</script>
EOT;

        $event->setSubject($toPrepend . $html);
    }

    public function onPostStylesTemplateRender(tubepress_api_event_EventInterface $event)
    {
        $html = $event->getSubject();

        $page = $this->_requestParameters->getParamValueAsInt('tubepress_page', 1);

        if ($page > 1) {

            $html .= "\n" . '<meta name="robots" content="noindex, nofollow" />';
        }

        $event->setSubject($html);
    }
}
