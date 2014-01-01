<?php 
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 * 
 * This file is part of TubePress (http://tubepress.com)
 * 
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
?>
<object id="<?php echo ${tubepress_api_const_template_Variable::VIDEO_DOM_ID}; ?>" type="application/x-shockwave-flash" width="<?php echo ${tubepress_api_const_template_Variable::EMBEDDED_WIDTH}; ?>" height="<?php echo intval(${tubepress_api_const_template_Variable::EMBEDDED_HEIGHT}) + 32; ?>" data="http://getembedplus.com/embedplus.swf">

	<param name="movie"		value="http://getembedplus.com/embedplus.swf" />
	<param name="quality"		value="high" />
	<param name="wmode"		value="transparent" />
	<param name="allowscriptaccess"	value="always" />
	<param name="allowFullScreen"	value="true" />
	<param name="flashvars" 	value="ytid=<?php echo ${tubepress_api_const_template_Variable::VIDEO_ID}; ?>&width=<?php echo ${tubepress_api_const_template_Variable::EMBEDDED_WIDTH}; ?>&height=<?php echo ${tubepress_api_const_template_Variable::EMBEDDED_HEIGHT}; ?>&hd=1" />

	<iframe class="cantembedplus" title="YouTube video player" width="<?php echo ${tubepress_api_const_template_Variable::EMBEDDED_WIDTH}; ?>" height="<?php echo ${tubepress_api_const_template_Variable::EMBEDDED_HEIGHT}; ?>" src="<?php echo ${tubepress_api_const_template_Variable::EMBEDDED_DATA_URL}; ?>" frameborder="0" allowfullscreen></iframe>
</object>
<!--[if lte IE 6]> <style type="text/css">.cantembedplus{display:none;}</style><![endif]-->