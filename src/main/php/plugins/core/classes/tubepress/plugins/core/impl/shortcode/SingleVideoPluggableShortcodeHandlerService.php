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
 * HTML generation command that generates HTML for a single video + meta info.
 */
class tubepress_plugins_core_impl_shortcode_SingleVideoPluggableShortcodeHandlerService implements tubepress_spi_shortcode_PluggableShortcodeHandlerService
{
    private $_logger;

    public function __construct()
    {
        $this->_logger = ehough_epilog_api_LoggerFactory::getLogger('Single Video Shortcode Handler');
    }

    /**
     * @return string The name of this shortcode handler. Never empty or null. All lowercase alphanumerics and dashes.
     */
    public final function getName()
    {
        return 'single-video';
    }

    /**
     * @return boolean True if this handler is interested in generating HTML, false otherwise.
     */
    public final function shouldExecute()
    {
        $execContext = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $videoId     = $execContext->get(tubepress_api_const_options_names_Output::VIDEO);

        return $videoId != '';
    }

    /**
     * @return string The HTML for this shortcode handler.
     */
    public final function getHtml()
    {
        $execContext = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $videoId     = $execContext->get(tubepress_api_const_options_names_Output::VIDEO);

        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug(sprintf('Building single video with ID %s', $videoId));
        }

        return $this->_getSingleVideoHtml($videoId);
    }

    private function _getSingleVideoHtml($videoId)
    {
        $pluginManager = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();
        $provider      = tubepress_impl_patterns_sl_ServiceLocator::getVideoCollector();
        $themeHandler  = tubepress_impl_patterns_sl_ServiceLocator::getThemeHandler();
        $template      = $themeHandler->getTemplateInstance('single_video.tpl.php', TUBEPRESS_ROOT . '/src/main/resources/default-themes/default');

        /* grab the video from the provider */
        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug(sprintf('Asking provider for video with ID %s', $videoId));
        }

        $video = $provider->collectSingleVideo($videoId);

        if ($video === null) {

            return sprintf('Video %s not found', $videoId);    //>(translatable)<
        }

        if ($pluginManager->hasListeners(tubepress_api_const_event_CoreEventNames::SINGLE_VIDEO_TEMPLATE_CONSTRUCTION)) {

            $event = new tubepress_api_event_TubePressEvent(

                $template,
                array(

                    'video'         => $video
                )
            );

            $pluginManager->dispatch(

                tubepress_api_const_event_CoreEventNames::SINGLE_VIDEO_TEMPLATE_CONSTRUCTION,
                $event
            );

            $template = $event->getSubject();
        }

        $html = $template->toString();

        if ($pluginManager->hasListeners(tubepress_api_const_event_CoreEventNames::SINGLE_VIDEO_HTML_CONSTRUCTION)) {

            $event = new tubepress_api_event_TubePressEvent($html);

            $pluginManager->dispatch(

                tubepress_api_const_event_CoreEventNames::SINGLE_VIDEO_HTML_CONSTRUCTION,
                $event
            );

            $html = $event->getSubject();
        }

        return $html;
    }
}
