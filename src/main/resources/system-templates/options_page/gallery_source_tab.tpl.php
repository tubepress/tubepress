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

    <?php if (!($field instanceof tubepress_impl_options_ui_fields_GallerySourceField)) continue; $name = $field->getGallerySourceName(); ?>



        <tr class="tubepress-participant tubepress-participant-<?php echo $participant->getName(); ?>">

            <th><label for="<?php echo $name; ?>"><?php echo $field->getTitle(); ?></label></th>

            <td>
                <input type="radio" name="mode" id="<?php echo $name; ?>" value="<?php echo $name; ?>" <?php if (${tubepress_impl_options_ui_tabs_GallerySourceTab::TEMPLATE_VAR_CURRENT_MODE} === $name) { echo 'CHECKED'; } ?>>&nbsp;
                <?php echo $field->getHtml(); ?>
                <br />
                <?php echo $field->getDescription(); ?>
            </td>
	    </tr>


<?php endforeach; ?>
    </table><?php if ($participant->getName() !== 'core'): ?></div><?php endif; ?>
<?php endforeach; ?>
</div>