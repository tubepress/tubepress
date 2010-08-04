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
    || require dirname(__FILE__) . '/../../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_template_Template',
	'org_tubepress_template_SimpleTemplate',
	'org_tubepress_util_FilesystemUtils',
	'org_tubepress_ioc_IocDelegateUtils',
	'org_tubepress_uploads_admin_SecurityHandler',
    'org_tubepress_album_DelegatingAlbumProvider'));

/**
 * Handles the video uploads admin page
 *
 */
class org_tubepress_uploads_admin_AdminPageHandler
{
    const LOG_PREFIX = 'Admin page handler';

    private static $_ioc;
    private static $_tubepressBaseInstallDir;

	public function handle()
	{
		if (!org_tubepress_uploads_admin_SecurityHandler::canContinue()) {
		    org_tubepress_log_Log::log(self::LOG_PREFIX, 'Denying access');
			return;
		}
		
		self::_init();

		$albums = org_tubepress_album_DelegatingAlbumProvider::getAlbums(self::$_ioc);
		
		$template = new org_tubepress_template_SimpleTemplate();
		$template->setPath(self::$_tubepressBaseInstallDir . '/ui/lib/uploads/templates/main.tpl.php');
		$template->setVariable(org_tubepress_template_Template::ALBUM_ARRAY, $albums);
		self::printTemplate($template);
	}
	
	private static function _init()
	{
	    self::$_ioc = org_tubepress_ioc_IocDelegateUtils::getIocContainerInstance();
        $tpom = self::$_ioc->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);
        org_tubepress_log_Log::setEnabled($tpom->get(org_tubepress_options_category_Advanced::DEBUG_ON), $_GET);
        
        self::$_tubepressBaseInstallDir = org_tubepress_util_FilesystemUtils::getTubePressBaseInstallationPath();
	}
	
	public static function printTemplate($template)
	{
		$headerTemplate = new org_tubepress_template_SimpleTemplate();
		$headerTemplate->setPath(self::$_tubepressBaseInstallDir . '/ui/lib/uploads/templates/header.tpl.php');
		$header = $headerTemplate->toString();
		
		$footerTemplate = new org_tubepress_template_SimpleTemplate();
		$footerTemplate->setPath(self::$_tubepressBaseInstallDir . '/ui/lib/uploads/templates/footer.tpl.php');
		$footer = $footerTemplate->toString();

		print $header . $template->toString() . $footer;
	}
}
