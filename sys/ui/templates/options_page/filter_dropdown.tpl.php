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
<div style="width: 30%; float: right; background-color: #FFFFFF; padding: .5em 1em .5em 1em" class="ui-corner-all">
	
    <p style="float: left"><?php echo ${org_tubepress_api_const_template_Variable::OPTIONS_PAGE_OPTIONS_FILTER}; ?></p>
        	
    <div style="float: right; vertical-align: middle; padding: 6px">
    	
    	<input type="checkbox" id="youtube-checkbox" /><img src="<?php echo ${org_tubepress_api_const_template_Variable::TUBEPRESS_BASE_URL}; ?>/sys/ui/static/images/youtube.png" style="margin: 0 1em -5px 3px" alt="YouTube"/>
    	<input type="checkbox" id="vimeo-checkbox" /><img src="<?php echo ${org_tubepress_api_const_template_Variable::TUBEPRESS_BASE_URL}; ?>/sys/ui/static/images/vimeo.png" style="margin: 0 0 -8px 3px"/ alt="Vimeo">
	</div>
</div>