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
 * Injects Ajax pagination code into the gallery's HTML.
 */
class tubepress_core_html_gallery_impl_listeners_CoreGalleryHtmlListener extends tubepress_core_html_gallery_impl_listeners_AbstractGalleryListener
{
    private static $_PROPERTY_NVPMAP = 'nvpMap';

    private static $_PROPERTY_JSMAP = 'jsMap';

    private static $_NAME_PARAM_PLAYERJSURL          = 'playerLocationJsUrl';
    private static $_NAME_PARAM_PLAYER_PRODUCES_HTML = 'playerLocationProducesHtml';

    /**
     * @var tubepress_core_environment_api_EnvironmentInterface
     */
    private $_environment;

    /**
     * @var tubepress_core_event_api_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var tubepress_core_http_api_RequestParametersInterface
     */
    private $_requestParameters;

    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_core_media_provider_api_CollectorInterface
     */
    private $_collector;

    /**
     * @var tubepress_core_template_api_TemplateFactoryInterface
     */
    private $_templateFactory;

    public function __construct(tubepress_api_log_LoggerInterface                    $logger,
                                tubepress_core_options_api_ContextInterface          $context,
                                tubepress_core_event_api_EventDispatcherInterface    $eventDispatcher,
                                tubepress_core_options_api_ReferenceInterface        $optionReference,
                                tubepress_core_environment_api_EnvironmentInterface  $environment,
                                tubepress_core_http_api_RequestParametersInterface   $requestParams,
                                tubepress_core_media_provider_api_CollectorInterface $collector,
                                tubepress_core_template_api_TemplateFactoryInterface $templateFactory)
    {
        parent::__construct($context, $optionReference);

        $this->_logger            = $logger;
        $this->_eventDispatcher   = $eventDispatcher;
        $this->_environment       = $environment;
        $this->_requestParameters = $requestParams;
        $this->_collector         = $collector;
        $this->_templateFactory   = $templateFactory;
    }

    /**
     * The following options are required by JS, so we explicity set them:
     *
     *  ajaxPagination
     *  autoNext
     *  embeddedHeight
     *  embeddedWidth
     *  fluidThumbs
     *  httpMethod
     *  playerLocation
     *
     * The following options are JS-specific
     *
     *  playerJsUrl
     *  playerLocationProducesHtml
     *
     * Otherwise, we simply set any "custom" options so they can be passed back in via Ajax operations.
     */
    public function onGalleryInitJs(tubepress_core_event_api_EventInterface $event)
    {
        $args = $event->getSubject();

        $requiredNvpMap = $this->_buildRequiredNvpMap();
        $jsMap          = $this->_buildJsMap();
        $customNvpMap   = $this->getExecutionContext()->getEphemeralOptions();

        $nvpMap = array_merge($requiredNvpMap, $customNvpMap);

        $newArgs = array(

            self::$_PROPERTY_NVPMAP => $this->_convertBooleans($nvpMap),
            self::$_PROPERTY_JSMAP  => $this->_convertBooleans($jsMap),
        );

        $event->setSubject(array_merge($args, $newArgs));
    }

    public function onGalleryHtml(tubepress_core_event_api_EventInterface $event)
    {
        $galleryId = $this->getExecutionContext()->get(tubepress_core_html_api_Constants::OPTION_GALLERY_ID);
        $jsEvent   = $this->_eventDispatcher->newEventInstance(array());

        $this->_eventDispatcher->dispatch(tubepress_core_html_gallery_api_Constants::EVENT_GALLERY_INIT_JS, $jsEvent);

        $args   = $jsEvent->getSubject();
        $asJson = json_encode($args);
        $html   = $event->getSubject();

        $toReturn = $html . <<<EOT
<script type="text/javascript">
   var tubePressDomInjector = tubePressDomInjector || [], tubePressGalleryRegistrar = tubePressGalleryRegistrar || [];
       tubePressDomInjector.push(['loadGalleryJs']);
       tubePressGalleryRegistrar.push(['register', '$galleryId', $asJson ]);
</script>
EOT;

        $event->setSubject($toReturn);
    }

    public function onBeforeCssHtml(tubepress_core_event_api_EventInterface $event)
    {
        $html = $event->getSubject();

        $html = $this->_addMetaTags($html);

        $event->setSubject($html);
    }

    public function onHtmlGeneration(tubepress_core_event_api_EventInterface $event)
    {
        $galleryId   = $this->getExecutionContext()->get(tubepress_core_html_api_Constants::OPTION_GALLERY_ID);
        $shouldLog   = $this->_logger->isEnabled();

        if ($galleryId == '') {

            $galleryId = mt_rand();
            $this->getExecutionContext()->setEphemeralOption(tubepress_core_html_api_Constants::OPTION_GALLERY_ID, $galleryId);
        }

        if ($shouldLog) {

            $this->_logger->debug(sprintf('Starting to build thumbnail gallery %s', $galleryId));
        }

        $template = $this->_templateFactory->fromFilesystem(array(

            'gallery.tpl.php',
            TUBEPRESS_ROOT . '/core/themes/web/default/gallery.tpl.php'
        ));
        $pageNumber = $this->_requestParameters->getParamValueAsInt(tubepress_core_http_api_Constants::PARAM_NAME_PAGE, 1);

        /* first grab the items */
        if ($shouldLog) {

            $this->_logger->debug('Asking collector for a page.');
        }

        $mediaPage = $this->_collector->collectPage();
        $itemCount = sizeof($mediaPage->getItems());

        if ($shouldLog) {

            $this->_logger->debug(sprintf('Collector has delivered %d item(s)', $itemCount));
        }

        /* send the template through the listeners */
        if ($this->_eventDispatcher->hasListeners(tubepress_core_html_gallery_api_Constants::EVENT_TEMPLATE_THUMBNAIL_GALLERY)) {

            $templateEvent = $this->_eventDispatcher->newEventInstance($template, array(

                'page'       => $mediaPage,
                'pageNumber' => $pageNumber,
            ));

            $this->_eventDispatcher->dispatch(

                tubepress_core_html_gallery_api_Constants::EVENT_TEMPLATE_THUMBNAIL_GALLERY,
                $templateEvent
            );

            $template = $templateEvent->getSubject();
        }

        $html = $template->toString();

        /* send gallery HTML through the listeners */
        if ($this->_eventDispatcher->hasListeners(tubepress_core_html_gallery_api_Constants::EVENT_HTML_THUMBNAIL_GALLERY)) {

            $htmlEvent = $this->_eventDispatcher->newEventInstance($html, array(

                'page'       => $mediaPage,
                'pageNumber' => $pageNumber,
            ));

            $this->_eventDispatcher->dispatch(

                tubepress_core_html_gallery_api_Constants::EVENT_HTML_THUMBNAIL_GALLERY,
                $htmlEvent
            );

            $html = $htmlEvent->getSubject();
        }

        /* we're done. tie up */
        if ($shouldLog) {

            $this->_logger->debug(sprintf('Done assembling gallery %d', $galleryId));
        }

        $event->setSubject($html);
        $event->stopPropagation();
    }

    private function _addMetaTags($html)
    {
        $page = $this->_requestParameters->getParamValueAsInt(tubepress_core_http_api_Constants::PARAM_NAME_PAGE, 1);

        if ($page > 1) {

            $html .= "\n" . '<meta name="robots" content="noindex, nofollow" />';
        }

        return $html;
    }

    private function _buildJsMap()
    {
        $toReturn = array();

        $playerLocation = $this->findCurrentPlayerLocation();

        if ($playerLocation !== null) {

            $toReturn[self::$_NAME_PARAM_PLAYERJSURL]          = $this->_getPlayerJsUrl($playerLocation);
            $toReturn[self::$_NAME_PARAM_PLAYER_PRODUCES_HTML] = (bool) $playerLocation->producesHtml();
        }

        $requiredOptions = array(

            tubepress_core_html_gallery_api_Constants::OPTION_AJAX_PAGINATION,
            tubepress_core_html_gallery_api_Constants::OPTION_FLUID_THUMBS,
            tubepress_core_http_api_Constants::OPTION_HTTP_METHOD,
        );

        foreach ($requiredOptions as $optionName) {

            $toReturn[$optionName] = $this->getExecutionContext()->get($optionName);
        }

        return $toReturn;
    }

    private function _buildRequiredNvpMap()
    {
        $toReturn = array();

        $requiredOptions = array(

            tubepress_core_embedded_api_Constants::OPTION_EMBEDDED_HEIGHT,
            tubepress_core_embedded_api_Constants::OPTION_EMBEDDED_WIDTH,
            tubepress_core_player_api_Constants::OPTION_PLAYER_LOCATION
        );

        foreach ($requiredOptions as $optionName) {

            $toReturn[$optionName] = $this->getExecutionContext()->get($optionName);
        }

        return $toReturn;
    }

    private function _getPlayerJsUrl(tubepress_core_player_api_PlayerLocationInterface $player)
    {
        return rtrim($player->getPlayerJsUrl($this->_environment), '/');
    }

    private function _convertBooleans($map)
    {
        foreach ($map as $key => $value) {

            if (!$this->getOptionReference()->optionExists($key) || !$this->getOptionReference()->isBoolean($key)) {

                continue;
            }

            $map[$key] = $value ? true : false;
        }

        return $map;
    }
}