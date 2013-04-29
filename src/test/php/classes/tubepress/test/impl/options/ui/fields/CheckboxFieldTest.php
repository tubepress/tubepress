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
class tubepress_test_impl_options_ui_fields_CheckboxFieldTest extends tubepress_test_impl_options_ui_fields_AbstractOptionDescriptorBasedFieldTest
{
    protected function _buildSut($name)
    {
        return new tubepress_impl_options_ui_fields_BooleanField($name, 'something');
    }

    protected function getTemplatePath()
    {
        return 'src/main/resources/system-templates/options_page/fields/checkbox.tpl.php';
    }
}
