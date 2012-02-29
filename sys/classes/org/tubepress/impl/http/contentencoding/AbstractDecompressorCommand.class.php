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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../../../../impl/classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_impl_http_AbstractDecodingCommand',
));

/**
 * Abstract decompressor.
 */
abstract class org_tubepress_impl_http_contentencoding_AbstractDecompressorCommand implements org_tubepress_spi_patterns_cor_Command
{
    /**
     * Execute the command.
     *
     * @param array $context An array of context elements (may be empty).
     *
     * @return boolean True if this command was able to handle the execution. False otherwise.
     */
    function execute($context)
    {
        $response = $context->response;
        $encoding = $response->getHeaderValue(org_tubepress_api_http_HttpResponse::HTTP_HEADER_CONTENT_ENCODING);

        if (strcasecmp($encoding, $this->getExpectedContentEncodingHeaderValue()) !== 0) {

            org_tubepress_impl_log_Log::log($this->logPrefix(), 'Content is not encoded with %s', $this->getExpectedContentEncodingHeaderValue());
            return false;
        }

        if (! $this->isAvailiable()) {

            org_tubepress_impl_log_Log::log($this->logPrefix(), 'Not available on this installation.');
            return false;
        }

        $compressed = $response->getEntity()->getContent();

        if (! is_string($compressed)) {

            throw new Exception('Can only decompress string data');
        }

        org_tubepress_impl_log_Log::log($this->logPrefix(), 'Attempting to decompress data...');

        /* this will throw an exception if we couldn't decompress it. */
        $uncompressed = $this->getUncompressed($compressed);

        /* do some logging. */
        if (org_tubepress_impl_log_Log::isEnabled()) {

            $this->_logSuccess(strlen($compressed), strlen($uncompressed));
        }

        $context->decoded = $uncompressed;

        /* signal that we've handled execution. */
        return true;
    }

    /**
     * Get the uncompressed version of the given data.
     *
     * @param string $compressed The compressed data.
     *
     * @return string The uncompressed data.
     */
    protected abstract function getUncompressed($compressed);

    /**
     * Get the "friendly" name for logging purposes.
     *
     * @return string The "friendly" name of this compression.
     */
    protected abstract function getDecompressionName();

    /**
     * Determines if this compression is available on the host system.
     *
     * @return boolean True if compression is available on the host system, false otherwise.
     */
    protected abstract function isAvailiable();

    /**
     * Get the Content-Encoding header value that this command can handle.
     *
     * @return string The Content-Encoding header value that this command can handle.
     */
    protected abstract function getExpectedContentEncodingHeaderValue();

    /**
     * A friendly log prefix for this command.
     *
     * @return string A friendly log prefix for this command.
     */
    protected function logPrefix()
    {
        return $this->getDecompressionName() . ' Decompressor';
    }

    private function _logSuccess($before, $after)
    {
        $ratio = 100;

        if ($before != 0) {

            $ratio = number_format(($after / $before) * 100, 2);
        }

        org_tubepress_impl_log_Log::log($this->logPrefix(), 'Successfully decoded entity with %s. Result is %s' . '%% of the original size (%s / %s).',
            $this->logPrefix(), $ratio, $after, $before);
    }
}
