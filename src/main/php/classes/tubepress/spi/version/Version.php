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
 * An OSGi-based version.
 *
 * http://www.osgi.org/wiki/uploads/Links/SemanticVersioning.pdf
 * http://www.osgi.org/javadoc/r4v43/org/osgi/framework/Version.html
 * http://trac.i2p2.de/browser/src/main/java/org/osgi/framework/Version.java?rev=c113cdcdaa29451f600437c9275762580386dbcf
 *
 */
class tubepress_spi_version_Version
{
    /** Version separator. */
    private static $_SEPARATOR = '.';

    /** Major version. */
    private $_major = 0;

    /** Minor version. */
    private $_minor = 0;

    /** Micro version. */
    private $_micro = 0;

    /** Qualifier. */
    private $_qualifier = null;

    /** Cached string representation. */
    private $_asString = null;

    public function __construct($major, $minor = 0, $micro = 0, $qualifier = '')
    {
        $this->_major = intval($major);
        $this->_minor = intval($minor);
        $this->_micro = intval($micro);

        if ($qualifier == '') {

            $this->_qualifier = null;

        } else {

            $this->_qualifier = $qualifier;
        }

        $this->_validate();

        $this->_asString = $this->_generateAsString();
    }

    public function compareTo($otherVersion)
    {
        if (!($otherVersion instanceof tubepress_spi_version_Version)) {

            return $this->compareTo(self::parse($otherVersion));
        }

        $result = $this->getMajor() - $otherVersion->getMajor();
        if ($result !== 0) {

            return $result;
        }

        $result = $this->getMinor() - $otherVersion->getMinor();
        if ($result !== 0) {

            return $result;
        }

        $result = $this->getMicro() - $otherVersion->getMicro();
        if ($result !== 0) {

            return $result;
        }

        return strcmp($this->getQualifier(), $otherVersion->getQualifier());
    }

    public static function parse($version)
    {
        if (! is_string($version)) {

            throw new InvalidArgumentException('Can only parse strings to generate version');
        }

        $empty = new tubepress_spi_version_Version(0, 0, 0);

        if ($version == '' || trim($version) == '') {

            return $empty;
        }

        $pieces = explode(self::$_SEPARATOR, $version);
        $pieceCount = count($pieces);

        switch ($pieceCount) {

            case 1:

                return new tubepress_spi_version_Version(self::_validateNumbersOnly($version));

            case 2:

                return new tubepress_spi_version_Version(self::_validateNumbersOnly($pieces[0]), self::_validateNumbersOnly($pieces[1]));

            case 3:

                return new tubepress_spi_version_Version(self::_validateNumbersOnly($pieces[0]), self::_validateNumbersOnly($pieces[1]), self::_validateNumbersOnly($pieces[2]));

            case 4:

                return new tubepress_spi_version_Version(self::_validateNumbersOnly($pieces[0]), self::_validateNumbersOnly($pieces[1]), self::_validateNumbersOnly($pieces[2]), $pieces[3]);

            default:

                throw new InvalidArgumentException("Invalid version: $version");
        }

    }

    public function __toString()
    {
        return $this->_asString;
    }

    public function getMajor()
    {
        return $this->_major;
    }

    public function getMinor()
    {
        return $this->_minor;
    }

    public function getMicro()
    {
        return $this->_micro;
    }

    public function getQualifier()
    {
        return $this->_qualifier;
    }

    private function _generateAsString()
    {
        $toReturn = $this->_major . self::$_SEPARATOR . $this->_minor . self::$_SEPARATOR . $this->_micro;

        if ($this->_qualifier !== null) {

            $toReturn = $toReturn . self::$_SEPARATOR . $this->_qualifier;
        }

        return $toReturn;
    }

    private function _validate()
    {
        self::_checkNonNegativeInteger($this->_major, 'Major');
        self::_checkNonNegativeInteger($this->_minor, 'Minor');
        self::_checkNonNegativeInteger($this->_micro, 'Micro');

        if ($this->_qualifier !== null && preg_match_all('/^(?:[0-9a-zA-Z_\-]+)$/', $this->_qualifier, $matches) !== 1) {

            throw new InvalidArgumentException("Version qualifier must only consist of alphanumerics plus hyphen and underscore (" . $this->_qualifier . ")");
        }
    }

    private static function _checkNonNegativeInteger($candidate, $name)
    {
        if ($candidate < 0) {

            throw new InvalidArgumentException("$name version must be non-negative ($candidate)");
        }
    }

    private static function _validateNumbersOnly($candidate)
    {
        if (preg_match_all('/^[0-9]+$/', $candidate, $matches) !== 1) {

            throw new InvalidArgumentException("$candidate is not a number");
        }

        return $candidate;
    }
}
