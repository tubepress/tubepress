<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

interface tubepress_dailymotion_api_Constants
{
    const GALLERY_SOURCE_FAVORITES     = 'dailymotionFavorites';
    const GALLERY_SOURCE_FEATURED      = 'dailymotionFeatured';
    const GALLERY_SOURCE_LIST          = 'dailymotionList';
    const GALLERY_SOURCE_PLAYLIST      = 'dailymotionPlaylist';
    const GALLERY_SOURCE_RELATED       = 'dailymotionRelated';
    const GALLERY_SOURCE_SEARCH        = 'dailymotionSearch';
    const GALLERY_SOURCE_SUBSCRIPTIONS = 'dailymotionSubscriptions';
    const GALLERY_SOURCE_TAG           = 'dailymotionTag';
    const GALLERY_SOURCE_USER          = 'dailymotionUser';

    const OPTION_FAVORITES_VALUE     = 'dailymotionFavoritesValue';
    const OPTION_FEATURED_VALUE      = 'dailymotionFeaturedValue';
    const OPTION_LIST_VALUE          = 'dailymotionListValue';
    const OPTION_PLAYLIST_VALUE      = 'dailymotionPlaylistValue';
    const OPTION_RELATED_VALUE       = 'dailymotionRelatedValue';
    const OPTION_SEARCH_VALUE        = 'dailymotionSearchValue';
    const OPTION_SUBSCRIPTIONS_VALUE = 'dailymotionSubscriptionsValue';
    const OPTION_TAG_VALUE           = 'dailymotionTagValue';
    const OPTION_USER_VALUE          = 'dailymotionUserValue';

    const OPTION_PLAYER_COLOR          = 'dailymotionPlayerColorHighlight';
    const OPTION_PLAYER_QUALITY        = 'dailymotionPlayerQuality';
    const OPTION_PLAYER_SHOW_CONTROLS  = 'dailymotionPlayerShowControls';
    const OPTION_PLAYER_SHOW_ENDSCREEN = 'dailymotionPlayerShowEndScreen';
    const OPTION_PLAYER_SHOW_LOGO      = 'dailymotionPlayerShowLogo';
    const OPTION_PLAYER_SHOW_SHARING   = 'dailymotionPlayerShowSharing';
    const OPTION_PLAYER_THEME          = 'dailymotionPlayerTheme';
    const OPTION_PLAYER_ORIGIN_DOMAIN  = 'dailymotionPlayerOriginDomain';
    const OPTION_PLAYER_ID             = 'dailymotionPlayerId';

    const OPTION_FEED_FAMILY_FILTER = 'dailymotionFamilyFilter';
    const OPTION_FEED_LOCALE        = 'dailymotionLocale';

    const OPTION_FEED_COUNTRY            = 'dailymotionCountry';
    const OPTION_FEED_LANGUAGE_DETECTED  = 'dailymotionLanguageDetected';
    const OPTION_FEED_LANGUAGES_DECLARED = 'dailymotionLanguagesDeclared';
    const OPTION_FEED_FEATURED_ONLY      = 'dailymotionFeaturedOnly';
    const OPTION_FEED_GENRE              = 'dailymotionGenre';
    const OPTION_FEED_NO_GENRE           = 'dailymotionExcludeGenre';
    const OPTION_FEED_HD_ONLY            = 'dailymotionHdOnly';
    const OPTION_FEED_LIVE_FILTER        = 'dailymotionLiveFilter';
    const OPTION_FEED_PREMIUM_FILTER     = 'dailymotionPremiumFilter';
    const OPTION_FEED_PARTNER_FILTER     = 'dailymotionPartnerFilter';
    const OPTION_FEED_SHORTER_THAN       = 'dailymotionOnlyShorterThan';
    const OPTION_FEED_LONGER_THAN        = 'dailymotionOnlyLongerThan';
    const OPTION_FEED_TAGS_STRONG        = 'dailymotionOnlyStrongTag';
    const OPTION_FEED_TAGS               = 'dailymotionOnlyTags';
    const OPTION_FEED_OWNERS_FILTER      = 'dailymotionOnlyOwners';
    const OPTION_FEED_SEARCH             = 'dailymotionOnlySearch';

    const OPTION_THUMBS_RATIO = 'dailymotionThumbnailRatio';
    const OPTION_THUMB_SIZE   = 'dailymotionThumbnailPreferredSize';

    const ORDER_BY_DEFAULT    = 'default';
    const ORDER_BY_NEWEST     = 'newest';
    const ORDER_BY_OLDEST     = 'oldest';
    const ORDER_BY_VIEW_COUNT = 'viewCount';
    const ORDER_BY_RELEVANCE  = 'relevance';
    const ORDER_BY_RANDOM     = 'random';
    const ORDER_BY_RANKING    = 'ranking';
    const ORDER_BY_TRENDING   = 'trending';

    const FILTER_LIVE_ALL           = 'all';
    const FILTER_LIVE_LIVE_ONLY     = 'onlyLive';
    const FILTER_LIVE_LIVE_OFF      = 'onlyLiveOffAir';
    const FILTER_LIVE_LIVE_ON       = 'onlyLiveOnAir';
    const FILTER_LIVE_LIVE_UPCOMING = 'onlyLiveUpcoming';
    const FILTER_LIVE_NON_LIVE      = 'onlyNonLive';

    const FILTER_PREMIUM_ALL              = 'all';
    const FILTER_PREMIUM_PREMIUM_ONLY     = 'onlyPremium';
    const FILTER_PREMIUM_NON_PREMIUM_ONLY = 'onlyNonPremium';

    const FILTER_PARTNER_ALL              = 'all';
    const FILTER_PARTNER_PARTNER_ONLY     = 'onlyPartner';
    const FILTER_PARTNER_NON_PARTNER_ONLY = 'onlyUserGenerated';

    const THUMB_SIZE_60  = '60px';
    const THUMB_SIZE_120 = '120px';
    const THUMB_SIZE_180 = '180px';
    const THUMB_SIZE_240 = '240px';
    const THUMB_SIZE_360 = '360px';
    const THUMB_SIZE_480 = '480px';
    const THUMB_SIZE_720 = '720px';
    const THUMB_SIZE_MAX = 'max';

    const THUMB_RATIO_ORIGINAL   = 'original';
    const THUMB_RATIO_SQUARE     = 'square';
    const THUMB_RATIO_WIDESCREEN = 'widescreen';

    const PLAYER_QUALITY_240  = '240p';
    const PLAYER_QUALITY_380  = '380p';
    const PLAYER_QUALITY_480  = '480p';
    const PLAYER_QUALITY_720  = '720p';
    const PLAYER_QUALITY_1080 = '1080p';
    const PLAYER_QUALITY_1440 = '1440p';
    const PLAYER_QUALITY_2160 = '2160p';
    const PLAYER_QUALITY_AUTO = 'auto';

    const PLAYER_THEME_LIGHT = 'light';
    const PLAYER_THEME_DARK  = 'dark';

}
