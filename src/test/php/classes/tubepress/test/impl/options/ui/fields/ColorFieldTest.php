<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_impl_options_ui_fields_ColorField<extended>
 */
class tubepress_test_impl_options_ui_fields_ColorFieldTest extends tubepress_test_impl_options_ui_fields_AbstractOptionDescriptorBasedFieldTest
{
    protected function buildSut($name)
    {
        return new tubepress_impl_options_ui_fields_ColorField($name);
    }

    protected function getAbsolutePathToTemplate()
    {
        return TUBEPRESS_ROOT . '/src/main/resources/admin-page-templates/fields/color.tpl.php';
    }
}
