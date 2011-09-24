<?php
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_const_options_names_Advanced',
    'org_tubepress_api_const_options_names_Display',
    'org_tubepress_api_const_options_names_Embedded',
    'org_tubepress_api_const_options_names_Feed',
    'org_tubepress_api_const_options_names_Meta',
    'org_tubepress_api_const_options_names_Output',
    'org_tubepress_api_const_options_names_Output',
    'org_tubepress_api_const_options_names_Widget',
    'org_tubepress_api_const_options_Type',
    'org_tubepress_api_const_options_values_ModeValue',
    'org_tubepress_api_const_options_values_OrderValue',
    'org_tubepress_api_const_options_values_PlayerImplementationValue',
    'org_tubepress_api_const_options_values_PlayerValue',
    'org_tubepress_api_const_options_values_SafeSearchValue',
    'org_tubepress_api_const_options_values_TimeFrameValue',
    'org_tubepress_api_embedded_EmbeddedHtmlGenerator',
    'org_tubepress_impl_options_OptionsReference',
));

/**
 * The master reference for TubePress options - their names, deprecated
 * names, default values, types, etc.
 *
 */
class org_tubepress_impl_options_OptionsReference
{
    private static $_options = array(
        org_tubepress_api_const_options_Type::COLOR => array(
            org_tubepress_api_const_options_names_Embedded::PLAYER_COLOR     => '999999',
            org_tubepress_api_const_options_names_Embedded::PLAYER_HIGHLIGHT => 'FFFFFF'
        ),
        org_tubepress_api_const_options_Type::MODE  => array(
            org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::FEATURED
        ),
        org_tubepress_api_const_options_Type::TEXT => array(
            org_tubepress_api_const_options_names_Advanced::DATEFORMAT           => 'M j, Y',
            org_tubepress_api_const_options_names_Advanced::KEYWORD              => 'tubepress',
            org_tubepress_api_const_options_names_Advanced::VIDEO_BLACKLIST      => '',
            org_tubepress_api_const_options_names_Advanced::GALLERY_ID           => '',
            org_tubepress_api_const_options_names_Output::FAVORITES_VALUE        => 'mrdeathgod',
            org_tubepress_api_const_options_names_Output::PLAYLIST_VALUE         => 'D2B04665B213AE35',
            org_tubepress_api_const_options_names_Output::TAG_VALUE              => 'pittsburgh steelers',
            org_tubepress_api_const_options_names_Output::USER_VALUE             => '3hough',
            org_tubepress_api_const_options_names_Feed::DEV_KEY                  => 'AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg',
            org_tubepress_api_const_options_names_Feed::VIMEO_KEY                => '',
            org_tubepress_api_const_options_names_Feed::VIMEO_SECRET             => '',
            org_tubepress_api_const_options_names_Widget::TITLE                  => 'TubePress',
            org_tubepress_api_const_options_names_Widget::TAGSTRING              => '[tubepress thumbHeight=\'105\' thumbWidth=\'135\']',
            org_tubepress_api_const_options_names_Output::VIDEO                  => '',
            org_tubepress_api_const_options_names_Output::VIMEO_UPLOADEDBY_VALUE => 'mattkaar',
            org_tubepress_api_const_options_names_Output::VIMEO_LIKES_VALUE      => 'coiffier',
            org_tubepress_api_const_options_names_Output::VIMEO_APPEARS_IN_VALUE => 'royksopp',
            org_tubepress_api_const_options_names_Output::VIMEO_SEARCH_VALUE     => 'cats playing piano',
            org_tubepress_api_const_options_names_Output::VIMEO_CREDITED_VALUE   => 'patricklawler',
            org_tubepress_api_const_options_names_Output::VIMEO_CHANNEL_VALUE    => 'splitscreenstuff',
            org_tubepress_api_const_options_names_Output::VIMEO_GROUP_VALUE      => 'hdxs',
            org_tubepress_api_const_options_names_Output::VIMEO_ALBUM_VALUE      => '140484',
            org_tubepress_api_const_options_names_Advanced::CACHE_DIR            => '',
            org_tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER         => '',
            org_tubepress_api_const_options_names_Output::SEARCH_RESULTS_URL     => '',
            org_tubepress_api_const_options_names_Output::SEARCH_PROVIDER        => 'youtube',
            org_tubepress_api_const_options_names_Output::SEARCH_RESULTS_DOM_ID  => ''
        ),
        org_tubepress_api_const_options_Type::BOOL => array(
            org_tubepress_api_const_options_names_Advanced::DEBUG_ON               => true,
            org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_CURL      => false,
            org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_EXTHTTP   => false,
            org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_FOPEN     => false,
            org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_FSOCKOPEN => false,
            org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_STREAMS   => false,
            org_tubepress_api_const_options_names_Display::RANDOM_THUMBS           => true,
            org_tubepress_api_const_options_names_Display::RELATIVE_DATES          => false,
            org_tubepress_api_const_options_names_Display::PAGINATE_ABOVE          => true,
            org_tubepress_api_const_options_names_Display::PAGINATE_BELOW          => true,
            org_tubepress_api_const_options_names_Display::AJAX_PAGINATION         => false,
            org_tubepress_api_const_options_names_Display::HQ_THUMBS               => false,
            org_tubepress_api_const_options_names_Display::FLUID_THUMBS            => true,
            org_tubepress_api_const_options_names_Embedded::AUTOPLAY               => false,
            org_tubepress_api_const_options_names_Embedded::LAZYPLAY               => true,
            org_tubepress_api_const_options_names_Embedded::LOOP                   => false,
            org_tubepress_api_const_options_names_Embedded::SHOW_INFO              => false,
            org_tubepress_api_const_options_names_Embedded::SHOW_RELATED           => true,
            org_tubepress_api_const_options_names_Embedded::FULLSCREEN             => true,
            org_tubepress_api_const_options_names_Embedded::HIGH_QUALITY           => false,
            org_tubepress_api_const_options_names_Meta::AUTHOR                     => false,
            org_tubepress_api_const_options_names_Meta::CATEGORY                   => false,
            org_tubepress_api_const_options_names_Meta::DESCRIPTION                => false,
            org_tubepress_api_const_options_names_Meta::ID                         => false,
            org_tubepress_api_const_options_names_Meta::LENGTH                     => true,
            org_tubepress_api_const_options_names_Meta::RATING                     => false,
            org_tubepress_api_const_options_names_Meta::RATINGS                    => false,
            org_tubepress_api_const_options_names_Meta::TAGS                       => false,
            org_tubepress_api_const_options_names_Meta::TITLE                      => true,
            org_tubepress_api_const_options_names_Meta::UPLOADED                   => false,
            org_tubepress_api_const_options_names_Meta::URL                        => false,
            org_tubepress_api_const_options_names_Meta::VIEWS                      => true,
            org_tubepress_api_const_options_names_Meta::LIKES                      => false,
            org_tubepress_api_const_options_names_Feed::CACHE_ENABLED              => false,
            org_tubepress_api_const_options_names_Feed::EMBEDDABLE_ONLY            => true,
            org_tubepress_api_const_options_names_Output::SEARCH_RESULTS_ONLY      => false
        ),
        org_tubepress_api_const_options_Type::INTEGRAL => array(
            org_tubepress_api_const_options_names_Display::DESC_LIMIT              => 80,
            org_tubepress_api_const_options_names_Display::RESULTS_PER_PAGE        => 20,
            org_tubepress_api_const_options_names_Display::THUMB_HEIGHT            => 90,
            org_tubepress_api_const_options_names_Display::THUMB_WIDTH             => 120,
            org_tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT        => 350,
            org_tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH         => 425,
            org_tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP           => 300,
            org_tubepress_api_const_options_names_Advanced::CACHE_CLEAN_FACTOR     => 20,
            org_tubepress_api_const_options_names_Advanced::CACHE_LIFETIME_SECONDS => 3600
        ),
        org_tubepress_api_const_options_Type::TIME_FRAME => array(
            org_tubepress_api_const_options_names_Output::MOST_VIEWED_VALUE   => org_tubepress_api_const_options_values_TimeFrameValue::TODAY,
            org_tubepress_api_const_options_names_Output::TOP_RATED_VALUE     => org_tubepress_api_const_options_values_TimeFrameValue::TODAY,
            org_tubepress_api_const_options_names_Output::TOP_FAVORITES_VALUE => org_tubepress_api_const_options_values_TimeFrameValue::TODAY
        ),
        org_tubepress_api_const_options_Type::ORDER => array(
            org_tubepress_api_const_options_names_Display::ORDER_BY => org_tubepress_api_const_options_values_OrderValue::VIEW_COUNT,
        ),
        org_tubepress_api_const_options_Type::PLAYER => array(
            org_tubepress_api_const_options_names_Display::CURRENT_PLAYER_NAME => org_tubepress_api_const_options_values_PlayerValue::NORMAL,
        ),
        org_tubepress_api_const_options_Type::SAFE_SEARCH => array(
            org_tubepress_api_const_options_names_Feed::FILTER => org_tubepress_api_const_options_values_SafeSearchValue::MODERATE
        ),
        org_tubepress_api_const_options_Type::PLAYER_IMPL => array(
            org_tubepress_api_const_options_names_Embedded::PLAYER_IMPL => org_tubepress_api_const_options_values_PlayerImplementationValue::PROVIDER_BASED
        ),
        org_tubepress_api_const_options_Type::THEME => array(
            org_tubepress_api_const_options_names_Display::THEME => ''
        ),
        org_tubepress_api_const_options_Type::OUTPUT => array(
            org_tubepress_api_const_options_names_Output::OUTPUT => ''
        )
    );

    private static $_vimeoOnly = array(
        org_tubepress_api_const_options_values_ModeValue::VIMEO_UPLOADEDBY,
        org_tubepress_api_const_options_values_ModeValue::VIMEO_LIKES,
        org_tubepress_api_const_options_values_ModeValue::VIMEO_APPEARS_IN,
        org_tubepress_api_const_options_values_ModeValue::VIMEO_SEARCH,
        org_tubepress_api_const_options_values_ModeValue::VIMEO_CREDITED,
        org_tubepress_api_const_options_values_ModeValue::VIMEO_ALBUM,
        org_tubepress_api_const_options_values_ModeValue::VIMEO_GROUP,
        org_tubepress_api_const_options_values_ModeValue::VIMEO_CHANNEL,
        org_tubepress_api_const_options_names_Meta::LIKES,
        org_tubepress_api_const_options_names_Feed::VIMEO_KEY,
        org_tubepress_api_const_options_names_Feed::VIMEO_SECRET
    );

    private static $_youtubeOnly = array(
        org_tubepress_api_const_options_values_ModeValue::FAVORITES,
        org_tubepress_api_const_options_values_ModeValue::PLAYLIST,
        org_tubepress_api_const_options_values_ModeValue::TAG,
        org_tubepress_api_const_options_values_ModeValue::USER,
        org_tubepress_api_const_options_values_ModeValue::FEATURED,
        org_tubepress_api_const_options_values_ModeValue::MOST_DISCUSSED,
        org_tubepress_api_const_options_values_ModeValue::MOST_RECENT,
        org_tubepress_api_const_options_values_ModeValue::MOST_RESPONDED,
        org_tubepress_api_const_options_values_ModeValue::POPULAR,
        org_tubepress_api_const_options_values_ModeValue::TOP_FAVORITES,
        org_tubepress_api_const_options_values_ModeValue::TOP_RATED,
        org_tubepress_api_const_options_names_Embedded::PLAYER_HIGHLIGHT,
        org_tubepress_api_const_options_names_Embedded::SHOW_RELATED,
        org_tubepress_api_const_options_names_Meta::RATING,
        org_tubepress_api_const_options_names_Meta::RATINGS,
        org_tubepress_api_const_options_names_Feed::DEV_KEY,
        org_tubepress_api_const_options_names_Feed::FILTER,
        org_tubepress_api_const_options_names_Display::RANDOM_THUMBS,
        org_tubepress_api_const_options_names_Feed::EMBEDDABLE_ONLY,
        org_tubepress_api_const_options_names_Embedded::HIGH_QUALITY,
        org_tubepress_api_const_options_names_Embedded::PLAYER_IMPL,
        org_tubepress_api_const_options_names_Embedded::FULLSCREEN
    );

    static function appliesToYouTube($optionName)
    {
        return !in_array($optionName, self::$_vimeoOnly);
    }

    static function appliesToVimeo($optionName)
    {
        return !in_array($optionName, self::$_youtubeOnly);
    }

    /**
     * Given an option name, determine if the option can be set via a shortcode
     *
     * @param string $optionName The name of the option to look up
     *
     * @return boolean True if the option can be set via a shortcode, false otherwise
     */
    static function canOptionBeSetViaShortcode($optionName)
    {
        return !in_array($optionName, array(
            org_tubepress_api_const_options_names_Advanced::KEYWORD
        ));
    }

    /**
     * Get all possible option names
     *
     * @return An array of all TubePress option names
     */
    static function getAllOptionNames()
    {
        $results = array();
        foreach (self::$_options as $optionGroup) {
            $results = array_merge($results, array_keys($optionGroup));
        }
        return $results;
    }

    /**
     * Determine the TubePress category of a given option. The
     *  valid option category names are defined as the class names in
     *  the org_tubepress_api_const_options package. Each option must
     *  fall into exactly one category
     *
     * @param string $optionName The name of the option to look up
     *
     * @return string The category name for the given option
     */
    static function getCategory($optionName)
    {
        foreach (self::getOptionCategoryNames() as $optionCategoryName) {
            if (in_array($optionName, self::getOptionNamesForCategory($optionCategoryName))) {
                return $optionCategoryName;
            }
        }
    }

    /**
     * Determine the default value of a given option. Each option must
     *  have exactly one default value.
     *
     * @param string $optionName The name of the option to look up
     *
     * @return string The default value for the given option
     */
    static function getDefaultValue($optionName)
    {
        foreach (self::$_options as $optionType) {
            if (array_key_exists($optionName, $optionType)) {
                return $optionType[$optionName];
            }
        }
        return null;
    }

    /**
     * Get all option category names. The
     *  valid option category names are defined as the class names in
     *  the org_tubepress_api_const_options package.
     *
     * @return array The category option names
     */
    static function getOptionCategoryNames()
    {
        return array('output', 'display', 'embedded', 'meta', 'feed', 'advanced', 'widget');
    }

    /**
     * Get all option names in a given category
     *
     * @param string $category The name of the category to look up
     *
     * @return array The option names of the options in the given category
     */
    static function getOptionNamesForCategory($category)
    {
        $className = 'org_tubepress_api_const_options_names_' . ucwords($category);
        return self::_getConstantsForClass($className);
    }

    /**
     * Given the name of an "enum" type option, return
     *  the valid values that this option may take on.
     *
     * @param $optionName The name of the option to look up
     *
     * @return array The valid option values for the given option
     */
    static function getValidEnumValues($optionType)
    {
        switch ($optionType) {

            case org_tubepress_api_const_options_Type::THEME:

            $ioc                           = org_tubepress_impl_ioc_IocContainer::getInstance();
            $fs                            = $ioc->get('org_tubepress_api_filesystem_Explorer');
            $themeHandler                  = $ioc->get('org_tubepress_api_theme_ThemeHandler');
            $tubepressBaseInstallationPath = $fs->getTubePressBaseInstallationPath();
            $sysdir                        = "$tubepressBaseInstallationPath/sys/ui/themes";
            $userdir                       = $themeHandler->getUserContentDirectory() . '/themes';
            $result                        = array();
            $sysdirs                       = $fs->getDirectoriesInDirectory($sysdir, 'Options Reference');
            $userdirs                      = $fs->getDirectoriesInDirectory($userdir, 'Options Reference');

            foreach ($sysdirs as $fullDir) {
                array_push($result, basename($fullDir));
            }
            foreach ($userdirs as $fullDir) {
                array_push($result, basename($fullDir));
            }

            return $result;

        default:
            $className = 'org_tubepress_api_const_options_values_' . ucwords($optionType) . 'Value';
            return self::_getConstantsForClass($className);
        }
    }

    /**
     * Given a name, determine if there is an option that has that
     * name.
     *
     * @param string $candidateOptionName The name of the option to look up
     *
     * @return boolean True if an option with the given name exists, false otherwise.
     */
    static function isOptionName($candidateOptionName)
    {
        foreach (self::$_options as $optionType) {
            if (array_key_exists($candidateOptionName, $optionType)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Determine the type of the given option. Valid ption types are
     *  defined by the constants of the org_tubepress_api_const_options_Type class.
     *  Each option must map to exactly one type.
     *
     * @param string $optionName The name of the option to look up
     *
     * @return string The type name of the given option
     */
    static function getType($optionName)
    {
        foreach (self::$_options as $optionType => $values) {
            if (array_key_exists($optionName, $values)) {
                return $optionType;
            }
        }
    }

    /**
     * Given an option name, determine if the option should be displayed on the
     *  TubePress options form (UI)
     *
     * @param string $optionName The name of the option to look up
     *
     * @return boolean True if the option should be displayed on the options form, false otherwise
     */
    static function isOptionApplicableToOptionsForm($optionName)
    {
        return !in_array($optionName, array(
            org_tubepress_api_const_options_names_Output::VIDEO,
            org_tubepress_api_const_options_names_Output::OUTPUT,
            org_tubepress_api_const_options_names_Output::SEARCH_RESULTS_URL,
            org_tubepress_api_const_options_names_Output::SEARCH_RESULTS_ONLY,
            org_tubepress_api_const_options_names_Output::SEARCH_RESULTS_DOM_ID,
            org_tubepress_api_const_options_names_Advanced::GALLERY_ID
        ));
    }

    /**
     * Given an option category name, determine if the category should be displayed on the
     *  TubePress options form (UI)
     *
     * @param string $optionCategoryName The name of the option category to look up
     *
     * @return boolean True if the category should be displayed on the options form, false otherwise
     */
    static function isOptionCategoryApplicableToOptionsForm($optionCategoryName)
    {
        return !in_array($optionCategoryName, array(
            org_tubepress_api_const_options_CategoryName::WIDGET
        ));
    }

    /**
     * Given an option name, determine if the option is only applicable to TubePress Pro
     *
     * @param string $optionName The name of the option to look up
     *
     * @return boolean True if the option is TubePress Pro only, false otherwise
     */
    static function isOptionProOnly($optionName)
    {
        return in_array($optionName, array(
            org_tubepress_api_const_options_names_Display::AJAX_PAGINATION,
            org_tubepress_api_const_options_names_Display::HQ_THUMBS
        ));
    }

    /**
     * Given an option name, determine if the option should be stored in persistent storage
     *
     * @param string $optionName The name of the option to look up
     *
     * @return boolean True if the option should be stored in persistent storage, false otherwise
     */
    static function shouldBePersisted($optionName)
    {
        return !in_array($optionName, array(
            org_tubepress_api_const_options_names_Output::VIDEO,
            org_tubepress_api_const_options_names_Output::OUTPUT,
            org_tubepress_api_const_options_names_Output::SEARCH_RESULTS_URL,
            org_tubepress_api_const_options_names_Output::SEARCH_RESULTS_ONLY,
            org_tubepress_api_const_options_names_Output::SEARCH_RESULTS_DOM_ID,
            org_tubepress_api_const_options_names_Advanced::GALLERY_ID
        ));
    }

    static private function _getConstantsForClass($className)
    {
        $ref = new ReflectionClass($className);
        return array_values($ref->getConstants());
    }
}
