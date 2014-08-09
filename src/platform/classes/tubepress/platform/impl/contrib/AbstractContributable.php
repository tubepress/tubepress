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

abstract class tubepress_platform_impl_contrib_AbstractContributable implements tubepress_platform_api_contrib_ContributableInterface
{
    /**
     * Required attributes.
     */
    private static $_PROPERTY_NAME     = 'name';
    private static $_PROPERTY_VERSION  = 'version';
    private static $_PROPERTY_TITLE    = 'title';
    private static $_PROPERTY_AUTHORS  = 'authors';
    private static $_PROPERTY_LICENSES = 'licenses';

    /**
     * Optional attributes.
     */
    private static $_PROPERTY_DESCRIPTION       = 'description';
    private static $_PROPERTY_KEYWORDS          = 'keywords';
    private static $_PROPERTY_SCREENSHOTS       = 'screenshots';
    private static $_PROPERTY_URL_HOMEPAGE      = 'urlHomepage';
    private static $_PROPERTY_URL_DOCUMENTATION = 'urlDocs';
    private static $_PROPERTY_URL_DEMO          = 'urlDemo';
    private static $_PROPERTY_URL_DOWNLOAD      = 'urlDownload';
    private static $_PROPERTY_URL_BUGS          = 'urlBugs';

    /**
     * @var tubepress_platform_api_collection_MapInterface
     */
    private $_properties;

    public function __construct($name, $version, $title, array $authors, array $licenses)
    {
        $this->_properties = new tubepress_platform_impl_collection_Map();

        $this->_elementsToMaps($authors);
        $this->_elementsToMaps($licenses);

        $this->_properties->put(self::$_PROPERTY_NAME, $name);
        $this->_properties->put(self::$_PROPERTY_VERSION, $version);
        $this->_properties->put(self::$_PROPERTY_TITLE, $title);
        $this->_properties->put(self::$_PROPERTY_AUTHORS, $authors);
        $this->_properties->put(self::$_PROPERTY_LICENSES, $licenses);
        $this->_properties->put(self::$_PROPERTY_KEYWORDS, array());
        $this->_properties->put(self::$_PROPERTY_SCREENSHOTS, array());
    }

    /**
     * @return string Required. The globally unique name of this contributable.
     *
     *                Must be 100 characters or less, all lowercase, and contain only URL-safe characters
     *                and slashes ([a-z0-9-_\./]{1,100}).
     *
     * @api
     * @since 4.0.0
     */
    public function getName()
    {
        return $this->_properties->get(self::$_PROPERTY_NAME);
    }

    /**
     * @return tubepress_platform_api_version_Version Required. The version of this contributable.
     *
     * @api
     * @since 4.0.0
     */
    public function getVersion()
    {
        return $this->_properties->get(self::$_PROPERTY_VERSION);
    }

    /**
     * @return string Required. A user-friendly title for the contributable. 255 characters or less.
     *
     * @api
     * @since 4.0.0
     */
    public function getTitle()
    {
        return $this->_properties->get(self::$_PROPERTY_TITLE);
    }

    /**
     * @return array Required. One or more associative arrays of author information. Each associative array
     *               may contain the following:
     *
     *               key 'name'  : required, string
     *               key 'email' : optional, string
     *               key 'url'   : optional, tubepress_platform_api_url_UrlInterface
     *
     * @api
     * @since 4.0.0
     */
    public function getAuthors()
    {
        return $this->_properties->get(self::$_PROPERTY_AUTHORS);
    }

    /**
     * @return array Required. One or more associative arrays of license information. Each associative array
     *               may contain the following:
     *
     *               key 'url'  : required, tubepress_platform_api_url_UrlInterface. URL to the license text.
     *               key 'type' : optional, string. An identifier to indicate to developer's the general license type.
     *                            Consider using one of http://www.opensource.org/licenses/alphabetical.
     *
     * @api
     * @since 4.0.0
     */
    public function getLicenses()
    {
        return $this->_properties->get(self::$_PROPERTY_LICENSES);
    }

    /**
     * @return string Optional. A longer description of this contributable that may be shown to the user.
     *
     *                5000 characters or less.
     *
     * @api
     * @since 4.0.0
     */
    public function getDescription()
    {
        return $this->getOptionalProperty(self::$_PROPERTY_DESCRIPTION, null);
    }

    /**
     * @return array Optional. An array of keywords that might help users discover this contributable.
     *
     *               Each keyword must be comprised of only letters, numbers, hypens, and dots. Each keyword must
     *               be 30 characters or less.
     *
     * @api
     * @since 4.0.0
     */
    public function getKeywords()
    {
        return $this->getOptionalProperty(self::$_PROPERTY_KEYWORDS, array());
    }

    /**
     * @return tubepress_platform_api_url_UrlInterface Optional. A link to the contributable's homepage. May be null.
     *
     * @api
     * @since 4.0.0
     */
    public function getHomepageUrl()
    {
        return $this->getOptionalProperty(self::$_PROPERTY_URL_HOMEPAGE, null);
    }

    /**
     * @return tubepress_platform_api_url_UrlInterface Optional. A link to the contributable's documentation. May be null.
     *
     * @api
     * @since 4.0.0
     */
    public function getDocumentationUrl()
    {
        return $this->getOptionalProperty(self::$_PROPERTY_URL_DOCUMENTATION, null);
    }

    /**
     * @return tubepress_platform_api_url_UrlInterface Optional. A link to a live demo of the contributable. May be null.
     *
     * @api
     * @since 4.0.0
     */
    public function getDemoUrl()
    {
        return $this->getOptionalProperty(self::$_PROPERTY_URL_DEMO, null);
    }

    /**
     * @return tubepress_platform_api_url_UrlInterface Optional. A link to a download URL. May be null.
     *
     * @api
     * @since 4.0.0
     */
    public function getDownloadUrl()
    {
        return $this->getOptionalProperty(self::$_PROPERTY_URL_DOWNLOAD, null);
    }

    /**
     * @return tubepress_platform_api_url_UrlInterface Optional. A link to a bug tracker for this contributable. May be null.
     *
     * @api
     * @since 4.0.0
     */
    public function getBugTrackerUrl()
    {
        return $this->getOptionalProperty(self::$_PROPERTY_URL_BUGS, null);
    }

    /**
     * @return array Optional. One or more screenshots of this contributable.
     *
     *               Each element of this array is an array of two tubepress_platform_api_url_UrlInterface instances
     *               that represent the screenshot.
     *
     *               The first URL points to the thumbnail version of the image pointed to by the second URL.
     *
     *               All URLs must be absolute, and the path must end with .png or .jpg.
     *
     * @api
     * @since 4.0.0
     */
    public function getScreenshots()
    {
        return $this->getOptionalProperty(self::$_PROPERTY_SCREENSHOTS, null);
    }

    /**
     * @return tubepress_platform_api_collection_MapInterface
     *
     * @api
     * @since 4.0.0
     */
    public function getProperties()
    {
        return $this->_properties;
    }

    public function setDescription($description)
    {
        $this->_properties->put(self::$_PROPERTY_DESCRIPTION, $description);
    }

    public function setKeywords(array $keywords)
    {
        $this->_properties->put(self::$_PROPERTY_KEYWORDS, $keywords);
    }

    public function setScreenshots(array $screenshots)
    {
        $this->_properties->put(self::$_PROPERTY_SCREENSHOTS, $screenshots);
    }

    public function setBugTrackerUrl(tubepress_platform_api_url_UrlInterface $url)
    {
        $this->_properties->put(self::$_PROPERTY_URL_BUGS, $url);
    }

    public function setDemoUrl(tubepress_platform_api_url_UrlInterface $url)
    {
        $this->_properties->put(self::$_PROPERTY_URL_DEMO, $url);
    }

    public function setDownloadUrl(tubepress_platform_api_url_UrlInterface $url)
    {
        $this->_properties->put(self::$_PROPERTY_URL_DOWNLOAD, $url);
    }

    public function setHomepageUrl(tubepress_platform_api_url_UrlInterface $url)
    {
        $this->_properties->put(self::$_PROPERTY_URL_HOMEPAGE, $url);
    }

    public function setDocumentationUrl(tubepress_platform_api_url_UrlInterface $url)
    {
        $this->_properties->put(self::$_PROPERTY_URL_DOCUMENTATION, $url);
    }

    protected function getOptionalProperty($key, $default)
    {
        if (!$this->_properties->containsKey($key)) {

            return $default;
        }

        return $this->_properties->get($key);
    }

    private function _elementsToMaps(array &$incoming)
    {
        for ($x = 0; $x < count($incoming); $x++) {

            $map = new tubepress_platform_impl_collection_Map();

            foreach ($incoming[$x] as $key => $value) {

                $map->put($key, $value);
            }

            $incoming[$x] = $map;
        }
    }
}