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
class tubepress_app_media_item_impl_listeners_template_ToShowAndLabelsMapSetter
{
    /**
     * @var tubepress_app_media_provider_api_MediaProviderInterface[]
     */
    private $_mediaProviders;

    /**
     * @var tubepress_lib_translation_api_TranslatorInterface
     */
    private $_translator;

    /**
     * @var tubepress_app_options_api_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_app_options_api_ReferenceInterface
     */
    private $_optionReference;

    /**
     * @var array
     */
    private $_cacheOfMetaOptionNamesToAttributeDisplayNames;

    public function __construct(tubepress_app_options_api_ContextInterface        $context,
                                tubepress_app_options_api_ReferenceInterface      $optionReference,
                                tubepress_lib_translation_api_TranslatorInterface $translator)
    {
        $this->_translator      = $translator;
        $this->_context         = $context;
        $this->_optionReference = $optionReference;
    }

    public function onTemplate(tubepress_lib_event_api_EventInterface $event)
    {
        /**
         * @var $template tubepress_lib_template_api_TemplateInterface
         */
        $template = $event->getSubject();

        $vars = array(

            tubepress_app_media_item_api_Constants::TEMPLATE_VAR_ATTRIBUTE_LABELS   => $this->_getLabelMap(),
            tubepress_app_media_item_api_Constants::TEMPLATE_VAR_ATTRIBUTES_TO_SHOW => $this->_getToShowMap()
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

            if ($attributeName === tubepress_app_media_item_api_Constants::ATTRIBUTE_DESCRIPTION) {

                continue;
            }

            if ($this->_context->get($metaOptionName)) {

                $toReturn[] = $attributeName;
            }
        }

        if (array_key_exists(tubepress_app_media_item_api_Constants::OPTION_DESCRIPTION, $map)) {

            if ($this->_context->get(tubepress_app_media_item_api_Constants::OPTION_DESCRIPTION)) {

                $toReturn[] = $map[tubepress_app_media_item_api_Constants::OPTION_DESCRIPTION];
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
}