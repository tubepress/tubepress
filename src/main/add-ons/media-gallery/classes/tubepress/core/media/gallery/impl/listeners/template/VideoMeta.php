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
 * Handles applying video meta info to the gallery template.
 */
class tubepress_core_media_gallery_impl_listeners_template_VideoMeta
{
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
    private $_optionProvider;

    /**
     * @var tubepress_core_provider_api_MediaProviderInterface[]
     */
    private $_videoProviders;

    public function __construct(tubepress_core_options_api_ContextInterface        $context,
                                tubepress_core_translation_api_TranslatorInterface $translator,
                                tubepress_core_options_api_ReferenceInterface      $optionProvider)
    {
        $this->_translator             = $translator;
        $this->_context                = $context;
        $this->_optionProvider         = $optionProvider;
    }

    public function onGalleryTemplate(tubepress_core_event_api_EventInterface $event)
    {
        $metaNames  = $this->_getAllMetaOptionNames();
        $shouldShow = array();
        $labels     = array();
        $template   = $event->getSubject();

        foreach ($metaNames as $metaName => $untranslatedLabel) {

            if (!$this->_optionProvider->optionExists($metaName)) {

                $shouldShow[$metaName] = false;
                $labels[$metaName]     = '';
                continue;
            }

            $shouldShow[$metaName] = $this->_context->get($metaName);
            $labels[$metaName]     = $this->_translator->_($untranslatedLabel);
        }

        $template->setVariable(tubepress_core_template_api_const_VariableNames::META_SHOULD_SHOW, $shouldShow);
        $template->setVariable(tubepress_core_template_api_const_VariableNames::META_LABELS, $labels);
    }

    public function setVideoProviders(array $providers)
    {
        $this->_videoProviders = $providers;
    }

    private function _getAllMetaOptionNames()
    {
        $toReturn = array();

        foreach ($this->_videoProviders as $videoProvider) {

            $toReturn = array_merge($toReturn, $videoProvider->getMapOfMetaOptionNamesToUntranslatedLabels());
        }

        return $toReturn;
    }
}