<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_platform_impl_boot_helper_uncached_Serializer
{
    /**
     * @var tubepress_platform_api_boot_BootSettingsInterface
     */
    private $_bootSettings;

    public function __construct(tubepress_platform_api_boot_BootSettingsInterface $bootSettings)
    {
        $this->_bootSettings = $bootSettings;
    }

    public function serialize($incomingData)
    {
        $serialized = @serialize($incomingData);

        if ($serialized === false) {

            throw new InvalidArgumentException('Failed to serialize data');
        }

        switch ($this->_bootSettings->getSerializationEncoding()) {

            /** @noinspection PhpMissingBreakStatementInspection */
            case 'gzip-then-base64':

                if (extension_loaded('zlib')) {

                    $toCompress = $serialized;
                    $compressed = gzcompress($toCompress);

                    if ($compressed !== false) {

                        $serialized = $compressed;
                    }
                }

            case 'base64':

                $encoded = @base64_encode($serialized);

                if ($encoded === false) {

                    throw new InvalidArgumentException('Failed to base64_encode() serialized data');
                }

                return $encoded;

            case 'urlencode':

                return urlencode($serialized);

            default:

                return $serialized;
        }
    }

    /**
     * @param string $serializedString
     *
     * @return tubepress_platform_api_contrib_ContributableInterface[]
     *
     * @throws InvalidArgumentException
     */
    public function unserialize($serializedString)
    {
        $decoded  = $serializedString;
        $encoding = $this->_bootSettings->getSerializationEncoding();

        switch ($encoding) {

            /** @noinspection PhpMissingBreakStatementInspection */
            case 'gzip-then-base64':
            case 'base64':

                $decoded = @base64_decode($serializedString);

                if ($decoded === false) {

                    throw new InvalidArgumentException('Failed to base64_decode() serialized data');
                }

                if ($encoding === 'gzip-then-base64') {

                    $decoded = gzuncompress($decoded);

                    if ($decoded === false) {

                        throw new InvalidArgumentException('Failed to gzuncompress() serialized data');
                    }
                }

                break;

            case 'urlencode':

                $decoded = urldecode($serializedString);
                break;

            default:
                break;
        }

        $unserialized = @unserialize($decoded);

        if ($unserialized === false) {

            throw new InvalidArgumentException('Failed to unserialize incoming data');
        }

        return $unserialized;
    }
}