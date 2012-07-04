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
<div id="tubepress_tabs" style="clear: both">

	<ul>

	<?php foreach (${org_tubepress_impl_options_ui_DefaultTabsHandler::TEMPLATE_VAR_TABS} as $tab): ?>
		<li>
			<a href="#<?php echo 'tubepress_' . md5($tab->getTitle()); ?>">
				<span><?php echo $tab->getTitle(); ?></span>
			</a>
		</li>
	<?php endforeach; ?>

	</ul>

	    <?php foreach (${org_tubepress_impl_options_ui_DefaultTabsHandler::TEMPLATE_VAR_TABS} as $tab): ?>

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
