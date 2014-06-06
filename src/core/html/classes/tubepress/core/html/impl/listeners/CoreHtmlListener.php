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
 *
 */
class tubepress_core_html_impl_listeners_CoreHtmlListener
{
    /**
     * @var tubepress_core_environment_api_EnvironmentInterface
     */
    private $_environment;

    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_core_event_api_EventDispatcherInterface
     */
    private $_eventDispatcher;

    public function __construct(tubepress_api_log_LoggerInterface                   $logger,
                                tubepress_core_environment_api_EnvironmentInterface $environment,
                                tubepress_core_event_api_EventDispatcherInterface   $eventDispatcher)
    {
        $this->_environment     = $environment;
        $this->_logger          = $logger;
        $this->_eventDispatcher = $eventDispatcher;
    }

    public function onPreScriptsHtml(tubepress_core_event_api_EventInterface $event)
    {
        $jsEvent = $this->_eventDispatcher->newEventInstance(array());

        $this->_eventDispatcher->dispatch(tubepress_core_html_api_Constants::EVENT_GLOBAL_JS_CONFIG, $jsEvent);

        $args   = $jsEvent->getSubject();
        $asJson = json_encode($args);
        $html   = $event->getSubject();

        $toPrepend = <<<EOT
<script type="text/javascript">var TubePressJsConfig = $asJson;</script>
EOT;

        $event->setSubject($toPrepend . $html);
    }

    public function onException(tubepress_core_event_api_EventInterface $event)
    {
        if (!$this->_logger->isEnabled()) {

            return;
        }

        /**
         * @var $exception Exception
         */
        $exception = $event->getSubject();
        $traceData = $exception->getTraceAsString();
        $traceData = explode("\n", $traceData);

        foreach ($traceData as $line) {

            $this->_logger->error("<tt>$line</tt><br />");
        }
    }

    public function onGlobalJsConfig(tubepress_core_event_api_EventInterface $event)
    {
        /**
         * @var $config array
         */
        $config = $event->getSubject();

        if (!isset($config['urls'])) {

            $config['urls'] = array();
        }

        $config['urls']['base'] = $this->_environment->getBaseUrl()->toString();
        $config['urls']['usr']  = $this->_environment->getUserContentUrl()->toString();

        $event->setSubject($config);
    }
}