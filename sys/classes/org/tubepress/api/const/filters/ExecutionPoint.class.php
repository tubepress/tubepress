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

/**
 * Filter execution point names.
 */
class org_tubepress_api_const_filters_ExecutionPoint
{
    /* immediately after videos are returned by the provider */
    const VIDEOS_DELIVERY = 'videosDelivery';

    /* immediately after thumbnail gallery template generation */
    const GALLERY_TEMPLATE = 'galleryTemplate';

    /* immediately after thumbnail gallery template is converted to HTML */
    const GALLERY_HTML = 'galleryHtml';

    /* immediately after single video template generation */
    const SINGLE_VIDEO_TEMPLATE = 'singleVideoTemplate';

    /* immediately after single video template is converted to HTML */
    const SINGLE_VIDEO_HTML = 'singleVideoHtml';

}
