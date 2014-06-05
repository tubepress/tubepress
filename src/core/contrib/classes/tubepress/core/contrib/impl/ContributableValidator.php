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
 *
 */
class tubepress_core_contrib_impl_ContributableValidator implements tubepress_core_contrib_api_ContributableValidatorInterface
{
    /**
     * @var tubepress_core_url_api_UrlFactoryInterface
     */
    private $_urlFactory;

    /**
     * @var tubepress_api_util_LangUtilsInterface
     */
    private $_langUtils;

    /**
     * @var tubepress_api_util_StringUtilsInterface
     */
    private $_stringUtils;

    public function __construct(tubepress_core_url_api_UrlFactoryInterface $urlFactory,
                                tubepress_api_util_LangUtilsInterface      $langUtils,
                                tubepress_api_util_StringUtilsInterface    $stringUtils)
    {
        $this->_urlFactory  = $urlFactory;
        $this->_langUtils   = $langUtils;
        $this->_stringUtils = $stringUtils;
    }

    /**
     * @param tubepress_api_contrib_ContributableInterface $contributable
     *
     * @return string|null The problem message for the given contributable, or null if no problem.
     *
     * @api
     * @since 4.0.0
     */
    public function getProblemMessage(tubepress_api_contrib_ContributableInterface $contributable)
    {
        try {

            $this->_validateAuthor($contributable);
            $this->_validateUrlAttribute($contributable, 'getBugTrackerUrl',    'bug tracker');
            $this->_validateUrlAttribute($contributable, 'getDemoUrl',          'demo');
            $this->_validateUrlAttribute($contributable, 'getDocumentationUrl', 'documentation');
            $this->_validateUrlAttribute($contributable, 'getDownloadUrl',      'download');
            $this->_validateUrlAttribute($contributable, 'getHomepageUrl',      'homepage');

            $this->_validateLicenses($contributable);
            $this->_validateScreenshots($contributable);
            $this->_validateVersion($contributable);

        } catch (Exception $e) {

            return $e->getMessage();
        }

        return null;
    }

    /**
     * @param tubepress_api_contrib_ContributableInterface $contributable
     *
     * @return bool True if the contributable is valid. False otherwise.
     *
     * @api
     * @since 4.0.0
     */
    public function isValid(tubepress_api_contrib_ContributableInterface $contributable)
    {
        return $this->getProblemMessage($contributable) === null;
    }

    private function _validateVersion(tubepress_api_contrib_ContributableInterface $contributable)
    {
        try {

            tubepress_core_version_api_Version::parse($contributable->getVersion());

        } catch (InvalidArgumentException $e) {

            throw new InvalidArgumentException('Invalid version');
        }
    }

    private function _validateScreenshots(tubepress_api_contrib_ContributableInterface $contributable) {

        $screenshots = $contributable->getScreenshots();

        if ($this->_langUtils->isAssociativeArray($screenshots)) {

            foreach ($screenshots as $thumbnailUrl => $fullsizeUrl) {

                $this->_validateScreenshotUrl($thumbnailUrl);
                $this->_validateScreenshotUrl($fullsizeUrl);
            }

        } else {

            foreach ($screenshots as $url) {

                $this->_validateScreenshotUrl($url);
            }
        }
    }

    private function _validateScreenshotUrl($candidate)
    {
        if (strpos($candidate, 'http') !== 0 && strpos($candidate, '//') !== 0) {

            $candidate = 'http://' . $candidate;
        }

        $this->_validateUrl($candidate, 'screenshot');

        if (!$this->_stringUtils->endsWith($candidate, '.png') && !$this->_stringUtils->endsWith($candidate, '.jpg')) {

            throw new InvalidArgumentException('Screenshot URLS must end with .png or .jpg');
        }
    }

    private function _validateLicenses(tubepress_api_contrib_ContributableInterface $contributable)
    {
        $licenses = $contributable->getLicenses();

        foreach ($licenses as $license) {

            if (isset($license['url'])) {

                $this->_validateUrl($license['url'], 'license');
            }
        }
    }

    private function _validateAuthor(tubepress_api_contrib_ContributableInterface $contributable)
    {
        $author = $contributable->getAuthor();

        if (isset($author['url'])) {

            $this->_validateUrl($author['url'], 'author');
        }
    }

    private function _validateUrlAttribute(tubepress_api_contrib_ContributableInterface $contributable, $method, $name)
    {
        $url = $contributable->$method();

        if ($url !== null) {

            $this->_validateUrl($url, $name);
        }
    }

    private function _validateUrl($candidate, $name, $absolute = true)
    {
        try {

            $url = $this->_urlFactory->fromString($candidate);

            if ($absolute && !$url->isAbsolute()) {

                throw new InvalidArgumentException("$name URL must be absolute");
            }

        } catch (InvalidArgumentException $e) {

            throw new InvalidArgumentException("Invalid $name URL");
        }
    }
}
