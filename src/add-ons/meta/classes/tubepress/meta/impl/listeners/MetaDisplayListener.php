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

/**
 * This listener is responsible for populating the template with the following
 * variables:
 *
 * tubepress_api_template_VariableNames::MEDIA_ITEM_ATTRIBUTES_TO_SHOW
 * tubepress_api_template_VariableNames::MEDIA_ITEM_ATTRIBUTE_LABELS
 */
class tubepress_meta_impl_listeners_MetaDisplayListener
{
    /**
     * @var tubepress_spi_media_MediaProviderInterface[]
     */
    private $_mediaProviders;

    /**
     * @var tubepress_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_api_options_ReferenceInterface
     */
    private $_optionReference;

    /**
     * @var array
     */
    private $_cacheOfMetaOptionNamesToAttributeDisplayNames;

    public function __construct(tubepress_api_options_ContextInterface   $context,
                                tubepress_api_options_ReferenceInterface $optionReference)
    {
        $this->_context         = $context;
        $this->_optionReference = $optionReference;
    }

    public function onPreTemplate(tubepress_api_event_EventInterface $event)
    {
        /*
         * @var array
         */
        $templateVars = $event->getSubject();

        if (!is_array($templateVars)) {

            $templateVars = array();
        }

        $vars = array(

            tubepress_api_template_VariableNames::MEDIA_ITEM_ATTRIBUTE_LABELS   => $this->_getLabelMap(),
            tubepress_api_template_VariableNames::MEDIA_ITEM_ATTRIBUTES_TO_SHOW => $this->_getToShowMap(),
        );

        $templateVars = array_merge($templateVars, $vars);

        $event->setSubject($templateVars);
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
            $toReturn[$attributeName] = $label;
        }

        return $toReturn;
    }

    private function _getMetaOptionNamesToAttributeDisplayNames()
    {
        if (!isset($this->_cacheOfMetaOptionNamesToAttributeDisplayNames)) {

            $this->_cacheOfMetaOptionNamesToAttributeDisplayNames = array();

            foreach ($this->_mediaProviders as $mediaProvider) {

                $mapFromProvider = $mediaProvider->getMapOfMetaOptionNamesToAttributeDisplayNames();

                foreach ($mapFromProvider as $metaOptionName => $attributeName) {

                    if (!isset($this->_cacheOfMetaOptionNamesToAttributeDisplayNames[$metaOptionName])) {

                        $this->_cacheOfMetaOptionNamesToAttributeDisplayNames[$metaOptionName] = $attributeName;
                    }
                }

                /*
                 * The description is best kept as the last element.
                 */
                if (isset($this->_cacheOfMetaOptionNamesToAttributeDisplayNames[tubepress_api_options_Names::META_DISPLAY_DESCRIPTION])) {

                    $oldVal = $this->_cacheOfMetaOptionNamesToAttributeDisplayNames[tubepress_api_options_Names::META_DISPLAY_DESCRIPTION];
                    unset($this->_cacheOfMetaOptionNamesToAttributeDisplayNames[tubepress_api_options_Names::META_DISPLAY_DESCRIPTION]);
                    $this->_cacheOfMetaOptionNamesToAttributeDisplayNames[tubepress_api_options_Names::META_DISPLAY_DESCRIPTION] = $oldVal;
                }
            }
        }

        return $this->_cacheOfMetaOptionNamesToAttributeDisplayNames;
    }
}
