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
 * @covers tubepress_app_ioc_AppExtension
 */
class tubepress_test_app_ioc_AppExtensionTest extends tubepress_test_platform_impl_ioc_AbstractContainerExtensionTest
{
    /**
     * @return tubepress_app_ioc_AppExtension
     */
    protected function buildSut()
    {
        return new tubepress_app_ioc_AppExtension();
    }

    protected function getExpectedExternalServicesMap()
    {
        $mockEventDispatcher = $this->mock(tubepress_lib_api_event_EventDispatcherInterface::_);
        $mockEventDispatcher->shouldReceive('newEventInstance')->atLeast(1)->andReturnUsing(function ($subject, $args) {

            return new tubepress_event_impl_tickertape_EventBase($subject, $args);
        });
        $mockEventDispatcher->shouldReceive('dispatch')->atLeast(1);

        $mockBootLogger = $this->mock('tubepress_internal_logger_BootLogger');
        $mockBootLogger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);

        $mockBootSettings = $this->mock(tubepress_platform_api_boot_BootSettingsInterface::_);

        $mockCurrentUrl = $this->mock(tubepress_platform_api_url_UrlInterface::_);
        $mockCurrentUrl->shouldReceive('removeSchemeAndAuthority');

        $mockUrlFactory = $this->mock(tubepress_platform_api_url_UrlFactoryInterface::_);
        $mockUrlFactory->shouldReceive('fromCurrent')->atLeast(1)->andReturn($mockCurrentUrl);

        $logger = $this->mock(tubepress_platform_api_log_LoggerInterface::_);
        $logger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);

        return array(

            tubepress_app_api_environment_EnvironmentInterface::_ => tubepress_app_api_environment_EnvironmentInterface::_,
            tubepress_lib_api_event_EventDispatcherInterface::_  => $mockEventDispatcher,
            tubepress_lib_api_http_ResponseCodeInterface::_      => tubepress_lib_api_http_ResponseCodeInterface::_,
            tubepress_lib_api_translation_TranslatorInterface::_ => tubepress_lib_api_translation_TranslatorInterface::_,
            tubepress_platform_api_url_UrlFactoryInterface::_    => $mockUrlFactory,
            tubepress_platform_api_boot_BootSettingsInterface::_ => $mockBootSettings,
            tubepress_platform_api_util_LangUtilsInterface::_    => tubepress_platform_api_util_LangUtilsInterface::_,
            tubepress_platform_api_util_StringUtilsInterface::_  => tubepress_platform_api_util_StringUtilsInterface::_,
            'tubepress_internal_logger_BootLogger'             => $mockBootLogger,
            tubepress_lib_api_util_TimeUtilsInterface::_         => tubepress_lib_api_util_TimeUtilsInterface::_,
            tubepress_lib_api_http_HttpClientInterface::_        => tubepress_lib_api_http_HttpClientInterface::_,
            tubepress_app_api_options_PersistenceBackendInterface::_ => tubepress_app_api_options_PersistenceBackendInterface::_,
            tubepress_lib_api_template_TemplatingInterface::_    => tubepress_lib_api_template_TemplatingInterface::_,
            tubepress_lib_api_template_TemplatingInterface::_ . '.admin'    => tubepress_lib_api_template_TemplatingInterface::_,
            tubepress_app_api_html_HtmlGeneratorInterface::_     => tubepress_app_api_html_HtmlGeneratorInterface::_,
            tubepress_platform_api_log_LoggerInterface::_ => $logger,
            tubepress_app_api_media_CollectorInterface::_ => tubepress_app_api_media_CollectorInterface::_,
            'tubepress_theme_impl_CurrentThemeService' => 'tubepress_theme_impl_CurrentThemeService',
            'tubepress_theme_impl_CurrentThemeService.admin' => 'tubepress_theme_impl_CurrentThemeService',
            tubepress_platform_api_contrib_RegistryInterface::_ . '.' . tubepress_app_api_theme_ThemeInterface::_ => tubepress_platform_api_contrib_RegistryInterface::_,
            tubepress_platform_api_contrib_RegistryInterface::_ . '.' . tubepress_app_api_theme_ThemeInterface::_ . '.admin' => tubepress_platform_api_contrib_RegistryInterface::_,
            tubepress_lib_api_http_RequestParametersInterface::_ => tubepress_lib_api_http_RequestParametersInterface::_,
            tubepress_app_api_options_ContextInterface::_ => tubepress_app_api_options_ContextInterface::_,
            tubepress_app_api_options_ReferenceInterface::_ => tubepress_app_api_options_ReferenceInterface::_,
            tubepress_app_api_options_AcceptableValuesInterface::_ => tubepress_app_api_options_AcceptableValuesInterface::_,
            tubepress_app_api_options_PersistenceInterface::_ => tubepress_app_api_options_PersistenceInterface::_,
        );
    }

    protected function prepareForLoad()
    {
        $this->_registerOptions();
        $this->_registerVendorServices();
    }

    private function _registerOptions()
    {
        $this->expectRegistration(
            'tubepress_app_api_options_Reference__core',
            'tubepress_app_api_options_Reference'
        )->withTag(tubepress_app_api_options_ReferenceInterface::_)
            ->withArgument(array(

                tubepress_app_api_options_Reference::PROPERTY_DEFAULT_VALUE => array(
                    tubepress_app_api_options_Names::DEBUG_ON                            => true,
                    tubepress_app_api_options_Names::FEED_ADJUSTED_RESULTS_PER_PAGE      => null,
                    tubepress_app_api_options_Names::FEED_ITEM_ID_BLACKLIST              => null,
                    tubepress_app_api_options_Names::FEED_ORDER_BY                       => 'default',
                    tubepress_app_api_options_Names::FEED_PER_PAGE_SORT                  => tubepress_app_api_options_AcceptableValues::PER_PAGE_SORT_NONE,
                    tubepress_app_api_options_Names::FEED_RESULT_COUNT_CAP               => 0,
                    tubepress_app_api_options_Names::FEED_RESULTS_PER_PAGE               => 20,
                    tubepress_app_api_options_Names::GALLERY_AJAX_PAGINATION             => false,
                    tubepress_app_api_options_Names::GALLERY_AUTONEXT                    => true,
                    tubepress_app_api_options_Names::GALLERY_FLUID_THUMBS                => true,
                    tubepress_app_api_options_Names::GALLERY_HQ_THUMBS                   => false,
                    tubepress_app_api_options_Names::GALLERY_PAGINATE_ABOVE              => true,
                    tubepress_app_api_options_Names::GALLERY_PAGINATE_BELOW              => true,
                    tubepress_app_api_options_Names::GALLERY_RANDOM_THUMBS               => true,
                    tubepress_app_api_options_Names::GALLERY_SOURCE                      => 'user',
                    tubepress_app_api_options_Names::GALLERY_THUMB_HEIGHT                => 90,
                    tubepress_app_api_options_Names::GALLERY_THUMB_WIDTH                 => 120,
                    tubepress_app_api_options_Names::HTML_GALLERY_ID                     => null,
                    tubepress_app_api_options_Names::HTML_HTTPS                          => false,
                    tubepress_app_api_options_Names::HTML_OUTPUT                         => null,
                    tubepress_app_api_options_Names::HTTP_METHOD                         => 'GET',
                    tubepress_app_api_options_Names::OPTIONS_UI_DISABLED_FIELD_PROVIDERS => null,
                    tubepress_app_api_options_Names::PLAYER_LOCATION                     => 'normal',
                    tubepress_app_api_options_Names::SHORTCODE_KEYWORD                   => 'tubepress',
                    tubepress_app_api_options_Names::SINGLE_MEDIA_ITEM_ID                => null,
                    tubepress_app_api_options_Names::SOURCES                             => null,
                ),

                tubepress_app_api_options_Reference::PROPERTY_UNTRANSLATED_LABEL => array(
                    tubepress_app_api_options_Names::DEBUG_ON                            => 'Enable debugging',   //>(translatable)<
                    tubepress_app_api_options_Names::FEED_ITEM_ID_BLACKLIST              => 'Video blacklist',                    //>(translatable)<
                    tubepress_app_api_options_Names::FEED_RESULT_COUNT_CAP               => 'Maximum total videos to retrieve',   //>(translatable)<
                    tubepress_app_api_options_Names::FEED_RESULTS_PER_PAGE               => 'Thumbnails per page',                //>(translatable)<,
                    tubepress_app_api_options_Names::FEED_PER_PAGE_SORT                  => 'Per-page sort order',                //>(translatable)<
                    tubepress_app_api_options_Names::GALLERY_AJAX_PAGINATION             => sprintf('<a href="%s" target="_blank">Ajax</a>-enabled pagination', "http://wikipedia.org/wiki/Ajax_(programming)"),  //>(translatable)<
                    tubepress_app_api_options_Names::GALLERY_AUTONEXT                    => 'Play videos sequentially without user intervention', //>(translatable)<
                    tubepress_app_api_options_Names::GALLERY_FLUID_THUMBS                => 'Use "fluid" thumbnails',             //>(translatable)<
                    tubepress_app_api_options_Names::GALLERY_HQ_THUMBS                   => 'Use high-quality thumbnails',        //>(translatable)<
                    tubepress_app_api_options_Names::GALLERY_PAGINATE_ABOVE              => 'Show pagination above thumbnails',   //>(translatable)<
                    tubepress_app_api_options_Names::GALLERY_PAGINATE_BELOW              => 'Show pagination below thumbnails',   //>(translatable)<
                    tubepress_app_api_options_Names::GALLERY_RANDOM_THUMBS               => 'Randomize thumbnail images',         //>(translatable)<
                    tubepress_app_api_options_Names::GALLERY_THUMB_HEIGHT                => 'Height (px) of thumbs',              //>(translatable)<
                    tubepress_app_api_options_Names::GALLERY_THUMB_WIDTH                 => 'Width (px) of thumbs',               //>(translatable)<
                    tubepress_app_api_options_Names::HTML_HTTPS                          => 'Enable HTTPS',       //>(translatable)<
                    tubepress_app_api_options_Names::HTTP_METHOD                         => 'HTTP method',        //>(translatable)<
                    tubepress_app_api_options_Names::OPTIONS_UI_DISABLED_FIELD_PROVIDERS => 'Only show options applicable to...', //>(translatable)<
                    tubepress_app_api_options_Names::PLAYER_LOCATION                     => 'Play each video',      //>(translatable)<
                    tubepress_app_api_options_Names::SHORTCODE_KEYWORD                   => 'Shortcode keyword',  //>(translatable)<

                ),

                tubepress_app_api_options_Reference::PROPERTY_UNTRANSLATED_DESCRIPTION => array(
                    tubepress_app_api_options_Names::DEBUG_ON                 => 'If checked, anyone will be able to view your debugging information. This is a rather small privacy risk. If you\'re not having problems with TubePress, or you\'re worried about revealing any details of your TubePress pages, feel free to disable the feature.',  //>(translatable)<
                    tubepress_app_api_options_Names::FEED_ITEM_ID_BLACKLIST   => 'A list of video IDs that should never be displayed.',                                          //>(translatable)<
                    tubepress_app_api_options_Names::FEED_RESULT_COUNT_CAP    => 'This can help to reduce the number of pages in your gallery. Set to "0" to remove any limit.', //>(translatable)<
                    tubepress_app_api_options_Names::FEED_RESULTS_PER_PAGE    => sprintf('Default is %s. Maximum is %s.', 20, 50),                                               //>(translatable)<
                    tubepress_app_api_options_Names::FEED_PER_PAGE_SORT       => 'Additional sort order applied to each individual page of a gallery',                           //>(translatable)<
                    tubepress_app_api_options_Names::GALLERY_AJAX_PAGINATION  => sprintf('<a href="%s" target="_blank">Ajax</a>-enabled pagination', "http://wikipedia.org/wiki/Ajax_(programming)"),  //>(translatable)<
                    tubepress_app_api_options_Names::GALLERY_AUTONEXT         => 'When a video finishes, this will start playing the next video in the gallery.',  //>(translatable)<
                    tubepress_app_api_options_Names::GALLERY_FLUID_THUMBS     => 'Dynamically set thumbnail spacing based on the width of their container.', //>(translatable)<
                    tubepress_app_api_options_Names::GALLERY_HQ_THUMBS        => 'Note: this option cannot be used with the "randomize thumbnails" feature.', //>(translatable)<
                    tubepress_app_api_options_Names::GALLERY_PAGINATE_ABOVE   => 'Only applies to galleries that span multiple pages.', //>(translatable)<
                    tubepress_app_api_options_Names::GALLERY_PAGINATE_BELOW   => 'Only applies to galleries that span multiple pages.', //>(translatable)<
                    tubepress_app_api_options_Names::GALLERY_RANDOM_THUMBS    => 'Most videos come with several thumbnails. By selecting this option, each time someone views your gallery they will see the same videos with each video\'s thumbnail randomized. Note: this option cannot be used with the "high quality thumbnails" feature.', //>(translatable)<
                    tubepress_app_api_options_Names::GALLERY_THUMB_HEIGHT     => sprintf('Default is %s.', 90),   //>(translatable)<
                    tubepress_app_api_options_Names::GALLERY_THUMB_WIDTH      => sprintf('Default is %s.', 120),  //>(translatable)<
                    tubepress_app_api_options_Names::HTML_HTTPS               => 'Serve thumbnails and embedded video player over a secure connection.',  //>(translatable)<
                    tubepress_app_api_options_Names::HTTP_METHOD              => 'Defines the HTTP method used in most TubePress Ajax operations',  //>(translatable)<
                    tubepress_app_api_options_Names::SHORTCODE_KEYWORD        => 'The word you insert (in plaintext, between square brackets) into your posts/pages to display a gallery.', //>(translatable)<,

                ),
            ))->withArgument(array(

                tubepress_app_api_options_Reference::PROPERTY_NO_PERSIST => array(
                    tubepress_app_api_options_Names::HTML_GALLERY_ID,
                    tubepress_app_api_options_Names::HTML_OUTPUT,
                    tubepress_app_api_options_Names::SINGLE_MEDIA_ITEM_ID,
                    tubepress_app_api_options_Names::FEED_ADJUSTED_RESULTS_PER_PAGE,
                ),

                tubepress_app_api_options_Reference::PROPERTY_PRO_ONLY => array(
                    tubepress_app_api_options_Names::GALLERY_AJAX_PAGINATION,
                    tubepress_app_api_options_Names::GALLERY_AUTONEXT,
                    tubepress_app_api_options_Names::GALLERY_HQ_THUMBS,
                    tubepress_app_api_options_Names::HTML_HTTPS,
                    tubepress_app_api_options_Names::SOURCES,
                )
            ));

        $toValidate = array(
            tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_INTEGER_POSITIVE => array(
                tubepress_app_api_options_Names::FEED_RESULTS_PER_PAGE,
                tubepress_app_api_options_Names::GALLERY_THUMB_HEIGHT,
                tubepress_app_api_options_Names::GALLERY_THUMB_WIDTH,
            ),
            tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_ONE_OR_MORE_WORDCHARS => array(
                tubepress_app_api_options_Names::HTML_GALLERY_ID,
                tubepress_app_api_options_Names::SHORTCODE_KEYWORD,
            ),
            tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_INTEGER_NONNEGATIVE => array(
                tubepress_app_api_options_Names::FEED_RESULT_COUNT_CAP,
            ),
        );

        foreach ($toValidate as $type => $optionNames) {
            foreach ($optionNames as $optionName) {
                $this->expectRegistration(
                    'regex_validator.' . $optionName,
                    'tubepress_app_api_listeners_options_RegexValidatingListener'
                )->withArgument($type)
                    ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_app_api_options_ReferenceInterface::_))
                    ->withArgument(new tubepress_platform_api_ioc_Reference(tubepress_lib_api_translation_TranslatorInterface::_))
                    ->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                        'event'    => tubepress_app_api_event_Events::OPTION_SET . ".$optionName",
                        'priority' => 100000,
                        'method'   => 'onOption',
                    ));
            }
        }

        $fixedValuesMap = array(
            tubepress_app_api_options_Names::HTTP_METHOD => array(
                'GET'  => 'GET',
                'POST' => 'POST'
            ),
            tubepress_app_api_options_Names::FEED_PER_PAGE_SORT => array(
                tubepress_app_api_options_AcceptableValues::PER_PAGE_SORT_NONE   => 'none',           //>(translatable)<
                tubepress_app_api_options_AcceptableValues::PER_PAGE_SORT_RANDOM => 'random',         //>(translatable)<
            )
        );
        foreach ($fixedValuesMap as $optionName => $valuesMap) {
            $this->expectRegistration(
                'fixed_values.' . $optionName,
                'tubepress_app_api_listeners_options_FixedValuesListener'
            )->withArgument($valuesMap)
                ->withTag(tubepress_lib_api_ioc_ServiceTags::EVENT_LISTENER, array(
                    'priority' => 100000,
                    'event'    => tubepress_app_api_event_Events::OPTION_ACCEPTABLE_VALUES . ".$optionName",
                    'method'   => 'onAcceptableValues'
                ));
        }
    }

    private function _registerVendorServices()
    {
        $this->expectRegistration(
            'ehough_finder_FinderFactoryInterface',
            'ehough_finder_FinderFactory'
        );
    }
}