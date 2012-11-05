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
<div class="tubepress-tab">

	<?php foreach (${tubepress_impl_options_ui_tabs_AbstractPluggableOptionsPageTab::TEMPLATE_VAR_PARTICIPANT_ARRAY} as $participant): ?>

    <div<?php if ($participant->getName() !== 'core'): ?> class="ui-corner-all ui-widget-content tubepress-participant tubepress-participant-<?php echo $participant->getName(); ?>"<?php endif; ?>>

        <?php if ($participant->getName() !== 'core'): ?>

        <div class="ui-widget ui-widget-header tubepress-participant-header">

            <span><?php echo $participant->getFriendlyName(); ?></span>
        </div>

        <?php endif; ?>

    <table>
    <?php foreach ($participant->getFieldsForTab(${tubepress_impl_options_ui_tabs_AbstractPluggableOptionsPageTab::TEMPLATE_VAR_TAB_NAME}) as $field): ?>

    <tr>

		<th class="tubepress-field-header"><?php if ($field->isProOnly()): ?><a href="http://tubepress.org/pro"><img src="<?php echo ${tubepress_api_const_template_Variable::TUBEPRESS_BASE_URL}; ?>/src/main/web/images/pro_tag.png" alt="TubePress Pro only" /></a><?php endif; ?><span><?php echo $field->getTitle(); ?></span></th>

		<td>
		    <?php echo $field->getHtml(); ?>
			<br />
			<?php echo $field->getDescription(); ?>
		</td>
	</tr>
<?php endforeach; ?>
    </table></div>
<?php endforeach; ?>
</div>