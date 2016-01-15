<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress Pro.
 *
 * License summary
 *   - Can be used on 1 site, 1 server
 *   - Cannot be resold or distributed
 *   - Commercial use allowed
 *   - Can modify source-code but cannot distribute modifications (derivative works)
 *
 * Please see http://tubepress.com/license for details.
 */

/**
 * This is a very high-level service that is intended for use in PHP.
 *
 * @api
 * @since 4.1.8
 */
interface tubepress_api_pro_TubePressProInterface
{
    const _ = 'tubepress_api_pro_TubePressProInterface';

    /**
     * @return string The CSS tags and inline CSS for the head of an HTML document that contains
     *                Tubepress.
     *
     * @api
     * @since 4.1.8
     */
    function getCSS();

    /**
     * @param bool $includeJQuery True to load a copy of jQuery, false if it's already included elsewhere
     *                            in the document.
     *
     * @return string The script tags and inline JavaScript for an HTML document that contains TubePress. This
     *                can be placed anywhere in the document.
     *
     * @api
     * @since 4.1.8
     */
    function getJS($includeJQuery = false);

    /**
     *
     * @param mixed $options One of the following:
     *                       1. a TubePress shortcode, e.g. [tubepress mode="tag"]
     *                       2. an NVP string, e.g. mode="tag" tagValue="something"
     *                       3. An associative array of TubePress options, e.g. array('mode' => 'tag')
     *                       4. a falsey value, like null or ""
     *
     * @return string The primary TubePress HTML.
     *
     * @api
     * @since 4.1.8
     */
    function getHTML($options = null);
}
