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
<select name="<?php echo ${tubepress_impl_options_ui_fields_AbstractOptionDescriptorBasedField::TEMPLATE_VAR_NAME}; ?>">

	<?php foreach (${tubepress_impl_options_ui_fields_DropdownField::TEMPLATE_VAR_ACCEPTABLE_VALUES} as $name => $value): ?>

	<option value="<?php echo $name; ?>" <?php if (${tubepress_impl_options_ui_fields_AbstractOptionDescriptorBasedField::TEMPLATE_VAR_VALUE} === $name) { echo 'SELECTED'; } ?>><?php echo $value; ?></option>

<?php endforeach; ?>

</select>