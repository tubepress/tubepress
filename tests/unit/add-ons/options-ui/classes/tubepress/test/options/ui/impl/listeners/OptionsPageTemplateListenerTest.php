<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_options_ui_impl_listeners_OptionsPageTemplateListener
 */
class tubepress_test_options_ui_impl_listeners_OptionsPageTemplateListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockIncomingEvent;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironment;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockFieldProviderVimeo;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockMediaProviderVimeo;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockMediaProviderYouTube;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockFieldProviderCore;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockCategoryEmbedded;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockCategoryGallerySource;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTranslator;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockBaseUrl;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockStringUtils;

    /**
     * @var tubepress_options_ui_impl_listeners_OptionsPageTemplateListener
     */
    private $_sut;

    /**
     * @var array
     */
    private $_fieldsVar;

    public function onSetup()
    {
        $this->_mockIncomingEvent         = $this->mock('tubepress_lib_api_event_EventInterface');
        $this->_mockEnvironment           = $this->mock(tubepress_app_api_environment_EnvironmentInterface::_);
        $this->_mockFieldProviderVimeo    = $this->mock('tubepress_app_api_options_ui_FieldProviderInterface');
        $this->_mockFieldProviderCore     = $this->mock('tubepress_app_api_options_ui_FieldProviderInterface');
        $this->_mockMediaProviderVimeo    = $this->mock('tubepress_app_api_media_MediaProviderInterface');
        $this->_mockMediaProviderYouTube  = $this->mock('tubepress_app_api_media_MediaProviderInterface');
        $this->_mockCategoryEmbedded      = $this->mock('tubepress_app_api_options_ui_ElementInterface');
        $this->_mockCategoryGallerySource = $this->mock('tubepress_app_api_options_ui_ElementInterface');
        $this->_mockTranslator            = $this->mock(tubepress_lib_api_translation_TranslatorInterface::_);
        $this->_mockStringUtils           = $this->mock(tubepress_platform_api_util_StringUtilsInterface::_);

        $this->_sut = new tubepress_options_ui_impl_listeners_OptionsPageTemplateListener(
            $this->_mockEnvironment,
            $this->_mockTranslator,
            $this->_mockStringUtils
        );

        $this->_sut->setFieldProviders(array($this->_mockFieldProviderVimeo, $this->_mockFieldProviderCore));
        $this->_sut->setMediaProviders(array($this->_mockMediaProviderVimeo, $this->_mockMediaProviderYouTube));
    }

    public function testEvent()
    {
        $this->_prepMockCategories();
        $this->_prepMockFieldProviders();
        $this->_prepEnvironment();
        $this->_prepEvent();
        $this->_prepTranslator();
        $this->_prepMediaProviders();
        $this->_prepStringUtils();

        $this->_sut->onOptionsGuiTemplate($this->_mockIncomingEvent);

        $this->assertTrue(true);
    }

    private function _prepStringUtils()
    {
        $stringUtils = new tubepress_util_impl_StringUtils();

        $this->_mockStringUtils->shouldReceive('startsWith')->andReturnUsing(array($stringUtils, 'startsWith'));
    }

    private function _prepMediaProviders()
    {
        $this->_mockMediaProviderYouTube->shouldReceive('getDisplayName')->once()->andReturn('YouTube');
        $this->_mockMediaProviderVimeo->shouldReceive('getDisplayName')->once()->andReturn('Vimeo');
        $this->_mockMediaProviderYouTube->shouldReceive('getName')->once()->andReturn('youtube-media-provider');
        $this->_mockMediaProviderVimeo->shouldReceive('getName')->once()->andReturn('vimeo-media-provider');

        $ytProps = new tubepress_platform_impl_collection_Map();
        $vimeoProps = new tubepress_platform_impl_collection_Map();

        $ytProps->put('miniIconUrl', 'yt-icon');
        $vimeoProps->put('miniIconUrl', 'vimeo-icon');
        $ytProps->put('untranslatedModeTemplateMap',array(
            'tag' => 'tag template',
            'user' => 'user template',
        ));
        $vimeoProps->put('untranslatedModeTemplateMap',array(
            'vimeoChannel' => 'template for channel',
            'vimeoAlbum' => 'template for album',
        ));

        $this->_mockMediaProviderYouTube->shouldReceive('getGallerySourceNames')->once()->andReturn(array(
            'tag', 'user'
        ));
        $this->_mockMediaProviderVimeo->shouldReceive('getGallerySourceNames')->once()->andReturn(array(
            'vimeoChannel', 'vimeoAlbum',
        ));

        $this->_mockMediaProviderYouTube->shouldReceive('getProperties')->times(4)->andReturn($ytProps);
        $this->_mockMediaProviderVimeo->shouldReceive('getProperties')->times(4)->andReturn($vimeoProps);
    }

    private function _prepTranslator()
    {

    }

    private function _prepEnvironment()
    {
        $this->_mockBaseUrl = $this->mock(tubepress_platform_api_url_UrlInterface::_);

        $this->_mockEnvironment->shouldReceive('isPro')->once()->andReturn(true);
        $this->_mockEnvironment->shouldReceive('getBaseUrl')->once()->andReturn($this->_mockBaseUrl);
    }

    private function _prepEvent()
    {
        $mockVimeoGallerySourceField1       = $this->mock('tubepress_app_api_options_ui_MultiSourceFieldInterface');
        $mockVimeoGallerySourceField2       = $this->mock('tubepress_app_api_options_ui_MultiSourceFieldInterface');
        $mockVimeoEmbeddedMultiSourceField1 = $this->mock('tubepress_app_api_options_ui_MultiSourceFieldInterface');
        $mockVimeoEmbeddedMultiSourceField2 = $this->mock('tubepress_app_api_options_ui_MultiSourceFieldInterface');
        $mockCoreGallerySourceField1        = $this->mock('tubepress_app_api_options_ui_MultiSourceFieldInterface');
        $mockCoreGallerySourceField2        = $this->mock('tubepress_app_api_options_ui_MultiSourceFieldInterface');
        $mockCoreEmbeddedMultiSourceField1  = $this->mock('tubepress_app_api_options_ui_MultiSourceFieldInterface');
        $mockCoreEmbeddedMultiSourceField2  = $this->mock('tubepress_app_api_options_ui_MultiSourceFieldInterface');

        $mockVimeoGallerySourceField1->shouldReceive('getId')->atLeast(1)->andReturn('tubepress-multisource-999999-vimeoGallerySource');
        $mockVimeoGallerySourceField2->shouldReceive('getId')->atLeast(1)->andReturn('tubepress-multisource-888888-vimeoGallerySource');
        $mockVimeoEmbeddedMultiSourceField1->shouldReceive('getId')->atLeast(1)->andReturn('tubepress-multisource-999999-vimeoEmbeddedOption');
        $mockVimeoEmbeddedMultiSourceField2->shouldReceive('getId')->atLeast(1)->andReturn('tubepress-multisource-888888-vimeoEmbeddedOption');
        $mockCoreGallerySourceField1->shouldReceive('getId')->atLeast(1)->andReturn('tubepress-multisource-999999-coreGallerySource');
        $mockCoreGallerySourceField2->shouldReceive('getId')->atLeast(1)->andReturn('tubepress-multisource-888888-coreGallerySource');
        $mockCoreEmbeddedMultiSourceField1->shouldReceive('getId')->atLeast(1)->andReturn('tubepress-multisource-999999-coreEmbeddedOption');
        $mockCoreEmbeddedMultiSourceField2->shouldReceive('getId')->atLeast(1)->andReturn('tubepress-multisource-888888-coreEmbeddedOption');

        $this->_fieldsVar = array(

            'tubepress-multisource-999999-vimeoGallerySource'  => $mockVimeoGallerySourceField1,
            'tubepress-multisource-888888-vimeoGallerySource'  => $mockVimeoGallerySourceField2,
            'tubepress-multisource-999999-vimeoEmbeddedOption' => $mockVimeoEmbeddedMultiSourceField1,
            'tubepress-multisource-888888-vimeoEmbeddedOption' => $mockVimeoEmbeddedMultiSourceField2,
            'tubepress-multisource-999999-coreGallerySource'   => $mockCoreGallerySourceField1,
            'tubepress-multisource-888888-coreGallerySource'   => $mockCoreGallerySourceField2,
            'tubepress-multisource-999999-coreEmbeddedOption'  => $mockCoreEmbeddedMultiSourceField1,
            'tubepress-multisource-888888-coreEmbeddedOption'  => $mockCoreEmbeddedMultiSourceField2,
        );

        $this->_mockIncomingEvent->shouldReceive('getSubject')->once()->andReturn(array(
            'foo'    => 'bar',
            'fields' => $this->_fieldsVar,
        ));

        $this->_mockIncomingEvent->shouldReceive('setSubject')->once()->with(ehough_mockery_Mockery::on(array($this, '__verifyFinalTemplateVars')));
    }

    private function _prepMockFieldProviders()
    {
        $this->_mockFieldProviderVimeo->shouldReceive('getId')->atLeast(1)->andReturn('field-provider-vimeo');
        $this->_mockFieldProviderCore->shouldReceive('getId')->atLeast(1)->andReturn('field-provider-core');

        $this->_mockFieldProviderVimeo->shouldReceive('getCategories')->atLeast(1)->andReturn(array(
            $this->_mockCategoryEmbedded,
            $this->_mockCategoryGallerySource
        ));

        $this->_mockFieldProviderCore->shouldReceive('getCategories')->atLeast(1)->andReturn(array(
            $this->_mockCategoryGallerySource
        ));

        $this->_mockFieldProviderVimeo->shouldReceive('getCategoryIdsToFieldIdsMap')->atLeast(1)->andReturn(array(

            tubepress_app_api_options_ui_CategoryNames::EMBEDDED => array(

                'vimeoEmbeddedOption',
            ),
            tubepress_app_api_options_ui_CategoryNames::GALLERY_SOURCE => array(
                'vimeoGallerySource',
            ),
        ));

        $this->_mockFieldProviderCore->shouldReceive('getCategoryIdsToFieldIdsMap')->atLeast(1)->andReturn(array(

            tubepress_app_api_options_ui_CategoryNames::GALLERY_SOURCE => array(
                'coreGallerySource',
            ),
            tubepress_app_api_options_ui_CategoryNames::EMBEDDED => array(
                'coreEmbeddedOption',
            ),
        ));
    }

    private function _prepMockCategories()
    {
        $this->_mockCategoryEmbedded->shouldReceive('__toString')->andReturn('category-1-as-string');
        $this->_mockCategoryGallerySource->shouldReceive('__toString')->andReturn('category-2-as-string');

        $this->_mockCategoryEmbedded->shouldReceive('getId')->atLeast(1)->andReturn(tubepress_app_api_options_ui_CategoryNames::EMBEDDED);
        $this->_mockCategoryGallerySource->shouldReceive('getId')->atLeast(1)->andReturn(tubepress_app_api_options_ui_CategoryNames::GALLERY_SOURCE);
    }

    public function __verifyFinalTemplateVars($candidate)
    {
        if (!is_array($candidate)) {

            return false;
        }

        if ($candidate['foo'] !== 'bar') {

            return false;
        }

        if ($candidate['categories'] !== array(
                $this->_mockCategoryGallerySource,
            $this->_mockCategoryEmbedded)) {

            return false;
        }

        if ($candidate['categoryIdToProviderIdToFieldsMap'] !== array(
                tubepress_app_api_options_ui_CategoryNames::EMBEDDED => array(
                    'field-provider-core' => array(
                        'coreEmbeddedOption'
                    ),
                    'field-provider-vimeo' => array(
                        'vimeoEmbeddedOption',
                    ),
                ),
                tubepress_app_api_options_ui_CategoryNames::GALLERY_SOURCE => array(
                    'field-provider-core' => array(
                        'coreGallerySource'
                    ),
                    'field-provider-vimeo' => array(
                        'vimeoGallerySource',
                    ),
                ),
            )) {

            return false;
        }

        if ($candidate['fieldProviders'] !== array(
                'field-provider-vimeo' => $this->_mockFieldProviderVimeo,
                'field-provider-core'  => $this->_mockFieldProviderCore)) {

            return false;
        }

        if ($candidate['baseUrl'] !== $this->_mockBaseUrl) {

            return false;
        }

        if ($candidate['isPro'] !== true) {

            return false;
        }

        if ($candidate['fields'] !== $this->_fieldsVar) {

            return false;
        }

        if (!is_array($candidate['gallerySources']) || count($candidate['gallerySources']) !== 2) {

            return false;
        }

        $firstSource = $candidate['gallerySources'][0];

        if (!is_array($firstSource)) {

            return false;
        }

        if ($firstSource['id'] !== 999999) {

            return false;
        }

        if ($candidate['mediaProviderPropertiesAsJson'] !== '{"vimeo-media-provider":{"displayName":"Vimeo","sourceNames":["vimeoChannel","vimeoAlbum"],"miniIconUrl":"vimeo-icon","untranslatedModeTemplateMap":{"vimeoChannel":"template for channel","vimeoAlbum":"template for album"}},"youtube-media-provider":{"displayName":"YouTube","sourceNames":["tag","user"],"miniIconUrl":"yt-icon","untranslatedModeTemplateMap":{"tag":"tag template","user":"user template"}}}') {


        }

        return true;

        //array(
//            'gallerySources' => array(
//                array(
//                    'id'                          => '999999',
//                    'icon'                        => '',
//                    'title'                       => '',
//                    'gallerySourceFieldProviders' => array(),
//                    'feedOptionFieldProviders'    => array(),
//                ),
//                array(
//                    'id'                          => '888888',
//                    'icon'                        => '',
//                    'title'                       => '',
//                    'gallerySourceFieldProviders' => array(),
//                    'feedOptionFieldProviders'    => array(),
//                )
//            ),
    }
}