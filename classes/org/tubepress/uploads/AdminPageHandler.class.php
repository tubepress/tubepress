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
	'org_tubepress_log_Log',
	'org_tubepress_ioc_IocDelegateUtils'));

/**
 * Handles the video uploads admin page
 *
 */
class org_tubepress_uploads_AdminPageHandler
{
	private $_baseDirectory;
	private $_logPrefix;
	private $_ioc;

	function __construct()
	{
		$this->_baseDirectory = org_tubepress_util_FilesystemUtils::getTubePressBaseInstallationPath() . '/ui/lib/uploads';
		$this->_ioc = org_tubepress_ioc_IocDelegateUtils::getIocContainerInstance();
		$this->_logPrefix = 'Uploads Admin Handler';
	}

	public function handle()
	{
		if (!$this->_passwordSet()) {
			$contentTemplate = new org_tubepress_template_SimpleTemplate();
			$contentTemplate->setPath($this->_baseDirectory . '/templates/no_password_set.tpl.php');
			$this->_printTemplate($contentTemplate);
			return;
		}
	}

	private function _passwordSet()
	{
		$tpom = $this->_ioc->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);
		$pass = $tpom->get(org_tubepress_options_category_Uploads::ADMIN_PAGE_PASSWORD);
		return $pass !== '';
	}

	private function _printTemplate($template)
	{
		$headerTemplate = new org_tubepress_template_SimpleTemplate();
		$headerTemplate->setPath($this->_baseDirectory . '/templates/header.tpl.php');
		$header = $headerTemplate->toString();
		
		$footerTemplate = new org_tubepress_template_SimpleTemplate();
		$footerTemplate->setPath($this->_baseDirectory . '/templates/footer.tpl.php');
		$footer = $footerTemplate->toString();

		print $header . $template->toString() . $footer;
	}
}
