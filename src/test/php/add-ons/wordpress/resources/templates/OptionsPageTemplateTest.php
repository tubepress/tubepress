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

class tubepress_test_addons_wordpress_resources_templates_OptionsPageTemplateTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockParticipantFilterField;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockNonceField;

    public function onSetup()
    {
        $this->_mockParticipantFilterField = ehough_mockery_Mockery::mock('tubepress_spi_options_ui_OptionsPageFieldInterface');
        $this->_mockParticipantFilterField->shouldReceive('getTranslatedDisplayName')->once()->andReturn('participant filter name');
        $this->_mockParticipantFilterField->shouldReceive('getWidgetHTML')->once()->andReturn('abc');

        $this->_mockNonceField = ehough_mockery_Mockery::mock('tubepress_spi_options_ui_OptionsPageFieldInterface');
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
        include __DIR__ . '/../../../../../../main/php/add-ons/wordpress/resources/templates/options_page.tpl.php';
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

                'participant-filter-field' => $this->_mockParticipantFilterField,
                'tubepress-nonce'          => $this->_mockNonceField
            ),
            'errors'         => array(),
            'isPro'          => false,
            'categories'     => array(),
            'justSubmitted'  => true,
        );
    }

    private function _expected($fileName)
    {
        return file_get_contents(TUBEPRESS_ROOT . '/src/test/resources/add-ons/wordpress/resources/templates/' . $fileName);
    }

    private function _sanitize($string)
    {
        $noNewLines = tubepress_impl_util_StringUtils::removeEmptyLines($string);

        $arr = explode("\n", $noNewLines);

        $stripped = array_map('trim', $arr);

        return implode("\n", $stripped);
    }

}