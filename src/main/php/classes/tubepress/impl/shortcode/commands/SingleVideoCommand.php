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
class tubepress_impl_shortcode_commands_SingleVideoCommand implements ehough_chaingang_api_Command
{
    private $_logger;

    public function __construct()
    {
        $this->_logger = ehough_epilog_api_LoggerFactory::getLogger('Single Video Command');
    }

    /**
     * Execute a unit of processing work to be performed.
     *
     * This Command may either complete the required processing and return true,
     * or delegate remaining processing to the next Command in a Chain containing
     * this Command by returning false.
     *
     * @param ehough_chaingang_api_Context $context The Context to be processed by this Command.
     *
     * @return boolean True if the processing of this Context has been completed, or false if the
     *                 processing of this Context should be delegated to a subsequent Command
     *                 in an enclosing Chain.
     */
    public function execute(ehough_chaingang_api_Context $context)
    {
        $execContext = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();
        $videoId     = $execContext->get(tubepress_api_const_options_names_Output::VIDEO);

        if ($videoId == '') {

            return false;
        }

        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug(sprintf('Building single video with ID %s', $videoId));
        }

        $context->put(tubepress_impl_shortcode_ShortcodeHtmlGeneratorChain::CHAIN_KEY_GENERATED_HTML, $this->_getSingleVideoHtml($videoId));

        return true;
    }

    private function _getSingleVideoHtml($videoId)
    {
        $pluginManager = tubepress_impl_patterns_ioc_KernelServiceLocator::getEventDispatcher();
        $provider      = tubepress_impl_patterns_ioc_KernelServiceLocator::getVideoProvider();
        $themeHandler  = tubepress_impl_patterns_ioc_KernelServiceLocator::getThemeHandler();
        $pc            = tubepress_impl_patterns_ioc_KernelServiceLocator::getVideoProviderCalculator();
        $template      = $themeHandler->getTemplateInstance('single_video.tpl.php');
        $providerName  = $pc->calculateProviderOfVideoId($videoId);

        /* grab the video from the provider */
        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug(sprintf('Asking provider for video with ID %s', $videoId));
        }

        $video = $provider->getSingleVideo($videoId);

        if ($pluginManager->hasListeners(tubepress_api_const_event_CoreEventNames::SINGLE_VIDEO_TEMPLATE_CONSTRUCTION)) {

            $event = new tubepress_api_event_TubePressEvent(

                $template,
                array(

                    'providerName' => $providerName,
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
