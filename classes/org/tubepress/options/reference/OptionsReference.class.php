<?php
/**
 * Copyright 2006 - 2010 Eric D. Hough (http://ehough.com)
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

function_exists('tubepress_load_classes')
    || require(dirname(__FILE__) . '/../../../../tubepress_classloader.php');
tubepress_load_classes(array('org_tubepress_options_reference_OptionsReference',
    'org_tubepress_options_Type',
    'org_tubepress_options_category_Embedded',
    'org_tubepress_options_category_Gallery',
    'org_tubepress_options_category_Advanced',
    'org_tubepress_options_category_Feed',
    'org_tubepress_options_category_Widget',
    'org_tubepress_options_category_Display',
    'org_tubepress_options_category_Meta',
    'org_tubepress_options_category_Uploads',
    'org_tubepress_gallery_Gallery',
    'org_tubepress_embedded_EmbeddedPlayerService'));

/**
 * The master reference for TubePress options - their names, deprecated
 * names, default values, types, etc.
 *
 */
class org_tubepress_options_reference_OptionsReference
{
    private static $_options = array(
        org_tubepress_options_Type::COLOR => array(
            org_tubepress_options_category_Embedded::PLAYER_COLOR   => '999999',
            org_tubepress_options_category_Embedded::PLAYER_HIGHLIGHT => 'FFFFFF'
        ),
        org_tubepress_options_Type::MODE => array(
            org_tubepress_options_category_Gallery::MODE => 'recently_featured'
        ),
        org_tubepress_options_Type::TEXT => array(
            org_tubepress_options_category_Advanced::DATEFORMAT            => 'M j, Y',
            org_tubepress_options_category_Advanced::KEYWORD               => 'tubepress',
            org_tubepress_options_category_Advanced::VIDEO_BLACKLIST       => '',
            org_tubepress_options_category_Gallery::FAVORITES_VALUE        => 'mrdeathgod',
            org_tubepress_options_category_Gallery::PLAYLIST_VALUE         => 'D2B04665B213AE35',
            org_tubepress_options_category_Gallery::TAG_VALUE              => 'stewart daily show',
            org_tubepress_options_category_Gallery::USER_VALUE             => '3hough',
            org_tubepress_options_category_Feed::DEV_KEY                   => 'AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg',
            org_tubepress_options_category_Widget::TITLE                   => 'TubePress',
            org_tubepress_options_category_Widget::TAGSTRING               => '[tubepress thumbHeight=\'105\' thumbWidth=\'135\']',
            org_tubepress_options_category_Gallery::VIDEO                  => '',
            org_tubepress_options_category_Gallery::VIMEO_UPLOADEDBY_VALUE => 'mattkaar',
            org_tubepress_options_category_Gallery::VIMEO_LIKES_VALUE      => 'coiffier',
            org_tubepress_options_category_Gallery::VIMEO_APPEARS_IN_VALUE => 'royksopp',
            org_tubepress_options_category_Gallery::VIMEO_SEARCH_VALUE     => 'cats playing piano',
            org_tubepress_options_category_Gallery::VIMEO_CREDITED_VALUE   => 'patricklawler',
            org_tubepress_options_category_Gallery::VIMEO_CHANNEL_VALUE    => 'splitscreenstuff',
            org_tubepress_options_category_Gallery::VIMEO_GROUP_VALUE      => 'hdxs',
            org_tubepress_options_category_Gallery::VIMEO_ALBUM_VALUE      => '140484',
            org_tubepress_options_category_Gallery::DIRECTORY_VALUE        => 'sample_videos',
            org_tubepress_options_category_Uploads::FFMPEG_BINARY_LOCATION => '',
            org_tubepress_options_category_Uploads::ADMIN_PAGE_PASSWORD    => ''
        ),
        org_tubepress_options_Type::BOOL => array(
            org_tubepress_options_category_Advanced::DEBUG_ON           => true,
            org_tubepress_options_category_Display::RANDOM_THUMBS       => true,
            org_tubepress_options_category_Display::RELATIVE_DATES      => false,
            org_tubepress_options_category_Display::PAGINATE_ABOVE      => true,
            org_tubepress_options_category_Display::PAGINATE_BELOW      => true,
            org_tubepress_options_category_Display::AJAX_PAGINATION     => false,
            org_tubepress_options_category_Display::HQ_THUMBS           => false,
            org_tubepress_options_category_Embedded::AUTOPLAY           => false,
            org_tubepress_options_category_Embedded::BORDER             => false,
            org_tubepress_options_category_Embedded::GENIE              => false,
            org_tubepress_options_category_Embedded::LOOP               => false,
            org_tubepress_options_category_Embedded::SHOW_INFO          => false,
            org_tubepress_options_category_Embedded::SHOW_RELATED       => true,
            org_tubepress_options_category_Embedded::FULLSCREEN         => true,
            org_tubepress_options_category_Embedded::HIGH_QUALITY       => false,
            org_tubepress_options_category_Meta::AUTHOR                 => false,
            org_tubepress_options_category_Meta::CATEGORY               => false,
            org_tubepress_options_category_Meta::DESCRIPTION            => false,
            org_tubepress_options_category_Meta::ID                     => false,
            org_tubepress_options_category_Meta::LENGTH                 => true,
            org_tubepress_options_category_Meta::RATING                 => false,
            org_tubepress_options_category_Meta::RATINGS                => false,
            org_tubepress_options_category_Meta::TAGS                   => false,
            org_tubepress_options_category_Meta::TITLE                  => true,
            org_tubepress_options_category_Meta::UPLOADED               => false,
            org_tubepress_options_category_Meta::URL                    => false,
            org_tubepress_options_category_Meta::VIEWS                  => true,
            org_tubepress_options_category_Meta::LIKES                  => false,
            org_tubepress_options_category_Feed::CACHE_ENABLED          => false,
            org_tubepress_options_category_Feed::EMBEDDABLE_ONLY        => true
        ),
        org_tubepress_options_Type::INTEGRAL => array(
            org_tubepress_options_category_Display::DESC_LIMIT          => 80,
            org_tubepress_options_category_Display::RESULTS_PER_PAGE    => 20,
            org_tubepress_options_category_Display::THUMB_HEIGHT        => 90,
            org_tubepress_options_category_Display::THUMB_WIDTH         => 120,
            org_tubepress_options_category_Embedded::EMBEDDED_HEIGHT    => 350,
            org_tubepress_options_category_Embedded::EMBEDDED_WIDTH     => 425,
            org_tubepress_options_category_Feed::RESULT_COUNT_CAP       => 300,
            org_tubepress_options_category_Uploads::THUMBS_PER_VIDEO    => 3,
        ),
        org_tubepress_options_Type::TIME_FRAME => array(
            org_tubepress_options_category_Gallery::MOST_VIEWED_VALUE   => 'today',
            org_tubepress_options_category_Gallery::TOP_RATED_VALUE     => 'today'
        ),
        org_tubepress_options_Type::ORDER => array(
            org_tubepress_options_category_Display::ORDER_BY            => 'viewCount',
        ),
        org_tubepress_options_Type::PLAYER => array(
            org_tubepress_options_category_Display::CURRENT_PLAYER_NAME => 'normal',
        ),
        org_tubepress_options_Type::SAFE_SEARCH => array(
            org_tubepress_options_category_Feed::FILTER                 => 'moderate'    
        ),
        org_tubepress_options_Type::PLAYER_IMPL => array(
            org_tubepress_options_category_Embedded::PLAYER_IMPL        => 'youtube'
        ),
        org_tubepress_options_Type::THEME => array(
            org_tubepress_options_category_Display::THEME => ''
        )
    );
    
    private static $_vimeoOnly = array(
        org_tubepress_gallery_Gallery::VIMEO_UPLOADEDBY,
        org_tubepress_gallery_Gallery::VIMEO_LIKES,
        org_tubepress_gallery_Gallery::VIMEO_APPEARS_IN,
        org_tubepress_gallery_Gallery::VIMEO_SEARCH,
        org_tubepress_gallery_Gallery::VIMEO_CREDITED,
        org_tubepress_gallery_Gallery::VIMEO_ALBUM,
        org_tubepress_gallery_Gallery::VIMEO_GROUP,
        org_tubepress_gallery_Gallery::VIMEO_CHANNEL,
        org_tubepress_options_category_Meta::LIKES
    );
    
    private static $_youtubeOnly = array(
        'favorites', 'playlist', 'tag', 'user', 'recently_featured', 'mobile', 'most_discussed',
        'most_linked', 'most_recent', 'most_responded', 'most_viewed', 'top_rated',
        org_tubepress_options_category_Embedded::GENIE,
        org_tubepress_options_category_Embedded::PLAYER_COLOR,
        org_tubepress_options_category_Embedded::SHOW_RELATED,
        org_tubepress_options_category_Embedded::BORDER,
        org_tubepress_options_category_Meta::RATING,
        org_tubepress_options_category_Meta::RATINGS,
        org_tubepress_options_category_Feed::DEV_KEY,
        org_tubepress_options_category_Feed::FILTER,
        org_tubepress_options_category_Display::RANDOM_THUMBS,
        org_tubepress_options_category_Feed::EMBEDDABLE_ONLY,
        org_tubepress_options_category_Embedded::LOOP,
        org_tubepress_options_category_Embedded::HIGH_QUALITY,
        org_tubepress_options_category_Embedded::PLAYER_IMPL
    );
    
    private static $_uploadsOnly = array(
        org_tubepress_options_category_Uploads::FFMPEG_BINARY_LOCATION,
        org_tubepress_options_category_Uploads::THUMBS_PER_VIDEO,
        org_tubepress_gallery_Gallery::DIRECTORY,
        org_tubepress_options_category_Uploads::ADMIN_PAGE_PASSWORD
    );

    static function appliesToYouTube($optionName)
    {
        return !in_array($optionName, self::$_vimeoOnly) &&
            !in_array($optionName, self::$_uploadsOnly);
    }
    
    static function appliesToVimeo($optionName)
    {
        return !in_array($optionName, self::$_youtubeOnly) &&
            !in_array($optionName, self::$_uploadsOnly);
    }
    
    /**
     * Given an option name, determine if the option can be set via a shortcode
     *
     * @param $candidateOptionName The name of the option to look up
     *
     * @return boolean True if the option can be set via a shortcode, false otherwise
     */
    static function canOptionBeSetViaShortcode($optionName)
    {
        return !in_array($optionName, array(
            org_tubepress_options_category_Advanced::KEYWORD,
            org_tubepress_options_category_Uploads::FFMPEG_BINARY_LOCATION,
            org_tubepress_options_category_Uploads::ADMIN_PAGE_PASSWORD
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
     *  the org_tubepress_options_category package. Each option must
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
        return NULL;
    }

    /**
     * Get all option category names. The
     *  valid option category names are defined as the class names in
     *  the org_tubepress_options_category package.
     *
     * @return array The category option names
     */
    static function getOptionCategoryNames()
    {
        return array('gallery', 'display', 'embedded', 'meta', 'feed', 'uploads', 'advanced', 'widget');
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
        $className = 'org_tubepress_options_category_' . ucwords($category);
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
            case org_tubepress_options_Type::PLAYER:
                return array('normal', 'popup','shadowbox','jqmodal', 'youtube', 'static', 'solo', 'vimeo');
            case org_tubepress_options_Type::ORDER:
                return array('relevance', 'viewCount', 'rating', 'published', 'random', 'position', 'commentCount', 'duration', 'title', 'newest', 'oldest');
            case org_tubepress_options_Type::MODE:
                return array(
                    org_tubepress_gallery_Gallery::DIRECTORY,
                    org_tubepress_gallery_Gallery::FAVORITES,
                    org_tubepress_gallery_Gallery::PLAYLIST,
                    org_tubepress_gallery_Gallery::TAG,
                    org_tubepress_gallery_Gallery::USER,
                    org_tubepress_gallery_Gallery::FEATURED,
                    org_tubepress_gallery_Gallery::MOBILE,
                    org_tubepress_gallery_Gallery::MOST_DISCUSSED,
                    org_tubepress_gallery_Gallery::MOST_LINKED,
                    org_tubepress_gallery_Gallery::MOST_RECENT,
                    org_tubepress_gallery_Gallery::MOST_RESPONDED,
                    org_tubepress_gallery_Gallery::POPULAR,
                    org_tubepress_gallery_Gallery::TOP_RATED, 
                    org_tubepress_gallery_Gallery::VIMEO_UPLOADEDBY,
                    org_tubepress_gallery_Gallery::VIMEO_LIKES,
                    org_tubepress_gallery_Gallery::VIMEO_APPEARS_IN,
                    org_tubepress_gallery_Gallery::VIMEO_SEARCH,
                    org_tubepress_gallery_Gallery::VIMEO_CREDITED,
                    org_tubepress_gallery_Gallery::VIMEO_CHANNEL,
                    org_tubepress_gallery_Gallery::VIMEO_ALBUM,
                    org_tubepress_gallery_Gallery::VIMEO_GROUP);
            case org_tubepress_options_Type::SAFE_SEARCH:
                return array('none', 'moderate', 'strict');
            case org_tubepress_options_Type::PLAYER_IMPL:
                return array(
                    org_tubepress_embedded_EmbeddedPlayerService::PROVIDER_BASED,
                    org_tubepress_embedded_EmbeddedPlayerService::LONGTAIL
                );
            case org_tubepress_options_Type::THEME:
                $tubepressBaseInstallationPath = org_tubepress_util_FilesystemUtils::getTubePressBaseInstallationPath();
                $dir = "$tubepressBaseInstallationPath/ui/themes";
                $result = array();
                $dirs = org_tubepress_util_FilesystemUtils::getDirectoriesInDirectory($dir, 'Options reference');
                foreach ($dirs as $fullDir) {
                    array_push($result, basename($fullDir));
                }
                return $result;
        }
        return array('today', 'this_week', 'this_month', 'all_time');
    }

    /**
     * Given a name, determine if there is an option that has that
     * name.
     *
     * @param $candidateOptionName The name of the option to look up
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
     *  defined by the constants of the org_tubepress_options_Type class.
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
     * @param $candidateOptionName The name of the option to look up
     *
     * @return boolean True if the option should be displayed on the options form, false otherwise
     */
    static function isOptionApplicableToOptionsForm($optionName)
    {
        return !in_array($optionName, array(
            org_tubepress_options_category_Gallery::VIDEO,
            org_tubepress_options_category_Uploads::ADMIN_PAGE_PASSWORD
        ));
    }

    /**
     * Given an option category name, determine if the category should be displayed on the
     *  TubePress options form (UI)
     *
     * @param $candidateOptionName The name of the option category to look up
     *
     * @return boolean True if the category should be displayed on the options form, false otherwise
     */
    static function isOptionCategoryApplicableToOptionsForm($optionCategoryName)
    {
        return !in_array($optionCategoryName, array(org_tubepress_options_Category::WIDGET));
    }

    /**
     * Given an option name, determine if the option is only applicable to TubePress Pro
     *
     * @param $optionName The name of the option to look up
     *
     * @return boolean True if the option is TubePress Pro only, false otherwise
     */
    static function isOptionProOnly($optionName)
    {
        return in_array($optionName, array(org_tubepress_options_category_Display::AJAX_PAGINATION,
            org_tubepress_options_category_Display::HQ_THUMBS));
    }

    /**
     * Given an option name, determine if the option should be stored in persistent storage
     *
     * @param $candidateOptionName The name of the option to look up
     *
     * @return boolean True if the option should be stored in persistent storage, false otherwise
     */
    static function shouldBePersisted($optionName)
    {
        return !in_array($optionName, array(
            org_tubepress_options_category_Gallery::VIDEO
        ));
    }

    static private function _getConstantsForClass($className)
    {
        $ref = new ReflectionClass($className);
        return array_values($ref->getConstants());
    }
}
