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
    'org_tubepress_options_category_Template'));

/**
 * The master reference for TubePress options - their names, deprecated
 * names, default values, types, etc.
 *
 */
class org_tubepress_options_reference_SimpleOptionsReference implements org_tubepress_options_reference_OptionsReference
{

    private $_options = array(
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
            org_tubepress_options_category_Template::TEMPLATE              => '',
            org_tubepress_options_category_Gallery::VIDEO                  => '',
            org_tubepress_options_category_Gallery::VIMEO_UPLOADEDBY_VALUE => 'mattkaar',
            org_tubepress_options_category_Gallery::VIMEO_LIKES_VALUE      => 'coiffier',
            org_tubepress_options_category_Gallery::VIMEO_APPEARS_IN_VALUE => 'royksopp',
            org_tubepress_options_category_Gallery::VIMEO_SEARCH_VALUE     => 'cats playing piano',
            org_tubepress_options_category_Gallery::VIMEO_CREDITED_VALUE   => 'patricklawler',
            org_tubepress_options_category_Gallery::VIMEO_CHANNEL_VALUE    => 'splitscreenstuff',
            org_tubepress_options_category_Gallery::VIMEO_GROUP_VALUE      => 'hdxs',
            org_tubepress_options_category_Gallery::VIMEO_ALBUM_VALUE      => '140484'
         
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
            org_tubepress_options_category_Feed::RESULT_COUNT_CAP       => 300
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
        )
    );
    
    private $_vimeoOnly = array(
        org_tubepress_gallery_TubePressGallery::VIMEO_UPLOADEDBY,
        org_tubepress_gallery_TubePressGallery::VIMEO_LIKES,
        org_tubepress_gallery_TubePressGallery::VIMEO_APPEARS_IN,
        org_tubepress_gallery_TubePressGallery::VIMEO_SEARCH,
        org_tubepress_gallery_TubePressGallery::VIMEO_CREDITED,
        org_tubepress_gallery_TubePressGallery::VIMEO_ALBUM,
        org_tubepress_gallery_TubePressGallery::VIMEO_GROUP,
        org_tubepress_gallery_TubePressGallery::VIMEO_CHANNEL,
        org_tubepress_options_category_Meta::LIKES
    );
    
    private $_youtubeOnly = array(
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

    function appliesToYouTube($optionName)
    {
        return !in_array($optionName, $this->_vimeoOnly);
    }
    
    function appliesToVimeo($optionName)
    {
        return !in_array($optionName, $this->_youtubeOnly);
    }
    
    /**
     * Given an option name, determine if the option can be set via a shortcode
     *
     * @param $candidateOptionName The name of the option to look up
     *
     * @return boolean True if the option can be set via a shortcode, false otherwise
     */
    function canOptionBeSetViaShortcode($optionName)
    {
        return !in_array($optionName, array(org_tubepress_options_category_Advanced::KEYWORD));
    }

    /**
     * Get all possible option names
     *
     * @return An array of all TubePress option names
     */
    function getAllOptionNames()
    {
        $results = array();
        foreach ($this->_options as $optionGroup) {
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
    function getCategory($optionName)
    {
        foreach ($this->getOptionCategoryNames() as $optionCategoryName) {
            if (in_array($optionName, $this->getOptionNamesForCategory($optionCategoryName))) {
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
    function getDefaultValue($optionName)
    {
        foreach ($this->_options as $optionType) {
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
    function getOptionCategoryNames()
    {
        return array('gallery', 'display', 'embedded', 'meta', 'feed', 'advanced', 'widget');
    }
    
    /**
     * Get all option names in a given category
     *
     * @param string $category The name of the category to look up
     *
     * @return array The option names of the options in the given category
     */
    function getOptionNamesForCategory($category)
    {
        $className = 'org_tubepress_options_category_' . ucwords($category);
        return $this->_getConstantsForClass($className);
    }
    
    /**
     * Given the name of an "enum" type option, return
     *  the valid values that this option may take on.
     *
     * @param $optionName The name of the option to look up
     *
     * @return array The valid option values for the given option
     */
    function getValidEnumValues($optionType)
    {
        switch ($optionType) {
            case org_tubepress_options_Type::PLAYER:
                return array('normal', 'popup','shadowbox','jqmodal', 'youtube', 'static', 'solo', 'vimeo');
            case org_tubepress_options_Type::ORDER:
                return array('relevance', 'viewCount', 'rating', 'published', 'random', 'position', 'commentCount', 'duration', 'title', 'newest', 'oldest');
            case org_tubepress_options_Type::MODE:
                return array('favorites', 'playlist', 'tag', 'user', 'recently_featured', 'mobile', 'most_discussed',
                    'most_linked', 'most_recent', 'most_responded',
                    org_tubepress_gallery_TubePressGallery::POPULAR,
                    org_tubepress_gallery_TubePressGallery::TOP_RATED, 
                    org_tubepress_gallery_TubePressGallery::VIMEO_UPLOADEDBY,
                    org_tubepress_gallery_TubePressGallery::VIMEO_LIKES,
                    org_tubepress_gallery_TubePressGallery::VIMEO_APPEARS_IN,
                    org_tubepress_gallery_TubePressGallery::VIMEO_SEARCH,
                    org_tubepress_gallery_TubePressGallery::VIMEO_CREDITED,
                    org_tubepress_gallery_TubePressGallery::VIMEO_CHANNEL,
                    org_tubepress_gallery_TubePressGallery::VIMEO_ALBUM,
                    org_tubepress_gallery_TubePressGallery::VIMEO_GROUP);
            case org_tubepress_options_Type::SAFE_SEARCH:
                return array('none', 'moderate', 'strict');
            case org_tubepress_options_Type::PLAYER_IMPL:
                return array(
                    org_tubepress_embedded_EmbeddedPlayerService::DDEFAULT,
                    org_tubepress_embedded_EmbeddedPlayerService::LONGTAIL
                );
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
    function isOptionName($candidateOptionName)
    {
        foreach ($this->_options as $optionType) {
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
    function getType($optionName)
    {
        foreach ($this->_options as $optionType => $values) {
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
    function isOptionApplicableToOptionsForm($optionName)
    {
        return !in_array($optionName, array(
            org_tubepress_options_category_Template::TEMPLATE,
            org_tubepress_options_category_Gallery::VIDEO
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
    function isOptionCategoryApplicableToOptionsForm($optionCategoryName)
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
    function isOptionProOnly($optionName)
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
    function shouldBePersisted($optionName)
    {
        return !in_array($optionName, array(
            org_tubepress_options_category_Template::TEMPLATE,
            org_tubepress_options_category_Gallery::VIDEO
        ));
    }

    private function _getConstantsForClass($className)
    {
        $ref = new ReflectionClass($className);
        return array_values($ref->getConstants());
    }
}
