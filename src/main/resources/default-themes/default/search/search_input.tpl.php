<?php 
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 * 
 * This file is part of TubePress (http://tubepress.org)
 * 
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 * 
 * Uber simple/fast template for TubePress. Idea from here: http://seanhess.net/posts/simple_templating_system_in_php
 * Sure, maybe your templating system of choice looks prettier but I'll bet it's not faster :)
 */
?>
<form accept-charset="utf-8" method="get" action="<?php echo ${tubepress_api_const_template_Variable::SEARCH_HANDLER_URL}; ?>">
	<fieldset class="tubepress_search">
		<?php 
		/* 
         * read http://stackoverflow.com/questions/1116019/submitting-a-get-form-with-query-string-params-and-hidden-params-disappear
         * if you're curious as to what's going on here
         */
		foreach (${tubepress_api_const_template_Variable::SEARCH_HIDDEN_INPUTS} as $name => $value) : ?>
		  <input type="hidden" name="<?php echo $name; ?>" value="<?php echo $value; ?>" />
		<?php endforeach; ?>
<input type="text" id="tubepress_search" name="tubepress_search" class="tubepress_text_input" value="<?php echo htmlspecialchars(${tubepress_api_const_template_Variable::SEARCH_TERMS}); ?>"/>
		<button class="tubepress_button" title="Submit Search"><?php echo htmlspecialchars(${tubepress_api_const_template_Variable::SEARCH_BUTTON}); ?></button>
	</fieldset>
</form>
