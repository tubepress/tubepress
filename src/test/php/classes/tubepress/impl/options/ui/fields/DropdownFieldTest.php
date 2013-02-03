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
class tubepress_impl_options_ui_fields_DropdownFieldTest extends tubepress_impl_options_ui_fields_AbstractOptionDescriptorBasedFieldTest
{
    protected function _buildSut($name)
    {
        return new tubepress_impl_options_ui_fields_DropdownField($name, 'someTab');
    }

    protected function getTemplatePath()
    {
        return 'src/main/resources/system-templates/options_page/fields/dropdown.tpl.php';
    }

    protected function _performAdditionToStringTestSetup($template)
    {
        $od = $this->getMockOptionDescriptor();

        $od->setAcceptableValues(array('foo' => 'bar', 'smack' => 'rock'));

        $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_fields_DropdownField::TEMPLATE_VAR_ACCEPTABLE_VALUES,
            array('foo' => '<<message: bar>>', 'smack' => '<<message: rock>>'));
    }
}
