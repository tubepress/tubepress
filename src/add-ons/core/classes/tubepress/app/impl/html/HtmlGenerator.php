<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 *
 */
class tubepress_app_impl_html_HtmlGenerator implements tubepress_app_api_html_HtmlGeneratorInterface
{
    /**
     * @var tubepress_lib_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var tubepress_app_impl_html_CssAndJsGenerationHelper
     */
    private $_cssJsGenerationHelper;

    /**
     * @var tubepress_lib_api_template_TemplatingInterface
     */
    private $_templating;

    /**
     * @var tubepress_platform_api_collection_MapInterface
     */
    private $_cache;

    /**
     * @var tubepress_app_api_environment_EnvironmentInterface
     */
    private $_environment;

    public function __construct(tubepress_lib_api_event_EventDispatcherInterface   $eventDispatcher,
                                tubepress_lib_api_template_TemplatingInterface     $templating,
                                tubepress_app_impl_html_CssAndJsGenerationHelper   $cssAndJsGenerationHelper,
                                tubepress_app_api_environment_EnvironmentInterface $environment)
    {
        $this->_eventDispatcher       = $eventDispatcher;
        $this->_cssJsGenerationHelper = $cssAndJsGenerationHelper;
        $this->_templating            = $templating;
        $this->_environment           = $environment;

        $this->_cache = new tubepress_platform_impl_collection_Map();
    }

    /**
     * Generates the HTML for the given shortcode.
     *
     * @return string The HTML, or the error message if there was a problem.
     *
     * @api
     * @since 4.0.0
     */
    public function getHtml()
    {
        try {

            $htmlGenerationEventPre = $this->_eventDispatcher->newEventInstance('');

            $this->_eventDispatcher->dispatch(tubepress_app_api_event_Events::HTML_GENERATION, $htmlGenerationEventPre);

            /**
             * @var $selected string
             */
            $html = $htmlGenerationEventPre->getSubject();

            if ($html === null) {

                throw new RuntimeException('Unable to generate HTML.');
            }

            $htmlGenerationEventPost = $this->_eventDispatcher->newEventInstance($html);

            $this->_eventDispatcher->dispatch(tubepress_app_api_event_Events::HTML_GENERATION_POST, $htmlGenerationEventPost);

            /**
             * @var $html string
             */
            $html = $htmlGenerationEventPost->getSubject();

            return $html;

        } catch (Exception $e) {

            $event = $this->_eventDispatcher->newEventInstance($e);

            $this->_eventDispatcher->dispatch(tubepress_app_api_event_Events::HTML_EXCEPTION_CAUGHT, $event);

            $args = array('exception' => $e);
            $html = $this->_templating->renderTemplate('exception/static', $args);

            return $html;
        }
    }


    /**
     * @return tubepress_platform_api_url_UrlInterface[]
     *
     * @api
     * @since 4.0.0
     */
    public function getUrlsCSS()
    {
        return $this->_cssJsGenerationHelper->getUrlsCSS();
    }

    /**
     * @return tubepress_platform_api_url_UrlInterface[]
     *
     * @api
     * @since 4.0.0
     */
    public function getUrlsJS()
    {
        return $this->_cssJsGenerationHelper->getUrlsJs();
    }

    /**
     * @return string
     *
     * @api
     * @since 4.0.0
     */
    public function getCSS()
    {
        return $this->_cssJsGenerationHelper->getCSS();
    }

    /**
     * @return string
     *
     * @api
     * @since 4.0.0
     */
    public function getJS()
    {
        return $this->_cssJsGenerationHelper->getJS();
    }

    public function onScripts(tubepress_lib_api_event_EventInterface $event)
    {
        $existingUrls = $event->getSubject();

        $tubepressJsUrl = $this->_environment->getBaseUrl()->getClone();

        $tubepressJsUrl->addPath('/web/js/tubepress.js');

        array_unshift($existingUrls, $tubepressJsUrl);

        $event->setSubject($existingUrls);
    }
}