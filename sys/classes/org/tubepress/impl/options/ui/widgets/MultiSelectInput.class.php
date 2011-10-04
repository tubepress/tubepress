<?php
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require(dirname(__FILE__) . '/../../../classloader/ClassLoader.class.php');
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_spi_options_ui_Widget',
));

/**
 * Displays a multi-select drop-down input.
 */
class org_tubepress_impl_options_ui_widgets_MultiSelect implements org_tubepress_spi_options_ui_Widget
{
    const TEMPLATE_VAR_NAME = 'org_tubepress_impl_options_ui_widgets_MultiSelect__name';

    const TEMPLATE_VAR_DESCRIPTORS = 'org_tubepress_impl_options_ui_widgets_MultiSelect__descriptors';

    const TEMPLATE_VAR_CURRENTVALUES = 'org_tubepress_impl_options_ui_widgets_MultiSelect__currentValues';

    /** Array of option descriptors. */
    private $_optionDescriptors;

    /** Array of current values. */
    private $_currentValues;

    /** Label. */
    private $_label;

    /** Description. */
    private $_description;

    public function __construct($optionDescriptors, $currentValues, $label, $description = '')
    {
        if (! is_array($optionDescriptors)) {

            throw new Exception('Option descriptors must be an array');
        }

        if (! is_array($currentValues)) {

            throw new Exception('Current values must be an array');
        }

        if (! is_string($label)) {

            throw new Exception('Label must be a string');
        }

        $this->_optionDescriptors = $optionDescriptors;
        $this->_currentValues     = $currentValues;
        $this->_label             = $label;
        $this->_description       = $description;
    }

    function getTitle()
    {
        return $this->_label;
    }

    function getDescription()
    {
        return $this->_description;
    }

    function isProOnly()
    {
        return false;
    }

    function getArrayOfApplicableProviderNames()
    {
        return array(

            org_tubepress_api_provider_Provider::YOUTUBE,
            org_tubepress_api_provider_Provider::VIMEO,
        );
    }

    function onSubmit($postVars)
    {

    }

    function getHtml()
    {

    }
}