<?php
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
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

/**
 * URL class. Lifted mostly from PEAR's Net_URL2 class.
 */
class org_tubepress_api_url_Url
{
    /**
     * @var  string|bool
     */
    private $_scheme = false;

    /**
     * @var  string|bool
     */
    private $_userinfo = false;

    /**
     * @var  string|bool
     */
    private $_host = false;

    /**
     * @var  string|bool
     */
    private $_port = false;

    /**
     * @var  string
     */
    private $_path = '';

    /**
     * @var  string|bool
     */
    private $_query = false;

    /**
     * @var  string|bool
     */
    private $_fragment = false;

    /**
     * Constructor.
     *
     * @param string $url     an absolute or relative URL
     */
    public function __construct($url)
    {
        // The regular expression is copied verbatim from RFC 3986, appendix B.
        // The expression does not validate the URL but matches any string.
        preg_match('!^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?!', $url, $matches);

        // "path" is always present (possibly as an empty string); the rest
        // are optional.
        $this->_scheme = !empty($matches[1]) ? $matches[2] : false;
        $this->setAuthority(!empty($matches[3]) ? $matches[4] : false);
        $this->_path = $matches[5];
        $this->_query = !empty($matches[6]) ? $matches[7] : false;
        $this->_fragment = !empty($matches[8]) ? $matches[9] : false;
    }

    /**
     * Sets the query string to the specified variable in the query string.
     *
     * @param array $array (name => value) array
     *
     * @return void
     */
    public function setQueryVariables(array $array)
    {
        if (!$array) {
            $this->_query = false;
        } else {
            foreach ($array as $name => $value) {
                $name = self::urlencode($name);

                if (is_array($value)) {
                    foreach ($value as $k => $v) {
                        $parts[] = sprintf('%s[%s]=%s', $name, $k, $v);
                    }
                } elseif (!is_null($value)) {
                    $parts[] = $name . '=' . self::urlencode($value);
                } else {
                    $parts[] = $name;
                }
            }
            $this->_query = implode('&', $parts);
        }
    }

    /**
     * Sets the specified variable in the query string.
     *
     * @param string $name  variable name
     * @param mixed  $value variable value
     *
     * @return  array
     */
    public function setQueryVariable($name, $value)
    {
        $array = $this->getQueryVariables();
        $array[$name] = $value;
        $this->setQueryVariables($array);
    }

    /**
     * Removes the specifed variable from the query string.
     *
     * @param string $name a query string variable, e.g. "foo" in "?foo=1"
     *
     * @return void
     */
    public function unsetQueryVariable($name)
    {
        $array = $this->getQueryVariables();
        unset($array[$name]);
        $this->setQueryVariables($array);
    }

    /**
     * Returns a string representation of this URL.
     *
     * @return  string
     */
    public function toString($encodeAmpersands = false)
    {
        // See RFC 3986, section 5.3
        $url = "";

        if ($this->_scheme !== false) {
            $url .= $this->_scheme . ':';
        }

        $authority = $this->getAuthority();
        if ($authority !== false) {
            $url .= '//' . $authority;
        }
        $url .= $this->_path;

        if ($this->_query !== false) {
            $url .= '?' . $this->_query;
        }

        if ($this->_fragment !== false) {
            $url .= '#' . $this->_fragment;
        }
        
        if ($encodeAmpersands) {
            return str_replace("&", "&amp;", $url);
        }

        return $url;
    }

    /**
     * Percent-encodes all non-alphanumeric characters except these: _ . - ~
     * Similar to PHP's rawurlencode(), except that it also encodes ~ in PHP
     * 5.2.x and earlier.
     *
     * @param  $raw the string to encode
     * @return string
     */
    public static function urlencode($string)
    {
        $encoded = rawurlencode($string);

        // This is only necessary in PHP < 5.3.
        $encoded = str_replace('%7E', '~', $encoded);
        return $encoded;
    }

    /**
     * Sets the authority part, i.e. [ userinfo "@" ] host [ ":" port ]. Specify
     * false if there is no authority.
     *
     * @param string|false $authority a hostname or an IP addresse, possibly
     *                                with userinfo prefixed and port number
     *                                appended, e.g. "foo:bar@example.org:81".
     *
     * @return void
     */
    public function setAuthority($authority)
    {
        $this->_userinfo = false;
        $this->_host     = false;
        $this->_port     = false;
        if (preg_match('@^(([^\@]*)\@)?([^:]+)(:(\d*))?$@', $authority, $reg)) {
            if ($reg[1]) {
                $this->_userinfo = $reg[2];
            }

            $this->_host = $reg[3];
            if (isset($reg[5])) {
                $this->_port = $reg[5];
            }
        }
    }

    /**
     * Returns the authority part, i.e. [ userinfo "@" ] host [ ":" port ], or
     * false if there is no authority.
     *
     * @return string|bool
     */
    public function getAuthority()
    {
        if (!$this->_host) {
            return false;
        }

        $authority = '';

        if ($this->_userinfo !== false) {
            $authority .= $this->_userinfo . '@';
        }

        $authority .= $this->_host;

        if ($this->_port !== false) {
            $authority .= ':' . $this->_port;
        }

        return $authority;
    }

    /**
     * Returns the query string like an array as the variables would appear in
     * $_GET in a PHP script. If the URL does not contain a "?", an empty array
     * is returned.
     *
     * @return  array
     */
    public function getQueryVariables()
    {
        $pattern = '/[' . preg_quote('&', '/') . ']/';
        $parts   = preg_split($pattern, $this->_query, -1, PREG_SPLIT_NO_EMPTY);
        $return  = array();

        foreach ($parts as $part) {
            if (strpos($part, '=') !== false) {
                list($key, $value) = explode('=', $part, 2);
            } else {
                $key   = $part;
                $value = null;
            }

            $key = rawurldecode($key);
            $value = rawurldecode($value);

            if (preg_match('#^(.*)\[([0-9a-z_-]*)\]#i', $key, $matches)) {

                $key = $matches[1];
                $idx = $matches[2];

                // Ensure is an array
                if (empty($return[$key]) || !is_array($return[$key])) {
                    $return[$key] = array();
                }

                // Add data
                if ($idx === '') {
                    $return[$key][] = $value;
                } else {
                    $return[$key][$idx] = $value;
                }
            } else {
                $return[$key] = $value;
            }
        }

        return $return;
    }

}
