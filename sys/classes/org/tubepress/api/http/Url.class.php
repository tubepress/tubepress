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
 * URL class. http://www.ietf.org/rfc/rfc3986.txt.
 *
 *    foo://username:password@example.com:8042/over/there?name=ferret#nose
 *    \_/   \______/ \______/ \______________/\_________/ \_________/ \__/
 *     |        |        |           |             |           |        |
 *  scheme    user      pass      authority       path        query   fragment
 */
class org_tubepress_api_http_Url
{
    //unreserved = '\w0-9\-\.\~';
    //pct_encoded = '%[A-Fa-f0-9]{2}';
    //sub_delims = '!\$&\'\(\)\*\+,;=';
    //gen_delims = ':\/\?#\[\]@';
    //pchar = '(?:[\w0-9\-\.\~]*(?:%[A-Fa-f0-9]{2})*[!\$&\'\(\)\*\+,;=]*:*@*)';

    /** scheme        = ALPHA *( ALPHA / DIGIT / "+" / "-" / "." ) */
    private static $_regex_scheme = '[a-z][a-z0-9\+\-\.]*';

    /** userinfo      = *( unreserved / pct-encoded / sub-delims ) */
    private static $_regex_user = '(?:[\w0-9\-\.\~]*(?:%[A-Fa-f0-9]{2})*[!\$&\'\(\)\*\+,;=]*)*';

    /** http://forums.intermapper.com/viewtopic.php?t=452 */
    private static $_regex_ipv6_dartware = '\s*(?:(?:(?:[0-9A-Fa-f]{1,4}:){7}(?:[0-9A-Fa-f]{1,4}|:))|(?:(?:[0-9A-Fa-f]{1,4}:){6}(?::[0-9A-Fa-f]{1,4}|(?:(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(?:\.(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(?:(?:[0-9A-Fa-f]{1,4}:){5}(?:(?:(?::[0-9A-Fa-f]{1,4}){1,2})|:(?:(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(?:\.(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(?:(?:[0-9A-Fa-f]{1,4}:){4}(?:(?:(?::[0-9A-Fa-f]{1,4}){1,3})|(?:(?::[0-9A-Fa-f]{1,4})?:(?:(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(?:\.(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(?:(?:[0-9A-Fa-f]{1,4}:){3}(?:(?:(?::[0-9A-Fa-f]{1,4}){1,4})|(?:(?::[0-9A-Fa-f]{1,4}){0,2}:(?:(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(?:\.(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(?:(?:[0-9A-Fa-f]{1,4}:){2}(?:(?:(?::[0-9A-Fa-f]{1,4}){1,5})|(?:(?::[0-9A-Fa-f]{1,4}){0,3}:(?:(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(?:\.(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(?:(?:[0-9A-Fa-f]{1,4}:){1}(?:(?:(?::[0-9A-Fa-f]{1,4}){1,6})|(?:(?::[0-9A-Fa-f]{1,4}){0,4}:(?:(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(?:\.(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(?::(?:(?:(?::[0-9A-Fa-f]{1,4}){1,7})|(?:(?::[0-9A-Fa-f]{1,4}){0,5}:(?:(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(?:\.(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:)))(?:%.+)?\s*';

    /** http://stackoverflow.com/questions/5284147/validating-ipv4-addresses-with-regexp/5284410#5284410 */
    private static $_regex_ipv4 = '(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)(?:\.|$)){4}';

    /** http://stackoverflow.com/questions/1418423/the-hostname-regex/1420225#1420225 */
    private static $_regex_hostname = '(?=.{1,255}$)[0-9A-Za-z](?:(?:[0-9A-Za-z]|\b-){0,61}[0-9A-Za-z])?(?:\.[0-9A-Za-z](?:(?:[0-9A-Za-z]|\b-){0,61}[0-9A-Za-z])?)*\.?';

    /** begins with "/" but not "//" */
    private static $_regex_path_absolute = '\/(?:(?:[\w0-9\-\.\~]*(?:%[A-Fa-f0-9]{2})*[!\$&\'\(\)\*\+,;=]*:*@*)+(?:\/(?:(?:[\w0-9\-\.\~]*(?:%[A-Fa-f0-9]{2})*[!\$&\'\(\)\*\+,;=]*:*@*)*))*)*';

    /** query         = *( pchar / "/" / "?" ) */
    private static $_regex_query_or_fragment = '(?:(?:[\w0-9\-\.\~]*(?:%[A-Fa-f0-9]{2})*[!\$&\'\(\)\*\+,;=]*:*@*)*\/*\?*)*';

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

        $regex  = '(?:(' . self::$_regex_scheme . ')://)?';                                  //scheme
        $regex .= '(?:';
        $regex .=   '(?:(' . self::$_regex_user . ')@)?';                                    //user
        $regex .=   "(?:\[(" . self::$_regex_ipv6_dartware . ")\])?";                        //IPv6
        $regex .=   "((?:" . self::$_regex_hostname . ")|(?:" . self::$_regex_ipv4 . "))?";  //IPv4
        $regex .=   '(?::(\d*))?';                                                           //port
        $regex .=   '(' . self::$_regex_path_absolute . ')?';                                //path
        $regex .=   "|";
        $regex .=   "(/?";
        $regex .=     "(?:[a-z0-9-._~!$&'()*+,;=:@]|%[0-9a-f]{2})+";
        $regex .=     "(?:[a-z0-9-._~!$&'()*+,;=:@\/]|%[0-9a-f]{2})*";
        $regex .=    ")?";
        $regex .= ")";
        $regex .= "(?:\?(" . self::$_regex_query_or_fragment . "))?";
        $regex .= "(?:#(" . self::$_regex_query_or_fragment . "))?";

        preg_match("`$regex`i", $url, $match);

        $matchLength = count($match);

        if ($matchLength < 4) {

            throw new Exception("Invalid URL ($url)");
        }

        switch ($matchLength) {

            case 10: $this->setFragment($match[9]);
            case 9:  $this->setQuery($match[8]);
            case 8:  if ($match[7]) { $this->setPath($match[7]); }
            case 7:  $this->setPath($match[6] . $this->getPath());
            case 6:  if ($match[5]) { $this->setPort($match[5]); }
            case 5:  $this->setHost($match[3] ? $match[3] : $match[4]);
            case 4:  $this->setUser($match[2]);
            case 3:  $this->setScheme($match[1]);
        }
    }

    public function setScheme($scheme)
    {
        if (! is_string($scheme)) {

            throw new Exception("Scheme must be a string ($scheme)");
        }

        $scheme = strtolower($scheme);

        if (preg_match_all('/^' . self::$_regex_scheme . '$/', $scheme, $matches) !== 1) {

            throw new Exception('Scheme names consist of a sequence of characters beginning with a'
               . ' letter and followed by any combination of letters, digits, plus ("+"), period (".")'
               . ', or hyphen ("-")');
        }

        $this->_scheme = $scheme;
    }

    public function setUser($user)
    {
        $regex = '/^' . self::$_regex_user . '$/';

        if (preg_match_all($regex, $user, $matches) !== 1) {

            throw new Exception('User must match ' . $regex);
        }

        $this->_user = $user;
    }

    public function setHost($host)
    {
        if (! (self::_isHostname($host) || self::_isIpAddress($host))) {

            throw new Exception("Invalid host ($host)");
        }

        $this->_host = strtolower(trim($host));
    }

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

        $port = intval($port);

        if (! is_int($port) || $port < 1) {

            throw new Exception("Port must be a positive integer ($port)");
        }

        $this->_port = $port;
    }

    public function setPath($path)
    {
        if (! is_string($path)) {

            throw new Exception("Path must be a string ($path)");
        }

        if (preg_match_all('/^' . self::$_regex_path_absolute . '$/', $path, $matches) !== 1) {

            throw new Exception("Invalid path ($path)");
        }

        $this->_path = $path;
    }

    public function setQuery($query)
    {
        if (! is_string($query)) {

            throw new Exception("Query must be a string ($query)");
        }

        if (preg_match_all('/^' . self::$_regex_query_or_fragment . '$/', $query, $matches) !== 1) {

            throw new Exception("Invalid query ($query)");
        }

        $this->_encodedQuery = $query;
    }

    public function setFragment($fragment)
    {
        if (! is_string($fragment)) {

            throw new Exception("Fragment must be a string ($fragment)");
        }

        if (preg_match_all('/^' . self::$_regex_query_or_fragment . '$/', $fragment, $matches) !== 1) {

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
        $pattern = '/[' . preg_quote('&', '/') . ']/';
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
        $toReturn = $this->getScheme() . '://' . $this->getAuthority() . $this->getPath();

        if ($this->getQuery()) {

            $toReturn .= '?' . $this->getQuery();
        }

        if ($this->getFragment()) {

            $toReturn .= '#' . $this->getFragment();
        }

        return $toReturn;
    }

    private static function _isHostname($name)
    {
        return is_string($name) && preg_match_all('/^' . self::$_regex_hostname . '$/', strtolower(trim($name)), $matches) === 1;
    }

    private static function _isIpv4Address($ip)
    {
        return is_string($ip) && preg_match_all('/^' . self::$_regex_ipv4 . '$/', strtolower(trim($ip)), $matches) === 1;
    }

    private static function _isIpv6Address($ip)
    {
        return is_string($ip) && preg_match_all('/^[:\.0-9a-f]+$/i', strtolower(trim($ip)), $matches) === 1
            && preg_match_all('/^' . self::$_regex_ipv6_dartware . '$/', strtolower(trim($ip)), $matches) === 1;
    }

    private static function _isIpAddress($ip)
    {
        return self::_isIpv4Address($ip) || self::_isIpv6Address($ip);
    }

}
