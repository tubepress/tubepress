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
	'org_tubepress_ioc_IocDelegateUtils',
	'org_tubepress_uploads_AdminPageHandler',
    'org_tubepress_env_EnvironmentDetector'));

/**
 * Handles the video uploads admin page
 *
 */
class org_tubepress_uploads_SecurityHandler
{
	const AUTH_ATTEMPT        = 'authAttempt';
	const PASSWORD_PARAM_NAME = 'password';
	const SESSION_VAR_NAME    = 'tubepressSessionId';

	public static function canContinue()
	{
	    if (org_tubepress_env_EnvironmentDetector::isWordPress()) {
	        return self::_canContinueWordPress();
	    }

	    session_start();

		/* user authenticated? continue. */
		if (self::_authenticated()) {
			return true;
		}

		/* anything else in the function is dealing with an unauthenticated user */

		/* make sure the password is set */
		if (!self::_passwordSet()) {

			$contentTemplate = new org_tubepress_template_SimpleTemplate();
			$contentTemplate->setPath(org_tubepress_util_FilesystemUtils::getTubePressBaseInstallationPath() . '/ui/lib/uploads/templates/no_password_set.tpl.php');
			org_tubepress_uploads_AdminPageHandler::printTemplate($contentTemplate);
			return false;
		}

		/* successful auth attempt? */
		if (self::_attemptingAuthentication() && self::_credentialsValid()) {
			self::_setAuthenticated();
			return true;
		}
		
		/* bad/no auth attempt */
		$contentTemplate = new org_tubepress_template_SimpleTemplate();
		$contentTemplate->setPath(org_tubepress_util_FilesystemUtils::getTubePressBaseInstallationPath() . '/ui/lib/uploads/templates/password_prompt.tpl.php');
		$contentTemplate->setVariable(self::PASSWORD_PARAM_NAME, self::PASSWORD_PARAM_NAME);
		$contentTemplate->setVariable(self::AUTH_ATTEMPT, self::_attemptingAuthentication());
		org_tubepress_uploads_AdminPageHandler::printTemplate($contentTemplate);
	}

	private static function _canContinueWordPress()
	{
	    if (current_user_can(9)) {
	        return true;
	    }
	    
        $baseName = basename(realpath(dirname(__FILE__) . '/../../../../'));
        $siteUrl = get_option('siteurl');
        $url =  urlencode("$siteUrl/wp-content/plugins/$baseName");
	    header("Location: $siteUrl/wp-login.php?redirect_to=$url/ui/lib/uploads/index.php");
	    exit;
	}
	
	private static function _credentialsValid()
	{
		$ioc  = org_tubepress_ioc_IocDelegateUtils::getIocContainerInstance();
		$tpom = $ioc->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);
		$pass    = $tpom->get(org_tubepress_options_category_Uploads::ADMIN_PAGE_PASSWORD);
		$correct = $pass === $_POST[self::PASSWORD_PARAM_NAME];
		if (!$correct) {
			sleep(4);
		}
		return $correct;
	}

	private static function _setAuthenticated()
	{
		$_SESSION[self::SESSION_VAR_NAME] = true;
	}

	private static function _attemptingAuthentication()
	{
		return isset($_POST[self::PASSWORD_PARAM_NAME]);
	}

	private static function _authenticated()
	{
		session_regenerate_id(true);
		return isset($_SESSION[self::SESSION_VAR_NAME]) && $_SESSION[self::SESSION_VAR_NAME] === true;
	}

	private static function _passwordSet()
	{
		$ioc  = org_tubepress_ioc_IocDelegateUtils::getIocContainerInstance();
		$tpom = $ioc->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);
		$pass = $tpom->get(org_tubepress_options_category_Uploads::ADMIN_PAGE_PASSWORD);
		return $pass !== '';
	}
}
