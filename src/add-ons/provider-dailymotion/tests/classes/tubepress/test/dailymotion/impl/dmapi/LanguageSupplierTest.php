<?php
/*
 * Copyright 2006 - 2018 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_dailymotion_impl_dmapi_LanguageSupplier<extended>
 */
class tubepress_test_dailymotion_impl_dmapi_LanguageSupplierTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var Mockery\MockInterface
     */
    private $_mockUrlFactory;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockApiUtility;

    /**
     * @var tubepress_util_impl_StringUtils
     */
    private $_stringUtils;

    public function onSetup()
    {
        $this->_mockUrlFactory = $this->mock(tubepress_api_url_UrlFactoryInterface::_);
        $this->_mockApiUtility = $this->mock('tubepress_dailymotion_impl_dmapi_ApiUtility');
        $this->_stringUtils    = new tubepress_util_impl_StringUtils();
    }

    /**
     * @dataProvider getData
     */
    public function testFetch($codeKey, $apiResponse, $expected)
    {
        $mockUrl = $this->mock(tubepress_api_url_UrlInterface::_);
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('https://foo.com/bar')->andReturn($mockUrl);

        $this->_mockApiUtility->shouldReceive('getDecodedApiResponse')->once()->with($mockUrl)->andReturn(
            json_decode($apiResponse, true)
        );

        $sut = new tubepress_dailymotion_impl_dmapi_LanguageSupplier(
            $this->_mockUrlFactory,
            $this->_stringUtils,
            $this->_mockApiUtility,
            'https://foo.com/bar',
            $codeKey
        );

        $actual = $sut->getValueMap();

        $this->assertEquals($expected, $actual);
    }

    public function getData()
    {
        return array(
            array(
                'code',
                $this->_getJsonLanguages(),
                array(
                    'none' => 'select ...',
                    'af'   => 'af - Afrikaans',
                    'ak'   => 'ak - Akan',
                ),
            ),
        );
    }

    private function _getJsonLanguages()
    {
        return <<<EOT
{
	"list": [{
		"code": "af",
		"name": "Afrikaans",
		"native_name": "Afrikaans",
		"localized_name": "Afrikaans",
		"display_name": "Afrikaans"
	}, {
		"code": "ak",
		"name": "Akan",
		"native_name": null,
		"localized_name": "Akan",
		"display_name": "Akan"
	}]
}
EOT;
    }
}
