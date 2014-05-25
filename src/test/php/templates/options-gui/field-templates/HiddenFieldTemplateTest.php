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
class tubepress_test_impl_template_templates_optionspage_fields_HiddenFieldTemplateTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_impl_util_StringUtils
     */
    private $_stringUtils;

    public function onSetup()
    {
        $this->_stringUtils = new tubepress_impl_util_StringUtils();
    }

    public function test()
    {
        $id = 'some-name';
        $value = 'some-value';

        ob_start();
        include TUBEPRESS_ROOT . '/src/main/resources/options-gui/field-templates/hidden.tpl.php';
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertEquals($this->_stringUtils->removeEmptyLines($this->_expected()), $this->_stringUtils->removeEmptyLines($result));
    }

    private function _expected()
    {
        return <<<EOT
<input type="hidden" id="some-name" name="some-name" value="some-value" />
EOT;
    }

}