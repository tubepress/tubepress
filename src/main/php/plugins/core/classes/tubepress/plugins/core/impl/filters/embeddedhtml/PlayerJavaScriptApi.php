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

/**
 * Registers videos with the JS player API.
 */
class tubepress_plugins_core_impl_filters_embeddedhtml_PlayerJavaScriptApi
{
    public function onEmbeddedHtml(tubepress_api_event_TubePressEvent $event)
    {
        $context             = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $environmentDetector = tubepress_impl_patterns_sl_ServiceLocator::getEnvironmentDetector();

        if (! $environmentDetector->isPro()) {

            return;
        }

        if (! $context->get(tubepress_api_const_options_names_Embedded::ENABLE_JS_API)) {

        	return;
        }

        $html    = $event->getSubject();
        $domId   = $this->_getDomIdFromHtml($html);
        $final   = $html . <<<EOT
<script type="text/javascript">
   var tubePressDomInjector = tubePressDomInjector || [], tubePressPlayerApi = tubePressPlayerApi || [];
       tubePressDomInjector.push(['loadPlayerApiJs']);
       tubePressPlayerApi.push(['register', '$domId' ]);
</script>
EOT;

        $event->setSubject($final);
    }

    private function _getDomIdFromHtml($html)
    {
        $result = preg_match('/\sid="(tubepress-video-object-[0-9]+)"\s.*/', $html, $matches);

        if ($result < 1 || count($matches) < 2) {

            throw new RuntimeException("TubePress-generated video embeds must have a DOM id attribute that starts with 'tubepress-video-object-'");
        }

        return $matches[1];
    }
}
