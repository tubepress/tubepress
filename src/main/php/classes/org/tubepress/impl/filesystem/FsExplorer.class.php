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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_filesystem_Explorer',
    'org_tubepress_impl_log_Log',
));

/**
 * Some filesystem utilities
 *
 */
class org_tubepress_impl_filesystem_FsExplorer implements org_tubepress_api_filesystem_Explorer
{
    const LOG_PREFIX       = 'FS Explorer';
    
	/**
	 * Finds the absolute path of the TubePress installation on the filesystem.
	 *
	 * @return string The absolute filesystem path of this TubePress installation.
     */
    public function getTubePressBaseInstallationPath()
    {
        return realpath(dirname(__FILE__) . '/../../../../../../');
    }
    
    /**
     * Find the directory name of the TubePress base installation.
     *
     * @return string The base name of the TubePress installation directory.
     */
    function getTubePressInstallationDirectoryBaseName()
    {
    	return basename($this->getTubePressBaseInstallationPath());
    }

    /**
     * Find the directories contained in the given directory (non-recursive).
     *
     * @param string $dir    The absolute filesystem path of the directory to examine.
     * @param string $prefix The logging prefix.
     *
     * @return array The names of the directories in the given directory (non-recursive).
     */
    public function getDirectoriesInDirectory($dir, $prefix)
    {
        $realDir = $dir;

        if (!is_dir($dir)) {
        	
            org_tubepress_impl_log_Log::log($prefix, '<tt>%s</tt> is not a directory', $realDir);
            
            return array();
        }

        $toReturn = array();
        if ($handle = opendir($dir)) {
            
            while (($file = readdir($handle)) !== false) {

                if ($file === '.' || $file === '..' || strpos($file, ".") === 0) {

                	continue;
                }

                if (!is_dir($dir . DIRECTORY_SEPARATOR . $file)) {

                	continue;
                }

                array_push($toReturn, realpath($dir . DIRECTORY_SEPARATOR . $file));
            }
            
            closedir($handle);
        
        } else {

        	org_tubepress_impl_log_Log::log($prefix, 'Could not open <tt>%s</tt>', $realDir);
        }
        
        return $toReturn;
    }

    /**
     * Find the files contained in the given directory (non-recursive).
     *
     * @param string $dir    The absolute filesystem path of the directory to examine.
     * @param string $prefix The logging prefix.
     *
     * @return array The names of the files in the given directory (non-recursive).
     */
    public function getFilenamesInDirectory($dir, $prefix)
    {
        $realDir = $dir;

        if (!is_dir($dir)) {
            org_tubepress_impl_log_Log::log($prefix, '<tt>%s</tt> is not a directory', $realDir);
            return array();
        }

        $toReturn = array();
        if ($handle = opendir($dir)) {

        	while (($file = readdir($handle)) !== false) {

                if ($file === '.' || $file === '..') {
                    continue;
                }
                if (is_dir($dir . DIRECTORY_SEPARATOR . $file)) {
                    continue;
                }

                array_push($toReturn, realpath($dir . DIRECTORY_SEPARATOR . $file));
            }
            closedir($handle);
        } else {
            org_tubepress_impl_log_Log::log($prefix, 'Could not open <tt>%s</tt>', $realDir);
        }
        return $toReturn;
    }

    /**
     * Attempt to get temporary directory.
     *
     * @return string The absolute path of a temporary directory, preferably the system directory.
     */
    public function getSystemTempDirectory()
    {
        if (function_exists('sys_get_temp_dir')) {
            return sys_get_temp_dir();
        }

        // Try to get from environment variable
        if (!empty($_ENV['TMP'])) {
            return realpath($_ENV['TMP']);
        } else if (!empty($_ENV['TMPDIR'])) {
            return realpath($_ENV['TMPDIR']);
        } else if (!empty($_ENV['TEMP'])) {
            return realpath($_ENV['TEMP']);
        } else {
            // Detect by creating a temporary file
            // Try to use system's temporary directory
            // as random name shouldn't exist
            $tempfile = @tempnam(md5(uniqid(rand(), true)), '');
            if ( $tempfile ) {
                $tempdir = realpath(dirname($tempfile));
                @unlink($tempfile);
                return $tempdir;
            } else {
                return false;
            }
        }
    }

    public function copyDirectory($source, $dest, $level = 0)
    {
    	$source = self::_cleanPath($source);
    	$dest   = self::_cleanPath($dest);
    	
    	org_tubepress_impl_log_Log::log(self::LOG_PREFIX, '%sAsked to copy %s to %s', self::_spaces($level), $source, $dest);
    	
    	if (!is_dir($source)) {
    		
    		org_tubepress_impl_log_Log::log(self::LOG_PREFIX, '%s%s is not a directory', self::_spaces($level), $source);
    		
    		return false;
    	}
    	
    	if (!is_readable($source)) {
    		
    		org_tubepress_impl_log_Log::log(self::LOG_PREFIX, '%sCannot read source directory at ', self::_spaces($level), $source);
    		
    		return false;
    	}
    	
    	if (!is_dir($dest)) {
    		
    		org_tubepress_impl_log_Log::log(self::LOG_PREFIX, '%s%s is not a directory', self::_spaces($level), $dest);
    	
    		return false;
    	}
    	 
    	if (!is_readable($dest)) {
    		
    		org_tubepress_impl_log_Log::log(self::LOG_PREFIX, '%sCannot write to destination directory at ', self::_spaces($level), $dest);
    	
    		return false;
    	}
    	
    	org_tubepress_impl_log_Log::log(self::LOG_PREFIX, '%sCopying %s to %s', self::_spaces($level), $source, $dest);
    	
    	return $this->_doCopyDirectory($source, $dest, $level);
    }
    
    private function _doCopyDirectory($source, $dest, $level)
    {
    	$files = $this->getFilenamesInDirectory($source, self::LOG_PREFIX);
    	org_tubepress_impl_log_Log::log(self::LOG_PREFIX, '%sWill try to copy %d file(s) from %s to %s', self::_spaces($level), count($files), $source, $dest);
    	
    	$dirs = $this->getDirectoriesInDirectory($source, self::LOG_PREFIX);
    	org_tubepress_impl_log_Log::log(self::LOG_PREFIX, '%sWill try to copy %d directories from %s to %s', self::_spaces($level), count($dirs), $source, $dest);
    	
    	$finalDest = $dest . DIRECTORY_SEPARATOR . basename($source);
    	
    	if ($this->ensureDirectoryExists($finalDest, $level) === false) {
    		
    		return false;
    	}

    	foreach ($files as $file) {
    		
    		$finalFileDest = $finalDest . DIRECTORY_SEPARATOR . basename($file);
    		$result        = @copy($file, $finalFileDest);
    		
    		if ($result === false) {
    			
    			org_tubepress_impl_log_Log::log(self::LOG_PREFIX, '%sCould not copy %s to %s', self::_spaces($level), $file, $finalFileDest);
    			return false;
    		}
    		
    		org_tubepress_impl_log_Log::log(self::LOG_PREFIX, '%sSuccessfully copied %s to %s', self::_spaces($level), $file, $finalFileDest);
    	}
    	
    	foreach ($dirs as $dir) {
    		
    		$finalDirDest = self::_cleanPath($finalDest . DIRECTORY_SEPARATOR . basename($dir));
    		
    		if ($this->ensureDirectoryExists($finalDest, $level) === false) {
    			
    			return false;
    		}
    		
    		$result = $this->copyDirectory($dir, $finalDest, $level + 1);
    		
    		if ($result === false) {
    			 
    			org_tubepress_impl_log_Log::log(self::LOG_PREFIX, '%sCould not copy %s to %s', self::_spaces($level), $dir, $finalDirDest);
    			return false;
    		}
    	}
    	
    	org_tubepress_impl_log_Log::log(self::LOG_PREFIX, '%sDone copying %s to %s', self::_spaces($level), $source, $dest);
    	
    	return true;
    }
    
    public function ensureDirectoryExists($path, $level = 0)
    {
    	$path = self::_cleanPath($path);
    	
    	if (!is_dir($path)) {
    	
    		org_tubepress_impl_log_Log::log(self::LOG_PREFIX, '%sAttempting to create %s', self::_spaces($level), $path);
    		
    		$result = @mkdir($path);
    	
    		if ($result === false) {
    			 
    			org_tubepress_impl_log_Log::log(self::LOG_PREFIX, '%sCould not create directory at %s', self::_spaces($level), $path);
    			return false;
    		}
    		
    		org_tubepress_impl_log_Log::log(self::LOG_PREFIX, '%sSuccessfully created directory at %s', self::_spaces($level), $path);
    		
    		return true;
    	}
    }
    
    private static function _cleanPath($path)
    {
    	return str_replace('//', '/', str_replace('\\', '/', $path));
    }
    
    private static function _spaces($level)
    {
    	return substr('                         ', 0, 3 * $level);
    }
}