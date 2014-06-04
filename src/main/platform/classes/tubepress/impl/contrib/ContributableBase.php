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
class tubepress_impl_contrib_ContributableBase implements tubepress_api_contrib_ContributableInterface
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

    const CATEGORY_URLS = 'urls';

    /**
     * @var string
     */
    private $_name;

    /**
     * @var tubepress_core_version_api_Version
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
     * @var string
     */
    private $_urlHomepage;

    /**
     * @var string
     */
    private $_urlDocs;

    /**
     * @var string
     */
    private $_urlDemo;

    /**
     * @var string
     */
    private $_urlDownload;

    /**
     * @var string
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
        $this->validateStringAndLength($description, 2000, 'description');

        $this->_description = $description;
    }

    public function setKeywords(array $keywords)
    {
        foreach ($keywords as $keyword) {

            $this->validateStringAndLength($keyword, 100, 'each keyword');

            if ($keyword == '') {

                throw new InvalidArgumentException('Keywords must not be empty.');
            }
        }

        $this->_keywords = $keywords;
    }

    public function setHomepageUrl($url)
    {
        $this->_urlHomepage = $url;
    }

    public function setDocumentationUrl($url)
    {
        $this->_urlDocs = $url;
    }

    public function setDemoUrl($url)
    {
        $this->_urlDemo = $url;
    }

    public function setDownloadUrl($url)
    {
        $this->_urlDownload = $url;
    }

    public function setBugTrackerUrl($url)
    {
        $this->_urlBugs = $url;
    }

    public function setScreenshots(array $screenshots)
    {
        $this->_screenshots = $screenshots;
    }

    /**
     * @return string The globally unique name of this add-on. Must be 100 characters or less,
     *                all lowercase, and contain only URL-safe characters ([a-z0-9-_\.]+).
     *
     * @api
     * @since 4.0.0
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * @return string The version of this add-on. This *should* be a semantic version number.
     *
     * @api
     * @since 4.0.0
     */
    public function getVersion()
    {
        return $this->_version;
    }

    /**
     * @return string A user-friendly title for the add-on. 255 characters or less.
     *
     * @api
     * @since 4.0.0
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * @return array An associative array of author information. The possible array keys are
     *               'name', 'email', and 'url'. 'name' is required, and the other fields are optional.
     *
     * @api
     * @since 4.0.0
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
     *
     * @api
     * @since 4.0.0
     */
    public function getLicenses()
    {
        return $this->_licenses;
    }

    /**
     * @return string Optional. A longer description of this add-on. 1000 characters or less.
     *
     * @api
     * @since 4.0.0
     */
    public function getDescription()
    {
        return $this->_description;
    }

    /**
     * @return array Optional. An array of keywords that might help folks discover this add-on. Only
     *               letters, numbers, hypens, and dots. Each keyword must be 30 characters or less.
     *
     * @api
     * @since 4.0.0
     */
    public function getKeywords()
    {
        return $this->_keywords;
    }

    /**
     * @return string Optional. A link to the add-on's homepage. May be null.
     *
     * @api
     * @since 4.0.0
     */
    public function getHomepageUrl()
    {
        return $this->_urlHomepage;
    }

    /**
     * @return string Optional. A link to the add-on's documentation. May be null.
     *
     * @api
     * @since 4.0.0
     */
    public function getDocumentationUrl()
    {
        return $this->_urlDocs;
    }

    /**
     * @return string Optional. A link to a live demo of the add-on. May be null.
     *
     * @api
     * @since 4.0.0
     */
    public function getDemoUrl()
    {
        return $this->_urlDemo;
    }

    /**
     * @return string Optional. A link to a download URL. May be null.
     *
     * @api
     * @since 4.0.0
     */
    public function getDownloadUrl()
    {
        return $this->_urlDownload;
    }

    /**
     * @return string Optional. A link to a bug tracker for this add-on. May be null.
     *
     * @api
     * @since 4.0.0
     */
    public function getBugTrackerUrl()
    {
        return $this->_urlBugs;
    }

    /**
     * @return string[] An array of strings, which may be empty but not null, of screenshots of this contributable.
     *                  URLs may either be absolute, or relative. In the latter case, they will be considered to be
     *                  relative from the contributable root. Array keys are considered to be thumbnails, and
     *                  values are considered to be full-sized images.
     *
     * @api
     * @since 4.0.0
     */
    public function getScreenshots()
    {
        return $this->_screenshots;
    }

    protected function validateStringAndLength($candidate, $length, $name)
    {
        $this->validateIsString($candidate, $name);

        $this->validateStringLength($candidate, $length, $name);
    }

    protected function validateStringLength($candidate, $length, $name)
    {
        if (strlen($candidate) > $length) {

            throw new InvalidArgumentException(sprintf('%s must be %d characters or less.',
                ucfirst($name), $length));
        }
    }

    protected function validateIsString($candidate, $name)
    {
        if (!is_string($candidate)) {

            throw new InvalidArgumentException(ucfirst($name) . ' must be a string.');
        }
    }

    protected function validateIsEmailAddress($candidate, $name)
    {
        $this->validateIsString($candidate, $name);

        if (preg_match('~[a-z0-9!#$%&\'*+/=?^_`{|}\~-]+(?:\.[a-z0-9!#$%&\'*+/=?^_`{|}\~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?~', $candidate, $matches) !== 1) {

            throw new InvalidArgumentException(ucfirst($name) . ' email is invalid.');
        }
    }

    protected function validateContributableName($candidate, $name)
    {
        $this->validateIsString($candidate, $name);

        if (preg_match('~^[A-Za-z0-9-_\./]{1,100}$~', $candidate, $matches) !== 1) {

            throw new InvalidArgumentException(sprintf('Invalid %s.', $name));
        }
    }

    protected function validateStringEndsWith($candidate, array $options, $name)
    {
        $this->validateIsString($candidate, $name);

        foreach ($options as $option) {

            if ($this->_endsWith($candidate, $option)) {

                return true;
            }
        }

        return false;
    }

    protected function validateArrayIsJustStrings(array $array, $name)
    {
        foreach ($array as $element) {

            $this->validateIsString($element, $name);
        }
    }

    private function _setVersion($version)
    {
        $this->validateStringAndLength($version, 20, 'version');

        $this->_version = $version;
    }

    private function _setName($name)
    {
        $this->validateContributableName($name, 'name');

        $this->_name = strtolower($name);
    }

    private function _setTitle($title)
    {
        $this->validateStringAndLength($title, 255, 'title');

        $this->_title = $title;
    }

    private function _setAuthor(array $author)
    {
        if (!isset($author['name'])) {

            throw new InvalidArgumentException('Must include author name');
        }

        $this->validateStringAndLength($author['name'], 200, 'author name');

        foreach ($author as $key => $value) {

            if ($key !== 'name' && $key !== 'email' && $key !== 'url') {

                throw new InvalidArgumentException('Author information must only include name, email, and/or URL');
            }
        }

        if (isset($author['email'])) {

            $this->validateIsEmailAddress($author['email'], 'author');
        }

        $this->_author = $author;
    }

    private function _setLicenses(array $licenses)
    {
        if (empty($licenses)) {

            throw new InvalidArgumentException('Must include at least one license.');
        }

        foreach ($licenses as $license) {

            if (!isset($license['type'])) {

                throw new InvalidArgumentException('License is missing type');
            }

            $this->validateStringAndLength($license['type'], 100, 'license type');

            if (count($license) > 1 && !isset($license['url'])) {

                throw new InvalidArgumentException('Only \'url\' and \'type\' attributes are supported for licenses');
            }
        }

        $this->_licenses = $licenses;
    }

    private function _endsWith($haystack, $needle)
    {
        if (! is_string($haystack) || ! is_string($needle)) {

            return false;
        }

        $length = strlen($needle);
        $start  = $length * -1; //negative

        return (substr($haystack, $start) === $needle);
    }
}
