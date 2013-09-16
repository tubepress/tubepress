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
class tubepress_test_impl_template_templates_optionspage_fields_SpectrumColorFieldTemplateTest extends tubepress_test_TubePressUnitTest
{
    public function test()
    {
        $id = 'some-name';
        $name = 'some-value';

        $value = '444554';
        $preferredFormat = 'hex';
        $showAlpha = false;
        $showInput = true;
        $showPalette = true;
        $cancelText = 'yikes';
        $chooseText = 'foo';

        ob_start();
        include TUBEPRESS_ROOT . '/src/main/resources/options-gui/field-templates/spectrum-color.tpl.php';
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(tubepress_impl_util_StringUtils::removeEmptyLines($this->_expected()), tubepress_impl_util_StringUtils::removeEmptyLines($result));
    }

    private function _expected()
    {
        return <<<EOT
<input id="some-name" type="text" name="some-name" size="6" class="tubepress-spectrum-field" value="444554"
    data-preferredformat="hex"
    data-showalpha="false"
    data-showinput="true"
    data-showpalette="true"
    data-canceltext="yikes"
    data-choosetext="foo"
    />
EOT;
    }

}