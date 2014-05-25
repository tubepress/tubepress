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

/**
 * @covers tubepress_core_impl_html_HtmlExtension
 */
class tubepress_test_core_impl_html_HtmlExtensionTest extends tubepress_test_impl_ioc_AbstractIocContainerExtensionTest
{

    /**
     * @return tubepress_core_impl_html_HtmlExtension
     */
    protected function buildSut()
    {
        return  new tubepress_core_impl_html_HtmlExtension();
    }

    protected function prepareForLoad()
    {
        $this->expectRegistration(

            tubepress_core_api_html_HtmlGeneratorInterface::_,
            'tubepress_core_impl_html_HtmlGenerator'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_event_EventDispatcherInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_shortcode_ParserInterface::_))
         ->withArgument(new tubepress_api_ioc_Reference(tubepress_core_api_theme_ThemeLibraryInterface::_));
    }

    protected function getExpectedServiceContructionMap()
    {
        return array(

            tubepress_core_api_html_HtmlGeneratorInterface::_ => 'tubepress_core_impl_html_HtmlGenerator'
        );
    }

    protected function getExpectedExternalServicesMap()
    {
        return array(

            tubepress_core_api_event_EventDispatcherInterface::_ => tubepress_core_api_event_EventDispatcherInterface::_,
            tubepress_core_api_shortcode_ParserInterface::_ => tubepress_core_api_shortcode_ParserInterface::_,
            tubepress_core_api_theme_ThemeLibraryInterface::_ => tubepress_core_api_theme_ThemeLibraryInterface::_
        );
    }
}
