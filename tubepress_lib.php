<?php
/**
tubepress.php

Copyright (C) 2007 Eric D. Hough (http://ehough.com)
    
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

function_exists('tp_executeOptionsPage')
    || require('env/WordPress/TubePressOptions.php');
function_exists('_tpMsg')
    || require('common/messages.php');
    
defined(TP_OPTION_NAME)
    || require('common/defines.php');
    
class_exists('TubePressOptionsPackage')
    || require('common/class/TubePressOptionsPackage.php');
class_exists('TubePressStatic')
    || require('common/class/TubePressStatic.php');
class_exists('TubePressGallery')
    || require('common/class/TubePressGallery.php');
class_exists('TubePressDebug')
    || require('common/class/TubePressDebug.php');

/**
 * Spits out the CSS and JS files that we always need for TubePress
 */
function tp_insertCSSJS()
{
    global $tubepress_base_url;
    $url = $tubepress_base_url . "/common";
    print<<<GBS
        <script type="text/javascript" src="{$url}/tubepress.js"></script>
        <link rel="stylesheet" href="{$url}/tubepress.css" 
            type="text/css" />
        <link rel="stylesheet" href="{$url}/pagination.css" 
            type="text/css" />
GBS;
}

/**
 * Spits out the CSS and JS files that we need for ThickBox
 */
function tp_insertGreyBox()
{
    global $tubepress_base_url;
    $url = $tubepress_base_url . "/lib/greybox";
    print<<<GBS
        <script type="text/javascript">
            var GB_ROOT_DIR = "$url/";
        </script>
        <script type="text/javascript" 
            src="{$url}/AJS.js"></script>
        <script type="text/javascript"
            src="{$url}/AJS_fx.js"></script>
        <script type="text/javascript"
            src="{$url}/gb_scripts.js"></script>
        <link rel="stylesheet"
            href="{$url}/gb_styles.css" type="text/css" />
GBS;
}
    
/**
 * Spits out the CSS and JS files that we need for LightWindow
 */
function tp_insertLightWindow()
{
    global $tubepress_base_url;
    $url = $tubepress_base_url . "/lib/lightWindow";
    print<<<GBS
        <script type="text/javascript" 
            src="{$url}/javascript/prototype.js"></script>
        <script type="text/javascript" 
            src="{$url}/javascript/effects.js"></script>
        <script type="text/javascript" 
            src="{$url}/javascript/lightWindow.js"></script>
        <link rel="stylesheet" 
            href="{$url}/css/lightWindow.css" 
            media="screen" type="text/css" />
GBS;
}
?>
