<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Simple implementation of an add-on or theme.
 */
class tubepress_impl_contrib_ContributableBase implements tubepress_spi_contrib_ContributableInterface
{
    /**
     * Required attributes.
     */
    const ATTRIBUTE_NAME     = 'name';
    const ATTRIBUTE_VERSION  = 'version';
    const ATTRIBUTE_TITLE    = 'title';
    const ATTRIBUTE_AUTHOR   = 'author';
    const ATTRIBUTE_LICENSES = 'licenses';

    /**
     * Optional attributes.
     */
    const ATTRIBUTE_DESCRIPTION       = 'description';
    const ATTRIBUTE_KEYWORDS          = 'keywords';
    const ATTRIBUTE_SCREENSHOTS       = 'screenshots';
    const ATTRIBUTE_URL_HOMEPAGE      = 'homepage';
    const ATTRIBUTE_URL_DOCUMENTATION = 'docs';
    const ATTRIBUTE_URL_DEMO          = 'demo';
    const ATTRIBUTE_URL_DOWNLOAD      = 'download';
    const ATTRIBUTE_URL_BUGS          = 'bugs';

    const CATEGORY_URLS      = 'urls';

    /**
     * @var string
     */
    private $_name;

    /**
     * @var tubepress_spi_version_Version
     */
    private $_version;

    /**
     * @var string
     */
    private $_title;

    /**
     * @var array
     */
    private $_author;

    /**
     * @var array
     */
    private $_licenses;

    /**
     * @var string
     */
    private $_description;

    /**
     * @var array
     */
    private $_keywords = array();

    /**
     * @var ehough_curly_Url
     */
    private $_urlHomepage;

    /**
     * @var ehough_curly_Url
     */
    private $_urlDocs;

    /**
     * @var ehough_curly_Url
     */
    private $_urlDemo;

    /**
     * @var ehough_curly_Url
     */
    private $_urlDownload;

    /**
     * @var ehough_curly_Url
     */
    private $_urlBugs;

    /**
     * @var string[]
     */
    private $_screenshots = array();

    public function __construct(

        $name,
        $version,
        $title,
        array $author,
        array $licenses) {

        $this->_setName($name);
        $this->_setVersion($version);
        $this->_setTitle($title);
        $this->_setAuthor($author);
        $this->_setLicenses($licenses);
    }

    public function setDescription($description)
    {
        if (!is_string($description)) {

            throw new InvalidArgumentException('Description must be a string');
        }

        if (strlen($description) > 1000) {

            throw new InvalidArgumentException('Description must be 1000 characters or less.');
        }

        $this->_description = $description;
    }

    public function setKeywords(array $keywords)
    {
        foreach ($keywords as $keyword) {

            if (!is_string($keyword)) {

                throw new InvalidArgumentException('Keywords must be strings');
            }

            if (preg_match('~^[A-Za-z0-9-\.]{1,30}$~', $keyword, $matches) !== 1) {

                throw new InvalidArgumentException("Invalid keyword: $keyword");
            }
        }

        $this->_keywords = $keywords;
    }

    public function setHomepageUrl($url)
    {
        $this->_validateUrl($url, 'Invalid homepage URL');

        $this->_urlHomepage = $url;
    }

    public function setDocumentationUrl($url)
    {
        $this->_validateUrl($url, 'Invalid documentation URL');

        $this->_urlDocs = $url;
    }

    public function setDemoUrl($url)
    {
        $this->_validateUrl($url, 'Invalid demo URL');

        $this->_urlDemo = $url;
    }

    public function setDownloadUrl($url)
    {
        $this->_validateUrl($url, 'Invalid download URL');

        $this->_urlDownload = $url;
    }

    public function setBugTrackerUrl($url)
    {
        $this->_validateUrl($url, 'Invalid bug tracker URL');

        $this->_urlBugs = $url;
    }

    public function setScreenshots(array $screenshots) {

        $toSet = array();

        if (tubepress_impl_util_LangUtils::isAssociativeArray($screenshots)) {

            foreach ($screenshots as $thumbnailUrl => $fullsizeUrl) {

                $this->_validateScreenshotUrl($thumbnailUrl);
                $this->_validateScreenshotUrl($fullsizeUrl);

                $toSet[$thumbnailUrl] = $fullsizeUrl;
            }

        } else {

            foreach ($screenshots as $url) {

                $this->_validateScreenshotUrl($url);

                $toSet[$url] = $url;
            }
        }

        $this->_screenshots = $toSet;
    }

    /**
     * @return string The globally unique name of this add-on. Must be 100 characters or less,
     *                all lowercase, and contain only URL-safe characters ([a-z0-9-_\.]+).
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @return tubepress_spi_version_Version The version of this add-on.
     */
    public function getVersion()
    {
        return $this->_version;
    }

    /**
     * @return string A user-friendly title for the add-on. 255 characters or less.
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * @return array An associative array of author information. The possible array keys are
     *               'name', 'email', and 'url'. 'name' is required, and the other fields are optional.
     */
    public function getAuthor()
    {
        return $this->_author;
    }

    /**
     * @return array An array of associative arrays of license information. The possible array keys are
     *               'url' and 'type'. 'url' is required and must link to the license text. 'type'
     *               may be supplied if the license is one of the official open source licenses found
     *               at http://www.opensource.org/licenses/alphabetical
     */
    public function getLicenses()
    {
        return $this->_licenses;
    }

    /**
     * @return string Optional. A longer description of this add-on. 1000 characters or less.
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * @return array Optional. An array of keywords that might help folks discover this add-on. Only
     *               letters, numbers, hypens, and dots. Each keyword must be 30 characters or less.
     */
    public function getKeywords()
    {
        return $this->_keywords;
    }

    /**
     * @return ehough_curly_Url Optional. A link to the add-on's homepage.
     */
    public function getHomepageUrl()
    {
        return $this->_urlHomepage;
    }

    /**
     * @return ehough_curly_Url Optional. A link to the add-on's documentation.
     */
    public function getDocumentationUrl()
    {
        return $this->_urlDocs;
    }

    /**
     * @return ehough_curly_Url Optional. A link to a live demo of the add-on.
     */
    public function getDemoUrl()
    {
        return $this->_urlDemo;
    }

    /**
     * @return ehough_curly_Url Optional. A link to a download URL.
     */
    public function getDownloadUrl()
    {
        return $this->_urlDownload;
    }

    /**
     * @return ehough_curly_Url Optional. A link to a bug tracker for this add-on.
     */
    public function getBugTrackerUrl()
    {
        return $this->_urlBugs;
    }

    /**
     * @return string[] An array of strings, which may be empty but not null, of screenshots of this contributable.
     *                  URLs may either be absolute, or relative. In the latter case, they will be considered to be
     *                  relative from the contributable root.
     */
    public function getScreenshots()
    {
        return $this->_screenshots;
    }



    private function _setName($name)
    {
        if (!is_string($name)) {

            throw new InvalidArgumentException('Name must be a string');
        }

        if (preg_match('~^[A-Za-z0-9-_\./]{1,100}$~', $name, $matches) !== 1) {

            throw new InvalidArgumentException('Invalid name.');
        }
        $this->_name = strtolower($name);
    }

    private function _setVersion($version)
    {
        if ($version instanceof tubepress_spi_version_Version) {

            $this->_version = $version;

        } else {

            try {

                $this->_version = tubepress_spi_version_Version::parse($version);

            } catch (InvalidArgumentException $e) {

                throw new InvalidArgumentException('Invalid version: ' . $e->getMessage());
            }
        }
    }

    private function _setTitle($title)
    {
        if (!is_string($title)) {

            throw new InvalidArgumentException('Title must be a string');
        }

        if (strlen($title) > 255) {

            throw new InvalidArgumentException('Titles must be 255 characters or less');
        }

        $this->_title = $title;
    }

    private function _setAuthor(array $author)
    {
        if (! isset($author['name'])) {

            throw new InvalidArgumentException('Must include author name');
        }

        foreach ($author as $key => $value) {

            if ($key !== 'name' && $key !== 'email' && $key !== 'url') {

                throw new InvalidArgumentException("Invalid author attribute: $key");
            }
        }

        if (isset($author['url'])) {

            $this->_validateUrl($author['url'], 'Invalid author URL: ' . $author['url']);
        }

        $this->_author = $author;
    }

    private function _setLicenses(array $licenses)
    {
        if (empty($licenses)) {

            throw new InvalidArgumentException('Missing licenses');
        }

        foreach ($licenses as $license) {

            if (!isset($license['url'])) {

                throw new InvalidArgumentException('License is missing URL');
            }

            $this->_validateUrl($license['url'], 'Invalid license URL: ' . $license['url']);

            if (count($license) > 1 && !isset($license['type'])) {

                throw new InvalidArgumentException('Only \'url\' and \'type\' attributes are supported for licenses');
            }
        }

        $this->_licenses = $licenses;
    }

    private function _validateUrl($url, $message)
    {
        try {

            new ehough_curly_Url($url);

        } catch (InvalidArgumentException $e) {

            throw new InvalidArgumentException($message);
        }
    }

    /**
     * @param $screenshotUrl
     *
     * @throws InvalidArgumentException
     */
    private function _validateScreenshotUrl($screenshotUrl)
    {
        if (!is_string($screenshotUrl)) {

            throw new InvalidArgumentException('Screenshots must be strings.');
        }

        if (strpos($screenshotUrl, 'http') === 0) {

            try {

                new ehough_curly_Url($screenshotUrl);

            } catch (InvalidArgumentException $e) {

                throw new InvalidArgumentException(sprintf('%s is not a valid screenshot URL', $screenshotUrl));
            }
        }

        if (!(tubepress_impl_util_StringUtils::endsWith($screenshotUrl, '.png') || tubepress_impl_util_StringUtils::endsWith($screenshotUrl, '.jpg'))) {

            throw new InvalidArgumentException('Screenshot URLs must end with .png or .jpg');
        }
    }
}
