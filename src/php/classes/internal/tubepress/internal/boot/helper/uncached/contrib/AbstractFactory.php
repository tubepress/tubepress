<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

abstract class tubepress_internal_boot_helper_uncached_contrib_AbstractFactory
{
    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_api_url_UrlFactoryInterface
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

    /**
     * @var boolean
     */
    private $_shouldLog;

    protected static $FIRST_LEVEL_KEY_NAME    = 'name';
    protected static $FIRST_LEVEL_KEY_TITLE   = 'title';
    protected static $FIRST_LEVEL_KEY_VERSION = 'version';
    protected static $FIRST_LEVEL_KEY_AUTHORS = 'authors';
    protected static $FIRST_LEVEL_KEY_LICENSE = 'license';

    private static $_FIRST_LEVEL_KEY_KEYWORDS    = 'keywords';
    private static $_FIRST_LEVEL_KEY_DESCRIPTION = 'description';
    private static $_FIRST_LEVEL_KEY_SCREENSHOTS = 'screenshots';
    private static $_FIRST_LEVEL_KEY_SUPPORT     = 'support';

    private static $_SECOND_LEVEL_KEY_URL_BUGS     = 'bugTracker';
    private static $_SECOND_LEVEL_KEY_URL_DEMO     = 'demo';
    private static $_SECOND_LEVEL_KEY_URL_DOWNLOAD = 'download';
    private static $_SECOND_LEVEL_KEY_URL_HOME     = 'homepage';
    private static $_SECOND_LEVEL_KEY_URL_DOCS     = 'documentation';
    private static $_SECOND_LEVEL_KEY_URL_FORUM    = 'forum';
    private static $_SECOND_LEVEL_KEY_URL_SOURCE   = 'source';

    private static $_SECOND_LEVEL_KEY_AUTHOR_NAME  = 'name';
    private static $_SECOND_LEVEL_KEY_AUTHOR_EMAIL = 'email';
    private static $_SECOND_LEVEL_KEY_AUTHOR_URL   = 'url';
    private static $_SECOND_LEVEL_KEY_AUTHOR_ROLE  = 'role';

    private static $_SECOND_LEVEL_KEY_LICENSE_URLS = 'urls';
    private static $_SECOND_LEVEL_KEY_LICENSE_TYPE = 'type';

    /**
     * @var string[]
     */
    private $_urlsToSettersMap;

    public function __construct(tubepress_api_log_LoggerInterface       $logger,
                                tubepress_api_url_UrlFactoryInterface   $urlFactory,
                                tubepress_api_util_LangUtilsInterface   $langUtils,
                                tubepress_api_util_StringUtilsInterface $stringUtils)
    {
        $this->_logger           = $logger;
        $this->_urlFactory       = $urlFactory;
        $this->_langUtils        = $langUtils;
        $this->_stringUtils      = $stringUtils;
        $this->_shouldLog        = $logger->isEnabled();
        $this->_urlsToSettersMap = array(

            self::$_SECOND_LEVEL_KEY_URL_HOME     => 'setHomepageUrl',
            self::$_SECOND_LEVEL_KEY_URL_BUGS     => 'setBugTrackerUrl',
            self::$_SECOND_LEVEL_KEY_URL_DEMO     => 'setDemoUrl',
            self::$_SECOND_LEVEL_KEY_URL_DOCS     => 'setDocumentationUrl',
            self::$_SECOND_LEVEL_KEY_URL_DOWNLOAD => 'setDownloadUrl',
            self::$_SECOND_LEVEL_KEY_URL_SOURCE   => 'setSourceUrl',
            self::$_SECOND_LEVEL_KEY_URL_FORUM    => 'setForumUrl',
        );
    }

    /**
     * @param string $manifestPath
     * @param array  $manifestData
     *
     * @return tubepress_internal_contrib_AbstractContributable
     */
    public function fromManifestData($manifestPath, array $manifestData)
    {
        $errors = $this->_normalizeAndReturnErrors($manifestPath, $manifestData);

        if (count($errors) !== 0) {

            if ($this->_logger->isEnabled()) {

                $this->_logger->error(sprintf('The following errors were detected when processing %s', $manifestPath));
                foreach ($errors as $error) {
                    $this->_logger->error($error);
                }
            }

            return $errors;
        }

        $contributable = $this->buildWithValidNormalizedData($manifestPath, $manifestData);

        $this->_setDescription($manifestData, $contributable);
        $this->_setKeywords($manifestData, $contributable);
        $this->_setScreenshots($manifestData, $contributable);
        $this->_setSupport($manifestData, $contributable);

        return $contributable;
    }

    private function _setDescription(array $manifestData, tubepress_internal_contrib_AbstractContributable $contrib)
    {
        if (isset($manifestData[self::$_FIRST_LEVEL_KEY_DESCRIPTION])) {

            $contrib->setDescription($manifestData[self::$_FIRST_LEVEL_KEY_DESCRIPTION]);
        }
    }

    private function _setKeywords(array $manifestData, tubepress_internal_contrib_AbstractContributable $contrib)
    {
        if (isset($manifestData[self::$_FIRST_LEVEL_KEY_KEYWORDS])) {

            $contrib->setKeywords($manifestData[self::$_FIRST_LEVEL_KEY_KEYWORDS]);
        }
    }

    private function _setScreenshots(array $manifestData, tubepress_internal_contrib_AbstractContributable $contrib)
    {
        if (isset($manifestData[self::$_FIRST_LEVEL_KEY_SCREENSHOTS])) {

            $contrib->setScreenshots($manifestData[self::$_FIRST_LEVEL_KEY_SCREENSHOTS]);
        }
    }

    private function _setSupport(array $manifestData, tubepress_internal_contrib_AbstractContributable $contrib)
    {
        $supportKey = self::$_FIRST_LEVEL_KEY_SUPPORT;

        if (!isset($manifestData[$supportKey])) {

            return;
        }

        foreach ($this->_urlsToSettersMap as $key => $setter) {

            if (isset($manifestData[$supportKey][$key])) {

                $contrib->$setter($manifestData[$supportKey][$key]);
            }
        }
    }

    protected function nonStringOrTooLong($candidate, $maxLength, $key, array &$errors)
    {
        if (!is_string($candidate)) {

            $errors[] = "Non-string $key";
            return true;
        }

        $length = strlen($candidate);

        if ($length > $maxLength || $length < 1) {

            $errors[] = "$key must be between 1 and $maxLength characters";
            return true;
        }

        return false;
    }

    protected function toUrl($candidate, $mustBeAbsolute = true)
    {
        if (!is_string($candidate) || !$candidate) {

            return false;
        }

        try {

            $url = $this->_urlFactory->fromString($candidate);

            if ($mustBeAbsolute && !$url->isAbsolute()) {

                return null;
            }

            return $url;

        } catch (InvalidArgumentException $e) {

            return null;
        }
    }

    /**
     * @return tubepress_api_util_LangUtilsInterface
     */
    protected function getLangUtils()
    {
        return $this->_langUtils;
    }

    /**
     * @return tubepress_api_url_UrlFactoryInterface
     */
    protected function getUrlFactory()
    {
        return $this->_urlFactory;
    }

    /**
     * @return tubepress_api_log_LoggerInterface
     */
    protected function getLogger()
    {
        return $this->_logger;
    }

    /**
     * @return bool
     */
    protected function shouldLog()
    {
        return $this->_shouldLog;
    }

    /**
     * @param string $manifestPath
     * @param array  $manifestData
     *
     * @return array
     */
    protected abstract function normalizeAndReturnErrors($manifestPath, array &$manifestData);

    /**
     * @param string $manifestPath
     * @param array  $manifestData
     *
     * @return tubepress_internal_contrib_AbstractContributable
     */
    protected abstract function buildWithValidNormalizedData($manifestPath, array &$manifestData);

    /**
     * @param string $manifestPath
     * @param array  $manifestData
     *
     * @return array
     */
    private function _normalizeAndReturnErrors($manifestPath, array &$manifestData)
    {
        $errors = array();

        $this->_handleName($manifestData, $errors);
        $this->_handleVersion($manifestData, $errors);
        $this->_handleTitle($manifestData, $errors);
        $this->_handleDescription($manifestData, $errors);
        $this->_handleAuthors($manifestData, $errors);
        $this->_handleLicense($manifestData, $errors);

        $this->_handleKeywords($manifestData, $errors);
        $this->_handleUrls($manifestData, $errors);
        $this->_handleScreenshots($manifestData, $errors);

        $childErrors = $this->normalizeAndReturnErrors($manifestPath, $manifestData);

        return array_merge($errors, $childErrors);
    }

    private function _handleName(array &$manifestData, array &$errors)
    {
        $key = self::$FIRST_LEVEL_KEY_NAME;

        if (!isset($manifestData[$key])) {

            $errors[] = 'Missing name';
            return;
        }

        $candidate = $manifestData[$key];

        if (!is_string($candidate)) {

            $errors[] = 'Add-on and theme names must be strings';
            return;
        }

        $matchesRegex = preg_match_all('~[0-9a-z-_/\.]{1,100}~', $candidate, $matches) === 1;

        if (!$matchesRegex) {

            $message = 'Add-on and theme names must be all lowercase, 100 characters or less, and contain only alphanumerics, dots, dashes, underscores, and slashes';

            $errors[] = $message;
        }
    }

    private function _handleVersion(array &$manifestData, array &$errors)
    {
        $key = self::$FIRST_LEVEL_KEY_VERSION;

        if (!isset($manifestData[$key])) {

            $errors[] = 'Missing version';
            return;
        }

        $candidate = $manifestData[$key];

        if ($candidate instanceof tubepress_api_version_Version) {

            return;
        }

        if (!$candidate) {

            $errors[] = 'Empty version';
            return;
        }

        try {

            $manifestData[$key] = tubepress_api_version_Version::parse($candidate);

        } catch (InvalidArgumentException $e) {

            $errors[] = 'Malformed version';
        }
    }

    private function _handleTitle(array &$manifestData, array &$errors)
    {
        $this->_handleTitleOrDesc($manifestData, $errors, self::$FIRST_LEVEL_KEY_TITLE, 255, true);
    }

    private function _handleDescription(array &$manifestData, array &$errors)
    {
        $this->_handleTitleOrDesc($manifestData, $errors, self::$_FIRST_LEVEL_KEY_DESCRIPTION, 5000, false);
    }

    private function _handleTitleOrDesc(array &$manifestData, array &$errors, $key, $maxLength, $required)
    {
        if (!isset($manifestData[$key])) {

            if ($required) {

                $errors[] = "Missing $key";
            }

            return;
        }

        $candidate = $manifestData[$key];

        if ($this->nonStringOrTooLong($candidate, $maxLength, $key, $errors)) {

            return;
        }

        $manifestData[$key] = trim($candidate);
    }

    private function _handleAuthors(array &$manifestData, array &$errors)
    {
        $key = self::$FIRST_LEVEL_KEY_AUTHORS;

        if (!isset($manifestData[$key])) {

            $errors[] = "Missing $key";
            return;
        }

        $candidate = $manifestData[$key];

        if (!is_array($candidate)) {

            $errors[] = "Non-array data for $key";
            return;
        }

        if (count($candidate) < 1) {

            $errors[] = "Missing $key";
            return;
        }

        $allAuthorsArray = &$manifestData[$key];

        for ($x = 0; $x < count($allAuthorsArray); $x++) {

            $author = &$allAuthorsArray[$x];

            $this->_handleSingleAuthor($author, $errors, $x);
        }
    }

    private function _handleSingleAuthor(array &$candidateAuthor, array &$errors, $index)
    {
        $authorNameKey  = self::$_SECOND_LEVEL_KEY_AUTHOR_NAME;
        $authorEmailKey = self::$_SECOND_LEVEL_KEY_AUTHOR_EMAIL;
        $authorUrlKey   = self::$_SECOND_LEVEL_KEY_AUTHOR_URL;
        $authorRoleKey  = self::$_SECOND_LEVEL_KEY_AUTHOR_ROLE;

        if (!isset($candidateAuthor[$authorNameKey])) {

            $errors[] = sprintf('Author %d is missing name', ($index + 1));
            return;
        }

        $candidateAuthor[$authorNameKey] = trim($candidateAuthor[$authorNameKey]);

        $extraKeys = array_diff(array_keys($candidateAuthor), array(
            $authorNameKey,
            $authorEmailKey,
            $authorUrlKey,
            $authorRoleKey,
        ));

        if (count($extraKeys) > 0) {

            $errors[] = sprintf('Author %d has attributes other than name, email, role, and url', ($index + 1));
            return;
        }

        if (isset($candidateAuthor[$authorEmailKey])) {

            $email = $candidateAuthor[$authorEmailKey];

            if (!$email || !is_string($email)) {

                $errors[] = sprintf('Author %d has an invalid email attribute', ($index + 1));
                return;

            } else {

                $candidateAuthor[$authorEmailKey] = trim($email);
            }
        }

        if (isset($candidateAuthor[$authorUrlKey])) {

            $realUrl = $this->toUrl($candidateAuthor[$authorUrlKey]);

            if ($realUrl) {

                $candidateAuthor[$authorUrlKey] = $realUrl;

            } else {

                $errors[] = sprintf('Author %d has an invalid URL attribute', ($index + 1));
            }
        }

        if (isset($candidateAuthor[$authorRoleKey])) {

            $role = $candidateAuthor[$authorRoleKey];

            if (!$role || !is_string($role)) {

                $errors[] = sprintf('Author %d has an invalid role attribute', ($index + 1));
                return;

            } else {

                $candidateAuthor[$authorRoleKey] = trim($role);
            }
        }
    }

    private function _handleLicense(array &$manifestData, array &$errors)
    {
        $key = self::$FIRST_LEVEL_KEY_LICENSE;

        if (!isset($manifestData[$key])) {

            $errors[] = 'Missing license';
            return;
        }

        $license        = $manifestData[$key];
        $licenseUrlKey  = self::$_SECOND_LEVEL_KEY_LICENSE_URLS;
        $licenseTypeKey = self::$_SECOND_LEVEL_KEY_LICENSE_TYPE;

        if (!is_array($license)) {

            $errors[] = 'License is not an array';
            return;
        }

        if (!isset($license[$licenseTypeKey])) {

            $errors[] = 'License is missing "type" attribute';
            return;
        }

        $type = $license[$licenseTypeKey];

        if (!$type || !is_string($type)) {

            $errors[] = 'License has an invalid "type" attribute';
            return;
        }

        $license[$licenseTypeKey] = trim($type);

        if (!isset($license[$licenseUrlKey])) {

            $errors[] = 'License is missing "urls" attribute';
            return;
        }

        $urls = &$license[$licenseUrlKey];

        if (!is_array($urls)) {

            $errors[] = 'License "urls" attribute is not an array';
            return;
        }

        if (count($urls) === 0) {

            $errors[] = 'License must define at least one URL';
            return;
        }

        for ($index = 0; $index < count($urls); $index++) {

            $realUrl = $this->toUrl($urls[$index]);

            if ($realUrl) {

                $urls[$index] = $realUrl;

            } else {

                $errors[] = sprintf('License URL %d is invalid', ($index + 1));
                return;
            }
        }

        $extraKeys = array_diff(array_keys($license), array($licenseUrlKey, $licenseTypeKey));

        if (count($extraKeys) > 0) {

            $errors[] = 'License has attributes other than urls and type';
            return;
        }

        $manifestData[$key] = $license;
    }

    private function _handleKeywords(array &$manifestData, array &$errors)
    {
        $key = self::$_FIRST_LEVEL_KEY_KEYWORDS;

        if (!isset($manifestData[$key])) {

            return;
        }

        $candidate = $manifestData[$key];

        if (!$this->_langUtils->isSimpleArrayOfStrings($candidate)) {

            $errors[] = 'Keywords must be an array of strings';
            return;
        }

        foreach ($candidate as $keyword) {

            if ($this->nonStringOrTooLong($keyword, 30, $key, $errors)) {

                return;
            }
        }
    }

    private function _handleUrls(array &$manifestData, array &$errors)
    {
        $urlsKey = self::$_FIRST_LEVEL_KEY_SUPPORT;

        if (!isset($manifestData[$urlsKey])) {

            return;
        }

        foreach ($this->_urlsToSettersMap as $key => $setter) {

            if (!isset($manifestData[$urlsKey][$key])) {

                continue;
            }

            $realUrl = $this->toUrl($manifestData[$urlsKey][$key]);

            if ($realUrl && $realUrl->isAbsolute()) {

                $manifestData[$urlsKey][$key] = $realUrl;

            } else {

                $errors[] = "Invalid $key URL";
            }
        }
    }

    private function _handleScreenshots(array &$manifestData, array &$errors)
    {
        $key = self::$_FIRST_LEVEL_KEY_SCREENSHOTS;

        if (!isset($manifestData[$key])) {

            return;
        }

        $allScreenshotsArray = $manifestData[$key];

        if (!is_array($allScreenshotsArray)) {

            $errors[] = 'Screenshots must be an array';
            return;
        }

        if ($this->getLangUtils()->isAssociativeArray($allScreenshotsArray)) {

            $errors[] = 'Screenshots must not be an associative array';
            return;
        }

       for ($index = 0; $index < count($allScreenshotsArray); $index++) {

           $candidateScreenshot = $allScreenshotsArray[$index];

           if (is_array($candidateScreenshot)) {

               if (count($candidateScreenshot) !== 2) {

                   $errors[] = sprintf('Screenshot %d must have exactly two URLs', ($index + 1));
                   continue;
               }

               $thumbUrl = $this->_toRealScreenshotUrl($candidateScreenshot[0]);
               $fullUrl  = $this->_toRealScreenshotUrl($candidateScreenshot[1]);

               if (!$thumbUrl) {

                   $errors[] = sprintf('Screenshot %d has an invalid thumbnail URL', ($index + 1));

               } else {

                   $allScreenshotsArray[$index][0] = $thumbUrl;
               }

               if (!$fullUrl) {

                   $errors[] = sprintf('Screenshot %d has an invalid full-size URL', ($index + 1));

               } else {

                   $allScreenshotsArray[$index][1] = $fullUrl;
               }

            } else {

                $realUrl = $this->_toRealScreenshotUrl($candidateScreenshot);

                if ($realUrl) {

                    $allScreenshotsArray[$index][0] = $realUrl;
                    $allScreenshotsArray[$index][1] = $realUrl;

                } else {

                    $errors[] = sprintf('Screenshot %d has an invalid URL', ($index + 1));
                }
            }
        }

        $manifestData[$key] = $allScreenshotsArray;
    }

    private function _toRealScreenshotUrl($candidate)
    {
        $realUrl = $this->toUrl($candidate);

        if (!$realUrl) {

            return $realUrl;
        }

        $path = $realUrl->getPath();

        if (!$this->_stringUtils->endsWith($path, '.png') && !$this->_stringUtils->endsWith($path, '.jpg')) {

            return null;
        }

        return $realUrl;
    }
}