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

        $messageService = tubepress_impl_patterns_sl_ServiceLocator::getMessageService();

        foreach ($map as $key => $value) {

            $values[$key] = $messageService->_($value);
        }

        $template->setVariable(self::TEMPLATE_VAR_ACCEPTABLE_VALUES, $values);
    }
}