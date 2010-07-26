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

    const ENGINE_WEBKIT = 'webkit';
    const DEVICE_ANDROID = 'android';
    const deviceIphone = 'iphone';
    const deviceIpod = 'ipod';
    const deviceIpad = 'ipad';
    const deviceMacPpc = 'macintosh';

    const deviceNuvifone = 'nuvifone';

    const deviceSymbian = 'symbian';
    const deviceS60 = 'series60';
    const deviceS70 = 'series70';
    const deviceS80 = 'series80';
    const deviceS90 = 'series90';
   
    const deviceWinPhone7 = 'windows phone os 7'; 
    const deviceWinMob = 'windows ce';
    const deviceWindows = 'windows'; 
    const deviceIeMob = 'iemobile';
    const devicePpc = 'ppc';
    const enginePie = 'wm5 pie';
   
    const deviceBB = 'blackberry';   
    const vndRIM = 'vnd.rim'; 
    const deviceBBStorm = 'blackberry95';
    const deviceBBBold = 'blackberry97'; 
    const deviceBBTour = 'blackberry96'; 
    const deviceBBCurve = 'blackberry89';
   
    const devicePalm = 'palm';
    const deviceWebOS = 'webos'; 
    const engineBlazer = 'blazer'; 
    const engineXiino = 'xiino'; 
   
    const deviceKindle = 'kindle'; 
   
    //Initialize variables for mobile-specific content.
    const vndwap = 'vnd.wap';
    const wml = 'wml';   
   
    //Initialize variables for other random devices and mobile browsers.
    const deviceBrew = 'brew';
    const deviceDanger = 'danger';
    const deviceHiptop = 'hiptop';
    const devicePlaystation = 'playstation';
    const deviceNintendoDs = 'nitro';
    const deviceNintendo = 'nintendo';
    const deviceWii = 'wii';
    const deviceXbox = 'xbox';
    const deviceArchos = 'archos';
   
    const engineOpera = 'opera'; //Popular browser
    const engineNetfront = 'netfront'; //Common embedded OS browser
    const engineUpBrowser = 'up.browser'; //common on some phones
    const engineOpenWeb = 'openweb'; //Transcoding by OpenWave server
    const deviceMidp = 'midp'; //a mobile Java technology
    const uplink = 'up.link';
    const engineTelecaQ = 'teleca q'; //a modern feature phone browser
   
    const devicePda = 'pda'; //some devices report themselves as PDAs
    const mini = 'mini';  //Some mobile browsers put 'mini' in their names.
    const mobile = 'mobile'; //Some mobile browsers put 'mobile' in their user agent strings.
    const mobi = 'mobi'; //Some mobile browsers put 'mobi' in their user agent strings.
   
    //Use Maemo, Tablet, and Linux to test for Nokia's Internet Tablets.
    const maemo = 'maemo';
    const maemoTablet = 'tablet';
    const linux = 'linux';
    const qtembedded = 'qt embedded'; //for Sony Mylo and others
    const mylocom2 = 'com2'; //for Sony Mylo also
   
    //In some UserAgents, the only clue is the manufacturer.
    const manuSonyEricsson = "sonyericsson";
    const manuericsson = "ericsson";
    const manuSamsung1 = "sec-sgh";
    const manuSony = "sony";
    const manuHtc = "htc"; //Popular Android and WinMo manufacturer

    //In some UserAgents, the only clue is the operator.
    const svcDocomo = "docomo";
    const svcKddi = "kddi";
    const svcVodafone = "vodafone";
    
    /**
     * Determines which HTTP client is in use.
     * 
     * @param array $serverVars The PHP $_SERVER variable.
     *
     * @return string iphone, ipod, or unknown.
     */
    public static function isMobile($serverVars)
    {
        if (!is_array($serverVars)
            || !array_key_exists(self::HTTP_USER_AGENT, $serverVars)) {
            return false;
        }

        $agent = $serverVars[self::HTTP_USER_AGENT];

        return self::detectTierIphone($agent);
    }

    private static function detectIphone($agent)
    {
        if (stripos($agent, self::deviceIphone) > -1) {
          
            //The iPad and iPod Touch say they're an iPhone! So let's disambiguate.
            if (self::detectIpad($agent) || self::detectIpod($agent)) {
                return false;
            }
            return true; 
        }
        return false; 
    }

    private static function detectIpod($agent)
    {
        return stripos($agent, self::deviceIpod) > -1;
    }
   
    private static function detectIpad($agent)
    {
        return stripos($agent, self::deviceIpad) > -1 && self::detectWebkit($agent);
    }

    private static function detectIphoneOrIpod($agent)
    {
        return stripos($agent, self::deviceIphone) > -1 || stripos($agent, self::deviceIpod) > -1;
    }

    private static function detectAndroid($agent)
    {
        return stripos($agent, self::DEVICE_ANDROID) > -1;
    }

    private static function detectAndroidWebKit($agent)
    {
        if (self::detectAndroid($agent)) {
            return self::detectWebkit($agent);
        }
        return false; 
    }

    private static function detectWebkit($agent)
    {
        return stripos($agent, self::ENGINE_WEBKIT) > -1;
    }

    private static function detectS60OssBrowser($agent)
    {
        //First, test for WebKit, then make sure it's either Symbian or S60.
        if (self::detectWebkit($agent)) {
            return stripos($agent, self::deviceSymbian) > -1 || stripos($agent, self::deviceS60) > -1;
        }
        return false; 
    }
   
    //**************************
    // Detects if the current device is any Symbian OS-based device,
    //   including older S60, Series 70, Series 80, Series 90, and UIQ, 
    //   or other browsers running on these devices.
    private static function detectSymbianOS($agent)
    {
        return stripos($agent, self::deviceSymbian) > -1 || 
           stripos($agent, self::deviceS60) > -1 ||
           stripos($agent, self::deviceS70) > -1 || 
           stripos($agent, self::deviceS80) > -1 ||
           stripos($agent, self::deviceS90) > -1;
    }

    //**************************
    // Detects if the current browser is a 
    // Windows Phone 7 device.
    private static function detectWindowsPhone7($agent)
    {
        return stripos($agent, self::deviceWinPhone7) > -1;
    }

    //**************************
    // Detects if the current browser is a Windows Mobile device.
    // Excludes Windows Phone 7 devices. 
    // Focuses on Windows Mobile 6.xx and earlier.
    private static function detectWindowsMobile($agent)
    {
        if (self::detectWindowsPhone7($agent)) {
            return false; 
        }
        
        //Most devices use 'Windows CE', but some report 'iemobile' 
        //  and some older ones report as 'PIE' for Pocket IE. 
        if (stripos($agent, self::deviceWinMob) > -1 ||
            stripos($agent, self::deviceIeMob) > -1 ||
            stripos($agent, self::enginePie) > -1) {
            return true; 
        }
        
        //Test for Windows Mobile PPC but not old Macintosh PowerPC.
        if (stripos($agent, self::devicePpc) > -1
            && !(stripos($agent, self::deviceMacPpc) > 1)) {
            return true; 
        }
        
        //Test for certain Windwos Mobile-based HTC devices.
        if (stripos($agent, self::manuHtc) > -1 && stripos($agent, self::deviceWindows) > -1) {
            return true; 
        }
        return self::detectWapWml($agent) && stripos($agent, self::deviceWindows) > -1;
    }

    //**************************
    // Detects if the current browser is a BlackBerry of some sort.
    private static function detectBlackBerry($agent)
    {
        if (stripos($agent, self::deviceBB) > -1) {
            return true; 
        }
        return stripos(self::httpaccept, self::vndRIM) > -1;
    }

    //**************************
    // Detects if the current browser is a BlackBerry Touch
    //    device, such as the Storm.
    private static function detectBlackBerryTouch($agent)
    {
        return stripos($agent, self::deviceBBStorm) > -1;
    }
   
    //**************************
    // Detects if the current browser is a BlackBerry device AND
    //    has a more capable recent browser. 
    //    Examples, Storm, Bold, Tour, Curve2
    private static function detectBlackBerryHigh($agent)
    {
        if (self::detectBlackBerry($agent)) {
            return (self::detectBlackBerryTouch($agent)) ||
                stripos($agent, self::deviceBBBold) > -1 ||
                stripos($agent, self::deviceBBTour) > -1 ||
                stripos($agent, self::deviceBBCurve) > -1;
        }
        return false; 
    }

    //**************************
    // Detects if the current browser is a BlackBerry device AND
    //    has an older, less capable browser. 
    //    Examples: Pearl, 8800, Curve1.
    private static function detectBlackBerryLow($agent)
    {
        if (self::detectBlackBerry($agent)) {
            //Assume that if it's not in the High tier, then it's Low.
            return self::detectBlackBerryHigh($agent);
        }
        return false; 
    }

    //**************************
    // Detects if the current browser is on a PalmOS device.
    private static function detectPalmOS($agent)
    {
        //Most devices nowadays report as 'Palm', but some older ones reported as Blazer or Xiino.
        if (stripos($agent, self::devicePalm) > -1 ||
            stripos($agent, self::engineBlazer) > -1 ||
            stripos($agent, self::engineXiino) > -1)  {
            //Make sure it's not WebOS first
            return !self::detectPalmWebOS($agent);
        }
        return false; 
    }

    //**************************
    // Detects if the current browser is on a Palm device
    //   running the new WebOS.
    private static function detectPalmWebOS($agent)
    {
        return stripos($agent, self::deviceWebOS) > -1;
    }

    //**************************
    // Detects if the current browser is a
    //   Garmin Nuvifone.
    private static function detectGarminNuvifone($agent)
    {
        return stripos($agent, self::deviceNuvifone) > -1;
    }

    //**************************
    // Check to see whether the device is any device
    //   in the 'smartphone' category.
    private static function detectSmartphone($agent)
    {
        return self::detectIphoneOrIpod($agent) ||
            self::detectS60OssBrowser($agent) ||
            self::detectSymbianOS($agent) ||
            self::detectAndroid($agent) ||
            self::detectWindowsMobile($agent) ||
            self::detectWindowsPhone7($agent) ||
            self::detectBlackBerry($agent) ||
            self::detectPalmWebOS($agent) ||
            self::detectPalmOS($agent) ||
            self::detectGarminNuvifone($agent);
    }

    //**************************
    // Detects whether the device is a Brew-powered device.
    private static function detectBrewDevice($agent)
    {
       return stripos($agent, self::deviceBrew) > -1;
    }

    //**************************
    // Detects the Danger Hiptop device.
    private static function detectDangerHiptop($agent)
    {
        return stripos($agent, self::deviceDanger) > -1 || stripos($agent, self::deviceHiptop) > -1;
    }

    //**************************
    // Detects if the current browser is Opera Mobile or Mini.
    private static function detectOperaMobile($agent)
    {
        if (stripos($agent, self::engineOpera) > -1) {
            return stripos($agent, self::mini) > -1 || stripos($agent, self::mobi) > -1;
        }
        return false; 
    }

    //**************************
    // Detects whether the device supports WAP or WML.
    private static function detectWapWml($agent)
    {
        return stripos(self::httpaccept, self::vndwap) > -1 || stripos(self::httpaccept, self::wml) > -1;
    }
   
    //**************************
    // Detects if the current device is an Amazon Kindle.
    private static function detectKindle($agent)
    {
        return stripos($agent, self::deviceKindle) > -1;
    }
   
    //**************************
    // The quick way to detect for a mobile device.
    //   Will probably detect most recent/current mid-tier Feature Phones
    //   as well as smartphone-class devices. Excludes Apple iPads.
    private static function detectMobileQuick($agent)
    {
        //Let's say no if it's an iPad, which contains 'mobile' in its user agent.
        if (self::detectiPad($agent)) {
            return false;
        }

        //Most mobile browsing is done on smartphones
        if (self::detectSmartphone($agent)) {
            return true;
        }

        return self::detectWapWml($agent) || 
            self::detectBrewDevice($agent) || 
            self::detectOperaMobile($agent) || 
            stripos($agent, self::engineNetfront) > -1 ||
            stripos($agent, self::engineUpBrowser) > -1 ||
            stripos($agent, self::engineOpenWeb) > -1 ||
            self::detectDangerHiptop($agent) || 
            self::detectMidpCapable($agent) || 
            self::detectMaemoTablet($agent) || 
            self::detectArchos($agent) || 
            stripos($agent, self::devicePda) > -1 ||
            stripos($agent, self::mobile) > -1;
    }
   
    //**************************
    // Detects if the current device is a Sony Playstation.
    private static function detectSonyPlaystation($agent)
    {
        return stripos($agent, self::devicePlaystation) > -1;
    }

    //**************************
    // Detects if the current device is a Nintendo game device.
    private static function detectNintendo($agent)
    {
        return stripos($agent, self::deviceNintendo) > -1 || 
            stripos($agent, self::deviceWii) > -1 ||
            stripos($agent, self::deviceNintendoDs) > -1;
    }

    //**************************
    // Detects if the current device is a Microsoft Xbox.
    private static function detectXbox($agent)
    {
        return stripos($agent, self::deviceXbox) > -1;
    }
   
    //**************************
    // Detects if the current device is an Internet-capable game console.
    private static function detectGameConsole($agent)
    {
        return self::detectSonyPlaystation($agent) ||
            self::detectNintendo($agent) ||
            self::detectXbox($agent);
    }
   
    //**************************
    // Detects if the current device supports MIDP, a mobile Java technology.
    private static function detectMidpCapable($agent)
    {
        return stripos($agent, self::deviceMidp) > -1 || stripos(self::httpaccept, self::deviceMidp) > -1;
    }
   
    //**************************
    // Detects if the current device is on one of the Maemo-based Nokia Internet Tablets.
    private static function detectMaemoTablet($agent)
    {
        if (stripos($agent, self::maemo) > -1) {
            return true; 
        }
        //Must be Linux + Tablet, or else it could be something else. 
        return stripos($agent, self::maemoTablet) > -1 && stripos($agent, self::linux) > -1;
    }

    //**************************
    // Detects if the current device is an Archos media player/Internet tablet.
    private static function detectArchos($agent)
    {
        return stripos($agent, self::deviceArchos) > -1;
    }

    //**************************
    // Detects if the current browser is a Sony Mylo device.
    private static function detectSonyMylo($agent)
    {
        if (stripos($agent, self::manuSony) > -1) {
            return stripos($agent, self::qtembedded) > -1 || stripos($agent, self::mylocom2) > -1;
        }
        return false; 
    }
  
    //**************************
    // The longer and more thorough way to detect for a mobile device.
    //   Will probably detect most feature phones,
    //   smartphone-class devices, Internet Tablets, 
    //   Internet-enabled game consoles, etc.
    //   This ought to catch a lot of the more obscure and older devices, also --
    //   but no promises on thoroughness!
    private static function detectMobileLong($agent)
    {
        return self::detectMobileQuick($agent) ||
            self::detectGameConsole($agent) ||
            self::detectSonyMylo($agent) ||
            stripos($agent, self::uplink) > -1 ||
            stripos($agent, self::manuSonyEricsson) > -1 ||
            stripos($agent, self::manuericsson) > -1 ||
            stripos($agent, self::manuSamsung1) > -1 ||
            stripos($agent, self::svcDocomo) > -1 ||
            stripos($agent, self::svcKddi) > -1 ||
            stripos($agent, self::svcVodafone) > -1;
    }

    //**************************
    // The quick way to detect for a tier of devices.
    //   This method detects for devices which can 
    //   display iPhone-optimized web content.
    //   Includes iPhone, iPod Touch, Android, WebOS, etc.
    private static function detectTierIphone($agent)
    {
        return self::detectIphoneOrIpod($agent) || 
            self::detectAndroid($agent) || 
            self::detectAndroidWebKit($agent) || 
            self::detectWindowsPhone7($agent) ||
            self::detectPalmWebOS($agent) || 
            self::detectGarminNuvifone($agent) ||
            self::detectMaemoTablet($agent);
    }
   
    //**************************
    // The quick way to detect for a tier of devices.
    //   This method detects for devices which are likely to be capable 
    //   of viewing CSS content optimized for the iPhone, 
    //   but may not necessarily support JavaScript.
    //   Excludes all iPhone Tier devices.
    private static function detectTierRichCss($agent)
    {
        if (self::detectMobileQuick($agent)) {
            if (self::detectTierIphone($agent)) {
                return false;
            }
           
            //The following devices are explicitly ok.
            if (self::detectWebkit($agent)) {//Any WebKit
                return true;
            }
            if (self::detectS60OssBrowser($agent)) {
                return true;
            }
           
            //Note: 'High' BlackBerry devices ONLY
            return self::detectBlackBerryHigh($agent) ||
                self::detectWindowsMobile($agent) ||
                stripos($agent, self::engineTelecaQ) > -1;
        }
        return false; 
    }

    //**************************
    // The quick way to detect for a tier of devices.
    //   This method detects for all other types of phones,
    //   but excludes the iPhone and RichCSS Tier devices.
    private static function detectTierOtherPhones($agent)
    {
        if (self::detectMobileLong($agent)) {
            //Exclude devices in the other 2 categories 
            if (self::detectTierIphone($agent)) {
                return false;
            }
            if (self::detectTierRichCss($agent)) {
                return false;
            }
            return true;
        }
        return false; 
    }
}