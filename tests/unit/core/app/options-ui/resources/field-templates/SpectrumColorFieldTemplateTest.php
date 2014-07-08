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
class tubepress_test_impl_template_templates_optionspage_fields_SpectrumColorFieldTemplateTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_platform_impl_util_StringUtils
     */
    private $_stringUtils;

    public function onSetup()
    {
        $this->_stringUtils = new tubepress_platform_impl_util_StringUtils();
    }

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
        include TUBEPRESS_ROOT . '/src/core/app/options-ui/resources/field-templates/spectrum-color.tpl.php';
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertEquals($this->_stringUtils->removeEmptyLines($this->_expected()), $this->_stringUtils->removeEmptyLines($result));
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