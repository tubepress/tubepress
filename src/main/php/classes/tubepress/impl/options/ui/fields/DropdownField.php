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
 * Displays a drop-down input.
 */
class tubepress_impl_options_ui_fields_DropdownField extends tubepress_impl_options_ui_fields_AbstractOptionDescriptorBasedField
{
    const FIELD_CLASS_NAME = 'tubepress_impl_options_ui_fields_DropdownField';

    const TEMPLATE_VAR_ACCEPTABLE_VALUES = 'tubepress_impl_options_ui_fields_DropdownField__options';

    /**
     * Get the path to the template for this field, relative
     * to TubePress's root.
     *
     * @return string The path to the template for this field, relative
     *                to TubePress's root.
     */
    protected final function getTemplatePath()
    {
        return 'src/main/resources/system-templates/options_page/fields/dropdown.tpl.php';
    }

    /**
     * Override point.
     *
     * Allows subclasses to perform additional modifications to this
     * field's template.
     *
     * @param ehough_contemplate_api_Template $template     The field's template.
     * @param string                          $currentValue The current value of this field.
     *
     * @throws InvalidArgumentException If a non-associative array is set for its value map.
     *
     * @return void
     */
    protected final function populateTemplate($template, $currentValue)
    {
        $values = array();
        $map    = $this->getOptionDescriptor()->getAcceptableValues();

        if (! tubepress_impl_util_LangUtils::isAssociativeArray($map)) {

            throw new InvalidArgumentException(sprintf('"%s" has a non-associative array set for its value map', $this->getOptionDescriptor()->getName()));
        }

        $messageService = tubepress_impl_patterns_ioc_KernelServiceLocator::getMessageService();

        foreach ($map as $key => $value) {

            $values[$key] = $messageService->_($value);
        }

        $template->setVariable(self::TEMPLATE_VAR_ACCEPTABLE_VALUES, $values);
    }
}