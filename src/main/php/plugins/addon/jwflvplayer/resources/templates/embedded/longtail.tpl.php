<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
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
?>
<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' width='<?php echo ${tubepress_api_const_template_Variable::EMBEDDED_WIDTH}; ?>' height='<?php echo ${tubepress_api_const_template_Variable::EMBEDDED_HEIGHT}; ?>'>
    <param name='movie' value='<?php echo ${tubepress_api_const_template_Variable::TUBEPRESS_BASE_URL}; ?>/src/main/web/flash/longtail/player.swf'>
    <param name='allowfullscreen' value='true'>
    <param name='allowscriptaccess' value='always'>
    <param name='wmode' value='transparent'>
    <param name='flashvars' value='file=<?php echo ${tubepress_api_const_template_Variable::EMBEDDED_DATA_URL}; ?>&amp;autostart=<?php echo ${tubepress_api_const_template_Variable::EMBEDDED_AUTOSTART}; ?>&amp;backcolor=<?php echo ${tubepress_plugins_jwflvplayer_api_const_template_Variable::COLOR_BACK}; ?>&amp;frontcolor=<?php echo ${tubepress_plugins_jwflvplayer_api_const_template_Variable::COLOR_FRONT}; ?>&amp;lightcolor=<?php echo ${tubepress_plugins_jwflvplayer_api_const_template_Variable::COLOR_LIGHT}; ?>&amp;screencolor=<?php echo ${tubepress_plugins_jwflvplayer_api_const_template_Variable::COLOR_SCREEN}; ?>'>

<embed	type='application/x-shockwave-flash'
        src='<?php echo ${tubepress_api_const_template_Variable::TUBEPRESS_BASE_URL}; ?>/src/main/web/flash/longtail/player.swf'
		width='<?php echo ${tubepress_api_const_template_Variable::EMBEDDED_WIDTH}; ?>'
		height='<?php echo ${tubepress_api_const_template_Variable::EMBEDDED_HEIGHT}; ?>'
		bgcolor='undefined'
        allowscriptaccess='always'
        allowfullscreen='true'
        wmode='transparent'
        flashvars='file=<?php echo ${tubepress_api_const_template_Variable::EMBEDDED_DATA_URL}; ?>&amp;autostart=<?php echo ${tubepress_api_const_template_Variable::EMBEDDED_AUTOSTART}; ?>&amp;backcolor=<?php echo ${tubepress_plugins_jwflvplayer_api_const_template_Variable::COLOR_BACK}; ?>&amp;frontcolor=<?php echo ${tubepress_plugins_jwflvplayer_api_const_template_Variable::COLOR_FRONT}; ?>&amp;lightcolor=<?php echo ${tubepress_plugins_jwflvplayer_api_const_template_Variable::COLOR_LIGHT}; ?>&amp;screencolor=<?php echo ${tubepress_plugins_jwflvplayer_api_const_template_Variable::COLOR_SCREEN}; ?>'
</embed>
</object>