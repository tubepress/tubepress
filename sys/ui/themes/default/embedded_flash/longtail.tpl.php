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
?>
<object type="application/x-shockwave-flash" data="<?php echo ${org_tubepress_api_const_template_Variable::TUBEPRESS_BASE_URL}; ?>/sys/ui/static/flash/longtail/player.swf" style="width: <?php echo ${org_tubepress_api_const_template_Variable::EMBEDDED_WIDTH}; ?>px; height: <?php echo ${org_tubepress_api_const_template_Variable::EMBEDDED_HEIGHT}; ?>px" >
  
        <param name="AllowScriptAccess" value="never" />
        <param name="wmode" value="opaque" />
        <param name="movie" value="<?php echo ${org_tubepress_api_const_template_Variable::TUBEPRESS_BASE_URL}; ?>/sys/ui/static/flash/longtail/player.swf" />
        <param name="bgcolor" value="<?php echo ${org_tubepress_api_const_template_Variable::EMBEDDED_COLOR_PRIMARY}; ?>" />
        <param name="frontcolor" value="<?php echo ${org_tubepress_api_const_template_Variable::EMBEDDED_COLOR_HIGHLIGHT}; ?>" />
        <param name="quality" value="high" />
        <param name="flashvars" value="file=<?php echo ${org_tubepress_api_const_template_Variable::EMBEDDED_DATA_URL}; ?>&amp;autostart=<?php echo ${org_tubepress_api_const_template_Variable::EMBEDDED_AUTOSTART}; ?>&amp;height=<?php echo ${org_tubepress_api_const_template_Variable::EMBEDDED_HEIGHT}; ?>&amp;width=<?php echo ${org_tubepress_api_const_template_Variable::EMBEDDED_WIDTH}; ?>&amp;frontcolor=<?php echo ${org_tubepress_api_const_template_Variable::EMBEDDED_COLOR_HIGHLIGHT}; ?>" />
      </object>
