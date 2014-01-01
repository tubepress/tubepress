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
 * Allows TubePress to work with YouTube.
 */
class tubepress_addons_youtube_impl_options_YouTubeOptionsProvider implements tubepress_spi_options_PluggableOptionDescriptorProvider
{
    /**
     * Fetch all the option descriptors from this provider.
     *
     * @return tubepress_spi_options_OptionDescriptor[]
     */
    public function getOptionDescriptors()
    {
        $_valueMapTime = array(

            tubepress_addons_youtube_api_const_options_values_YouTube::TIMEFRAME_ALL_TIME   => 'all time',        //>(translatable)<
            tubepress_addons_youtube_api_const_options_values_YouTube::TIMEFRAME_TODAY      => 'today',           //>(translatable)<
        );
        $_regexWordChars = '/\w+/';
        $_regexYouTubeVideo = '/[a-zA-Z0-9_-]{11}/';
        
        $toReturn = array();

        /**
         * EMBEDDED PLAYER OPTIONS
         */

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_Embedded::AUTOHIDE);
        $option->setLabel('Fade progress bar and video controls');                                                                              //>(translatable)<
        $option->setDefaultValue(tubepress_addons_youtube_api_const_options_values_YouTube::AUTOHIDE_HIDE_BAR_SHOW_CONTROLS);
        $option->setAcceptableValues(array(

            tubepress_addons_youtube_api_const_options_values_YouTube::AUTOHIDE_HIDE_BAR_SHOW_CONTROLS => 'Fade progress bar only',     //>(translatable)<
            tubepress_addons_youtube_api_const_options_values_YouTube::AUTOHIDE_HIDE_BOTH              => 'Fade progress bar and video controls', //>(translatable)<
            tubepress_addons_youtube_api_const_options_values_YouTube::AUTOHIDE_SHOW_BOTH              => 'Disable fading - always show both'   //>(translatable)<
        ));
        $option->setDescription('After video playback begins, choose which elements (if any) of the embedded video player to automatically hide.');   //>(translatable)<
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_Embedded::CLOSED_CAPTIONS);
        $option->setLabel('Show closed captions by default');                                                  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $option->setProOnly();
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_Embedded::DISABLE_KEYBOARD);
        $option->setLabel('Disable keyboard controls');                                                  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $option->setProOnly();
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_Embedded::FULLSCREEN);
        $option->setLabel('Allow fullscreen playback.');  //>(translatable)<
        $option->setDefaultValue(true);
        $option->setBoolean();
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_Embedded::MODEST_BRANDING);
        $option->setDefaultValue(true);
        $option->setLabel('"Modest" branding');                          //>(translatable)<
        $option->setDescription('Hide the YouTube logo from the control area.'); //>(translatable)<
        $option->setBoolean();
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_Embedded::SHOW_ANNOTATIONS);
        $option->setLabel('Show video annotations by default');                                                  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $option->setProOnly();
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_Embedded::SHOW_CONTROLS);
        $option->setLabel('Show or hide video controls');                                                                               //>(translatable)<
        $option->setDefaultValue(tubepress_addons_youtube_api_const_options_values_YouTube::CONTROLS_SHOW_IMMEDIATE_FLASH);
        $option->setProOnly();
        $option->setAcceptableValues(array(

            tubepress_addons_youtube_api_const_options_values_YouTube::CONTROLS_SHOW_IMMEDIATE_FLASH => 'Show controls - load Flash player immediately',          //>(translatable)<
            tubepress_addons_youtube_api_const_options_values_YouTube::CONTROLS_SHOW_DELAYED_FLASH   => 'Show controls - load Flash player when playback begins', //>(translatable)<
            tubepress_addons_youtube_api_const_options_values_YouTube::CONTROLS_HIDE                 => 'Hide controls',                                          //>(translatable)<
        ));
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_Embedded::SHOW_RELATED);
        $option->setDefaultValue(true);
        $option->setLabel('Show related videos');                                                //>(translatable)<
        $option->setDescription('Toggles the display of related videos after a video finishes.'); //>(translatable)<
        $option->setBoolean();
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_Embedded::THEME);
        $option->setLabel('YouTube player theme');                                                  //>(translatable)<
        $option->setAcceptableValues(array(

            tubepress_addons_youtube_api_const_options_values_YouTube::PLAYER_THEME_DARK  => 'Dark',     //>(translatable)<
            tubepress_addons_youtube_api_const_options_values_YouTube::PLAYER_THEME_LIGHT => 'Light'    //>(translatable)<
        ));
        $option->setDefaultValue(tubepress_addons_youtube_api_const_options_values_YouTube::PLAYER_THEME_DARK);
        $option->setProOnly();
        $toReturn[] = $option;


        /**
         * FEED OPTIONS
         */

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_Feed::DEV_KEY);
        $option->setDefaultValue('AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg');
        $option->setLabel('YouTube API Developer Key');                                                                                                                                                                                                                                                                                   //>(translatable)<
        $option->setDescription('YouTube will use this developer key for logging and debugging purposes if you experience a service problem on their end. You can register a new client ID and developer key <a href="http://code.google.com/apis/youtube/dashboard/" target="_blank">here</a>. Don\'t change this unless you know what you\'re doing.'); //>(translatable)<
        $option->setValidValueRegex('/[\w-]+/');
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_Feed::EMBEDDABLE_ONLY);
        $option->setDefaultValue(true);
        $option->setLabel('Only retrieve embeddable videos');                                                                                //>(translatable)<
        $option->setDescription('Some videos have embedding disabled. Checking this option will exclude these videos from your galleries.'); //>(translatable)<
        $option->setBoolean();
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_Feed::FILTER);
        $option->setLabel('Filter "racy" content');                                                    //>(translatable)<
        $option->setDescription('Don\'t show videos that may not be suitable for minors.');            //>(translatable)<
        $option->setDefaultValue(tubepress_addons_youtube_api_const_options_values_YouTube::SAFESEARCH_MODERATE);
        $option->setAcceptableValues(array(
            tubepress_addons_youtube_api_const_options_values_YouTube::SAFESEARCH_NONE     => 'none',     //>(translatable)<
            tubepress_addons_youtube_api_const_options_values_YouTube::SAFESEARCH_MODERATE => 'moderate', //>(translatable)<
            tubepress_addons_youtube_api_const_options_values_YouTube::SAFESEARCH_STRICT   => 'strict',   //>(translatable)<
        ));
        $toReturn[] = $option;


        /**
         * GALLERY SOURCE OPTIONS
         */

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_POPULAR_VALUE);
        $option->setDefaultValue(tubepress_addons_youtube_api_const_options_values_YouTube::TIMEFRAME_TODAY);
        $option->setAcceptableValues($_valueMapTime);
        $option->setLabel('Most-viewed YouTube videos from');  //>(translatable)<
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_RELATED_VALUE);
        $option->setLabel('Videos related to this YouTube video');  //>(translatable)<
        $option->setValidValueRegex($_regexYouTubeVideo);
        $option->setDefaultValue('P9M__yYbsZ4');
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE);
        $option->setDefaultValue('F679CB240DD4C112');
        $option->setDescription(sprintf('The URL to any YouTube playlist (e.g. <a href="%s" target="_blank">%s</a>) or just the playlist identifier (e.g. %s).',  //>(translatable)<
            'http://youtube.com/playlist?list=48A83AD3506C9D36', 'http://youtube.com/playlist?list=48A83AD3506C9D36', '48A83AD3506C9D36'));
        $option->setLabel('This YouTube playlist');                                                                                                                                                                                                                                                                                                          //>(translatable)<
        $option->setValidValueRegex('/[\w-]+/');
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_FAVORITES_VALUE);
        $option->setDefaultValue('FPSRussia');
        $option->setLabel('This YouTube user\'s "favorites"');  //>(translatable)<
        $option->setValidValueRegex($_regexWordChars);
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_TAG_VALUE);
        $option->setDefaultValue('pittsburgh steelers');
        $option->setDescription('YouTube limits this to 1,000 results.');  //>(translatable)<
        $option->setLabel('YouTube search for');                            //>(translatable)<
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_GallerySource::YOUTUBE_USER_VALUE);
        $option->setDefaultValue('3hough');
        $option->setLabel('Videos from this YouTube user');  //>(translatable)<
        $option->setValidValueRegex('/[\w-]+/');
        $toReturn[] = $option;




        /**
         * META OPTIONS
         */

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_Meta::RATING);
        $option->setLabel('Average rating');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_youtube_api_const_options_names_Meta::RATINGS);
        $option->setLabel('Number of ratings');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $toReturn[] = $option;
        
        return $toReturn;
    }
}