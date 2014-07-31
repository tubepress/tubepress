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
class tubepress_app_impl_listeners_html_scripts_GlobalJsConfigListener
{
    /**
     * @var tubepress_lib_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    public function __construct(tubepress_lib_api_event_EventDispatcherInterface $eventDispatcher)
    {
        $this->_eventDispatcher = $eventDispatcher;
    }

    public function onPreScriptsHtml(tubepress_lib_api_event_EventInterface $event)
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
}