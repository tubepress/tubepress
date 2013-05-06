<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_addons_core_impl_listeners_cssjs_DefaultPathsListener
{
    public function onJqueryScriptTag(tubepress_api_event_TubePressEvent $event)
    {
        $environmentDetector = tubepress_impl_patterns_sl_ServiceLocator::getEnvironmentDetector();
        $baseUrl             = $environmentDetector->getBaseUrl();
        $raw                 = sprintf('<script type="text/javascript" src="%s/src/main/web/vendor/jquery-1.9.1.min.js"></script>',
            $baseUrl);

        $event->setSubject($raw);
    }

    public function onTubePressScriptTag(tubepress_api_event_TubePressEvent $event)
    {
        $environmentDetector = tubepress_impl_patterns_sl_ServiceLocator::getEnvironmentDetector();
        $baseUrl             = $environmentDetector->getBaseUrl();
        $raw                 = sprintf('<script type="text/javascript" src="%s/src/main/web/js/tubepress.js"></script>',
            $baseUrl);

        $event->setSubject($raw);
    }

    public function onTubePressStylesheetTag(tubepress_api_event_TubePressEvent $event)
    {
        $environmentDetector = tubepress_impl_patterns_sl_ServiceLocator::getEnvironmentDetector();
        $baseUrl             = $environmentDetector->getBaseUrl();
        $raw                 = sprintf('<link rel="stylesheet" href="%s/src/main/web/css/tubepress.css" type="text/css" />',
            $baseUrl);

        $event->setSubject($raw);
    }

    public function onMetaTags(tubepress_api_event_TubePressEvent $event)
    {
        $qss    = tubepress_impl_patterns_sl_ServiceLocator::getHttpRequestParameterService();
        $page   = $qss->getParamValueAsInt(tubepress_spi_const_http_ParamName::PAGE, 1);
        $result = $page > 1 ? '<meta name="robots" content="noindex, nofollow" />' : '';

        $event->setSubject($result);
    }
}