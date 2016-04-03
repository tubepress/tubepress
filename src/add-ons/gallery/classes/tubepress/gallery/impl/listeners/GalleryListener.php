<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_gallery_impl_listeners_GalleryListener
{
    /**
     * @var tubepress_api_http_RequestParametersInterface
     */
    private $_requestParameters;

    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_api_media_CollectorInterface
     */
    private $_collector;

    /**
     * @var tubepress_api_template_TemplatingInterface
     */
    private $_templating;

    /**
     * @var tubepress_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_api_options_ReferenceInterface
     */
    private $_optionsReference;

    /**
     * @var tubepress_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    public function __construct(tubepress_api_log_LoggerInterface             $logger,
                                tubepress_api_options_ContextInterface        $context,
                                tubepress_api_http_RequestParametersInterface $requestParams,
                                tubepress_api_media_CollectorInterface        $collector,
                                tubepress_api_template_TemplatingInterface    $templating,
                                tubepress_api_event_EventDispatcherInterface  $eventDispatcher,
                                tubepress_api_options_ReferenceInterface      $optionsReference)
    {
        $this->_logger            = $logger;
        $this->_context           = $context;
        $this->_requestParameters = $requestParams;
        $this->_collector         = $collector;
        $this->_templating        = $templating;
        $this->_eventDispatcher   = $eventDispatcher;
        $this->_optionsReference  = $optionsReference;
    }

    public function onHtmlGeneration(tubepress_api_event_EventInterface $event)
    {
        $galleryId = $this->_context->get(tubepress_api_options_Names::HTML_GALLERY_ID);
        $shouldLog = $this->_logger->isEnabled();

        if ($galleryId == '') {

            $galleryId = mt_rand();
            $this->_context->setEphemeralOption(tubepress_api_options_Names::HTML_GALLERY_ID, $galleryId);
        }

        if ($shouldLog) {

            $this->_logDebug(sprintf('Starting to build thumbnail gallery <code>%s</code>', $galleryId));
        }

        $pageNumber = $this->_requestParameters->getParamValueAsInt('tubepress_page', 1);

        /* first grab the items */
        if ($shouldLog) {

            $this->_logDebug('Asking collector for a page.');
        }

        $mediaPage = $this->_collector->collectPage($pageNumber);
        $itemCount = sizeof($mediaPage->getItems());

        if ($shouldLog) {

            $this->_logDebug(sprintf('Collector has delivered <code>%d</code> item(s)', $itemCount));
        }

        $templateVars = array(

            'mediaPage'  => $mediaPage,
            'pageNumber' => $pageNumber,
        );

        $html = $this->_templating->renderTemplate('gallery/main', $templateVars);

        /* we're done. tie up */
        if ($shouldLog) {

            $this->_logDebug(sprintf('Done assembling gallery <code>%d</code>', $galleryId));
        }

        $event->setSubject($html);
        $event->stopPropagation();
    }

    public function onGalleryInitJs(tubepress_api_event_EventInterface $event)
    {
        $args                = $event->getSubject();
        $ephemeral           = $this->_context->getEphemeralOptions();
        $optionsToAdd        = array();
        $requiredOptionNames = array(

            tubepress_api_options_Names::GALLERY_AJAX_PAGINATION,
            tubepress_api_options_Names::GALLERY_FLUID_THUMBS,
            tubepress_api_options_Names::GALLERY_AUTONEXT,
            tubepress_api_options_Names::HTTP_METHOD,
        );

        foreach ($requiredOptionNames as $optionName) {

            $optionsToAdd[$optionName] = $this->_context->get($optionName);
        }

        foreach (array('ephemeral', 'options') as $key) {

            if (!array_key_exists($key, $args) || !is_array($args[$key])) {

                $args[$key] = array();
            }
        }

        $args['ephemeral'] = array_merge($args['ephemeral'], $ephemeral);
        $args['options']   = array_merge($args['options'], $optionsToAdd);

        $event->setSubject($args);
    }

    public function onGalleryTemplatePreRender(tubepress_api_event_EventInterface $event)
    {
        /*
         * @var array
         */
        $existingArgs = $event->getSubject();

        if (!is_array($existingArgs)) {

            $existingArgs = array();
        }

        $galleryId   = $this->_context->get(tubepress_api_options_Names::HTML_GALLERY_ID);
        $thumbWidth  = $this->_context->get(tubepress_api_options_Names::GALLERY_THUMB_WIDTH);
        $thumbHeight = $this->_context->get(tubepress_api_options_Names::GALLERY_THUMB_HEIGHT);

        $existingArgs[tubepress_api_template_VariableNames::HTML_WIDGET_ID]              = $galleryId;
        $existingArgs[tubepress_api_template_VariableNames::GALLERY_THUMBNAIL_WIDTH_PX]  = $thumbWidth;
        $existingArgs[tubepress_api_template_VariableNames::GALLERY_THUMBNAIL_HEIGHT_PX] = $thumbHeight;

        $event->setSubject($existingArgs);
    }

    public function onPostGalleryTemplateRender(tubepress_api_event_EventInterface $event)
    {
        $galleryId = $this->_context->get(tubepress_api_options_Names::HTML_GALLERY_ID);
        $jsEvent   = $this->_eventDispatcher->newEventInstance(array(), array(
            'mediaPage'  => $event->getArgument('mediaPage'),
            'pageNumber' => $event->getArgument('pageNumber'),
        ));

        $this->_eventDispatcher->dispatch(tubepress_api_event_Events::GALLERY_INIT_JS, $jsEvent);

        $args = $jsEvent->getSubject();

        $this->_deepConvertBooleans($args);

        $asJson = json_encode($args);

        $html = $event->getSubject();

        $toReturn = $html . <<<EOT
<script type="text/javascript">
   var tubePressDomInjector = tubePressDomInjector || [], tubePressGalleryRegistrar = tubePressGalleryRegistrar || [];
       tubePressDomInjector.push(['loadGalleryJs']);
       tubePressGalleryRegistrar.push(['register', '$galleryId', $asJson ]);
</script>
EOT;

        $event->setSubject($toReturn);
    }

    private function _deepConvertBooleans(array &$candidate)
    {
        foreach ($candidate as $key => $value) {

            if (is_array($value)) {

                $this->_deepConvertBooleans($value);
                $candidate[$key] = $value;
            }

            if (!$this->_optionsReference->optionExists($key)) {

                continue;
            }

            if (!$this->_optionsReference->isBoolean($key)) {

                continue;
            }

            $candidate[$key] = (bool) $value;
        }
    }

    private function _logDebug($msg)
    {
        $this->_logger->debug(sprintf('(Gallery Listener) %s', $msg));
    }
}
