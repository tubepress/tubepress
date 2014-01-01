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
