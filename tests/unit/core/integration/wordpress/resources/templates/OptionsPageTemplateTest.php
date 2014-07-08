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

class tubepress_test_wordpress_resources_templates_OptionsPageTemplateTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockFilterField;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockNonceField;

    public function onSetup()
    {
        $this->_mockFilterField = $this->mock('tubepress_app_options_ui_api_FieldInterface');
        $this->_mockFilterField->shouldReceive('getTranslatedDisplayName')->once()->andReturn('field provider filter name');
        $this->_mockFilterField->shouldReceive('getWidgetHTML')->once()->andReturn('abc');

        $this->_mockNonceField = $this->mock('tubepress_app_options_ui_api_FieldInterface');
        $this->_mockNonceField->shouldReceive('getWidgetHTML')->once()->andReturn('xyz');
    }

    public function testNotSubmittedNoError()
    {
        $variables = $this->_getBaseVariables();

        $variables['justSubmitted'] = false;

        $this->_runTemplateTest($variables, 'expected-options-page-notsubmitted-noerror.html');
    }

    public function testSubmittedNoError()
    {
        $variables = $this->_getBaseVariables();

        $this->_runTemplateTest($variables, 'expected-options-page-submitted-noerror.html');
    }

    private function _runTemplateTest($variables, $fileName)
    {
        extract($variables);

        ob_start();
        include TUBEPRESS_ROOT  .'/src/core/integration/wordpress/resources/templates/options_page.tpl.php';
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertEquals(
            $this->_sanitize($this->_expected($fileName)),
            $this->_sanitize($result)
        );
    }

    private function _getBaseVariables()
    {
        return array(

            'pageTitle'      => 'The page title',
            'introBlurb'     => 'The intro blurb',
            'successMessage' => 'SUCCESS',
            'fields'         => array(

                'field-provider-filter-field' => $this->_mockFilterField,
                'tubepress-nonce'             => $this->_mockNonceField
            ),
            'errors'         => array(),
            'isPro'          => false,
            'categories'     => array(),
            'justSubmitted'  => true,
            'saveText'       => 'Save me',
        );
    }

    private function _expected($fileName)
    {
        return file_get_contents(TUBEPRESS_ROOT . '/tests/unit/core/integration/wordpress/resources/templates/templates/' . $fileName);
    }

    private function _sanitize($string)
    {
        $stringUtils = new tubepress_platform_impl_util_StringUtils();
        $noNewLines = $stringUtils->removeEmptyLines($string);

        $arr = explode("\n", $noNewLines);

        $stripped = array_map('trim', $arr);

        return implode("\n", $stripped);
    }

}