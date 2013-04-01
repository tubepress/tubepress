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
        $execContext = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
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

        $provider      = tubepress_impl_patterns_sl_ServiceLocator::getVideoCollector();
        $pluginManager = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();
        $themeHandler  = tubepress_impl_patterns_sl_ServiceLocator::getThemeHandler();
        $ms            = tubepress_impl_patterns_sl_ServiceLocator::getMessageService();
        $qss           = tubepress_impl_patterns_sl_ServiceLocator::getHttpRequestParameterService();
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

            return $ms->_('No matching videos');     //>(translatable)<
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
