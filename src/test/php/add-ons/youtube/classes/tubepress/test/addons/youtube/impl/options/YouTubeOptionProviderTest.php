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
 * @covers tubepress_youtube_impl_options_YouTubeOptionProvider<extended>
 */
class tubepress_test_youtube_impl_options_YouTubeOptionsProviderTest
{
    /**
     * @return tubepress_core_api_options_ProviderInterface
     */
    protected function buildSut()
    {
        return new tubepress_youtube_impl_options_YouTubeOptionProvider($this->getMockMessageService(), $this->getMockEventDispatcher());
    }

    protected function getMapOfOptionNamesToDefaultValues()
    {
        return array(

            tubepress_youtube_api_const_options_Names::AUTOHIDE         => tubepress_youtube_api_const_options_Values::AUTOHIDE_HIDE_BAR_SHOW_CONTROLS,
            tubepress_youtube_api_const_options_Names::CLOSED_CAPTIONS  => false,
            tubepress_youtube_api_const_options_Names::DISABLE_KEYBOARD => false,
            tubepress_youtube_api_const_options_Names::FULLSCREEN       => true,
            tubepress_youtube_api_const_options_Names::MODEST_BRANDING  => true,
            tubepress_youtube_api_const_options_Names::SHOW_ANNOTATIONS => false,
            tubepress_youtube_api_const_options_Names::SHOW_CONTROLS    => tubepress_youtube_api_const_options_Values::CONTROLS_SHOW_IMMEDIATE_FLASH,
            tubepress_youtube_api_const_options_Names::SHOW_RELATED     => true,
            tubepress_youtube_api_const_options_Names::THEME            => tubepress_youtube_api_const_options_Values::PLAYER_THEME_DARK,

            tubepress_youtube_api_const_options_Names::DEV_KEY         => 'AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg',
            tubepress_youtube_api_const_options_Names::EMBEDDABLE_ONLY => true,
            tubepress_youtube_api_const_options_Names::FILTER          => tubepress_youtube_api_const_options_Values::SAFESEARCH_MODERATE,

            tubepress_youtube_api_const_options_Names::YOUTUBE_MOST_POPULAR_VALUE => tubepress_youtube_api_const_options_Values::TIMEFRAME_TODAY,
            tubepress_youtube_api_const_options_Names::YOUTUBE_RELATED_VALUE      => 'P9M__yYbsZ4',
            tubepress_youtube_api_const_options_Names::YOUTUBE_PLAYLIST_VALUE     => 'F679CB240DD4C112',
            tubepress_youtube_api_const_options_Names::YOUTUBE_FAVORITES_VALUE    => 'FPSRussia',
            tubepress_youtube_api_const_options_Names::YOUTUBE_TAG_VALUE          => 'pittsburgh steelers',
            tubepress_youtube_api_const_options_Names::YOUTUBE_USER_VALUE         => '3hough',

            tubepress_youtube_api_const_options_Names::RATING  => false,
            tubepress_youtube_api_const_options_Names::RATINGS => false,
        );
    }

    protected function getMapOfOptionNamesToUntranslatedLabels()
    {
        return array(

            tubepress_youtube_api_const_options_Names::AUTOHIDE         => 'Fade progress bar and video controls', //>(translatable)<
            tubepress_youtube_api_const_options_Names::CLOSED_CAPTIONS  => 'Show closed captions by default',      //>(translatable)<
            tubepress_youtube_api_const_options_Names::DISABLE_KEYBOARD => 'Disable keyboard controls',            //>(translatable)<
            tubepress_youtube_api_const_options_Names::FULLSCREEN       => 'Allow fullscreen playback.',           //>(translatable)<
            tubepress_youtube_api_const_options_Names::MODEST_BRANDING  => '"Modest" branding',                    //>(translatable)<
            tubepress_youtube_api_const_options_Names::SHOW_ANNOTATIONS => 'Show video annotations by default',    //>(translatable)<
            tubepress_youtube_api_const_options_Names::SHOW_CONTROLS    => 'Show or hide video controls',          //>(translatable)<
            tubepress_youtube_api_const_options_Names::SHOW_RELATED     => 'Show related videos',                  //>(translatable)<
            tubepress_youtube_api_const_options_Names::THEME            => 'YouTube player theme',                 //>(translatable)<

            tubepress_youtube_api_const_options_Names::DEV_KEY         => 'YouTube API Developer Key',       //>(translatable)<
            tubepress_youtube_api_const_options_Names::EMBEDDABLE_ONLY => 'Only retrieve embeddable videos', //>(translatable)<
            tubepress_youtube_api_const_options_Names::FILTER          => 'Filter "racy" content',           //>(translatable)<

            tubepress_youtube_api_const_options_Names::YOUTUBE_MOST_POPULAR_VALUE => 'Most-viewed YouTube videos from',       //>(translatable)<
            tubepress_youtube_api_const_options_Names::YOUTUBE_RELATED_VALUE      => 'Videos related to this YouTube video',  //>(translatable)<
            tubepress_youtube_api_const_options_Names::YOUTUBE_PLAYLIST_VALUE     => 'This YouTube playlist',                 //>(translatable)<
            tubepress_youtube_api_const_options_Names::YOUTUBE_FAVORITES_VALUE    => 'This YouTube user\'s "favorites"',      //>(translatable)<
            tubepress_youtube_api_const_options_Names::YOUTUBE_TAG_VALUE          => 'YouTube search for',                    //>(translatable)<
            tubepress_youtube_api_const_options_Names::YOUTUBE_USER_VALUE         => 'Videos from this YouTube user',         //>(translatable)<

            tubepress_youtube_api_const_options_Names::RATING  => 'Average rating',     //>(translatable)<
            tubepress_youtube_api_const_options_Names::RATINGS => 'Number of ratings',  //>(translatable)<
        );
    }

    protected function getMapOfOptionNamesToUntranslatedDescriptions()
    {
        return array(

            tubepress_youtube_api_const_options_Names::AUTOHIDE         => 'After video playback begins, choose which elements (if any) of the embedded video player to automatically hide.', //>(translatable)<
            tubepress_youtube_api_const_options_Names::MODEST_BRANDING  => 'Hide the YouTube logo from the control area.',                    //>(translatable)<
            tubepress_youtube_api_const_options_Names::SHOW_RELATED     => 'Toggles the display of related videos after a video finishes.',                  //>(translatable)<

            tubepress_youtube_api_const_options_Names::DEV_KEY         => sprintf('YouTube will use this developer key for logging and debugging purposes if you experience a service problem on their end. You can register a new client ID and developer key <a href="%s" target="_blank">here</a>. Don\'t change this unless you know what you\'re doing.', "http://code.google.com/apis/youtube/dashboard/"),       //>(translatable)<
            tubepress_youtube_api_const_options_Names::EMBEDDABLE_ONLY => 'Some videos have embedding disabled. Checking this option will exclude these videos from your galleries.', //>(translatable)<
            tubepress_youtube_api_const_options_Names::FILTER          => 'Don\'t show videos that may not be suitable for minors.',           //>(translatable)<

            tubepress_youtube_api_const_options_Names::YOUTUBE_PLAYLIST_VALUE     => sprintf('The URL to any YouTube playlist (e.g. <a href="%s" target="_blank">%s</a>) or just the playlist identifier (e.g. %s).',  //>(translatable)<
                'http://youtube.com/playlist?list=48A83AD3506C9D36', 'http://youtube.com/playlist?list=48A83AD3506C9D36', '48A83AD3506C9D36'),
            tubepress_youtube_api_const_options_Names::YOUTUBE_TAG_VALUE          => 'YouTube limits this to 1,000 results.',                    //>(translatable)<
        );
    }

    /**
     * @return string[] An array, which may be empty but not null, of Pro option names from this provider.
     */
    protected function getAllProOptionNames()
    {
        return array(

            tubepress_youtube_api_const_options_Names::CLOSED_CAPTIONS,
            tubepress_youtube_api_const_options_Names::DISABLE_KEYBOARD,
            tubepress_youtube_api_const_options_Names::SHOW_ANNOTATIONS,
            tubepress_youtube_api_const_options_Names::SHOW_CONTROLS,
            tubepress_youtube_api_const_options_Names::THEME,
        );
    }
}