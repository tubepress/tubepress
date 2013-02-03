<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Displays a multi-select drop-down input for video meta.
 */
class tubepress_impl_options_ui_fields_MetaMultiSelectField extends tubepress_impl_options_ui_fields_AbstractMultiSelectField
{
    public function __construct()
    {
        $metaNames = array(

            tubepress_api_const_options_names_Meta::AUTHOR,
            tubepress_api_const_options_names_Meta::CATEGORY,
            tubepress_api_const_options_names_Meta::UPLOADED,
            tubepress_api_const_options_names_Meta::DESCRIPTION,
            tubepress_api_const_options_names_Meta::ID,
            tubepress_api_const_options_names_Meta::KEYWORDS,
            tubepress_api_const_options_names_Meta::LENGTH,
            tubepress_api_const_options_names_Meta::TITLE,
            tubepress_api_const_options_names_Meta::URL,
            tubepress_api_const_options_names_Meta::VIEWS,
        );

        $videoProviders = tubepress_impl_patterns_sl_ServiceLocator::getVideoProviders();

        foreach ($videoProviders as $videoProvider) {

            $metaNames = array_merge($metaNames, $videoProvider->getAdditionalMetaNames());
        }

        $metas     = array();
        $reference = tubepress_impl_patterns_sl_ServiceLocator::getOptionDescriptorReference();

        foreach ($metaNames as $metaName) {

            $metas[] = $reference->findOneByName($metaName);
        }

        parent::__construct($metas, 'metadropdown');
    }

    /**
     * Get the untranslated title of this field.
     *
     * @return string The untranslated title of this field.
     */
    protected final function getRawTitle()
    {
        return 'Show each video\'s...';   //>(translatable)<
    }

    /**
     * Get the untranslated description of this field.
     *
     * @return string The untranslated description of this field.
     */
    protected final function getRawDescription()
    {
        return '';
    }
}