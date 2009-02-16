<?php
/**
 * Copyright 2006, 2007, 2008, 2009 Eric D. Hough (http://ehough.com)
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
 * Simple implementation of options reference
 *
 */
class org_tubepress_options_reference_SimpleOptionsReference implements org_tubepress_options_reference_OptionsReference
{

    private $_options = array(
        org_tubepress_options_Type::COLOR => array(
            org_tubepress_options_category_Embedded::PLAYER_COLOR   => "999999",
            org_tubepress_options_category_Embedded::PLAYER_HIGHLIGHT => "FFFFFF"
        ),
        org_tubepress_options_Type::MODE => array(
            org_tubepress_options_category_Gallery::MODE => "recently_featured"
        ),
        org_tubepress_options_Type::TEXT => array(
            org_tubepress_options_category_Advanced::DATEFORMAT     => "M j, Y",
            org_tubepress_options_category_Advanced::KEYWORD        => "tubepress",
            org_tubepress_options_category_Gallery::FAVORITES_VALUE => "mrdeathgod",
            org_tubepress_options_category_Gallery::PLAYLIST_VALUE  => "D2B04665B213AE35",
            org_tubepress_options_category_Gallery::TAG_VALUE       => "stewart daily show",
            org_tubepress_options_category_Gallery::USER_VALUE      => "3hough",
            org_tubepress_options_category_Feed::CLIENT_KEY         => "ytapi-EricHough-TubePress-ki6oq9tc-0",
            org_tubepress_options_category_Feed::DEV_KEY            => "AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg",
            org_tubepress_options_category_Widget::TITLE            => "TubePress",
            org_tubepress_options_category_Widget::TAGSTRING        => "[tubepress thumbHeight='105', thumbWidth='135']"
        ),
        org_tubepress_options_Type::BOOL => array(
            org_tubepress_options_category_Advanced::DEBUG_ON           => true,
            org_tubepress_options_category_Advanced::NOFOLLOW_LINKS     => true,
            org_tubepress_options_category_Advanced::RANDOM_THUMBS      => true,
            org_tubepress_options_category_Display::RELATIVE_DATES      => false,
            org_tubepress_options_category_Embedded::AUTOPLAY           => false,
            org_tubepress_options_category_Embedded::BORDER             => false,
            org_tubepress_options_category_Embedded::GENIE              => false,
            org_tubepress_options_category_Embedded::LOOP               => false,
            org_tubepress_options_category_Embedded::SHOW_INFO          => false,
            org_tubepress_options_category_Embedded::SHOW_RELATED       => true,
            org_tubepress_options_category_Embedded::FULLSCREEN         => true,
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
            org_tubepress_options_category_Feed::CACHE_ENABLED          => true,
            org_tubepress_options_category_Feed::EMBEDDABLE_ONLY        => true
        ),
        org_tubepress_options_Type::INTEGRAL => array(
            org_tubepress_options_category_Display::DESC_LIMIT          => 80,
            org_tubepress_options_category_Display::RESULTS_PER_PAGE    => 20,
            org_tubepress_options_category_Display::THUMB_HEIGHT        => 90,
            org_tubepress_options_category_Display::THUMB_WIDTH         => 120,
            org_tubepress_options_category_Embedded::EMBEDDED_HEIGHT    => 355,
            org_tubepress_options_category_Embedded::EMBEDDED_WIDTH     => 425
        ),
        org_tubepress_options_Type::TIME_FRAME => array(
            org_tubepress_options_category_Gallery::MOST_VIEWED_VALUE   => "today",
            org_tubepress_options_category_Gallery::TOP_RATED_VALUE     => "today"
        ),
        org_tubepress_options_Type::ORDER => array(
            org_tubepress_options_category_Display::ORDER_BY            => "viewCount",
        ),
        org_tubepress_options_Type::PLAYER => array(
            org_tubepress_options_category_Display::CURRENT_PLAYER_NAME => "normal",
        ),
        org_tubepress_options_Type::QUALITY => array(
            org_tubepress_options_category_Embedded::QUALITY            => "normal"
        ),
        org_tubepress_options_Type::SAFE_SEARCH => array(
            org_tubepress_options_category_Feed::FILTER                 => "moderate"    
        )
    );

    function getAllOptionNames()
    {
        $results = array();
        foreach ($this->_options as $optionGroup) {
            $results = array_merge($results, array_keys($optionGroup));
        }
        return $results;
    }

    function getCategory($optionName)
    {
        foreach ($this->getOptionCategoryNames() as $optionCategoryName) {
            if (in_array($optionName, $this->getOptionNamesForCategory($optionCategoryName))) {
                return $optionCategoryName;
            }
        }
    }
    
    function getDefaultValue($optionName)
    {
        foreach ($this->_options as $optionType) {
            if (array_key_exists($optionName, $optionType)) {
                return $optionType[$optionName];
            }
        }
        return NULL;
    }

    function getOptionCategoryNames()
    {
        return array("gallery", "display", "embedded", "meta", "feed", "advanced", "widget");
    }
    
    function getOptionNamesForCategory($category)
    {
        $className = "org_tubepress_options_category_" . ucwords($category);
        return $this->_getConstantsForClass($className);
    }
    
    function getValidEnumValues($optionType)
    {
        switch ($optionType) {
            case org_tubepress_options_Type::PLAYER:
                return array("greybox", "lightwindow", "normal", "popup", "shadowbox", "youtube");
            case org_tubepress_options_Type::ORDER:
                return array("relevance", "viewCount", "rating", "updated", "random");
            case org_tubepress_options_Type::QUALITY:
                return array("normal", "high", "higher", "highest");
            case org_tubepress_options_Type::MODE:
                return array("favorites", "recently_featured", "mobile", "most_discussed",
                    "most_linked", "most_recent", "most_responded", "playlist", "most_viewed",
                    "tag", "top_rated", "user");
            case org_tubepress_options_Type::SAFE_SEARCH:
                return array("none", "moderate", "strict");
        }
        return array("today", "this_week", "this_month", "all_time");
    }

    function isOptionName($candidateOptionName)
    {
        foreach ($this->_options as $optionType) {
            if (array_key_exists($candidateOptionName, $optionType)) {
                return true;
            }
        }
        return false;
    }

    function getType($optionName)
    {
        foreach ($this->_options as $optionType => $values) {
            if (array_key_exists($optionName, $values)) {
                return $optionType;
            }
        }
    }

    private function _getConstantsForClass($className)
    {
        $ref = new ReflectionClass($className);
        return array_values($ref->getConstants());
    }
}
