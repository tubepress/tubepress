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
 * @api
 * @since 4.2.0
 */
interface tubepress_api_shortcode_ShortcodeExtractorInterface
{
    const _ = 'tubepress_api_shortcode_ShortcodeExtractorInterface';

    /**
     * @api
     * @since 4.2.0
     *
     * @param string $name The shortcode name to look for.
     * @param string $text The text to scan.
     *
     * @return array An array, which may be empty but never null, of
     *               tubepress_api_shortcode_ShortcodeInterface instances.
     */
    function getShortcodes($name, $text);
}