<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 * Displays a multi-select drop-down input for video meta.
 */
class tubepress_impl_options_ui_fields_MetaMultiSelectField extends tubepress_impl_options_ui_fields_AbstractMultiSelectField
{
    public function __construct()
    {
        $reference = tubepress_impl_patterns_ioc_KernelServiceLocator::getOptionDescriptorReference();

        parent::__construct(array(

            $reference->findOneByName(tubepress_api_const_options_names_Meta::AUTHOR),
            $reference->findOneByName(tubepress_api_const_options_names_Meta::RATING),
            $reference->findOneByName(tubepress_api_const_options_names_Meta::CATEGORY),
            $reference->findOneByName(tubepress_api_const_options_names_Meta::UPLOADED),
            $reference->findOneByName(tubepress_api_const_options_names_Meta::DESCRIPTION),
            $reference->findOneByName(tubepress_api_const_options_names_Meta::ID),
            $reference->findOneByName(tubepress_api_const_options_names_Meta::KEYWORDS),
            $reference->findOneByName(tubepress_api_const_options_names_Meta::LIKES),
            $reference->findOneByName(tubepress_api_const_options_names_Meta::RATINGS),
            $reference->findOneByName(tubepress_api_const_options_names_Meta::LENGTH),
            $reference->findOneByName(tubepress_api_const_options_names_Meta::TITLE),
            $reference->findOneByName(tubepress_api_const_options_names_Meta::URL),
            $reference->findOneByName(tubepress_api_const_options_names_Meta::VIEWS),

        ), 'metadropdown');
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

    public final function getDesiredTabName()
    {
        return 'meta';
    }

}