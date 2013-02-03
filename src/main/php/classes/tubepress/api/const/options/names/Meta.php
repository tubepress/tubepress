<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Option names that control which meta info is displayed below video
 * thumbnails
 */
class tubepress_api_const_options_names_Meta
{
    const AUTHOR      = 'author';
    const CATEGORY    = 'category';
    const DESCRIPTION = 'description';
    const ID          = 'id';
    const KEYWORDS    = 'tags';
    const LENGTH      = 'length';
    const TITLE       = 'title';
    const UPLOADED    = 'uploaded';
    const URL         = 'url';
    const VIEWS       = 'views';

    const DATEFORMAT     = 'dateFormat';
    const DESC_LIMIT     = 'descriptionLimit';
    const RELATIVE_DATES = 'relativeDates';

    //DEPRECATED:
    const TAGS    = 'tags';
    const LIKES   = 'likes'; //this has been moved to vimeo
    const RATING  = 'rating';//this has been moved to YouTube
    const RATINGS = 'ratings';//this has been moved to YouTube
}
