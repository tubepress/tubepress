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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_const_options_names_Advanced',
    'org_tubepress_api_const_options_names_Display',
    'org_tubepress_api_const_options_names_Embedded',
    'org_tubepress_api_const_options_names_Feed',
    'org_tubepress_api_const_options_names_Meta',
    'org_tubepress_api_const_options_values_ModeValue',
    'org_tubepress_api_url_Url',
    'org_tubepress_api_exec_ExecutionContext',
    'org_tubepress_impl_url_commands_AbstractUrlBuilderCommand',
));

/**
 * Builds URLs to send out to YouTube for gdata
 *
 */
class org_tubepress_impl_url_commands_YouTubeUrlBuilderCommand extends org_tubepress_impl_url_commands_AbstractUrlBuilderCommand
{
    /**
     * Builds a gdata request url for a list of videos
     *
     * @param int $currentPage The current page of the gallery.
     *
     * @return string The gdata request URL for this gallery
     */
    protected function _buildGalleryUrl($currentPage)
    {
        $url         = '';
        $ioc         = org_tubepress_impl_ioc_IocContainer::getInstance();
        $execContext = $ioc->get('org_tubepress_api_exec_ExecutionContext');

        switch ($execContext->get(org_tubepress_api_const_options_names_Output::MODE)) {

        case org_tubepress_api_const_options_values_ModeValue::USER:
            $url = 'users/' . $execContext->get(org_tubepress_api_const_options_names_Output::USER_VALUE) . '/uploads';
            break;

        case org_tubepress_api_const_options_values_ModeValue::TOP_RATED:
            $url = 'standardfeeds/top_rated?time=' . $execContext->get(org_tubepress_api_const_options_names_Output::TOP_RATED_VALUE);
            break;

        case org_tubepress_api_const_options_values_ModeValue::POPULAR:
            $url = 'standardfeeds/most_viewed?time=' . $execContext->get(org_tubepress_api_const_options_names_Output::MOST_VIEWED_VALUE);
            break;

        case org_tubepress_api_const_options_values_ModeValue::PLAYLIST:
            $url = 'playlists/' . $execContext->get(org_tubepress_api_const_options_names_Output::PLAYLIST_VALUE);
            break;

        case org_tubepress_api_const_options_values_ModeValue::MOST_RESPONDED:
            $url = 'standardfeeds/most_responded';
            break;

        case org_tubepress_api_const_options_values_ModeValue::MOST_RECENT:
            $url = 'standardfeeds/most_recent';
            break;

        case org_tubepress_api_const_options_values_ModeValue::TOP_FAVORITES:
            $url = 'standardfeeds/top_favorites';
            break;

        case org_tubepress_api_const_options_values_ModeValue::MOST_DISCUSSED:
            $url = 'standardfeeds/most_discussed';
            break;

        case org_tubepress_api_const_options_values_ModeValue::FAVORITES:
            $url = 'users/' . $execContext->get(org_tubepress_api_const_options_names_Output::FAVORITES_VALUE) . '/favorites';
            break;

        case org_tubepress_api_const_options_values_ModeValue::TAG:
            $tags = $execContext->get(org_tubepress_api_const_options_names_Output::TAG_VALUE);
            $tags = str_replace(' ', '+', self::_replaceQuotes($tags));
            $tags = rawurlencode($tags);
            $url  = "videos?q=$tags";

            $filter = $execContext->get(org_tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER);
            if ($filter != '') {
                $url .= "&author=$filter";
            }
            break;

        default:
            $url = 'standardfeeds/recently_featured';
            break;
        }

        $request = new org_tubepress_api_url_Url("http://gdata.youtube.com/feeds/api/$url");
        $this->_commonUrlPostProcessing($execContext, $request);
        $this->_galleryUrlPostProcessing($execContext, $request, $currentPage);
        return $request->toString();
    }

    protected function _getHandledProviderName()
    {
        return org_tubepress_api_provider_Provider::YOUTUBE;
    }

    private static function _replaceQuotes($text)
    {
        return str_replace(array('&#8216', '&#8217', '&#8242;', '&#34', '&#8220;', '&#8221;', '&#8243;'), '"', $text);
    }

    protected function _buildSingleVideoUrl($id)
    {
        $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();
        $pc           = $ioc->get('org_tubepress_api_provider_ProviderCalculator');
        
        //TODO: what if this bails?
        $providerName = $pc->calculateProviderOfVideoId($id);

        if ($providerName !== org_tubepress_api_provider_Provider::YOUTUBE) {
            throw new Exception("Unable to build YouTube URL for video with ID $id");
        }

        $requestURL = new org_tubepress_api_url_Url("http://gdata.youtube.com/feeds/api/videos/$id");
        $this->_commonUrlPostProcessing($ioc->get('org_tubepress_api_exec_ExecutionContext'), $requestURL);

        return $requestURL->toString();
    }

    private function _commonUrlPostProcessing(org_tubepress_api_exec_ExecutionContext $execContext, org_tubepress_api_url_Url $url)
    {
        $url->setQueryVariable('v', 2);
        $url->setQueryVariable('key', $execContext->get(org_tubepress_api_const_options_names_Feed::DEV_KEY));
    }

    /**
     * Appends some global query parameters on the request
     * before we fire it off to YouTube
     *
     * @param string                  &$request The request to be manipulated
     * 
     * @return void
     */
    private function _galleryUrlPostProcessing(org_tubepress_api_exec_ExecutionContext $execContext, org_tubepress_api_url_Url $url, $currentPage)
    {
        $perPage = $execContext->get(org_tubepress_api_const_options_names_Display::RESULTS_PER_PAGE);

        /* start index of the videos */
        $start = ($currentPage * $perPage) - $perPage + 1;

        $url->setQueryVariable('start-index', $start);
        $url->setQueryVariable('max-results', $perPage);
        
        $this->_setOrderBy($execContext, $url);
        
        $url->setQueryVariable('safeSearch', $execContext->get(org_tubepress_api_const_options_names_Feed::FILTER));

        if ($execContext->get(org_tubepress_api_const_options_names_Feed::EMBEDDABLE_ONLY)) {
            $url->setQueryVariable('format', '5');
        }
    }

    private function _setOrderBy(org_tubepress_api_exec_ExecutionContext $execContext, org_tubepress_api_url_Url $url) 
    {
        $order = $execContext->get(org_tubepress_api_const_options_names_Display::ORDER_BY);
        $mode  = $execContext->get(org_tubepress_api_const_options_names_Output::MODE);

        if ($order == 'random') {
            return;
        }

        /* any feed can take these */
        if ($order == 'viewCount' || $order =='published') {
            $url->setQueryVariable('orderby', $order);
            return;
        }

        /* playlist specific stuff */
        if ($mode == org_tubepress_api_const_options_values_ModeValue::PLAYLIST) {
            if (in_array($order, array('position', 'commentCount', 'duration', 'title'))) {
                $url->setQueryVariable('orderby', $order);
            }
            return;
        }

        if (in_array($order, array('relevance', 'rating'))) {
            $url->setQueryVariable('orderby', $order);
        }
    }
}
