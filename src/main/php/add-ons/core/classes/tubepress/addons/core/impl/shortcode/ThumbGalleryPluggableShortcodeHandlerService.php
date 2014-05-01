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

class tubepress_addons_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService implements tubepress_spi_shortcode_PluggableShortcodeHandlerService
{
    /**
     * @var ehough_epilog_Logger
     */
    private $_logger;

    /**
     * @var tubepress_api_translation_TranslatorInterface
     */
    private $_translator;

    /**
     * @var tubepress_api_options_ContextInterface
     */
    private $_context;

    public function __construct(
        tubepress_api_options_ContextInterface $context,
        tubepress_api_translation_TranslatorInterface $translator)
    {
        $this->_translator = $translator;
        $this->_logger     = ehough_epilog_LoggerFactory::getLogger('Thumb Gallery Shortcode Handler');
        $this->_context    = $context;
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
        $galleryId   = $this->_context->get(tubepress_api_const_options_names_Advanced::GALLERY_ID);
        $shouldLog   = $this->_logger->isHandling(ehough_epilog_Logger::DEBUG);

        if ($galleryId == '') {

            $galleryId = mt_rand();

            $result = $this->_context->set(tubepress_api_const_options_names_Advanced::GALLERY_ID, $galleryId);

            if ($result !== true) {

                return false;
            }
        }

        if ($shouldLog) {

            $this->_logger->debug(sprintf('Starting to build thumbnail gallery %s', $galleryId));
        }

        $provider        = tubepress_impl_patterns_sl_ServiceLocator::getVideoCollector();
        $eventDispatcher = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();
        $themeHandler    = tubepress_impl_patterns_sl_ServiceLocator::getThemeHandler();
        $qss             = tubepress_impl_patterns_sl_ServiceLocator::getHttpRequestParameterService();
        $template        = $themeHandler->getTemplateInstance('gallery.tpl.php', TUBEPRESS_ROOT . '/src/main/web/themes/default');
        $page            = $qss->getParamValueAsInt(tubepress_spi_const_http_ParamName::PAGE, 1);

        /* first grab the videos */
        if ($shouldLog) {

            $this->_logger->debug('Asking provider for videos');
        }

        $feedResult = $provider->collectVideoGalleryPage();

        $numVideos  = sizeof($feedResult->getVideos());

        if ($shouldLog) {

            $this->_logger->debug(sprintf('Provider has delivered %d videos', $numVideos));
        }

        if ($numVideos == 0) {

            return $this->_translator->_('No matching videos');     //>(translatable)<
        }

        /* send the template through the listeners */
        if ($eventDispatcher->hasListeners(tubepress_api_const_event_EventNames::TEMPLATE_THUMBNAIL_GALLERY)) {

            $event = new tubepress_spi_event_EventBase($template, array(

                'page'             => $page,
                'videoGalleryPage' => $feedResult
            ));

            $eventDispatcher->dispatch(

                tubepress_api_const_event_EventNames::TEMPLATE_THUMBNAIL_GALLERY,
                $event
            );

            $template = $event->getSubject();
        }

        $html = $template->toString();

        /* send gallery HTML through the listeners */
        if ($eventDispatcher->hasListeners(tubepress_api_const_event_EventNames::HTML_THUMBNAIL_GALLERY)) {

            $event = new tubepress_spi_event_EventBase($html, array(

                'page'             => $page,
                'videoGalleryPage' => $feedResult
            ));

            $eventDispatcher->dispatch(

                tubepress_api_const_event_EventNames::HTML_THUMBNAIL_GALLERY,
                $event
            );

            $html = $event->getSubject();
        }

        /* we're done. tie up */
        if ($shouldLog) {

            $this->_logger->debug(sprintf('Done assembling gallery %d', $galleryId));
        }

        return $html;
    }
}
