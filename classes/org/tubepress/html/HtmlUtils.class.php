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

/**
 * HTML utiilities for TubePress.
 */
class org_tubepress_html_HtmlUtils
{
    public static function getHeadElementsAsString($getVars, $include_jQuery = false)
    {
        global $tubepress_base_url;

        $jqueryInclude = '';
        if ($include_jQuery) {
            $jqueryInclude = "<script type=\"text/javascript\" src=\"$tubepress_base_url/ui/lib/jquery-1.4.2.min.js\"></script>";
        }

        $result = <<<GBS
    $jqueryInclude
<script type="text/javascript">function getTubePressBaseUrl(){return "$tubepress_base_url";}</script>
<script type="text/javascript" src="$tubepress_base_url/ui/lib/tubepress.js"></script>
<link rel="stylesheet" href="$tubepress_base_url/ui/themes/default/style.css" type="text/css" />

GBS;
    
        if (isset($getVars['tubepress_page']) && $getVars['tubepress_page'] > 1) {
            $result .= '<meta name="robots" content="noindex, nofollow" />
    ';
        }
        return $result;
    }
}
