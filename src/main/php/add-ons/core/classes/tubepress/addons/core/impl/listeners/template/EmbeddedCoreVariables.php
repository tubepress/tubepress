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
 * Core variables for the embedded template.
 */
class tubepress_addons_core_impl_listeners_template_EmbeddedCoreVariables
{
    public function onEmbeddedTemplate(tubepress_api_event_EventInterface $event)
    {
        $context      = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $environment  = tubepress_impl_patterns_sl_ServiceLocator::getEnvironmentDetector();
        $template     = $event->getSubject();
        $dataUrl      = $event->getArgument('dataUrl');
        $videoId      = $event->getArgument('videoId');
        $providerName = $event->getArgument('providerName');

        $autoPlay    = $context->get(tubepress_api_const_options_names_Embedded::AUTOPLAY);
        $embedWidth  = $context->get(tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH);
        $embedHeight = $context->get(tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT);

        $vars = array(

            tubepress_api_const_template_Variable::EMBEDDED_DATA_URL   => $dataUrl->toString(true),
            tubepress_api_const_template_Variable::TUBEPRESS_BASE_URL  => $environment->getBaseUrl(),
            tubepress_api_const_template_Variable::EMBEDDED_AUTOSTART  => tubepress_impl_embedded_EmbeddedPlayerUtils::booleanToString($autoPlay),
            tubepress_api_const_template_Variable::EMBEDDED_WIDTH      => $embedWidth,
            tubepress_api_const_template_Variable::EMBEDDED_HEIGHT     => $embedHeight,
            tubepress_api_const_template_Variable::VIDEO_ID            => $videoId,
            tubepress_api_const_template_Variable::VIDEO_DOM_ID        => $this->_getVideoDomId($providerName, $dataUrl),
            tubepress_api_const_template_Variable::EMBEDDED_IMPL_NAME  => $event->getArgument('embeddedImplementationName'),
            tubepress_api_const_template_Variable::VIDEO_PROVIDER_NAME => $providerName,
        );

        foreach ($vars as $key => $value) {

            $template->setVariable($key, $value);
        }

        return $template;
    }

    private function _getVideoDomId($providerName, ehough_curly_Url $dataUrl)
    {
        if ($providerName !== 'vimeo') {

            return 'tubepress-video-object-' . mt_rand();
        }

        $queryVars = $dataUrl->getQueryVariables();

        if (isset($queryVars['player_id'])) {

            return $queryVars['player_id'];
        }

        //this should never happen
        return 'tubepress-video-object-' . mt_rand();
    }
}