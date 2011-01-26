<?php
/**
 * Copyright 2006 - 2010 Eric D. Hough (http://ehough.com)
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
 * Lifted from http://core.trac.wordpress.org/browser/tags/3.0.4/wp-includes/class-http.php
 *
 * Base HTTP strategy.
 */
abstract class org_tubepress_impl_http_clientimpl_strategies_AbstractHttpStrategy implements org_tubepress_api_patterns_Strategy
{
    /**
     * Execute an HTTP request.
     *
     * @return array 'headers', 'body', 'cookies' and 'response' keys.
     */
    public function execute()
    {
        $defaults = array(
            'method'      => 'GET', 
            'timeout'     => 5,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => array(),
            'body'        => null,
            'cookies'     => array()
        );

        $r = array_merge($defaults, $args);

        if (isset($r['headers']['User-Agent'])) {
            $r['user-agent'] = $r['headers']['User-Agent'];
            unset($r['headers']['User-Agent']);
        }

        // Construct Cookie: header if any cookies are set.
        org_wordpress_HttpClient::buildCookieHeader($r);

        return $this->_doExecute($url, $args);
    }

    /**
     * Called *before* canHandle() and execute() to allow the strategy
     *  to initialize itself.
     *
     * @return void
     */
    function start()
    {
        return; //do nothing
    }
    
    /**
     * Called *after* canHandle() and execute() to allow the strategy
     *  to tear itself down.
     *
     * @return void
     */
    function stop()
    {
        return; //do nothing
    }

    protected abstract _doExecute($url, $args);
}
