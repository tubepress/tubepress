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

/* keep this to do security checks */
require dirname(__FILE__) . "/../../../../../../wp-blog-header.php";

/* make sure they're allowed to do this at all */
if (!current_user_can(9)) {
		echo "NOT AUTHORIZED";
		exit();
}

if (isset($_POST["tubepress_init_db"])) {
    $ioc = new org_tubepress_impl_ioc_FreeWordPressPluginIocService();
	$wpsm = $ioc->get('org_tubepress_api_exec_ExecutionContext');
	$wpsm->nuclear();
	echo "TubePress options initialized<br /><br />";
}
?>

This will clear out your TubePress options and reset them to default values. Your 
other WordPress options will be safe.<br /><br />

<form method="post">
	<input type="hidden" name="tubepress_init_db" />
	<input type="submit" name="tubepress_nuke_button" value="Reset TubePress Options" />
</form>

