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
 * TEMPLATE_VAR_META_LABELS
 * TEMPLATE_VAR_META_SHOULD_SHOW
 */
class tubepress_core_deprecated_impl_listeners_LegacyMetadataTemplateListener
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

    public function __construct(tubepress_core_options_api_ContextInterface        $context,
                                tubepress_core_options_api_ReferenceInterface      $optionReference,
                                tubepress_core_translation_api_TranslatorInterface $translator)
    {
        $this->_translator      = $translator;
        $this->_context         = $context;
        $this->_optionReference = $optionReference;
    }

    public function onSingleTemplate(tubepress_core_event_api_EventInterface $event)
    {
        /**
         * @var $template tubepress_core_template_api_TemplateInterface
         */
        $template = $event->getSubject();

        $template->setVariable('video', $event->getArgument('item'));
    }

    public function onTemplate(tubepress_core_event_api_EventInterface $event)
    {
        /**
         * @var $template tubepress_core_template_api_TemplateInterface
         */
        $template = $event->getSubject();

        $this->_setLegacyVariables($template);
    }

    public function setMediaProviders(array $providers)
    {
        $this->_mediaProviders = $providers;
    }

    private function _setLegacyVariables(tubepress_core_template_api_TemplateInterface $template)
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

        $template->setVariable(tubepress_api_const_template_Variable::META_SHOULD_SHOW, $shouldShow);
        $template->setVariable(tubepress_api_const_template_Variable::META_LABELS, $labels);
    }

    private function _getAllMetaOptionNames()
    {
        $toReturn = array();

        foreach ($this->_mediaProviders as $mediaProvider) {

            $toReturn = array_merge($toReturn, array_keys($mediaProvider->getMapOfMetaOptionNamesToAttributeDisplayNames()));
        }

        return array_unique($toReturn);
    }
}
