<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
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
        $execContext = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();
        $videoId     = $execContext->get(tubepress_api_const_options_names_Output::VIDEO);

        return $videoId != '';
    }

    /**
     * @return string The HTML for this shortcode handler.
     */
    public final function getHtml()
    {
        $execContext = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();
        $videoId     = $execContext->get(tubepress_api_const_options_names_Output::VIDEO);

        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug(sprintf('Building single video with ID %s', $videoId));
        }

        return $this->_getSingleVideoHtml($videoId);
    }

    private function _getSingleVideoHtml($videoId)
    {
        $pluginManager = tubepress_impl_patterns_ioc_KernelServiceLocator::getEventDispatcher();
        $provider      = tubepress_impl_patterns_ioc_KernelServiceLocator::getVideoCollector();
        $themeHandler  = tubepress_impl_patterns_ioc_KernelServiceLocator::getThemeHandler();
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
