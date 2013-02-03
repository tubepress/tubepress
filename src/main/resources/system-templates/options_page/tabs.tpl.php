<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
?>
<div id="tubepress_tabs" style="clear: both">

	<ul>

	<?php foreach (${tubepress_impl_options_ui_DefaultTabsHandler::TEMPLATE_VAR_TABS} as $tab): ?>
		<li>
			<a href="#<?php echo 'tubepress_' . md5($tab->getTitle()); ?>">
				<span><?php echo $tab->getTitle(); ?></span>
			</a>
		</li>
	<?php endforeach; ?>

	</ul>

	    <?php foreach (${tubepress_impl_options_ui_DefaultTabsHandler::TEMPLATE_VAR_TABS} as $tab): ?>

		<div id="<?php echo 'tubepress_' . md5($tab->getTitle()); ?>">

		   	<?php echo $tab->getHtml(); ?>
		</div>

        <?php endforeach; ?>

</div>

<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery("#tubepress_tabs").tabs();
	});
</script>
