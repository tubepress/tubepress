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
 * 
 * Uber simple/fast template for TubePress. Idea from here: http://seanhess.net/posts/simple_templating_system_in_php
 * Sure, maybe your templating system of choice looks prettier but I'll bet it's not faster :)
 */
?>
<form accept-charset="utf-8" method="get" action="<?php echo ${org_tubepress_api_const_template_Variable::SEARCH_HANDLER_URL}; ?>">
	<fieldset class="tubepress_search">
		<?php 
		/* 
         * read http://stackoverflow.com/questions/1116019/submitting-a-get-form-with-query-string-params-and-hidden-params-disappear
         * if you're curious as to what's going on here
         */
		foreach (${org_tubepress_api_const_template_Variable::SEARCH_HIDDEN_INPUTS} as $name => $value) : ?>
		  <input type="hidden" name="<?php echo $name; ?>" value="<?php echo $value; ?>" />
		<?php endforeach; ?>
<input type="text" id="tubepress_search" name="tubepress_search" class="tubepress_text_input" value="<?php echo htmlspecialchars(${org_tubepress_api_const_template_Variable::SEARCH_TERMS}); ?>"/>
		<button class="tubepress_button" title="Submit Search"><?php echo htmlspecialchars(${org_tubepress_api_const_template_Variable::SEARCH_BUTTON}); ?></button>
	</fieldset>
</form>
