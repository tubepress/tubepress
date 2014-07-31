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
class tubepress_deprecated_impl_listeners_LegacyMetadataTemplateListener
{
    /**
     * @var tubepress_app_api_media_MediaProviderInterface[]
     */
    private $_mediaProviders;

    /**
     * @var tubepress_lib_api_translation_TranslatorInterface
     */
    private $_translator;

    /**
     * @var tubepress_app_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_app_api_options_ReferenceInterface
     */
    private $_optionReference;

    public function __construct(tubepress_app_api_options_ContextInterface        $context,
                                tubepress_app_api_options_ReferenceInterface      $optionReference,
                                tubepress_lib_api_translation_TranslatorInterface $translator)
    {
        $this->_translator      = $translator;
        $this->_context         = $context;
        $this->_optionReference = $optionReference;
    }

    public function onSingleTemplate(tubepress_lib_api_event_EventInterface $event)
    {
        /**
         * @var $template tubepress_lib_api_template_TemplateInterface
         */
        $template = $event->getSubject();

        if (!$event->hasArgument('item')) {

            $template->setVariable('video', new tubepress_app_api_media_MediaItem('id'));

        } else {

            $template->setVariable('video', $event->getArgument('item'));
        }
    }

    public function onTemplate(tubepress_lib_api_event_EventInterface $event)
    {
        /**
         * @var $template tubepress_lib_api_template_TemplateInterface
         */
        $template = $event->getSubject();

        $this->_setLegacyVariables($template);
    }

    public function setMediaProviders(array $providers)
    {
        $this->_mediaProviders = $providers;
    }

    private function _setLegacyVariables(tubepress_lib_api_template_TemplateInterface $template)
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
