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
<div id="<?php echo ${tubepress_api_const_template_Variable::VIDEO_DOM_ID}; ?>">
<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' width='<?php echo ${tubepress_api_const_template_Variable::EMBEDDED_WIDTH}; ?>' height='<?php echo ${tubepress_api_const_template_Variable::EMBEDDED_HEIGHT}; ?>'>
    <param name='movie' value='<?php echo ${tubepress_api_const_template_Variable::TUBEPRESS_BASE_URL}; ?>/src/main/web/flash/longtail/player.swf'>
    <param name='allowfullscreen' value='true'>
    <param name='allowscriptaccess' value='always'>
    <param name='wmode' value='transparent'>
    <param name='flashvars' value='file=<?php echo ${tubepress_api_const_template_Variable::EMBEDDED_DATA_URL}; ?>&amp;autostart=<?php echo ${tubepress_api_const_template_Variable::EMBEDDED_AUTOSTART}; ?>&amp;backcolor=<?php echo ${tubepress_addons_jwplayer_api_const_template_Variable::COLOR_BACK}; ?>&amp;frontcolor=<?php echo ${tubepress_addons_jwplayer_api_const_template_Variable::COLOR_FRONT}; ?>&amp;lightcolor=<?php echo ${tubepress_addons_jwplayer_api_const_template_Variable::COLOR_LIGHT}; ?>&amp;screencolor=<?php echo ${tubepress_addons_jwplayer_api_const_template_Variable::COLOR_SCREEN}; ?>'>

<embed	type='application/x-shockwave-flash'
        src='<?php echo ${tubepress_api_const_template_Variable::TUBEPRESS_BASE_URL}; ?>/src/main/web/flash/longtail/player.swf'
		width='<?php echo ${tubepress_api_const_template_Variable::EMBEDDED_WIDTH}; ?>'
		height='<?php echo ${tubepress_api_const_template_Variable::EMBEDDED_HEIGHT}; ?>'
		bgcolor='undefined'
        allowscriptaccess='always'
        allowfullscreen='true'
        wmode='transparent'
        flashvars='file=<?php echo ${tubepress_api_const_template_Variable::EMBEDDED_DATA_URL}; ?>&amp;autostart=<?php echo ${tubepress_api_const_template_Variable::EMBEDDED_AUTOSTART}; ?>&amp;backcolor=<?php echo ${tubepress_addons_jwplayer_api_const_template_Variable::COLOR_BACK}; ?>&amp;frontcolor=<?php echo ${tubepress_addons_jwplayer_api_const_template_Variable::COLOR_FRONT}; ?>&amp;lightcolor=<?php echo ${tubepress_addons_jwplayer_api_const_template_Variable::COLOR_LIGHT}; ?>&amp;screencolor=<?php echo ${tubepress_addons_jwplayer_api_const_template_Variable::COLOR_SCREEN}; ?>'
</embed>
</object>
</div>