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
    /**
     * @var tubepress_api_environment_EnvironmentInterface
     */
    private $_environment;

    /**
     * @var tubepress_api_options_ContextInterface
     */
    private $_context;

    public function __construct(
        tubepress_api_options_ContextInterface $context,
        tubepress_api_environment_EnvironmentInterface $environment)
    {
        $this->_context     = $context;
        $this->_environment = $environment;
    }

    public function onEmbeddedTemplate(tubepress_api_event_EventInterface $event)
    {
        $template     = $event->getSubject();
        $dataUrl      = $event->getArgument('dataUrl');
        $videoId      = $event->getArgument('videoId');
        $providerName = $event->getArgument('providerName');

        $autoPlay    = $this->_context->get(tubepress_api_const_options_names_Embedded::AUTOPLAY);
        $embedWidth  = $this->_context->get(tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH);
        $embedHeight = $this->_context->get(tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT);

        $vars = array(

            tubepress_api_const_template_Variable::EMBEDDED_DATA_URL   => $dataUrl->toString(),
            tubepress_api_const_template_Variable::TUBEPRESS_BASE_URL  => $this->_environment->getBaseUrl()->toString(),
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

    private function _getVideoDomId($providerName, tubepress_api_url_UrlInterface $dataUrl)
    {
        if ($providerName !== 'vimeo') {

            return 'tubepress-video-object-' . mt_rand();
        }

        $query = $dataUrl->getQuery();

        if ($query->hasKey('player_id')) {

            return $query->get('player_id');
        }

        //this should never happen
        return 'tubepress-video-object-' . mt_rand();
    }
}