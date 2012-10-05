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
class tubepress_plugins_youtube_impl_listeners_YouTubesOptionsRegistrarTest extends TubePressUnitTest
{
    private $_sut;

    private $_mockOptionsDescriptorReference;

    private static $_providerArrayVimeo = array('vimeo');
    private static $_regexColor              = '/^([0-9a-f]{1,2}){3}$/i';
    private static $_valueMapTime = array(

        tubepress_plugins_youtube_api_const_options_values_TimeFrameValue::ALL_TIME   => 'all time',        //>(translatable)<
        tubepress_plugins_youtube_api_const_options_values_TimeFrameValue::THIS_MONTH => 'this month',      //>(translatable)<
        tubepress_plugins_youtube_api_const_options_values_TimeFrameValue::THIS_WEEK  => 'this week',       //>(translatable)<
        tubepress_plugins_youtube_api_const_options_values_TimeFrameValue::TODAY      => 'today',           //>(translatable)<
    );
    private static $_regexWordChars          = '/\w+/';

    public function setUp()
    {
        $this->_sut = new tubepress_plugins_youtube_impl_listeners_YouTubeOptionsRegistrar();

        $this->_mockOptionsDescriptorReference = Mockery::mock(tubepress_spi_options_OptionDescriptorReference::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionDescriptorReference($this->_mockOptionsDescriptorReference);
    }

    public function testOptions()
    {
        $option = new tubepress_api_model_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_Embedded::AUTOHIDE);
        $option->setLabel('Auto-hide video controls');                                                  //>(translatable)<
        $option->setDescription('A few seconds after playback begins, fade out the video controls.');   //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->_verifyOption($option);

        $option = new tubepress_api_model_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_Embedded::CLOSED_CAPTIONS);
        $option->setLabel('Show closed captions by default');                                                  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->_verifyOption($option);

        $option = new tubepress_api_model_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_Embedded::DISABLE_KEYBOARD);
        $option->setLabel('Disable keyboard controls');                                                  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->_verifyOption($option);

        $option = new tubepress_api_model_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_Embedded::SHOW_ANNOTATIONS);
        $option->setLabel('Show video annotations by default');                                                  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->_verifyOption($option);

        $option = new tubepress_api_model_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_Embedded::SHOW_CONTROLS);
        $option->setLabel('Show video controls');                                                  //>(translatable)<
        $option->setDefaultValue(true);
        $option->setBoolean();
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->_verifyOption($option);

        $option = new tubepress_api_model_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_Embedded::CLOSED_CAPTIONS);
        $option->setLabel('YouTube player theme');                                                  //>(translatable)<
        $option->setAcceptableValues(array(

            tubepress_plugins_youtube_api_const_options_values_ThemeValue::DARK  => 'Dark',     //>(translatable)<
            tubepress_plugins_youtube_api_const_options_values_ThemeValue::LIGHT => 'Light'    //>(translatable)<
        ));
        $option->setDefaultValue(tubepress_plugins_youtube_api_const_options_values_ThemeValue::DARK);
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->_verifyOption($option);
        
        $option = new tubepress_api_model_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_Embedded::FULLSCREEN);
        $option->setLabel('Allow fullscreen playback.');  //>(translatable)<
        $option->setDefaultValue(true);
        $option->setBoolean();
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->_verifyOption($option);

        $option = new tubepress_api_model_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_Embedded::MODEST_BRANDING);
        $option->setDefaultValue(true);
        $option->setLabel('"Modest" branding');                          //>(translatable)<
        $option->setDescription('Hide the YouTube logo from the control area.'); //>(translatable)<
        $option->setBoolean();
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->_verifyOption($option);

        $option = new tubepress_api_model_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_Embedded::SHOW_RELATED);
        $option->setDefaultValue(true);
        $option->setLabel('Show related videos');                                                //>(translatable)<
        $option->setDescription('Toggles the display of related videos after a video finishes.'); //>(translatable)<
        $option->setBoolean();
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->_verifyOption($option);

        $option = new tubepress_api_model_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_Feed::DEV_KEY);
        $option->setDefaultValue('AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg');
        $option->setLabel('YouTube API Developer Key');                                                                                                                                                                                                                                                                                   //>(translatable)<
        $option->setDescription('YouTube will use this developer key for logging and debugging purposes if you experience a service problem on their end. You can register a new client ID and developer key <a href="http://code.google.com/apis/youtube/dashboard/">here</a>. Don\'t change this unless you know what you\'re doing.'); //>(translatable)<
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->_verifyOption($option);

        $option = new tubepress_api_model_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_Feed::EMBEDDABLE_ONLY);
        $option->setDefaultValue(true);
        $option->setLabel('Only retrieve embeddable videos');                                                                                //>(translatable)<
        $option->setDescription('Some videos have embedding disabled. Checking this option will exclude these videos from your galleries.'); //>(translatable)<
        $option->setBoolean();
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->_verifyOption($option);

        $option = new tubepress_api_model_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_Feed::FILTER);
        $option->setLabel('Filter "racy" content');                                                    //>(translatable)<
        $option->setDescription('Don\'t show videos that may not be suitable for minors.');            //>(translatable)<
        $option->setDefaultValue(tubepress_plugins_youtube_api_const_options_values_SafeSearchValue::MODERATE);
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $option->setAcceptableValues(array(
            tubepress_plugins_youtube_api_const_options_values_SafeSearchValue::NONE     => 'none',     //>(translatable)<
            tubepress_plugins_youtube_api_const_options_values_SafeSearchValue::MODERATE => 'moderate', //>(translatable)<
            tubepress_plugins_youtube_api_const_options_values_SafeSearchValue::STRICT   => 'strict',   //>(translatable)<
        ));
        $this->_verifyOption($option);

        $option = new tubepress_api_model_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_TAG_VALUE);
        $option->setDefaultValue('pittsburgh steelers');
        $option->setDescription('YouTube limits this to 1,000 results.');  //>(translatable)<
        $option->setLabel('YouTube search for');                            //>(translatable)<
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->_verifyOption($option);

        $option = new tubepress_api_model_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_TOP_RATED_VALUE);
        $option->setDefaultValue(tubepress_plugins_youtube_api_const_options_values_TimeFrameValue::TODAY);
        $option->setAcceptableValues(self::$_valueMapTime);
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $option->setLabel('Top-rated YouTube videos from');  //>(translatable)<
        $this->_verifyOption($option);

        $option = new tubepress_api_model_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_TOP_FAVORITES_VALUE);
        $option->setDefaultValue(tubepress_plugins_youtube_api_const_options_values_TimeFrameValue::TODAY);
        $option->setAcceptableValues(self::$_valueMapTime);
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $option->setLabel('Most-favorited YouTube videos from');  //>(translatable)<
        $this->_verifyOption($option);

        $option = new tubepress_api_model_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_USER_VALUE);
        $option->setDefaultValue('3hough');
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $option->setLabel('Videos from this YouTube user');  //>(translatable)<
        $option->setValidValueRegex(self::$_regexWordChars);
        $this->_verifyOption($option);

        $option = new tubepress_api_model_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_FAVORITES_VALUE);
        $option->setDefaultValue('mrdeathgod');
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $option->setLabel('This YouTube user\'s "favorites"');  //>(translatable)<
        $option->setValidValueRegex(self::$_regexWordChars);
        $this->_verifyOption($option);

        $option = new tubepress_api_model_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_POPULAR_VALUE);
        $option->setDefaultValue(tubepress_plugins_youtube_api_const_options_values_TimeFrameValue::TODAY);
        $option->setAcceptableValues(self::$_valueMapTime);
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $option->setLabel('Most-viewed YouTube videos from');  //>(translatable)<
        $this->_verifyOption($option);

        $option = new tubepress_api_model_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE);
        $option->setDefaultValue('D2B04665B213AE35');
        $option->setDescription('Limited to 200 videos per playlist. Will usually look something like this: D2B04665B213AE35. Copy the playlist id from the end of the URL in your browser\'s address bar (while looking at a YouTube playlist). It comes right after the "p=". For instance: http://youtube.com/my_playlists?p=D2B04665B213AE35');  //>(translatable)<
        $option->setLabel('This YouTube playlist');                                                                                                                                                                                                                                                                                                          //>(translatable)<
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $option->setValidValueRegex('/[\w-]+/');
        $this->_verifyOption($option);

        $option = new tubepress_api_model_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_FEATURED_VALUE);
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $option->setLabel('The latest "featured" videos on YouTube\'s homepage from');    //>(translatable)<
        $option->setAcceptableValues(self::$_valueMapTime);
        $this->_verifyOption($option);

        $option = new tubepress_api_model_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_DISCUSSED_VALUE);
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $option->setLabel('Most-discussed YouTube videos from');    //>(translatable)<
        $option->setAcceptableValues(self::$_valueMapTime);
        $this->_verifyOption($option);

        $option = new tubepress_api_model_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_RECENT_VALUE);
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $option->setLabel('Most-recently added YouTube videos from');    //>(translatable)<
        $option->setAcceptableValues(self::$_valueMapTime);
        $this->_verifyOption($option);

        $option = new tubepress_api_model_options_OptionDescriptor(tubepress_plugins_youtube_api_const_options_names_GallerySource::YOUTUBE_MOST_RESPONDED_VALUE);
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $option->setLabel('Most-responded to YouTube videos from');    //>(translatable)<
        $option->setAcceptableValues(self::$_valueMapTime);
        $this->_verifyOption($option);

        $option = new tubepress_api_model_options_OptionDescriptor(tubepress_api_const_options_names_Meta::RATING);
        $option->setLabel('Average rating');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->_verifyOption($option);

        $option = new tubepress_api_model_options_OptionDescriptor(tubepress_api_const_options_names_Meta::RATINGS);
        $option->setLabel('Number of ratings');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->_verifyOption($option);

        $option = new tubepress_api_model_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::RANDOM_THUMBS);
        $option->setDefaultValue(true);
        $option->setLabel('Randomize thumbnail images');                                                                                                                                                                                                                                              //>(translatable)<
        $option->setDescription('Most videos come with several thumbnails. By selecting this option, each time someone views your gallery they will see the same videos with each video\'s thumbnail randomized. Note: this option cannot be used with the "high quality thumbnails" feature.'); //>(translatable)<
        $option->setBoolean();
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->_verifyOption($option);

        $option = new tubepress_api_model_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::PLAYER_IMPL);
        $option->setDefaultValue(tubepress_api_const_options_values_PlayerImplementationValue::PROVIDER_BASED);
        $option->setLabel('Implementation');                                                                                  //>(translatable)<
        $option->setDescription('The brand of the embedded player. Default is the provider\'s player (YouTube, Vimeo, etc).'); //>(translatable)<
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $option->setAcceptableValues(array(
            tubepress_api_const_options_values_PlayerImplementationValue::PROVIDER_BASED => 'Provider default',                         //>(translatable)<
        ));
        $this->_verifyOption($option);


        $this->_sut->onBoot(new tubepress_api_event_TubePressEvent());

        $this->assertTrue(true);
    }

    private function _verifyOption(tubepress_api_model_options_OptionDescriptor $expectedOption)
    {
        $this->_mockOptionsDescriptorReference->shouldReceive('registerOptionDescriptor')->once()->with(Mockery::on(function ($registeredOption) use ($expectedOption) {

               return $registeredOption instanceof tubepress_api_model_options_OptionDescriptor
                   && $registeredOption->getAcceptableValues() === $expectedOption->getAcceptableValues()
                   && $registeredOption->getAliases() === $expectedOption->getAliases()
                   && $registeredOption->getDefaultValue() === $expectedOption->getDefaultValue()
                   && $registeredOption->getDescription() === $expectedOption->getDescription()
                   && $registeredOption->getLabel() === $expectedOption->getLabel()
                   && $registeredOption->getName() === $expectedOption->getName()
                   && $registeredOption->getValidValueRegex() === $expectedOption->getValidValueRegex()
                   && $registeredOption->isAbleToBeSetViaShortcode() === $expectedOption->isAbleToBeSetViaShortcode()
                   && $registeredOption->isApplicableToAllProviders() === $expectedOption->isApplicableToAllProviders()
                   && $registeredOption->isApplicableToVimeo() === $expectedOption->isApplicableToVimeo()
                   && $registeredOption->isApplicableToYouTube() === $expectedOption->isApplicableToYouTube()
                   && $registeredOption->isBoolean() === $expectedOption->isBoolean()
                   && $registeredOption->isMeantToBePersisted() === $expectedOption->isMeantToBePersisted()
                   && $registeredOption->hasDiscreteAcceptableValues() === $expectedOption->hasDiscreteAcceptableValues()
                   && $registeredOption->isProOnly() === $expectedOption->isProOnly();
        }));
    }

}
