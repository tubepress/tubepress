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
 * HTTP client detection service. Yanked just about all of this code from
 * http://mobileesp.googlecode.com/.
 */
class org_tubepress_browser_BrowserDetector
{
    const HTTP_USER_AGENT = 'HTTP_USER_AGENT';
    const HTTP_ACCEPT     = 'HTTP_ACCEPT';

    const ENGINE_WEBKIT    = 'webkit';
    const ENGINE_PIE       = 'wm5 pie';
    const ENGINE_BLAZER    = 'blazer';
    const ENGINE_XIINO     = 'xiino';
    const ENGINE_OPERA     = 'opera';
    const ENGINE_NETFRONT  = 'netfront';
    const ENGINE_UPBROWSER = 'up.browser';
    const ENGINE_OPENWEB   = 'openweb';
    const ENGINE_TELECAQ   = 'teleca q';

    const DEVICE_ANDROID     = 'android';
    const DEVICE_IPHONE      = 'iphone';
    const DEVICE_IPOD        = 'ipod';
    const DEVICE_IPAD        = 'ipad';
    const DEVICE_MAC_PPC     = 'macintosh';
    const DEVICE_NUVIFONE    = 'nuvifone';
    const DEVICE_BREW        = 'brew';
    const DEVICE_DANGER      = 'danger';
    const DEVICE_HIPTOP      = 'hiptop';
    const DEVICE_PLAYSTATION = 'playstation';
    const DEVICE_NINTENDO_DS = 'nitro';
    const DEVICE_NINTENDO    = 'nintendo';
    const DEVICE_WII         = 'wii';
    const DEVICE_XBOX        = 'xbox';
    const DEVICE_ARCHOS      = 'archos';
    const DEVICE_MIDP        = 'midp';
    const DEVICE_PDA         = 'pda';
    const DEVICE_SYMBIAN     = 'symbian';
    const DEVICE_S60         = 'series60';
    const DEVICE_S70         = 'series70';
    const DEVICE_S80         = 'series80';
    const DEVICE_S90         = 'series90';
    const DEVICE_WINPHONE7   = 'windows phone os 7';
    const DEVICE_WINMOB      = 'windows ce';
    const DEVICE_WINDOWS     = 'windows';
    const DEVICE_IEMOBILE    = 'iemobile';
    const DEVICE_PPC         = 'ppc';
    const DEVICE_BB          = 'blackberry';
    const DEVICE_BBSTORM     = 'blackberry95';
    const DEVICE_BBBOLD      = 'blackberry97';
    const DEVICE_BBTOUR      = 'blackberry96';
    const DEVICE_BBCURVE     = 'blackberry89';
    const DEVICE_PALM        = 'palm';
    const DEVICE_WEBOS       = 'webos';
    const DEVICE_KINDLE      = 'kindle';

    const VNDWAP       = 'vnd.wap';
    const WML          = 'wml';
    const VNDRIM       = 'vnd.rim';
    const UPLINK       = 'up.link';
    const MINI         = 'mini';
    const MOBILE       = 'mobile';
    const MOBI         = 'mobi';
    const MAEMO        = 'maemo';
    const MAEMO_TABLET = 'tablet';
    const LINUX        = 'linux';
    const QT_EMBEDDED  = 'qt embedded';
    const MYLOCOM2     = 'com2';
    const SVC_DOCOMO   = 'docomo';
    const SVC_KDDI     = 'kddi';
    const SVC_VODAFONE = 'vodafone';

    const MANU_SONYERICSSON = 'sonyericsson';
    const MANU_ERICSSON     = 'ericsson';
    const MANU_SAMSUNG1     = 'sec-sgh';
    const MANU_SONY         = 'sony';
    const MANU_HTC          = 'htc';

    /**
     * Detects whether the device is an iPhone
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is an iPhone, false otherwise.
     */
    public static function isIphone($agent)
    {
        if (stripos($agent, self::DEVICE_IPHONE) > -1) {

            //The iPad and iPod Touch say they're an iPhone! So let's disambiguate.
            return !(self::isIpad($agent) || self::isIpod($agent));
        }
        return false;
    }

    /**
     * Detects whether the device is an iPod
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is an iPod, false otherwise.
     */
    public static function isIpod($agent)
    {
        return stripos($agent, self::DEVICE_IPOD) > -1;
    }

    /**
     * Detects whether the device is an iPad
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is an iPad, false otherwise.
     */
    public static function isIpad($agent)
    {
        return stripos($agent, self::DEVICE_IPAD) > -1 && self::isWebkit($agent);
    }

    /**
     * Detects whether the device is an iPhone or iPad.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is an iPod or iPad, false otherwise.
     */
    public static function isIphoneOrIpod($agent)
    {
        return stripos($agent, self::DEVICE_IPHONE) > -1 || stripos($agent, self::DEVICE_IPOD) > -1;
    }

    /**
     * Detects whether the device is an Android phone.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is an Android phone, false otherwise.
     */
    public static function isAndroid($agent)
    {
        return stripos($agent, self::DEVICE_ANDROID) > -1;
    }

    /**
     * Detects whether the device is an Android phone running WebKit
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is an Android phone running WebKit, false otherwise.
     */
    public static function isAndroidWebKit($agent)
    {
        return self::isAndroid($agent) && self::isWebkit($agent);
    }

    /**
     * Detects whether the agent is running WebKit.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is running WebKit, false otherwise.
     */
    public static function isWebkit($agent)
    {
        return stripos($agent, self::ENGINE_WEBKIT) > -1;
    }

    /**
     * Detects whether the device is running the S60 platform
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is running the S60 platform, false otherwise.
     */
    public static function isS60OsBrowser($agent)
    {
        if (self::isWebkit($agent)) {
            return stripos($agent, self::DEVICE_SYMBIAN) > -1 || stripos($agent, self::DEVICE_S60) > -1;
        }
        return false;
    }

    /**
     * Detects if the current device is any Symbian OS-based device,
     *   including older S60, Series 70, Series 80, Series 90, and UIQ,
     *   or other browsers running on these devices.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is any Symbian OS-based device, false otherwise.
     */
    public static function isSymbianOS($agent)
    {
        return stripos($agent, self::DEVICE_SYMBIAN) > -1 ||
           stripos($agent, self::DEVICE_S60) > -1 ||
           stripos($agent, self::DEVICE_S70) > -1 ||
           stripos($agent, self::DEVICE_S80) > -1 ||
           stripos($agent, self::DEVICE_S90) > -1;
    }

    /**
     * Detects if the current browser is a Windows Phone 7 device.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is a Windows Phone 7 device, false otherwise.
     */
    public static function isWindowsPhone7($agent)
    {
        return stripos($agent, self::DEVICE_WINPHONE7) > -1;
    }

    /**
     *  Detects if the current browser is a Windows Mobile device.
     *   Excludes Windows Phone 7 devices.
     *   Focuses on Windows Mobile 6.xx and earlier.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is a Windows Mobile device, false otherwise.
     */
    public static function isWindowsMobile($agent)
    {
        if (self::isWindowsPhone7($agent)) {
            return false;
        }

        //Most devices use 'Windows CE', but some report 'iemobile'
        //  and some older ones report as 'PIE' for Pocket IE.
        if (stripos($agent, self::DEVICE_WINMOB) > -1 ||
            stripos($agent, self::DEVICE_IEMOBILE) > -1 ||
            stripos($agent, self::ENGINE_PIE) > -1) {
            return true;
        }

        //Test for Windows Mobile PPC but not old Macintosh PowerPC.
        if (stripos($agent, self::DEVICE_PPC) > -1
            && !(stripos($agent, self::DEVICE_MAC_PPC) > 1)) {
            return true;
        }

        //Test for certain Windwos Mobile-based HTC devices.
        if (stripos($agent, self::MANU_HTC) > -1 && stripos($agent, self::DEVICE_WINDOWS) > -1) {
            return true;
        }
        return self::isWapOrWml($agent) && stripos($agent, self::DEVICE_WINDOWS) > -1;
    }

    /**
     * Detects if the current browser is a BlackBerry of some sort.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is a BlackBerry, false otherwise.
     */
    public static function isBlackBerry($agent)
    {
        if (stripos($agent, self::DEVICE_BB) > -1) {
            return true;
        }
        return stripos(self::_getHttpAccept(), self::VNDRIM) > -1;
    }

    /**
     * Detects if the current browser is a BlackBerry Touch
     *    device, such as the Storm.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is a BlackBerry touch, false otherwise.
     */
    public static function isBlackBerryTouch($agent)
    {
        return stripos($agent, self::DEVICE_BBSTORM) > -1;
    }

    /**
     * Detects if the current browser is a BlackBerry device AND
     *    has a more capable recent browser.
     *    Examples, Storm, Bold, Tour, Curve2
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is a "high" BlackBerry, false otherwise.
     */
    public static function isBlackBerryHigh($agent)
    {
        if (self::isBlackBerry($agent)) {
            return (self::isBlackBerryTouch($agent)) ||
                stripos($agent, self::DEVICE_BBBOLD) > -1 ||
                stripos($agent, self::DEVICE_BBTOUR) > -1 ||
                stripos($agent, self::DEVICE_BBCURVE) > -1;
        }
        return false;
    }

    /**
     * Detects if the current browser is a BlackBerry device AND
     *    has an older, less capable browser.
     *    Examples: Pearl, 8800, Curve1.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is a "low" BlackBerry, false otherwise.
     */
    public static function isBlackBerryLow($agent)
    {
        if (self::isBlackBerry($agent)) {
            //Assume that if it's not in the High tier, then it's Low.
            return !self::isBlackBerryHigh($agent);
        }
        return false;
    }

    /**
     * Detects if the current browser is on a PalmOS device.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is a PalmOS device, false otherwise.
     */
    public static function isPalmOS($agent)
    {
        //Most devices nowadays report as 'Palm', but some older ones reported as Blazer or Xiino.
        if (stripos($agent, self::DEVICE_PALM) > -1 ||
            stripos($agent, self::ENGINE_BLAZER) > -1 ||
            stripos($agent, self::ENGINE_XIINO) > -1) {
            //Make sure it's not WebOS first
            return !self::isPalmWebOS($agent);
        }
        return false;
    }

    /**
     * Detects if the current browser is running WebOS.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is running WebOS, false otherwise.
     */
    public static function isPalmWebOS($agent)
    {
        return stripos($agent, self::DEVICE_WEBOS) > -1;
    }

    /**
     * Detects if the current browser is a
     *   Garmin Nuvifone.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is a Garmin Nuvifone, false otherwise.
     */
    public static function isGarminNuvifone($agent)
    {
        return stripos($agent, self::DEVICE_NUVIFONE) > -1;
    }

    /**
     * Check to see whether the device is any device
     *   in the 'smartphone' category.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is a "smartphone", false otherwise.
     */
    public static function isSmartphone($agent)
    {
        return self::isIphoneOrIpod($agent) ||
            self::isS60OsBrowser($agent) ||
            self::isSymbianOS($agent) ||
            self::isAndroid($agent) ||
            self::isWindowsMobile($agent) ||
            self::isWindowsPhone7($agent) ||
            self::isBlackBerry($agent) ||
            self::isPalmWebOS($agent) ||
            self::isPalmOS($agent) ||
            self::isGarminNuvifone($agent);
    }

    /**
     * Detects whether the device is a Brew-powered device.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is Brew powered, false otherwise.
     */
    public static function isBrewDevice($agent)
    {
        return stripos($agent, self::DEVICE_BREW) > -1;
    }

    /**
     * Detects the Danger Hiptop device.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is a Danger Hiptop device, false otherwise.
     */
    public static function isDangerHiptop($agent)
    {
        return stripos($agent, self::DEVICE_DANGER) > -1 || stripos($agent, self::DEVICE_HIPTOP) > -1;
    }

    /**
     * Detects if the current browser is Opera Mobile or Mini.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is running Opera Mobile or Mini, false otherwise.
     */
    public static function isOperaMobile($agent)
    {
        if (stripos($agent, self::ENGINE_OPERA) > -1) {
            return stripos($agent, self::MINI) > -1 || stripos($agent, self::MOBI) > -1;
        }
        return false;
    }

    /**
     * Detects whether the device supports WAP or WML.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent supports WAP or WML, false otherwise.
     */
    public static function isWapOrWml($agent)
    {
        return stripos(self::_getHttpAccept(), self::VNDWAP) > -1 || stripos(self::_getHttpAccept(), self::WML) > -1;
    }

    /**
     * Detects if the current device is an Amazon Kindle.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is an Amazon Kindle, false otherwise.
     */
    public static function isKindle($agent)
    {
        return stripos($agent, self::DEVICE_KINDLE) > -1;
    }

    /**
     * The quick way to detect for a MOBILE device.
     *   Will probably detect most recent/current mid-tier Feature Phones
     *   as well as smartphone-class devices. Excludes Apple iPads.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is a mobile device, false otherwise.
     */
    public static function isMobileQuick($agent)
    {
        //Let's say no if it's an iPad, which contains 'MOBILE' in its user agent.
        if (self::isIpad($agent)) {
            return false;
        }

        //Most MOBILE browsing is done on smartphones
        if (self::isSmartphone($agent)) {
            return true;
        }

        return self::isWapOrWml($agent) ||
            self::isBrewDevice($agent) ||
            self::isOperaMobile($agent) ||
            stripos($agent, self::ENGINE_NETFRONT) > -1 ||
            stripos($agent, self::ENGINE_UPBROWSER) > -1 ||
            stripos($agent, self::ENGINE_OPENWEB) > -1 ||
            self::isDangerHiptop($agent) ||
            self::isMidpCapable($agent) ||
            self::isMaemoTablet($agent) ||
            self::isArchos($agent) ||
            stripos($agent, self::DEVICE_PDA) > -1 ||
            stripos($agent, self::MOBILE) > -1;
    }

    /**
     * Detects if the current device is a Sony Playstation.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is a Sony Playstation, false otherwise.
     */
    public static function isSonyPlaystation($agent)
    {
        return stripos($agent, self::DEVICE_PLAYSTATION) > -1;
    }

    /**
     * Detects if the current device is a Nintendo game device.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is a Nintendo game device, false otherwise.
     */
    public static function isNintendo($agent)
    {
        return stripos($agent, self::DEVICE_NINTENDO) > -1 ||
            stripos($agent, self::DEVICE_WII) > -1 ||
            stripos($agent, self::DEVICE_NINTENDO_DS) > -1;
    }

    /**
     * Detects if the current device is a Microsoft Xbox.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is a Microsoft Xbox, false otherwise.
     */
    public static function isXbox($agent)
    {
        return stripos($agent, self::DEVICE_XBOX) > -1;
    }

    /**
     * Detects if the current device is an Internet-capable game console.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is an Internet-capable game console, false otherwise.
     */
    public static function isGameConsole($agent)
    {
        return self::isSonyPlaystation($agent) ||
            self::isNintendo($agent) ||
            self::isXbox($agent);
    }

    /**
     * Detects if the current device supports MIDP, a MOBILE Java technology.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent supports MIDP, false otherwise.
     */
    public static function isMidpCapable($agent)
    {
        return stripos($agent, self::DEVICE_MIDP) > -1 || stripos(self::_getHttpAccept(), self::DEVICE_MIDP) > -1;
    }

    /**
     * Detects if the current device is on one of the Maemo-based Nokia Internet Tablets.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is on one of the Maemo-based Nokia Internet Tablets, false otherwise.
     */
    public static function isMaemoTablet($agent)
    {
        if (stripos($agent, self::MAEMO) > -1) {
            return true;
        }
        //Must be Linux + Tablet, or else it could be something else.
        return stripos($agent, self::MAEMO_TABLET) > -1 && stripos($agent, self::LINUX) > -1;
    }

    /**
     * Detects if the current device is an Archos media player/Internet tablet.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is an iPhone-tier phone, false otherwise.
     */
    public static function isArchos($agent)
    {
        return stripos($agent, self::DEVICE_ARCHOS) > -1;
    }

    /**
     * Detects if the current browser is a Sony Mylo device.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is an iPhone-tier phone, false otherwise.
     */
    public static function isSonyMylo($agent)
    {
        if (stripos($agent, self::MANU_SONY) > -1) {
            return stripos($agent, self::QT_EMBEDDED) > -1 || stripos($agent, self::MYLOCOM2) > -1;
        }
        return false;
    }

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
    public static function isMobileLong($agent)
    {
        return self::isMobileQuick($agent) ||
            self::isGameConsole($agent) ||
            self::isSonyMylo($agent) ||
            stripos($agent, self::UPLINK) > -1 ||
            stripos($agent, self::MANU_SONYERICSSON) > -1 ||
            stripos($agent, self::MANU_ERICSSON) > -1 ||
            stripos($agent, self::MANU_SAMSUNG1) > -1 ||
            stripos($agent, self::SVC_DOCOMO) > -1 ||
            stripos($agent, self::SVC_KDDI) > -1 ||
            stripos($agent, self::SVC_VODAFONE) > -1;
    }

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
    public static function isTierIphone($agent)
    {
        return self::isIphoneOrIpod($agent) ||
            self::isAndroid($agent) ||
            self::isAndroidWebKit($agent) ||
            self::isWindowsPhone7($agent) ||
            self::isPalmWebOS($agent) ||
            self::isGarminNuvifone($agent) ||
            self::isMaemoTablet($agent);
    }

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
    public static function isTierRichCss($agent)
    {
        if (self::isMobileQuick($agent)) {
            if (self::isTierIphone($agent)) {
                return false;
            }

            //The following devices are explicitly ok.
            if (self::isWebkit($agent)) {
                return true;
            }
            if (self::isS60OsBrowser($agent)) {
                return true;
            }

            //Note: 'High' BlackBerry devices ONLY
            return self::isBlackBerryHigh($agent) ||
                self::isWindowsMobile($agent) ||
                stripos($agent, self::ENGINE_TELECAQ) > -1;
        }
        return false;
    }

    /**
     * The quick way to detect for a tier of devices.
     * This method detects for all other types of phones,
     * but excludes the iPhone and RichCSS Tier devices.
     *
     * @param string $agent The HTTP user agent.
     *
     * @return boolean True if the agent is an "Other" tier phone, false otherwise.
     */
    public static function isTierOtherPhones($agent)
    {
        if (self::isMobileLong($agent)) {
            //Exclude devices in the other 2 categories
            if (self::isTierIphone($agent)) {
                return false;
            }
            if (self::isTierRichCss($agent)) {
                return false;
            }
            return true;
        }
        return false;
    }

    /**
     * Safe-get of the HTTP agent header
     *
     * @param array $serverVars The PHP $_SERVER array.
     *
     * @return string The value of the HTTP agent header, of '' if not found
     */
    public static function getHttpAgent($serverVars)
    {
        if (!is_array($serverVars) || !array_key_exists(self::HTTP_USER_AGENT, $serverVars)) {
            return '';
        }
        return $serverVars[self::HTTP_USER_AGENT];
    }

    /**
     * Safe-get of the HTTP accept header
     *
     * @param array $serverVars The PHP $_SERVER array.
     *
     * @return string The value of the HTTP accept header, of '' if not found
     */
    private static function _getHttpAccept($serverVars)
    {
        if (!is_array($serverVars) || !array_key_exists(self::HTTP_ACCEPT, $serverVars)) {
            return '';
        }
        return $serverVars[self::HTTP_ACCEPT];
    }
}
