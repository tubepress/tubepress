<?php
/**
 * Copyright 2006 - 2010 Eric D. Hough (http://ehough.com)
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

function_exists('tubepress_load_classes')
    || require dirname(__FILE__) . '/../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_template_Template',
	'org_tubepress_template_SimpleTemplate',
	'org_tubepress_util_FilesystemUtils',
	'org_tubepress_ioc_IocDelegateUtils',
	'org_tubepress_uploads_SecurityHandler'));

/**
 * Handles the video uploads admin page
 *
 */
class org_tubepress_uploads_AdminPageHandler
{
	private $_baseDirectory;
	private $_ioc;

	function __construct()
	{
		$this->_baseDirectory = org_tubepress_util_FilesystemUtils::getTubePressBaseInstallationPath() . '/content/uploads';
		$this->_ioc = org_tubepress_ioc_IocDelegateUtils::getIocContainerInstance();
	}

	public function handle()
	{
		if (!org_tubepress_uploads_SecurityHandler::canContinue()) {
			return;
		}
		$template = new org_tubepress_template_SimpleTemplate();
		$template->setPath(org_tubepress_util_FilesystemUtils::getTubePressBaseInstallationPath() . '/ui/lib/uploads/templates/main.tpl.php');
		self::printTemplate($template);
	}

	public static function printTemplate($template)
	{
		$headerTemplate = new org_tubepress_template_SimpleTemplate();
		$headerTemplate->setPath(org_tubepress_util_FilesystemUtils::getTubePressBaseInstallationPath() . '/ui/lib/uploads/templates/header.tpl.php');
		$header = $headerTemplate->toString();
		
		$footerTemplate = new org_tubepress_template_SimpleTemplate();
		$footerTemplate->setPath(org_tubepress_util_FilesystemUtils::getTubePressBaseInstallationPath() . '/ui/lib/uploads/templates/footer.tpl.php');
		$footer = $footerTemplate->toString();

		print $header . $template->toString() . $footer;
	}
}
