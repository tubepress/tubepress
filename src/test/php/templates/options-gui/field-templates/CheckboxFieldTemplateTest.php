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
class tubepress_test_impl_template_templates_optionspage_fields_CheckboxFieldTemplateTest extends tubepress_test_TubePressUnitTest
{
    public function test()
    {
        $id = 'some-name';
        $value = true;

        ob_start();
        include TUBEPRESS_ROOT . '/src/main/resources/options-gui/field-templates/checkbox.tpl.php';
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(tubepress_impl_util_StringUtils::removeEmptyLines($this->_expected(true)), tubepress_impl_util_StringUtils::removeEmptyLines($result));

        $value = false;

        ob_start();
        include TUBEPRESS_ROOT . '/src/main/resources/options-gui/field-templates/checkbox.tpl.php';
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(tubepress_impl_util_StringUtils::removeEmptyLines($this->_expected(false)), tubepress_impl_util_StringUtils::removeEmptyLines($result));
    }

    private function _expected($checked)
    {
        if ($checked) {

            return '<input id="some-name" type="checkbox" name="some-name" CHECKED />';
        }

        return '<input id="some-name" type="checkbox" name="some-name"  />';
    }

}