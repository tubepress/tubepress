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
 * Adds some core variables to the single media item template.
 */
class tubepress_core_html_single_impl_listeners_template_SingleVideoCoreVariables
{
    /**
     * @var tubepress_core_options_api_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_core_embedded_api_EmbeddedHtmlInterface
     */
    private $_embeddedHtml;

    /**
     * @var tubepress_core_media_provider_api_MediaProviderInterface[]
     */
    private $_mediaProviders;

    /**
     * @var tubepress_core_options_api_ReferenceInterface
     */
    private $_optionReference;

    /**
     * @var tubepress_core_translation_api_TranslatorInterface
     */
    private $_translator;

    public function __construct(tubepress_core_options_api_ContextInterface        $context,
                                tubepress_core_embedded_api_EmbeddedHtmlInterface  $embeddedHtml,
                                tubepress_core_options_api_ReferenceInterface      $optionReference,
                                tubepress_core_translation_api_TranslatorInterface $translator)
    {
        $this->_context         = $context;
        $this->_embeddedHtml    = $embeddedHtml;
        $this->_optionReference = $optionReference;
        $this->_translator      = $translator;
    }

    public function onSingleVideoTemplate(tubepress_core_event_api_EventInterface $event)
    {
        $mediaItem = $event->getArgument('item');
        $template  = $event->getSubject();

        $embeddedString = $this->_embeddedHtml->getHtml($mediaItem->getId());
        $width          = $this->_context->get(tubepress_core_embedded_api_Constants::OPTION_EMBEDDED_WIDTH);

        /* apply it to the template */
        $template->setVariable(tubepress_core_embedded_api_Constants::TEMPLATE_VAR_SOURCE, $embeddedString);
        $template->setVariable(tubepress_core_embedded_api_Constants::TEMPLATE_VAR_WIDTH, $width);
        $template->setVariable(tubepress_core_html_single_api_Constants::TEMPLATE_VAR_MEDIA_ITEM, $mediaItem);

        $this->_setVideoMetaDisplay($template);
    }

    public function setMediaProviders(array $providers)
    {
        $this->_mediaProviders = $providers;
    }

    private function _setVideoMetaDisplay(tubepress_core_template_api_TemplateInterface $template)
    {
        $metaNames  = $this->_getAllMetaOptionNames();
        $shouldShow = array();
        $labels     = array();

        foreach ($metaNames as $metaName) {

            if (!$this->_optionReference->optionExists($metaName)) {

                $shouldShow[$metaName] = false;
                $labels[$metaName]     = '';
                continue;
            }

            $shouldShow[$metaName] = $this->_context->get($metaName);
            $untranslatedLabel     = $this->_optionReference->getUntranslatedLabel($metaName);
            $labels[$metaName]     = $this->_translator->_($untranslatedLabel);
        }

        $template->setVariable(tubepress_core_media_item_api_Constants::TEMPLATE_VAR_META_SHOULD_SHOW, $shouldShow);
        $template->setVariable(tubepress_core_media_item_api_Constants::TEMPLATE_VAR_META_LABELS, $labels);
    }

    private function _getAllMetaOptionNames()
    {
        $toReturn = array();

        foreach ($this->_mediaProviders as $mediaProvider) {

            $toReturn = array_merge($toReturn, $mediaProvider->getMetaOptionNames());
        }

        return array_unique($toReturn);
    }
}