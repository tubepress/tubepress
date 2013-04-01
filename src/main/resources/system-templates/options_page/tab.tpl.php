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
<div class="tubepress-tab">

	<?php foreach (${tubepress_impl_options_ui_tabs_AbstractPluggableOptionsPageTab::TEMPLATE_VAR_PARTICIPANT_ARRAY} as $participant): ?>

    <?php if ($participant->getName() !== 'core'): ?>

    <div class="ui-corner-all ui-widget-content tubepress-participant tubepress-participant-<?php echo $participant->getName(); ?>">

        <div class="ui-widget ui-widget-header tubepress-participant-header">

            <span><?php echo $participant->getFriendlyName(); ?></span>
        </div>

    <?php endif; ?>

    <table>
    <?php foreach ($participant->getFieldsForTab(${tubepress_impl_options_ui_tabs_AbstractPluggableOptionsPageTab::TEMPLATE_VAR_TAB_NAME}) as $field): ?>

    <tr>

        <?php if ($field->isProOnly()): ?>
        <td class="tubepress-pro-banner"><a href="http://tubepress.org/pro"><img src="<?php echo ${tubepress_api_const_template_Variable::TUBEPRESS_BASE_URL}; ?>/src/main/web/images/pro_tag.png" alt="TubePress Pro only" /></a></td>
        <?php else: ?>
        <td style="margin: 0; padding: 0"></td>
        <?php endif; ?>

		<th class="tubepress-field-header<?php if ($field->isProOnly()): ?> tubepress-pro-field-header<?php endif; ?>"><span><?php echo $field->getTitle(); ?></span></th>

		<td>
		    <?php echo $field->getHtml(); ?>
			<br />
			<?php echo $field->getDescription(); ?>
		</td>
	</tr>
<?php endforeach; ?>
    </table>

    <?php if ($participant->getName() !== 'core'): ?></div><?php endif; ?>
<?php endforeach; ?>
</div>