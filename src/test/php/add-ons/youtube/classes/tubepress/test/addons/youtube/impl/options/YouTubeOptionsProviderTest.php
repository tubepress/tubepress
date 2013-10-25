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
class tubepress_test_addons_youtube_impl_options_YouTubeOptionsProviderTest extends tubepress_test_impl_options_AbstractOptionDescriptorProviderTest
{
    private static $_youTubeVideo = '/[a-zA-Z0-9_-]{11}/';

    private static $_valueMapTime = array(

        tubepress_addons_youtube_api_const_options_values_YouTube::TIMEFRAME_ALL_TIME   => 'all time',
        tubepress_addons_youtube_api_const_options_values_YouTube::TIMEFRAME_TODAY      => 'today',
    );

    private static $_regexWordChars = '/\w+/';

    /**
     * @return tubepress_spi_options_OptionDescriptor[]
     */
    protected function getExpectedOptions()
    {
        $toReturn = array();
        
        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_Embedded::AUTOHIDE);
        $option->setLabel('Fade progress bar and video controls');
        $option->setDefaultValue(tubepress_addons_youtube_api_const_options_values_YouTube::AUTOHIDE_HIDE_BAR_SHOW_CONTROLS);
        $option->setAcceptableValues(array(

            tubepress_addons_youtube_api_const_options_values_YouTube::AUTOHIDE_HIDE_BAR_SHOW_CONTROLS => 'Fade progress bar only',
            tubepress_addons_youtube_api_const_options_values_YouTube::AUTOHIDE_HIDE_BOTH              => 'Fade progress bar and video controls',
            tubepress_addons_youtube_api_const_options_values_YouTube::AUTOHIDE_SHOW_BOTH              => 'Disable fading - always show both'
        ));
        $option->setDescription('After video playback begins, choose which elements (if any) of the embedded video player to automatically hide.');
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_Embedded::CLOSED_CAPTIONS);
        $option->setLabel('Show closed captions by default');
        $option->setDefaultValue(false);
        $option->setBoolean();
        $option->setProOnly();
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_Embedded::DISABLE_KEYBOARD);
        $option->setLabel('Disable keyboard controls');
        $option->setDefaultValue(false);
        $option->setBoolean();
        $option->setProOnly();
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_Embedded::SHOW_ANNOTATIONS);
        $option->setLabel('Show video annotations by default');
        $option->setDefaultValue(false);
        $option->setBoolean();
        $option->setProOnly();
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_Embedded::SHOW_CONTROLS);
        $option->setLabel('Show or hide video controls');
        $option->setDefaultValue(tubepress_addons_youtube_api_const_options_values_YouTube::CONTROLS_SHOW_IMMEDIATE_FLASH);
        $option->setProOnly();
        $option->setAcceptableValues(array(

            tubepress_addons_youtube_api_const_options_values_YouTube::CONTROLS_SHOW_IMMEDIATE_FLASH => 'Show controls - load Flash player immediately',
            tubepress_addons_youtube_api_const_options_values_YouTube::CONTROLS_SHOW_DELAYED_FLASH   => 'Show controls - load Flash player when playback begins',
            tubepress_addons_youtube_api_const_options_values_YouTube::CONTROLS_HIDE                 => 'Hide controls',
        ));
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_Embedded::THEME);
        $option->setLabel('YouTube player theme');
        $option->setAcceptableValues(array(

            tubepress_addons_youtube_api_const_options_values_YouTube::PLAYER_THEME_DARK  => 'Dark',
            tubepress_addons_youtube_api_const_options_values_YouTube::PLAYER_THEME_LIGHT => 'Light'
        ));
        $option->setDefaultValue(tubepress_addons_youtube_api_const_options_values_YouTube::PLAYER_THEME_DARK);
        $option->setProOnly();
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_Embedded::FULLSCREEN);
        $option->setLabel('Allow fullscreen playback.');
        $option->setDefaultValue(true);
        $option->setBoolean();
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_Embedded::MODEST_BRANDING);
        $option->setDefaultValue(true);
        $option->setLabel('"Modest" branding');
        $option->setDescription('Hide the YouTube logo from the control area.');
        $option->setBoolean();
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_Embedded::SHOW_RELATED);
        $option->setDefaultValue(true);
        $option->setLabel('Show related videos');
        $option->setDescription('Toggles the display of related videos after a video finishes.');
        $option->setBoolean();
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_Feed::DEV_KEY);
        $option->setDefaultValue('AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg');
        $option->setLabel('YouTube API Developer Key');
        $option->setDescription('YouTube will use this developer key for logging and debugging purposes if you experience a service problem on their end. You can register a new client ID and developer key <a href="http://code.google.com/apis/youtube/dashboard/" target="_blank">here</a>. Don\'t change this unless you know what you\'re doing.');
        $option->setValidValueRegex('/[\w-]+/');
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_Feed::EMBEDDABLE_ONLY);
        $option->setDefaultValue(true);
        $option->setLabel('Only retrieve embeddable videos');
        $option->setDescription('Some videos have embedding disabled. Checking this option will exclude these videos from your galleries.');
        $option->setBoolean();
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_Feed::FILTER);
        $option->setLabel('Filter "racy" content');
        $option->setDescription('Don\'t show videos that may not be suitable for minors.');
        $option->setDefaultValue(tubepress_addons_youtube_api_const_options_values_YouTube::SAFESEARCH_MODERATE);
        $option->setAcceptableValues(array(
            tubepress_addons_youtube_api_const_options_values_YouTube::SAFESEARCH_NONE     => 'none',
            tubepress_addons_youtube_api_const_options_values_YouTube::SAFESEARCH_MODERATE => 'moderate',
            tubepress_addons_youtube_api_const_options_values_YouTube::SAFESEARCH_STRICT   => 'strict',
        ));
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_TAG_VALUE);
        $option->setDefaultValue('pittsburgh steelers');
        $option->setDescription('YouTube limits this to 1,000 results.');
        $option->setLabel('YouTube search for');
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_USER_VALUE);
        $option->setDefaultValue('3hough');
        $option->setLabel('Videos from this YouTube user');
        $option->setValidValueRegex('/[\w-]+/');
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_FAVORITES_VALUE);
        $option->setDefaultValue('FPSRussia');
        $option->setLabel('This YouTube user\'s "favorites"');
        $option->setValidValueRegex(self::$_regexWordChars);
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_POPULAR_VALUE);
        $option->setDefaultValue(tubepress_addons_youtube_api_const_options_values_YouTube::TIMEFRAME_TODAY);
        $option->setAcceptableValues(self::$_valueMapTime);
        $option->setLabel('Most-viewed YouTube videos from');
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE);
        $option->setDefaultValue('F679CB240DD4C112');
        $option->setDescription('Limited to 200 videos per playlist. Will usually look something like this: F679CB240DD4C112. Copy the playlist id from the end of the URL in your browser\'s address bar (while looking at a YouTube playlist). It comes right after the "p=". For instance: <a href="http://www.youtube.com/playlist?p=F679CB240DD4C112" target="_blank">http://www.youtube.com/playlist?p=F679CB240DD4C112</a>');  //>(translatable)<
        $option->setLabel('This YouTube playlist');
        $option->setValidValueRegex('/[\w-]+/');
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_Meta::RATING);
        $option->setLabel('Average rating');
        $option->setDefaultValue(false);
        $option->setBoolean();
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_Meta::RATINGS);
        $option->setLabel('Number of ratings');
        $option->setDefaultValue(false);
        $option->setBoolean();
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_RELATED_VALUE);
        $option->setLabel('Videos related to this YouTube video');
        $option->setValidValueRegex(self::$_youTubeVideo);
        $option->setDefaultValue('P9M__yYbsZ4');
        $toReturn[] = $option;
        
        return $toReturn;
    }

    /**
     * @return tubepress_spi_options_PluggableOptionDescriptorProvider
     */
    protected function buildSut()
    {
        return new tubepress_addons_youtube_impl_options_YouTubeOptionsProvider();
    }
}