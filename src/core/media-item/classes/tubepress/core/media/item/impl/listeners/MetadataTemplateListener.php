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
 * This listener is responsible for populating the template with the following
 * variables:
 *
 * TEMPLATE_VAR_ATTRIBUTES_TO_SHOW
 * TEMPLATE_VAR_ATTRIBUTE_LABELS
 */
class tubepress_core_media_item_impl_listeners_MetadataTemplateListener
{
    /**
     * @var tubepress_core_media_provider_api_MediaProviderInterface[]
     */
    private $_mediaProviders;

    /**
     * @var tubepress_core_translation_api_TranslatorInterface
     */
    private $_translator;

    /**
     * @var tubepress_core_options_api_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_core_options_api_ReferenceInterface
     */
    private $_optionReference;

    /**
     * @var tubepress_core_event_api_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var array
     */
    private $_cacheOfMetaOptionNamesToAttributeDisplayNames;

    public function __construct(tubepress_core_options_api_ContextInterface        $context,
                                tubepress_core_options_api_ReferenceInterface      $optionReference,
                                tubepress_core_translation_api_TranslatorInterface $translator,
                                tubepress_core_event_api_EventDispatcherInterface  $eventDispatcher)
    {
        $this->_translator      = $translator;
        $this->_context         = $context;
        $this->_optionReference = $optionReference;
        $this->_eventDispatcher = $eventDispatcher;
    }

    public function onGalleryTemplate(tubepress_core_event_api_EventInterface $event)
    {
        /**
         * @var $template tubepress_core_template_api_TemplateInterface
         */
        $template = $event->getSubject();

        /**
         * @var $page tubepress_core_media_provider_api_Page
         */
        $page = $event->getArgument('page');

        $this->_commonTemplateProcessing($template);
        $this->_setInvocationAnchorForPage($page);
    }

    public function onSingleTemplate(tubepress_core_event_api_EventInterface $event)
    {
        /**
         * @var $template tubepress_core_template_api_TemplateInterface
         */
        $template = $event->getSubject();

        /**
         * @var $item tubepress_core_media_item_api_MediaItem
         */
        $item = $event->getArgument('item');

        $this->_commonTemplateProcessing($template);
        $this->_setInvocationAnchorForSingleItem($item);
        $this->_setPreAndPostAttributes($item);
    }

    private function _commonTemplateProcessing(tubepress_core_template_api_TemplateInterface $template)
    {
        $vars = array(

            tubepress_core_media_item_api_Constants::TEMPLATE_VAR_ATTRIBUTE_LABELS   => $this->_getLabelMap(),
            tubepress_core_media_item_api_Constants::TEMPLATE_VAR_ATTRIBUTES_TO_SHOW => $this->_getToShowMap()
        );

        foreach ($vars as $name => $value) {

            $template->setVariable($name, $value);
        }
    }

    public function setMediaProviders(array $providers)
    {
        $this->_mediaProviders = $providers;
    }

    private function _getToShowMap()
    {
        $toReturn = array();
        $map      = $this->_getMetaOptionNamesToAttributeDisplayNames();

        foreach ($map as $metaOptionName => $attributeName) {

            if ($this->_context->get($metaOptionName)) {

                $toReturn[] = $attributeName;
            }
        }

        return $toReturn;
    }

    private function _getLabelMap()
    {
        $toReturn = array();
        $map      = $this->_getMetaOptionNamesToAttributeDisplayNames();

        foreach ($map as $metaOptionName => $attributeName) {

            $label                    = $this->_optionReference->getUntranslatedLabel($metaOptionName);
            $toReturn[$attributeName] = $this->_translator->_($label);
        }

        return $toReturn;
    }

    private function _getMetaOptionNamesToAttributeDisplayNames()
    {
        if (!isset($this->_cacheOfMetaOptionNamesToAttributeDisplayNames)) {

            $this->_cacheOfMetaOptionNamesToAttributeDisplayNames = array();

            foreach ($this->_mediaProviders as $mediaProvider) {

                $this->_cacheOfMetaOptionNamesToAttributeDisplayNames = array_merge(
                    $this->_cacheOfMetaOptionNamesToAttributeDisplayNames,
                    $mediaProvider->getMapOfMetaOptionNamesToAttributeDisplayNames()
                );
            }
        }

        return $this->_cacheOfMetaOptionNamesToAttributeDisplayNames;
    }

    private function _setInvocationAnchorForPage(tubepress_core_media_provider_api_Page $page)
    {
        $galleryId  = $this->_context->get(tubepress_core_html_api_Constants::OPTION_GALLERY_ID);
        $playerName = $this->_context->get(tubepress_core_player_api_Constants::OPTION_PLAYER_LOCATION);

        foreach ($page->getItems() as $mediaItem) {

            $this->_setInvocationAnchorAttribute($galleryId, $playerName, $mediaItem);
            $this->_setPreAndPostAttributes($mediaItem);
        }
    }

    private function _setPreAndPostAttributes(tubepress_core_media_item_api_MediaItem $item)
    {
        if (!$item->hasAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_TITLE)) {

            return;
        }

        $anchorAttributes = $item->getAttribute(tubepress_core_media_item_api_Constants::ATTRIBUTE_INVOCATION_ANCHOR_ATTRIBUTES);
        $item->setAttribute(
            sprintf('%s.preHtml', tubepress_core_media_item_api_Constants::ATTRIBUTE_TITLE),
            sprintf('<a %s>', $anchorAttributes)
        );
        $item->setAttribute(
            sprintf('%s.postHtml', tubepress_core_media_item_api_Constants::ATTRIBUTE_TITLE),
            '</a>'
        );
    }

    private function _setInvocationAnchorForSingleItem(tubepress_core_media_item_api_MediaItem $item)
    {
        $galleryId  = $this->_context->get(tubepress_core_html_api_Constants::OPTION_GALLERY_ID);
        $playerName = $this->_context->get(tubepress_core_player_api_Constants::OPTION_PLAYER_LOCATION);

        $this->_setInvocationAnchorAttribute($galleryId, $playerName, $item);
    }

    private function _setInvocationAnchorAttribute($galleryId, $playerName, tubepress_core_media_item_api_MediaItem $item)
    {
        $data = array(
            'rel' => sprintf('tubepress_x_%s_%s', $playerName, $galleryId)
        );

        $event = $this->_eventDispatcher->newEventInstance($data, array(
            'item'       => $item,
            'galleryId'  => $galleryId,
            'playerName' => $playerName
        ));

        $this->_eventDispatcher->dispatch(tubepress_core_media_item_api_Constants::EVENT_ANCHOR_INVOCATION, $event);

        $result   = $event->getSubject();
        $toReturn = array();
        foreach ($result as $attributeName => $attributeValue) {
            $toReturn[] = sprintf('%s="%s"', $attributeName, str_replace('"', '\\"', $attributeValue));
        }

        $item->setAttribute(
            tubepress_core_media_item_api_Constants::ATTRIBUTE_INVOCATION_ANCHOR_ATTRIBUTES,
            implode(' ', $toReturn)
        );
    }
}
