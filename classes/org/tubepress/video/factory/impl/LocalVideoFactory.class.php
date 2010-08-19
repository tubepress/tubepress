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
    || require dirname(__FILE__) . '/../../../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_video_factory_VideoFactory',
    'org_tubepress_video_Video',
    'org_tubepress_options_category_Display',
    'org_tubepress_uploads_UploadsUtils',
    'org_tubepress_util_FilesystemUtils',
    'com_googlecode_spyc_Spyc'));

/**
 * Video factory for uploads
 */
class org_tubepress_video_factory_impl_LocalVideoFactory implements org_tubepress_video_factory_VideoFactory
{
    private $_logPrefix;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->_logPrefix = 'Local Video Factory';
    }

    /**
     * Converts raw video feeds to TubePress videos
     *
     * @param org_tubepress_ioc_IocService $ioc        The IOC container
     * @param unknown                      $galleryDir The directory containing the videos
     * @param int                          $limit      The max number of videos to return
     * 
     * @return array an array of TubePress videos generated from the feed
     */
    public function feedToVideoArray(org_tubepress_ioc_IocService $ioc, $galleryDir, $limit)
    {
        /* get the base uploads directory */
        $baseDir = org_tubepress_uploads_UploadsUtils::getBaseVideoDirectory();

        /** get a list of videos in the relative directory */
        $videoNames = org_tubepress_uploads_UploadsUtils::findVideos("$baseDir/$galleryDir", $this->_logPrefix);

        $toReturn = array();
        $index    = 0;

        /* loop over each potential video */
        foreach ($videoNames as $filename) {

            /* get the filename component */
            $basename = basename($filename);

            if ($index > 0 && $index++ >= $limit) {
                org_tubepress_log_Log::log($this->_logPrefix, 'Reached limit of %d videos', $limit);
                break;
            }

            /* add the video to the list */
            $toReturn[] = $this->_createVideo($filename, $baseDir, $galleryDir, $ioc);
        }

        return $toReturn;
    }

    /**
     * Converts a single raw video into a TubePress video
     *
     * @param org_tubepress_ioc_IocService $ioc     The IOC container
     * @param unknown                      $rawFeed The raw feed result from the video provider
     * 
     * @return array an array of TubePress videos generated from the feed
     */
    public function convertSingleVideo(org_tubepress_ioc_IocService $ioc, $rawFeed)
    {

    }

    private function _createVideo($filename, $baseDir, $galleryDir, org_tubepress_ioc_IocService $ioc)
    {
        org_tubepress_log_Log::log($this->_logPrefix, 'Assembling video for <tt>%s</tt>', $filename);

        $video = new org_tubepress_video_Video();

        /* set the attributes that don't require parsing a .yml file */
        $video->setId(md5($filename));
        $video->setThumbnailUrl($this->_getThumbnailUrl($filename, $baseDir, $galleryDir, $ioc));

        /* set the attributes that require parsing a .yml file */
        $yamlArray = $this->_getyamlArray($filename, $baseDir, $galleryDir);
        $video->setTitle($yamlArray['title']);
        $video->setDescription($yamlArray['description']);
        $video->setTimePublished($yamlArray['uploaded']);
        $video->setAuthorDisplayName($yamlArray['author']);

        $keywords = $yamlArray['tags'];
        $tags     = explode(',', $keywords);
        $video->setKeywords($tags);
        return $video;
    }

    private function _getyamlArray($filename, $baseDir, $galleryDir)
    {
        /* calculate the name of the yaml file we're looking for */
        $fileNameWithoutExtension = substr($filename, 0, strlen($filename) - 4);
        org_tubepress_log_Log::log($this->_logPrefix, 'Filename without extension is <tt>%s</tt>', $fileNameWithoutExtension);

        $yamlFile = basename($fileNameWithoutExtension) . '.yml';
        org_tubepress_log_Log::log($this->_logPrefix, 'YML base filename is <tt>%s</tt>', $yamlFile);

        $yamlFile = realpath("$baseDir/$galleryDir") . "/$yamlFile";
        org_tubepress_log_Log::log($this->_logPrefix, 'Absolute path to YML file is <tt>%s</tt>', $yamlFile);

        /* make sure we can read the file */
        if (!is_readable($yamlFile)) {
            org_tubepress_log_Log::log($this->_logPrefix, 'YML file at <tt>%s</tt> does not exist or is not readable.', $yamlFile);
            return array();
        }

        /* load up the file contents */
        org_tubepress_log_Log::log($this->_logPrefix, 'Loading YML file at <tt>%s</tt>.', $yamlFile);
        $contents = file_get_contents($yamlFile);
        org_tubepress_log_Log::log($this->_logPrefix, 'YML file at <tt>%s</tt> has the following contents: <tt>%s</tt>', $yamlFile, $contents);

        /* load the contents into spyc */
        $result = com_googlecode_spyc_Spyc::YAMLLoadString($contents);
        org_tubepress_log_Log::log($this->_logPrefix, 'YML file at <tt>%s</tt> was parsed to <pre>%s</pre>.', $yamlFile, var_export($result, true));

        return $result;
    }

    private function _getThumbnailUrl($filename, $baseDir, $galleryDir, org_tubepress_ioc_IocService $ioc)
    {
        global $tubepress_base_url;

        $thumbs = org_tubepress_uploads_UploadsUtils::getExistingThumbnails($filename, $galleryDir, $ioc, $this->_logPrefix);

        if (sizeof($thumbs) === 0) {
            org_tubepress_log_Log::log($this->_logPrefix, 'No potential thumbs for <tt>%s</tt>. Using filler thumbnail.', $filename);
            return "$tubepress_base_url/ui/lib/gallery_html_snippets/missing_thumbnail.png";
        }

        $prefix = "$tubepress_base_url/uploads/$galleryName/generated_thumbnails/";

        if ($tpom->get(org_tubepress_options_category_Display::RANDOM_THUMBS)) {
            org_tubepress_log_Log::log($this->_logPrefix, 'Using a random thumbnail for <tt>%s</tt>.', $filename);
            return $prefix . $thumbs[array_rand($thumbs)];
        }

        return $prefix . $thumbs[0];
    }
}
