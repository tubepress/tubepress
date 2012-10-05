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

class tubepress_plugins_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService implements tubepress_spi_shortcode_PluggableShortcodeHandlerService
{
    private $_logger;

    public function __construct()
    {
        $this->_logger = ehough_epilog_api_LoggerFactory::getLogger('Thumb Gallery Shortcode Handler');
    }

    /**
     * @return string The name of this shortcode handler. Never empty or null. All lowercase alphanumerics and dashes.
     */
    public final function getName()
    {
        return 'thumb-gallery';
    }

    /**
     * @return boolean True if this handler is interested in generating HTML, false otherwise.
     */
    public final function shouldExecute()
    {
        return true;
    }

    /**
     * @return string The HTML for this shortcode handler.
     */
    public final function getHtml()
    {
        $execContext = tubepress_impl_patterns_ioc_KernelServiceLocator::getExecutionContext();
        $galleryId   = $execContext->get(tubepress_api_const_options_names_Advanced::GALLERY_ID);

        if ($galleryId == '') {

            $galleryId = mt_rand();

            $result = $execContext->set(tubepress_api_const_options_names_Advanced::GALLERY_ID, $galleryId);

            if ($result !== true) {

                return false;
            }
        }

        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug(sprintf('Starting to build thumbnail gallery %s', $galleryId));
        }

        $provider      = tubepress_impl_patterns_ioc_KernelServiceLocator::getVideoCollector();
        $pluginManager = tubepress_impl_patterns_ioc_KernelServiceLocator::getEventDispatcher();
        $themeHandler  = tubepress_impl_patterns_ioc_KernelServiceLocator::getThemeHandler();
        $ms            = tubepress_impl_patterns_ioc_KernelServiceLocator::getMessageService();
        $qss           = tubepress_impl_patterns_ioc_KernelServiceLocator::getHttpRequestParameterService();
        $template      = $themeHandler->getTemplateInstance('gallery.tpl.php', TUBEPRESS_ROOT . '/src/main/resources/default-themes/default');
        $page          = $qss->getParamValueAsInt(tubepress_spi_const_http_ParamName::PAGE, 1);

        /* first grab the videos */
        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug('Asking provider for videos');
        }

        $feedResult = $provider->collectVideoGalleryPage();

        $numVideos  = sizeof($feedResult->getVideos());

        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug(sprintf('Provider has delivered %d videos', $numVideos));
        }

        if ($numVideos == 0) {

            return $ms->_('no-videos-found');
        }

        /* send the template through the plugins */
        if ($pluginManager->hasListeners(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_TEMPLATE_CONSTRUCTION)) {

            $event = new tubepress_api_event_TubePressEvent($template, array(

                'page'             => $page,
                'videoGalleryPage' => $feedResult
            ));

            $pluginManager->dispatch(

                tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_TEMPLATE_CONSTRUCTION,
                $event
            );

            $template = $event->getSubject();
        }

        $html = $template->toString();

        /* send gallery HTML through the plugins */
        if ($pluginManager->hasListeners(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_HTML_CONSTRUCTION)) {

            $event = new tubepress_api_event_TubePressEvent($html, array(

                'page'             => $page,
                'videoGalleryPage' => $feedResult
            ));

            $pluginManager->dispatch(

                tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_HTML_CONSTRUCTION,
                $event
            );

            $html = $event->getSubject();
        }

        /* we're done. tie up */
        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug(sprintf('Done assembling gallery %d', $galleryId));
        }

        return $html;
    }
}
