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
class tubepress_impl_options_ui_fields_FilterMultiSelectField extends tubepress_impl_options_ui_fields_AbstractMultiSelectField
{
    public function __construct()
    {
        $odr = tubepress_impl_patterns_ioc_KernelServiceLocator::getOptionDescriptorReference();

        parent::__construct(

            array(

                $odr->findOneByName(tubepress_api_const_options_names_OptionsUi::SHOW_VIMEO_OPTIONS),
                $odr->findOneByName(tubepress_api_const_options_names_OptionsUi::SHOW_YOUTUBE_OPTIONS),

            ), 'filterdropdown');
    }

    /**
     * Get the untranslated title of this field.
     *
     * @return string The untranslated title of this field.
     */
    protected final function getRawTitle()
    {
        return 'Only show options applicable to...';    //>(translatable)<
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
        return null;
    }
}