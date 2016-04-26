<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_single_ioc_SingleExtension
 */
class tubepress_test_single_ioc_SingleExtensionTest extends tubepress_api_test_ioc_AbstractContainerExtensionTest
{
    /**
     * @return tubepress_single_ioc_SingleExtension
     */
    protected function buildSut()
    {
        return  new tubepress_single_ioc_SingleExtension();
    }

    protected function prepareForLoad()
    {
        $this->_registerListeners();
        $this->_registerTemplatePathProvider();
        $this->_registerOptions();
    }

    private function _registerOptions()
    {
        $this->expectRegistration(
            'tubepress_api_options_Reference__single',
            'tubepress_api_options_Reference'
        )->withTag(tubepress_api_options_ReferenceInterface::_)
            ->withArgument(array(
                tubepress_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(
                    tubepress_api_options_Names::SINGLE_MEDIA_ITEM_ID => null,
                ),
            ))->withArgument(array(
                tubepress_api_options_Reference::PROPERTY_NO_PERSIST => array(
                    tubepress_api_options_Names::SINGLE_MEDIA_ITEM_ID,
                ),
            ));
    }

    private function _registerListeners()
    {
        $this->expectRegistration(
            'tubepress_single_impl_listeners_SingleItemListener',
            'tubepress_single_impl_listeners_SingleItemListener'
        )->withArgument(new tubepress_api_ioc_Reference(tubepress_api_log_LoggerInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_options_ContextInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_media_CollectorInterface::_))
            ->withArgument(new tubepress_api_ioc_Reference(tubepress_api_template_TemplatingInterface::_))
            ->withTag(tubepress_api_ioc_ServiceTags::EVENT_LISTENER, array(
                'event'    => tubepress_api_event_Events::HTML_GENERATION,
                'priority' => 94000,
                'method'   => 'onHtmlGeneration',
            ));
    }

    private function _registerTemplatePathProvider()
    {
        $this->expectRegistration(
            'tubepress_api_template_BasePathProvider__single',
            'tubepress_api_template_BasePathProvider'
        )->withArgument(array(
            TUBEPRESS_ROOT . '/src/add-ons/single/templates',
        ))->withTag('tubepress_spi_template_PathProviderInterface');
    }

    protected function getExpectedExternalServicesMap()
    {
        return array(
            tubepress_api_log_LoggerInterface::_          => tubepress_api_log_LoggerInterface::_,
            tubepress_api_options_ContextInterface::_     => tubepress_api_options_ContextInterface::_,
            tubepress_api_media_CollectorInterface::_     => tubepress_api_media_CollectorInterface::_,
            tubepress_api_template_TemplatingInterface::_ => tubepress_api_template_TemplatingInterface::_,
        );
    }
}
