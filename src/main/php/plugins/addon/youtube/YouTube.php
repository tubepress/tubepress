<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 * Registers a few extensions to allow TubePress to work with YouTube.
 */
class tubepress_plugins_youtube_YouTube
{
    private static $_valueMapTime = array(

        tubepress_plugins_youtube_api_const_options_values_TimeFrameValue::ALL_TIME   => 'all time',        //>(translatable)<
        tubepress_plugins_youtube_api_const_options_values_TimeFrameValue::THIS_MONTH => 'this month',      //>(translatable)<
        tubepress_plugins_youtube_api_const_options_values_TimeFrameValue::THIS_WEEK  => 'this week',       //>(translatable)<
        tubepress_plugins_youtube_api_const_options_values_TimeFrameValue::TODAY      => 'today',           //>(translatable)<
    );
    private static $_regexWordChars = '/\w+/';
    private static $_youTubeVideo = '/[a-zA-Z0-9_-]{11}/';

    public static function registerYouTubeListeners()
    {
        self::_registerYouTubeOptions();
        self::_registerYouTubeEmbeddedPlayer();
        self::_registerYouTubeOptionsPageItems();
        self::_registerYouTubeVideoProvider();
    }

    private static function _registerYouTubeOptions()
    {
        $odr = tubepress_impl_patterns_ioc_KernelServiceLocator::getOptionDescriptorReference();

        /**
         * EMBEDDED PLAYER OPTIONS
         */

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_Embedded::AUTOHIDE);
        $option->setLabel('Auto-hide video controls');                                                  //>(translatable)<
        $option->setDescription('A few seconds after playback begins, fade out the video controls.');   //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_Embedded::CLOSED_CAPTIONS);
        $option->setLabel('Show closed captions by default');                                                  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_Embedded::DISABLE_KEYBOARD);
        $option->setLabel('Disable keyboard controls');                                                  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_Embedded::FULLSCREEN);
        $option->setLabel('Allow fullscreen playback.');  //>(translatable)<
        $option->setDefaultValue(true);
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_Embedded::MODEST_BRANDING);
        $option->setDefaultValue(true);
        $option->setLabel('"Modest" branding');                          //>(translatable)<
        $option->setDescription('Hide the YouTube logo from the control area.'); //>(translatable)<
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_Embedded::SHOW_ANNOTATIONS);
        $option->setLabel('Show video annotations by default');                                                  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_Embedded::SHOW_CONTROLS);
        $option->setLabel('Show video controls');                                                  //>(translatable)<
        $option->setDefaultValue(true);
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_Embedded::SHOW_RELATED);
        $option->setDefaultValue(true);
        $option->setLabel('Show related videos');                                                //>(translatable)<
        $option->setDescription('Toggles the display of related videos after a video finishes.'); //>(translatable)<
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_Embedded::THEME);
        $option->setLabel('YouTube player theme');                                                  //>(translatable)<
        $option->setAcceptableValues(array(

            tubepress_plugins_youtube_api_const_options_values_ThemeValue::DARK  => 'Dark',     //>(translatable)<
            tubepress_plugins_youtube_api_const_options_values_ThemeValue::LIGHT => 'Light'    //>(translatable)<
        ));
        $option->setDefaultValue(tubepress_plugins_youtube_api_const_options_values_ThemeValue::DARK);
        $odr->registerOptionDescriptor($option);


        /**
         * FEED OPTIONS
         */

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_Feed::DEV_KEY);
        $option->setDefaultValue('AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg');
        $option->setLabel('YouTube API Developer Key');                                                                                                                                                                                                                                                                                   //>(translatable)<
        $option->setDescription('YouTube will use this developer key for logging and debugging purposes if you experience a service problem on their end. You can register a new client ID and developer key <a href="http://code.google.com/apis/youtube/dashboard/">here</a>. Don\'t change this unless you know what you\'re doing.'); //>(translatable)<
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_Feed::EMBEDDABLE_ONLY);
        $option->setDefaultValue(true);
        $option->setLabel('Only retrieve embeddable videos');                                                                                //>(translatable)<
        $option->setDescription('Some videos have embedding disabled. Checking this option will exclude these videos from your galleries.'); //>(translatable)<
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_Feed::FILTER);
        $option->setLabel('Filter "racy" content');                                                    //>(translatable)<
        $option->setDescription('Don\'t show videos that may not be suitable for minors.');            //>(translatable)<
        $option->setDefaultValue(tubepress_plugins_youtube_api_const_options_values_SafeSearchValue::MODERATE);
        $option->setAcceptableValues(array(
            tubepress_plugins_youtube_api_const_options_values_SafeSearchValue::NONE     => 'none',     //>(translatable)<
            tubepress_plugins_youtube_api_const_options_values_SafeSearchValue::MODERATE => 'moderate', //>(translatable)<
            tubepress_plugins_youtube_api_const_options_values_SafeSearchValue::STRICT   => 'strict',   //>(translatable)<
        ));
        $odr->registerOptionDescriptor($option);


        /**
         * GALLERY SOURCE OPTIONS
         */

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_TOP_RATED_VALUE);
        $option->setDefaultValue(tubepress_plugins_youtube_api_const_options_values_TimeFrameValue::TODAY);
        $option->setAcceptableValues(self::$_valueMapTime);
        $option->setLabel('Top-rated YouTube videos from');  //>(translatable)<
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_TOP_FAVORITES_VALUE);
        $option->setDefaultValue(tubepress_plugins_youtube_api_const_options_values_TimeFrameValue::TODAY);
        $option->setAcceptableValues(self::$_valueMapTime);
        $option->setLabel('Most-favorited YouTube videos from');  //>(translatable)<
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_SHARED_VALUE);
        $option->setLabel('YouTube videos most-shared on Facebook and Twitter from');  //>(translatable)<
        $option->setDefaultValue(tubepress_plugins_youtube_api_const_options_values_TimeFrameValue::TODAY);
        $option->setAcceptableValues(self::$_valueMapTime);
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_POPULAR_VALUE);
        $option->setDefaultValue(tubepress_plugins_youtube_api_const_options_values_TimeFrameValue::TODAY);
        $option->setAcceptableValues(self::$_valueMapTime);
        $option->setLabel('Most-viewed YouTube videos from');  //>(translatable)<
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_RECENT_VALUE);
        $option->setLabel('Most-recently added YouTube videos from');    //>(translatable)<
        $option->setAcceptableValues(self::$_valueMapTime);
        $option->setDefaultValue(tubepress_plugins_youtube_api_const_options_values_TimeFrameValue::TODAY);
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_DISCUSSED_VALUE);
        $option->setLabel('Most-discussed YouTube videos from');    //>(translatable)<
        $option->setAcceptableValues(self::$_valueMapTime);
        $option->setDefaultValue(tubepress_plugins_youtube_api_const_options_values_TimeFrameValue::TODAY);
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_RESPONDED_VALUE);
        $option->setLabel('Most-responded to YouTube videos from');    //>(translatable)<
        $option->setAcceptableValues(self::$_valueMapTime);
        $option->setDefaultValue(tubepress_plugins_youtube_api_const_options_values_TimeFrameValue::TODAY);
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_FEATURED_VALUE);
        $option->setLabel('The latest "featured" videos on YouTube\'s homepage from');    //>(translatable)<
        $option->setAcceptableValues(self::$_valueMapTime);
        $option->setDefaultValue(tubepress_plugins_youtube_api_const_options_values_TimeFrameValue::TODAY);
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_TRENDING_VALUE);
        $option->setLabel('Popular videos on <a href="http://www.youtube.com/trends">YouTube Trends</a> from');  //>(translatable)<
        $option->setAcceptableValues(self::$_valueMapTime);
        $option->setDefaultValue(tubepress_plugins_youtube_api_const_options_values_TimeFrameValue::TODAY);
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_RELATED_VALUE);
        $option->setLabel('Videos related to this YouTube video');  //>(translatable)<
        $option->setValidValueRegex(self::$_youTubeVideo);
        $option->setDefaultValue('P9M__yYbsZ4');
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_RESPONSES_VALUE);
        $option->setLabel('Videos responses to this YouTube video');  //>(translatable)<
        $option->setValidValueRegex(self::$_youTubeVideo);
        $option->setDefaultValue('9bZkp7q19f0');
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE);
        $option->setDefaultValue('PLF679CB240DD4C112');
        $option->setDescription('Limited to 200 videos per playlist. Will usually look something like this: PLF679CB240DD4C112. Copy the playlist id from the end of the URL in your browser\'s address bar (while looking at a YouTube playlist). It comes right after the "p=". For instance: <a href="http://www.youtube.com/playlist?p=PLF679CB240DD4C112">http://www.youtube.com/playlist?p=PLF679CB240DD4C112</a>');  //>(translatable)<
        $option->setLabel('This YouTube playlist');                                                                                                                                                                                                                                                                                                          //>(translatable)<
        $option->setValidValueRegex('/[\w-]+/');
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_FAVORITES_VALUE);
        $option->setDefaultValue('FPSRussia');
        $option->setLabel('This YouTube user\'s "favorites"');  //>(translatable)<
        $option->setValidValueRegex(self::$_regexWordChars);
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_TAG_VALUE);
        $option->setDefaultValue('pittsburgh steelers');
        $option->setDescription('YouTube limits this to 1,000 results.');  //>(translatable)<
        $option->setLabel('YouTube search for');                            //>(translatable)<
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_USER_VALUE);
        $option->setDefaultValue('3hough');
        $option->setLabel('Videos from this YouTube user');  //>(translatable)<
        $option->setValidValueRegex(self::$_regexWordChars);
        $odr->registerOptionDescriptor($option);




        /**
         * META OPTIONS
         */

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_Meta::RATING);
        $option->setLabel('Average rating');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_Meta::RATINGS);
        $option->setLabel('Number of ratings');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);
    }

    private static function _registerYouTubeEmbeddedPlayer()
    {
        $serviceCollectionsRegistry = tubepress_impl_patterns_ioc_KernelServiceLocator::getServiceCollectionsRegistry();

        $serviceCollectionsRegistry->registerService(

            tubepress_spi_embedded_PluggableEmbeddedPlayerService::_,
            new tubepress_plugins_youtube_impl_embedded_YouTubePluggableEmbeddedPlayerService()
        );
    }

    private static function _registerYouTubeOptionsPageItems()
    {
        $serviceCollectionsRegistry = tubepress_impl_patterns_ioc_KernelServiceLocator::getServiceCollectionsRegistry();
        $fieldBuilder               = tubepress_impl_patterns_ioc_KernelServiceLocator::getOptionsUiFieldBuilder();

        $gallerySources = array(

            array(

                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH,
                tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_TAG_VALUE,
                tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),

            array(

                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_USER,
                tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_USER_VALUE,
                tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),

            array(

                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
                tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE,
                tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),

            array(

                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FAVORITES,
                tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_FAVORITES_VALUE,
                tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),

            array(

                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED,
                tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_FEATURED_VALUE,
                tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),

            array(

                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TRENDING,
                tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_TRENDING_VALUE,
                tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),

            array(

                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_POPULAR,
                tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_POPULAR_VALUE,
                tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),

            array(

                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_RATED,
                tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_TOP_RATED_VALUE,
                tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),

            array(

                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_FAVORITES,
                tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_TOP_FAVORITES_VALUE,
                tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),

            array(

                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_SHARED,
                tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_SHARED_VALUE,
                tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),

            array(

                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RECENT,
                tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_RECENT_VALUE,
                tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),

            array(

                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_DISCUSSED,
                tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_DISCUSSED_VALUE,
                tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),

            array(

                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RESPONDED,
                tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_RESPONDED_VALUE,
                tubepress_impl_options_ui_fields_DropdownField::FIELD_CLASS_NAME),

            array(

                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_RELATED,
                tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_RELATED_VALUE,
                tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),

            array(

                tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_RESPONSES,
                tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_RESPONSES_VALUE,
                tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME),
        );

        foreach ($gallerySources as $gallerySource) {

            $field = $fieldBuilder->build($gallerySource[1],
                $gallerySource[2], 'gallery-source');

            $field = new tubepress_impl_options_ui_fields_GallerySourceField($gallerySource[0], $field);

            $serviceCollectionsRegistry->registerService(

                tubepress_spi_options_ui_Field::CLASS_NAME,
                $field
            );
        }

        $embeddedBooleans = array(

            tubepress_plugins_youtube_api_const_options_names_Embedded::AUTOHIDE,
            tubepress_plugins_youtube_api_const_options_names_Embedded::CLOSED_CAPTIONS,
            tubepress_plugins_youtube_api_const_options_names_Embedded::DISABLE_KEYBOARD,
            tubepress_plugins_youtube_api_const_options_names_Embedded::FULLSCREEN,
            tubepress_plugins_youtube_api_const_options_names_Embedded::MODEST_BRANDING,
            tubepress_plugins_youtube_api_const_options_names_Embedded::SHOW_ANNOTATIONS,
            tubepress_plugins_youtube_api_const_options_names_Embedded::SHOW_CONTROLS,
            tubepress_plugins_youtube_api_const_options_names_Embedded::SHOW_RELATED,
        );

        foreach ($embeddedBooleans as $embeddedBoolean) {

            $serviceCollectionsRegistry->registerService(

                tubepress_spi_options_ui_Field::CLASS_NAME,
                new tubepress_impl_options_ui_fields_BooleanField($embeddedBoolean, 'embedded')
            );
        }

        $serviceCollectionsRegistry->registerService(

            tubepress_spi_options_ui_Field::CLASS_NAME,
            new tubepress_impl_options_ui_fields_DropdownField(tubepress_plugins_youtube_api_const_options_names_Embedded::THEME, 'embedded')
        );

        $serviceCollectionsRegistry->registerService(

            tubepress_spi_options_ui_Field::CLASS_NAME,
            new tubepress_impl_options_ui_fields_BooleanField(tubepress_plugins_youtube_api_const_options_names_Feed::EMBEDDABLE_ONLY, 'feed')
        );

        $serviceCollectionsRegistry->registerService(

            tubepress_spi_options_ui_Field::CLASS_NAME,
            new tubepress_impl_options_ui_fields_TextField(tubepress_plugins_youtube_api_const_options_names_Feed::DEV_KEY, 'feed')
        );

        $serviceCollectionsRegistry->registerService(

            tubepress_spi_options_ui_Field::CLASS_NAME,
            new tubepress_impl_options_ui_fields_DropdownField(tubepress_plugins_youtube_api_const_options_names_Feed::FILTER, 'feed')
        );
    }

    private static function _registerYouTubeVideoProvider()
    {
        $serviceCollectionsRegistry = tubepress_impl_patterns_ioc_KernelServiceLocator::getServiceCollectionsRegistry();

        $serviceCollectionsRegistry->registerService(

            tubepress_spi_provider_PluggableVideoProviderService::_,
            new tubepress_plugins_youtube_impl_provider_YouTubePluggableVideoProviderService(

                new tubepress_plugins_youtube_impl_provider_YouTubeUrlBuilder()
            )
        );
    }
}

tubepress_plugins_youtube_YouTube::registerYouTubeListeners();