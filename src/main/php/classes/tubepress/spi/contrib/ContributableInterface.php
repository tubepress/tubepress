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
 * A TubePress add-on or theme.
 */
interface tubepress_spi_contrib_ContributableInterface
{
    /**
     * @return string The globally unique name of this add-on. Must be 100 characters or less,
     *                all lowercase, and contain only URL-safe characters ([a-z0-9-_\.]+).
     */
    function getName();

    /**
     * @return tubepress_spi_version_Version The version of this add-on.
     */
    function getVersion();

    /**
     * @return string A user-friendly title for the add-on. 255 characters or less.
    */
    function getTitle();

    /**
     * @return array An associative array of author information. The possible array keys are
     *               'name', 'email', and 'url'. 'name' is required, and the other fields are optional.
     */
    function getAuthor();

    /**
     * @return array An array of associative arrays of license information. The possible array keys are
     *               'url' and 'type'. 'url' is required and must link to the license text. 'type'
     *               may be supplied if the license is one of the official open source licenses found
     *               at http://www.opensource.org/licenses/alphabetical
     */
    function getLicenses();

    /**
     * @return string Optional. A longer description of this add-on. 1000 characters or less.
     */
    function getDescription();

    /**
     * @return array Optional. An array of keywords that might help folks discover this add-on. Only
     *               letters, numbers, hypens, and dots. Each keyword must be 30 characters or less.
     */
    function getKeywords();

    /**
     * @return ehough_curly_Url Optional. A link to the add-on's homepage.
     */
    function getHomepageUrl();

    /**
     * @return ehough_curly_Url Optional. A link to the add-on's documentation.
     */
    function getDocumentationUrl();

    /**
     * @return ehough_curly_Url Optional. A link to a live demo of the add-on.
     */
    function getDemoUrl();

    /**
     * @return ehough_curly_Url Optional. A link to a download URL.
     */
    function getDownloadUrl();

    /**
     * @return ehough_curly_Url Optional. A link to a bug tracker for this add-on.
     */
    function getBugTrackerUrl();

    /**
     * @return string[] An array of strings, which may be empty but not null, of screenshots of this contributable.
     *                  URLs may either be absolute, or relative. In the latter case, they will be considered to be
     *                  relative from the contributable root.
     */
    function getScreenshots();
}
