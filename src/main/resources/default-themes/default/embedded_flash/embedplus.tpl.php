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
<object type="application/x-shockwave-flash" width="<?php echo ${org_tubepress_api_const_template_Variable::EMBEDDED_WIDTH}; ?>" height="<?php echo intval(${org_tubepress_api_const_template_Variable::EMBEDDED_HEIGHT}) + 32; ?>" data="http://getembedplus.com/embedplus.swf">

	<param name="movie"		value="http://getembedplus.com/embedplus.swf" />
	<param name="quality"		value="high" />
	<param name="wmode"		value="transparent" />
	<param name="allowscriptaccess"	value="always" />
	<param name="allowFullScreen"	value="true" />
	<param name="flashvars" 	value="ytid=<?php echo ${org_tubepress_api_const_template_Variable::VIDEO_ID}; ?>&width=<?php echo ${org_tubepress_api_const_template_Variable::EMBEDDED_WIDTH}; ?>&height=<?php echo ${org_tubepress_api_const_template_Variable::EMBEDDED_HEIGHT}; ?>&hd=1" />

	<iframe class="cantembedplus" title="YouTube video player" width="<?php echo ${org_tubepress_api_const_template_Variable::EMBEDDED_WIDTH}; ?>" height="<?php echo ${org_tubepress_api_const_template_Variable::EMBEDDED_HEIGHT}; ?>" src="<?php echo ${org_tubepress_api_const_template_Variable::EMBEDDED_DATA_URL}; ?>" frameborder="0" allowfullscreen></iframe>
</object>
<!--[if lte IE 6]> <style type="text/css">.cantembedplus{display:none;}</style><![endif]-->
