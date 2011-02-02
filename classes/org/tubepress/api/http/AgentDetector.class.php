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
 * HTTP client detection service. Yanked just about all of this code from
 * http://mobileesp.googlecode.com/.
 */
interface org_tubepress_api_http_AgentDetector
{
    /**
     * Detects whether the device is an iPhone
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is an iPhone, false otherwise.
     */
    function isIphone($agent);

    /**
     * Detects whether the device is an iPod
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is an iPod, false otherwise.
     */
    function isIpod($agent);

    /**
     * Detects whether the device is an iPad
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is an iPad, false otherwise.
     */
    function isIpad($agent);

    /**
     * Detects whether the device is an iPhone or iPad.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is an iPod or iPad, false otherwise.
     */
    function isIphoneOrIpod($agent);

    /**
     * Detects whether the device is an Android phone.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is an Android phone, false otherwise.
     */
    function isAndroid($agent);

    /**
     * Detects whether the device is an Android phone running WebKit
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is an Android phone running WebKit, false otherwise.
     */
    function isAndroidWebKit($agent);

    /**
     * Detects whether the agent is running WebKit.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is running WebKit, false otherwise.
     */
    function isWebkit($agent);

    /**
     * Detects whether the device is running the S60 platform
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is running the S60 platform, false otherwise.
     */
    function isS60OsBrowser($agent);

    /**
     * Detects if the current device is any Symbian OS-based device,
     *   including older S60, Series 70, Series 80, Series 90, and UIQ,
     *   or other browsers running on these devices.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is any Symbian OS-based device, false otherwise.
     */
    function isSymbianOS($agent);

    /**
     * Detects if the current browser is a Windows Phone 7 device.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is a Windows Phone 7 device, false otherwise.
     */
    function isWindowsPhone7($agent);

    /**
     *  Detects if the current browser is a Windows Mobile device.
     *   Excludes Windows Phone 7 devices.
     *   Focuses on Windows Mobile 6.xx and earlier.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is a Windows Mobile device, false otherwise.
     */
    function isWindowsMobile($agent);

    /**
     * Detects if the current browser is a BlackBerry of some sort.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is a BlackBerry, false otherwise.
     */
    function isBlackBerry($agent);

    /**
     * Detects if the current browser is a BlackBerry Touch
     *    device, such as the Storm.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is a BlackBerry touch, false otherwise.
     */
    function isBlackBerryTouch($agent);

    /**
     * Detects if the current browser is a BlackBerry device AND
     *    has a more capable recent browser.
     *    Examples, Storm, Bold, Tour, Curve2
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is a "high" BlackBerry, false otherwise.
     */
    function isBlackBerryHigh($agent);

    /**
     * Detects if the current browser is a BlackBerry device AND
     *    has an older, less capable browser.
     *    Examples: Pearl, 8800, Curve1.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is a "low" BlackBerry, false otherwise.
     */
    function isBlackBerryLow($agent);

    /**
     * Detects if the current browser is on a PalmOS device.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is a PalmOS device, false otherwise.
     */
    function isPalmOS($agent);

    /**
     * Detects if the current browser is running WebOS.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is running WebOS, false otherwise.
     */
    function isPalmWebOS($agent);

    /**
     * Detects if the current browser is a
     *   Garmin Nuvifone.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is a Garmin Nuvifone, false otherwise.
     */
    function isGarminNuvifone($agent);

    /**
     * Check to see whether the device is any device
     *   in the 'smartphone' category.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is a "smartphone", false otherwise.
     */
    function isSmartphone($agent);

    /**
     * Detects whether the device is a Brew-powered device.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is Brew powered, false otherwise.
     */
    function isBrewDevice($agent);

    /**
     * Detects the Danger Hiptop device.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is a Danger Hiptop device, false otherwise.
     */
    function isDangerHiptop($agent);

    /**
     * Detects if the current browser is Opera Mobile or Mini.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is running Opera Mobile or Mini, false otherwise.
     */
    function isOperaMobile($agent);

    /**
     * Detects whether the device supports WAP or WML.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent supports WAP or WML, false otherwise.
     */
    function isWapOrWml($agent);

    /**
     * Detects if the current device is an Amazon Kindle.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is an Amazon Kindle, false otherwise.
     */
    function isKindle($agent);

    /**
     * The quick way to detect for a MOBILE device.
     *   Will probably detect most recent/current mid-tier Feature Phones
     *   as well as smartphone-class devices. Excludes Apple iPads.
     *
     * @param array $serverVars The PHP $_SERVER variable.
     *
     * @return boolean True if the agent is a mobile device, false otherwise.
     */
    function isMobileQuick($serverVars);

    /**
     * Detects if the current device is a Sony Playstation.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is a Sony Playstation, false otherwise.
     */
    function isSonyPlaystation($agent);

    /**
     * Detects if the current device is a Nintendo game device.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is a Nintendo game device, false otherwise.
     */
    function isNintendo($agent);

    /**
     * Detects if the current device is a Microsoft Xbox.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is a Microsoft Xbox, false otherwise.
     */
    function isXbox($agent);

    /**
     * Detects if the current device is an Internet-capable game console.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is an Internet-capable game console, false otherwise.
     */
    function isGameConsole($agent);

    /**
     * Detects if the current device supports MIDP, a MOBILE Java technology.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent supports MIDP, false otherwise.
     */
    function isMidpCapable($agent);

    /**
     * Detects if the current device is on one of the Maemo-based Nokia Internet Tablets.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is on one of the Maemo-based Nokia Internet Tablets, false otherwise.
     */
    function isMaemoTablet($agent);

    /**
     * Detects if the current device is an Archos media player/Internet tablet.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is an iPhone-tier phone, false otherwise.
     */
    function isArchos($agent);

    /**
     * Detects if the current browser is a Sony Mylo device.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is an iPhone-tier phone, false otherwise.
     */
    function isSonyMylo($agent);

    /**
     * The longer and more thorough way to detect for a MOBILE device.
     *   Will probably detect most feature phones,
     *   smartphone-class devices, Internet Tablets,
     *   Internet-enabled game consoles, etc.
     *   This ought to catch a lot of the more obscure and older devices, also --
     *   but no promises on thoroughness!
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is an iPhone-tier phone, false otherwise.
     */
    function isMobileLong($agent);

    /**
     * The quick way to detect for a tier of devices.
     *   This method detects for devices which can
     *   display iPhone-optimized web content.
     *   Includes iPhone, iPod Touch, Android, WebOS, etc.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is an iPhone-tier phone, false otherwise.
     */
    function isTierIphone($agent);

    /**
     * The quick way to detect for a tier of devices.
     *   This method detects for devices which are likely to be capable 
     *   of viewing CSS content optimized for the iPhone, 
     *   but may not necessarily support JavaScript.
     *   Excludes all iPhone Tier devices.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is an iPhone-tier phone, false otherwise.
     */
    function isTierRichCss($agent);

    /**
     * The quick way to detect for a tier of devices.
     * This method detects for all other types of phones,
     * but excludes the iPhone and RichCSS Tier devices.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is an "Other" tier phone, false otherwise.
     */
    function isTierOtherPhones($agent);

    /**
     * Safe-get of the HTTP agent header
     *
     * @param array $serverVars The PHP $_SERVER array.
     *
     * @return string The value of the HTTP agent header, of '' if not found
     */
    function getHttpAgent($serverVars);
}
