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
 * Displays a drop-down input.
 */
class tubepress_impl_options_ui_fields_DropdownField extends tubepress_impl_options_ui_fields_AbstractOptionDescriptorBasedField
{
    protected final function getAbsolutePathToTemplate()
    {
        return TUBEPRESS_ROOT . '/src/main/resources/options-gui/field-templates/dropdown.tpl.php';
    }

    protected function getAdditionalTemplateVariables()
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

        return array('choices' => $values);
    }
}