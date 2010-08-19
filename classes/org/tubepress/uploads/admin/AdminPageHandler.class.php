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
    'org_tubepress_album_DelegatingAlbumProvider',
    'org_tubepress_uploads_admin_model_AdminAlbum'));

/**
 * Handles the video uploads admin page
 *
 */
class org_tubepress_uploads_admin_AdminPageHandler
{
    const LOG_PREFIX = 'Admin page handler';
    
    const ADMIN_ALBUM_ARRAY = 'adminAlbumArray';
    const PATH_TO_VIDEO_UPLOADS = 'pathToVideoUploads';

    private static $_ioc;
    private static $_tubepressBaseInstallDir;

	public function handle()
	{
		if (!org_tubepress_uploads_admin_SecurityHandler::canContinue()) {
		    org_tubepress_log_Log::log(self::LOG_PREFIX, 'Denying access');
			return;
		}
		
		self::_init();

		if (isset($_GET['video'])) {
		    self::_handleSingleVideoRequest($_GET['video']);
		    return;
		}
		
		$albums = self::_getAdminAlbums(self::$_ioc);
		
		$template = new org_tubepress_template_SimpleTemplate();
		$template->setPath(self::$_tubepressBaseInstallDir . '/ui/lib/uploads/templates/main.tpl.php');
		$template->setVariable(self::PATH_TO_VIDEO_UPLOADS, org_tubepress_uploads_UploadsUtils::getBaseVideoDirectory());
		$template->setVariable(self::ADMIN_ALBUM_ARRAY, $albums);
		self::printTemplate($template);
	}

	private static function _handleSingleVideoRequest($base64EncodedRelativeVideoPath)
	{
		$baseVideoDirectory = org_tubepress_uploads_UploadsUtils::getBaseVideoDirectory();
		$relativePath = base64_decode($base64EncodedRelativeVideoPath);
		$absPath = "$baseVideoDirectory/$relativePath";
		
		$template = new org_tubepress_template_SimpleTemplate();
		$template->setPath(self::$_tubepressBaseInstallDir . '/ui/lib/uploads/templates/single_video.tpl.php');
		$template->setVariable('test', $absPath);
		print $template->toString();
	}

    private static function _getAdminAlbums(org_tubepress_ioc_IocService $ioc)
    {
        return self::_findAlbumsRecursive(org_tubepress_uploads_UploadsUtils::getBaseVideoDirectory(), $ioc, 0);
    }
    
    private static function _findAlbumsRecursive($dir, org_tubepress_ioc_IocService $ioc, $index)
    {
        /* pressure release valve, just in case! */
        if ($index++ > 200) {
            return array();
        }
        
        $albums = array();
        
        /* don't search for videos in the base directory */
        if ($index !== 1) {
            $baseVideoDirectory = org_tubepress_uploads_UploadsUtils::getBaseVideoDirectory();
            $albumName = str_replace($baseVideoDirectory. '/', '', $dir);
            
            $album = new org_tubepress_uploads_admin_model_AdminAlbum();
            $album->setRelativeContainerPath($albumName);
            $absPathsToVideos = org_tubepress_uploads_UploadsUtils::findVideos($dir, self::LOG_PREFIX);
            $relativePathsToVideos = array();
            foreach ($absPathsToVideos as $absPath) {
                $relativePathsToVideos[] = str_replace($baseVideoDirectory, '', $absPath);
            }
            $album->setRelativeVideoPaths($relativePathsToVideos);
            $albums[] = $album;
        }
        
        $dirsInDir = org_tubepress_util_FilesystemUtils::getDirectoriesInDirectory($dir, self::LOG_PREFIX);
        org_tubepress_log_Log::log(self::LOG_PREFIX, 'Found %d directories in <tt>%s</tt>', sizeof($dirsInDir), $dir);
        
        foreach ($dirsInDir as $subDir) {
            $albums = array_merge($albums, self::_findAlbumsRecursive($subDir, $ioc, $index));
        }
        
        return $albums;
    }
	
	private static function _init()
	{
	    self::$_ioc = org_tubepress_ioc_IocDelegateUtils::getIocContainerInstance();
        $tpom = self::$_ioc->get('org_tubepress_options_manager_OptionsManager');
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
