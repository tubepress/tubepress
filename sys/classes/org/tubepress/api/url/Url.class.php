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

/**
 * URL class. http://www.ietf.org/rfc/rfc3986.txt.
 *
 *    foo://username:password@example.com:8042/over/there?name=ferret#nose
 *    \_/   \______/ \______/ \______________/\_________/ \_________/ \__/
 *     |        |        |           |             |           |        |
 *  scheme    user      pass      authority       path        query   fragment
 */
class org_tubepress_api_url_Url
{
    const _ = 'org_tubepress_api_url_Url';

    //unreserved = '\w0-9\-\.\~';
    //pct_encoded = '%[A-Fa-f0-9]{2}';
    //sub_delims = '!\$&\'\(\)\*\+,;=';
    //gen_delims = ':\/\?#\[\]@';
    //pchar = '(?:[\w0-9\-\.\~]*(?:%[A-Fa-f0-9]{2})*[!\$&\'\(\)\*\+,;=]*:*@*)';

    /** scheme        = ALPHA *( ALPHA / DIGIT / "+" / "-" / "." ) */
    private static $_regexScheme = '[a-z][a-z0-9\+-\.]*';

    /** userinfo      = *( unreserved / pct-encoded / sub-delims ) */
    private static $_regexUser = '(?:[\w0-9\-\.\~]*(?:%[A-Fa-f0-9]{2})*[!\$&\'\(\)\*\+,;=]*)';

    /** http://forums.intermapper.com/viewtopic.php?t=452 */
    private static $_regexIpv6Dartware = '\s*(?:(?:(?:[0-9A-Fa-f]{1,4}:){7}(?:[0-9A-Fa-f]{1,4}|:))|(?:(?:[0-9A-Fa-f]{1,4}:){6}(?::[0-9A-Fa-f]{1,4}|(?:(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(?:\.(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(?:(?:[0-9A-Fa-f]{1,4}:){5}(?:(?:(?::[0-9A-Fa-f]{1,4}){1,2})|:(?:(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(?:\.(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(?:(?:[0-9A-Fa-f]{1,4}:){4}(?:(?:(?::[0-9A-Fa-f]{1,4}){1,3})|(?:(?::[0-9A-Fa-f]{1,4})?:(?:(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(?:\.(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(?:(?:[0-9A-Fa-f]{1,4}:){3}(?:(?:(?::[0-9A-Fa-f]{1,4}){1,4})|(?:(?::[0-9A-Fa-f]{1,4}){0,2}:(?:(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(?:\.(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(?:(?:[0-9A-Fa-f]{1,4}:){2}(?:(?:(?::[0-9A-Fa-f]{1,4}){1,5})|(?:(?::[0-9A-Fa-f]{1,4}){0,3}:(?:(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(?:\.(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(?:(?:[0-9A-Fa-f]{1,4}:){1}(?:(?:(?::[0-9A-Fa-f]{1,4}){1,6})|(?:(?::[0-9A-Fa-f]{1,4}){0,4}:(?:(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(?:\.(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(?::(?:(?:(?::[0-9A-Fa-f]{1,4}){1,7})|(?:(?::[0-9A-Fa-f]{1,4}){0,5}:(?:(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(?:\.(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:)))(?:%.+)?\s*';

    /** http://stackoverflow.com/questions/5284147/validating-ipv4-addresses-with-regexp/5284410#5284410 */
    private static $_regexIpv4 = '(?:(?:[0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}(?:[0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])';

    /** http://stackoverflow.com/questions/106179/regular-expression-to-match-hostname-or-ip-address */
    private static $_regexHostname = '(?:(?:[a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*(?:[A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]*[A-Za-z0-9])+';

    /** http://www.php.net/manual/en/function.parse-url.php#90365 */
    private static $_regexPathSegment = '(?:[a-z0-9-._~!$&\'()*+,;=:@]|%[0-9a-f]{2})+';

    /** http://www.php.net/manual/en/function.parse-url.php#90365 */
    private static $_regexPathCharacter = '(?:[a-z0-9-._~!$&\'()*+,;=:@\/]|%[0-9a-f]{2})';

    /** begins with "/" but not "//" */
    private static $_regexPathAbsolute = '/(?:[a-z0-9-._~!$&\'()*+,;=:@/]|%[0-9a-f]{2})*';

    /** query         = *( pchar / "/" / "?" ) */
    private static $_regexQueryOrFragment = '(?:(?:[\w0-9\-\.\~]*(?:%[A-Fa-f0-9]{2})*[!\$&\'\(\)\*\+,;=]*:*@*)*\/*\?*)*';

    private $_scheme;

    private $_user;

    private $_host;

    private $_port;

    private $_path;

    private $_encodedQuery;

    private $_fragment;

    /**
     * Constructor.
     *
     * @param string $url An absolute URL.
     */
    public function __construct($url)
    {
        /* http://www.php.net/manual/en/function.parse-url.php#90365 */

        $regex  = '(?:(' . self::$_regexScheme . ')://)?';                                  //scheme
        $regex .= '(?:';
        $regex .=   '(?:(' . self::$_regexUser . ')@)?';                                    //user
        $regex .=   "(?:\[(" . self::$_regexIpv6Dartware . ")\])?";                        //IPv6
        $regex .=   "((?:" . self::$_regexHostname . ")|(?:" . self::$_regexIpv4 . "))?";  //IPv4
        $regex .=   '(?::(\d*))?';                                                           //port
        $regex .=   '(' . self::$_regexPathAbsolute . ')?|(/?' . self::$_regexPathSegment . self::$_regexPathCharacter . "*)?";
        $regex .= ")";
        $regex .= "(?:\?(" . self::$_regexQueryOrFragment . "))?";
        $regex .= "(?:#(" . self::$_regexQueryOrFragment . "))?";

        preg_match("`$regex`i", $url, $match);

        $matchLength = count($match);

        if ($matchLength < 4) {

            throw new Exception("Invalid URL ($url)");
        }

        switch ($matchLength) {

        case 10:

            $this->setFragment($match[9]);

        case 9:

            $this->setQuery($match[8]);

        case 8:

            if ($match[7]) {

                $this->setPath($match[7]);
            }

        case 7:

            $this->setPath($match[6] . $this->getPath());

        case 6:

            if ($match[5]) {

                $this->setPort($match[5]);
            }

        case 5:

            $this->setHost($match[3] ? $match[3] : $match[4]);

        case 4:

            $this->setUser($match[2]);

        case 3:

            $this->setScheme($match[1]);
        }
    }

    /**
     * Set the scheme for this URL (e.g. HTTP, HTTPS, FTP, etc)
     *
     * @param string $scheme The scheme for this URL.
     *
     * @throws Exception If the provided scheme is not a string or is malformed.
     *
     * @return void
     */
    public function setScheme($scheme)
    {
        if (! is_string($scheme)) {

            throw new Exception("Scheme must be a string ($scheme)");
        }

        $scheme = strtolower($scheme);

        if (preg_match_all('`^' . self::$_regexScheme . '$`', $scheme, $matches) !== 1) {

            throw new Exception('Scheme names consist of a sequence of characters beginning with a'
               . ' letter and followed by any combination of letters, digits, plus ("+"), period (".")'
               . ', or hyphen ("-")');
        }

        $this->_scheme = $scheme;
    }

    /**
     * Set the user for this URL.
     *
     * @param string $user The user name to send.
     *
     * @throws Exception If the supplied username is in invalid syntax.
     *
     * @return void
     */
    public function setUser($user)
    {
        $regex = '`^' . self::$_regexUser . '$`i';

        if (preg_match_all($regex, $user, $matches) !== 1) {

            throw new Exception('User must match ' . $regex);
        }

        $this->_user = $user;
    }

    /**
     * Sets the host for this URL.
     *
     * @param string $host The hostname or IP address for this URL.
     *
     * @throws Exception If the given host is not an IP address or hostname.
     *
     * @return void
     */
    public function setHost($host)
    {
        if (! (self::_isHostname($host) || self::_isIpAddress($host))) {

            throw new Exception("Invalid host ($host)");
        }

        $this->_host = strtolower(trim($host));
    }

    /**
     * Sets the host name.
     *
     * @param string $host The hostname.
     *
     * @throws Exception If the supplied hostname is invalid.
     *
     * @return void
     */
    public function setHostName($host)
    {
        if (! self::_isHostname($host)) {

            throw new Exception("Invalid host name ($host)");
        }

        $this->setHost($host);
    }

    public function setHostIpv4($host)
    {
        if (! self::_isIpv4Address($host)) {

            throw new Exception("Invalid IPv4 ($host)");
        }

        $this->setHost($host);
    }

    public function setHostIpv6($host)
    {
        if (! self::_isIpv6Address($host)) {

            throw new Exception("Invalid IPv6 ($host)");
        }

        $this->setHost($host);
    }

    public function setPort($port)
    {
        if (! is_numeric($port)) {

            throw new Exception("Port must be numeric ($port)");
        }

        $intPort = intval($port);

        if (! is_int($intPort) || $intPort < 1) {

            throw new Exception("Port must be a positive integer ($port)");
        }

        $this->_port = $intPort;
    }

    public function setPath($path)
    {
        if ($path == '') {

            return;
        }

        if (! is_string($path)) {

            throw new Exception("Path must be a string ($path)");
        }

        if (preg_match_all('`^' . self::$_regexPathAbsolute . '|/?' . self::$_regexPathSegment . self::$_regexPathCharacter . '*$`i', $path, $matches) !== 1) {

            throw new Exception("Invalid path ($path)");
        }

        $this->_path = $path;
    }

    public function setQuery($query)
    {
        if (! is_string($query)) {

            throw new Exception("Query must be a string ($query)");
        }

        if (preg_match_all('`^' . self::$_regexQueryOrFragment . '$`i', $query, $matches) !== 1) {

            throw new Exception("Invalid query ($query)");
        }

        $this->_encodedQuery = $query;
    }

    public function setFragment($fragment)
    {
        if (! is_string($fragment)) {

            throw new Exception("Fragment must be a string ($fragment)");
        }

        if (preg_match_all('`^' . self::$_regexQueryOrFragment . '$`i', $fragment, $matches) !== 1) {

            throw new Exception("Invalid fragment ($fragment)");
        }

        $this->_fragment = $fragment;
    }

    /**
     * Get this URL's authority.
     * user@host:22
     * user:pass@foo
     * host.com:34
     *
     * @return string The authority of this URL.
     */
    public function getAuthority()
    {
        return ( $this->_user ? $this->_user . '@' : '') . $this->_host . (isset($this->_port) ? ':' . $this->_port : '');
    }

    public function getScheme()
    {
        return $this->_scheme;
    }

    public function getUser()
    {
        return $this->_user;
    }

    public function getHost()
    {
        return $this->_host;
    }

    public function getPort()
    {
        return $this->_port;
    }

    public function getPath()
    {
        return $this->_path;
    }

    public function getQuery()
    {
        return $this->_encodedQuery;
    }

    public function getFragment()
    {
        return $this->_fragment;
    }

    /**
     * Sets the query string to the specified variable in the query stsring.
     *
     * @param array $array (name => value) array
     *
     * @return void
     */
    public function setQueryVariables($array)
    {
        if (! is_array($array)) {

            throw new Exception('Must pass an array to setQueryVariables()');
        }

        $parts = array();

        foreach ($array as $name => $value) {

            $name = urlencode($name);

            if (! is_null($value)) {

                $parts[] = $name . '=' . urlencode($value);

            } else {

                $parts[] = $name;
            }
        }

        $this->setQuery(implode('&', $parts));
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
     * Returns the query string like an array as the variables would appear in
     * $_GET in a PHP script. If the URL does not contain a "?", an empty array
     * is returned.
     *
     * @return  array
     */
    public function getQueryVariables()
    {
        $pattern = '`[' . preg_quote('&', '/') . ']`';
        $parts   = preg_split($pattern, $this->_encodedQuery, -1, PREG_SPLIT_NO_EMPTY);
        $return  = array();

        foreach ($parts as $part) {

            if (strpos($part, '=') !== false) {

                list($key, $value) = explode('=', $part, 2);

            } else {

                $key   = $part;
                $value = null;
            }

            $key          = urldecode($key);
            $value        = urldecode($value);
            $return[$key] = $value;
        }

        return $return;
    }

    public function toString()
    {
        $toReturn = $this->getScheme() . '://' . $this->_getAuthorityAsString() . $this->getPath();

        if ($this->getQuery()) {

            $toReturn .= '?' . $this->getQuery();
        }

        if ($this->getFragment()) {

            $toReturn .= '#' . $this->getFragment();
        }

        return $toReturn;
    }

    public function __toString()
    {
        return $this->toString();
    }

    private function _getAuthorityAsString()
    {
        if (self::_isIpv6Address($this->_host)) {

            $host = '[' . $this->_host . ']';

        } else {

            $host = $this->_host;
        }

        return ( $this->_user ? $this->_user . '@' : '') . $host . (isset($this->_port) ? ':' . $this->_port : '');
    }

    private static function _isHostname($name)
    {
        return is_string($name) && preg_match_all('`^' . self::$_regexHostname . '$`i', strtolower(trim($name)), $matches) === 1;
    }

    private static function _isIpv4Address($ip)
    {
        return is_string($ip) && preg_match_all('`^' . self::$_regexIpv4 . '$`', strtolower(trim($ip)), $matches) === 1;
    }

    private static function _isIpv6Address($ip)
    {
        return is_string($ip) && preg_match_all('`^[:\.0-9a-f]+$`i', strtolower(trim($ip)), $matches) === 1
            && preg_match_all('`^' . self::$_regexIpv6Dartware . '$`i', strtolower(trim($ip)), $matches) === 1;
    }

    private static function _isIpAddress($ip)
    {
        return self::_isIpv4Address($ip) || self::_isIpv6Address($ip);
    }

}
