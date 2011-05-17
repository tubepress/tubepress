=== TubePress ===
Contributors: k2eric
Donate link: http://tubepress.org
Tags: video, youtube, gallery, videos, vimeo
Requires at least: 2.2
Stable tag: trunk

Displays gorgeous YouTube and Vimeo galleries in your posts, pages, and/or sidebar. Please visit http://tubepress.org for more info!

== Installation ==

1. Unzip into your plugins directory at `(wp-content/plugins)`
1. Activate TubePress from Site Admin > Plugins
1. Configure from Site Admin > Settings > TubePress
1. Type `[tubepress]` in a post or a page where you'd like to insert your first gallery

== Changelog ==

= 2.2.0 =
* Interactive searching! (closes  issue 138 )
* YouTube iframe embedded player (closes  issue 265  and  issue 259 )
* Improved inclusion of JS/CSS resources in WordPress environments (closes  issue 191 )
* Ability to restrict search-based galleries to videos from a specific YouTube/Vimeo user
* New HTTP subsystem provides faster and more robust feed retrieval
* Added advanced caching options
* Improved visual feedback during Ajax operations (closes  issue 142 )
* Better upgrade notice for TubePress Pro users in a WordPress environment (closes  issue 252 )
* Graceful handling of single video embedding when video isn't found (closes  issue 267 )
* Broken gallery when trying to display Vimeo videos with JW FLV Media Player (closes  issue 242 )
* Incorrect detection of multi-gallery mode in some circumstances (closes  issue 238 )
* Duplicate videos no longer appear in galleries (closes  issue 248 )
* Private Vimeo videos no longer appear in galleries
* Non-functional message service when using TubePress Pro in a PHP environment without built-in gettext support
* Fixed bug that could affect TubePress Pro users in a WordPress environment hosted on Windows-based PHP installations
* Removed option for "mobile" YouTube videos feed as it appears to be completely abandoned
* YouTube now uses the iframe embedded player by default
* Removed "enhanced genie menu" and "border" options from YouTube embedded player
* Version bump for php-gettext to 1.0.11
* Version bump for jQuery to 1.5.1

= 2.1.2 =
* Fixed JavaScript error in Internet Explorer when determining height/width of embedded video player
* Fixed ajax pagination appearance when non-default thumbnail sizes are in use
* Fixed non-functional WordPress widget control
* Updated Italian translation thanks to Gianni Diurno

= 2.1.1 =
* Fixed major JavaScript bug in determining embedded player height/width on jQuery 1.4.2
* Fixed bug with ajax pagination on some PHP installations

= 2.1.0 =
* Ability to create galleries from multiple sources (TubePress Pro only) (closes issue 135)
* Lightweight theming support
* Support for "fluid" thumbnails
* Vimeo universal player (closes issue 215)
* Improved video search capability (closes issue 81)
* Improved mobile user experience
* Single video mode now works in WordPress widget
* Fixed aliased text with Ajax pagination in Internet Explorer 7
* Search engines no longer index TubePress's internal directories (closes issue 221)
* Static player now works with Vimeo (closes issue 217)
* Fixed color support for JW FLV Player
* YouTube "most linked" gallery replaced with YouTube "top favorites" gallery due to change in API
* Version bump for Shadowbox.js to 3.0.3
* Version bump for JW FLV Media Player to 5.2
* Version bump for jQuery to 1.4.2
* Version bump for php-gettext to 1.0.10
* Added Arabic translation thanks to Abdullah Hamed
* Updated Italian translation thanks to Gianni Diurno
* Updated Spanish translation thanks to Luis Fok

= 2.0.0 =
* Vimeo support! Choose from 8 different types of Vimeo galleries. (closes issue 108)
* Brought back ability to play each video from a gallery in a new window by itself (closes issue 94)
* Now detects and parses HTML links found in video descriptions
* Updated Italian translation thanks to Gianni Diurno
* Added Spanish translation thanks to Luis Fok
* Added Hebrew translation thanks to Yaron Ofer
* Ability to blacklist individual videos (closes issue 162)
* Option to use high-quality thumbnails (closes issue 96) (TubePress Pro only)
* HTML popups are now centered on screen (closes issue 160)
* Fixed regression bug for description limit (closes issue 153)
* Incorrect video could load into embedded player after thumbnail click in certain configurations (closes issue 175)
* resultCountCap now works on a single gallery page
* Fixes single video mode in TubePress Pro
* Static player now works with Ajax pagination (closes issue 177)
* Version bump for FancyBox (to 1.3.1)
* FancyBox now operates correctly in all browsers (closes issue 172)

= 1.9.0 =
* Fixed a bug that breaks the WordPress widget administration page in some PHP installations
* Added Portugese translation thanks to Miriam de Pauala

= 1.8.9 =

* Now includes ability to embed individual videos along with all of their meta info (title, author, description, etc)!
* Request cache is now disabled by default
* Updated sorting functionality. Fixes possible "bad request" response from YouTube. Fixes sorting of playlist galleries.
* Fixed blank item on video sort order dropdown of WordPress options page (closes issue 155)
* descLimit shortcode can now be set to zero (closes issue 153)
* Improved compatibility with older versions of PHP (closes issue 163)
* Added Russian translation (thanks to an anonymous supporter)
* ShadowBox version bump to 3.0rc1
* Improved documentation
* (TubePress Pro) Improved i18n support
* (TubePress Pro) FancyBox version bump to 1.2.6
* (TubePress Pro) Fixed bug that prevented FancyBox from operating correctly in IE (closes issue 165)

= 1.8.8 =
* JavaScript initialization is much more robust. Reduces chances of un-clickable video thumbnails.
* Fixed potentially fatal error in cache mechanism
* Improved sidebar's CSS
* Fixed bug in video sort order (closes issue 146)
* Fixed bug that could cause fatal error in templates
* Version bump for JW FLV Media Player (to 5.0)
* Updated Italian translation thanks to Gianni Diurno

= 1.8.7 =
* Improved compatibility with PEAR (closes issue 140)
* Fixed bug that affected determining video ID in some YouTube feeds
* New static player option. Produces page refresh on each thumbnail click.
* Gallery HTML is now semantically correct and more structured (closes issue 125 and issue 117)
* TubePress Pro: New player: TinyBox (closes issue 110)
* TubePress Pro: New player: FancyBox (closes issue 118)
* TubePress Pro: Greatly improved WordPress integration
* TubePress Pro: Fixed bug that produced open_basedir warning message in some installations
* Fixed bug that prevented use of custom templates
* Overhaul and simplification of templating system
* Improved performance of logging system
* Improved iPhone/iPod playback (keeps user on site instead of redirecting to YouTube player) (closes issue 143)

= 1.8.6 =
* WordPress options page now uses jQuery tabs
* Fixed bug that could prevent request cache from working in some PHP installations
* (TubePress Pro only) Ajax pagination (closes issue 45 and issue 111)
* (TubePress Pro only) Version bump for jQuery (1.2.6 -> 1.3.2)
* (TubePress Pro only) Removed potential "Invalid locale category name" warning that shows up on some PHP installations
* Version bump for JW FLV Media Player (to 4.6)
* Fixed bug that prevented display of videos with "limited syndication" restriction
* Shortcode and input validation is much more comprehensive (closes issue 129)
* Fixed bug that prevented videos from playing in high definition upon request (closes issue 137)
* Additional gallery pages are no longer indexed by search engines (closes issue 133)
* Minor refactoring of tubepress.js functions
* Updated WordPress plugins page blurb
* Updated Italian translation thanks to Gianni Diurno
* Various trivial improvements to HTML templates 

= 1.8.5 =
* Videos now play correctly on iPhone and iPod Touch (closes issue 101)
* Flexible shortcodes! No need to include commas between attribute/value pairs. Can use single, double, or no quotes around attribute values.
* Greatly improved debugging mode
* Fixed bug that prevents all videos in gallery from playing if first video in gallery is unavailable (closes issue 115)
* Fixed bug that could prevent video playback, and JavaScript error, on Firefox with AdBlock Plus enabled (closes issue 124)
* Added ability to cap the total number of videos in a gallery (closes issue 65)
* Updated Italian translation thanks to Gianni Diurno
* Shadowbox CSS no longer tries to load non-existent images. (closes issue 112)
* WordPress galleries are no longer wrapped with HTML paragraph tags (closes issue 79)
* WordPress.org compliant readme.txt changelog (closes issue 123)

= 1.8.0 =
* Includes all the changes listed in 1.8.0.RC1
* Fixed typo on options page regarding embedded player width/height
* Unavailable videos will now not appear in galleries at all 

= 1.8.0 =
* New embedded player option: JW Flv Media Player
* New player location: jqModal
* All classes use dependency injection via Crafty
* jQuery 1.2.6 or higher is now required (built-in for WordPress users)
* Multiple galleries on a single page now behave correctly with "normal" player
* Dynamically load JavaScript libraries as needed (Prototype, Shadowbox, etc) (closes issue 56)
* All JavaScript is now unobtrusive
* Can now use modal players (e.g. Shadowbox) in a TubePress shortcode 
* Removed extra HTML comments and whitespace. Galleries now use about 16% less bandwidth.
* CSS will stay valid for multiple galleries on a single page
* CSS classes now have sensible names (renamed some classes)
* Updated Italian translation thanks to Gianni Diurno (closes issue 83)
* Fixed bug that affected some PHP installations with PEAR installed (closes issue 84)
* Fixed bug that blocked WordPress plugin's "database nuke" functionality
* Removed GreyBox as a player location due to its inability to display inline content
* Version bump for ShadowBox (closes issue 77)
* popup.php has been removed and replaced with JavaScript functionality (closes issue 76)
* Better control over pagination visibility for multi-page galleries (closes issue 93)
* Removed GreyBox and LightWindow as players
* Graceful failure for videos that are unavailable 

= 1.7.2 =

* Fullscreen playback now available in embedded player (closes issue 64)
* Created "YouTube Feed" options category. Moved some of the advanced options into this category.
* Added ability to exclude non-embeddable videos (closes issue 69)
* Added jscolor HTML color picker for embedded player colors
* Fixed bug where debugging mode threw a fatal error (closes issue 80)
* Fixed bug where random video sort order would throw a fatal error
* Added ability to toggle display of title/rating before video starts playing
* Now using version 2.0 of YouTube gdata API (closes issue 68) 

= 1.7.1 =

* Drastically improved class loading mechanism (uses several thousand less system calls)
* Added Italian translation thanks to Gianni Diurno (closes issue 75)
* Minified Shadowbox JS source using YUI Compressor
* Fixed critical bug that broke embedded YouTube player on IE7 (closes issue 73)
* Fixed minor bug in normal player where embedded player would shift by a few pixels after user clicked a thumbnail 

= 1.7.0 =
* Option to initialize TubePress options in WordPress (closes issue 52)
* Now works with PHP <= 5.1.0 again (closes issue 59 and issue 67)
* Links to popup.php are drastically shorter, which avoids a 404 on some webservers (closes issue 55)
* Added German translation thanks to Pascal Berger (closes issue 58)
* Option to play videos in HD (closes issue 33)
* Option to show custom video in embedded player on page load while using "normal" player (closes issue 26) 

= 1.6.9 =
* TubePress Pro is now available! Use TubePress anywhere that runs PHP
* Default thumbmail URL now uses "default.jpg" (closes issue 47)
* Fixed bug that resulted in inability to paginate past the first page of a multi-page gallery.
* Version bump for Net_URL to Net_URL2
* Version bump for HTTP_Request to HTTP_Request2
* Version bump for Cache_Lite 
* Huge amounts of unit/integration tests
* Major refactoring of classes (now using dependency injection) 

= 1.6.8 =
* Fixed overly large gap between thumbnail rows 

= 1.6.7 =
* Added YouTube connection test to debug output
* Added YouTube API client ID and developer key to aide in debugging
* Fixed bug where TubePress would remove all post/page content if no videos were found for your request
* Toggle request cache on/off (closes issue 43)
* Ampersands in query strings are now properly escaped (closes issue 38)
* Galleries can now sort videos randomly (closes issue 23)
* Toggle "nofollow" attributes to YouTube links (closes issue 35) 

= 1.6.6 =
* Widget-enabled! Put TubePress in your sidebar. (closes issue 12)
* Removed pass-by-reference warnings (closes issue 34)
* Ability to limit length of video descriptions
* Option to toggle relative dates/times for video upload timestamps
* Swedish translation. Thanks to Mikael Jorhult
* Improved error handling for PHP installations with suppressed error output
* Removed several small bugs that affected users of PHP < 5.2.1 

= 1.6.5 =
* Full internationalization capability (closes issue 21)
* Upgrades, from this version on, will no longer destroy your old TubePress default settings (closes issue 28)
* Multiple galleries on a single post/page now possible (closes issue 20)
* Fixes broken YouTube link (closes issue 30)
* Video playback now functions correctly in IE7
* Cross site scripting vulnerability fixed (thanks Numline1 for reporting)
* Fixed debug output
* Options page now looks good in WordPress 2.5.1
* Various pass-by-reference warnings eliminated 

= 1.6.0 =
* PHP5 only. This includes an overall rewrite of the code base to take advantage of PHP5-only stuff
* Responses from YouTube are now cached
* New galleries: Top favorites, Most recently added, Most discussed, Most linked, Most responded, Responses to a video 
* New way to watch: Shadowbox.js
* Removed "New Window" player
* Customize textual formatting of dates
* Much more control over embedded player: Toggle "related videos" feature after a video finishes, Choose from several colors, Toggle auto-play, Toggle "enhanced genie menu" when mouse enters video area (instead of user clicking the "menu" button), Toggle video loop 
* Interface improvements: Removed border and scroll bars from gallery, Removed time of day from date uploaded, Changed upload label from "Uploaded date" to "Uploaded", Pagination now supports HTTP/HTTPS and non-standard ports, Simplified templating (easier to customize the look of your galleries) 
* Issues/enhancements closed: #20, #25, #7 
* Upgrade source to GPLv3 

= 1.5.7 =
* Fixed yet another title problem due to YouTube changing their feed
* Fixes issue #16 

= 1.5.6 =
* Fixes a major problem displaying video titles due to change in YouTube feed format 

= 1.5.5 RC3 =
* Fixes issue #13 

= 1.5.5 RC2 =
* Fixes issue #5
* Changes default thumb width from 33% to 32% (helps with IE) 

= 1.5.5 RC1 =
* "Favorites" mode now displays up to 500 videos
* "Search" mode now can return up to 1,000 videos
* New mode: most-viewed videos from today, this week, this month, or all time
* Customize the order of galleries by view-count, rating, relevance, or date-updated
* Randomize thumbnails (most videos come with 4 thumbnails, this option will mix it up for each pageload)
* Filter "racy" videos from galleries
* Option to show a video’s category in its metadata 
* All access to TubePressVideo member variables is now done through getters
* All HTML is generated via PEAR's HTML_Template_IT package 

= 1.5.2 =
* Fixes a fatal error upon plugin activation in some PHP installations 

= 1.5.1 =
* Fixes a minor bug with "search" mode due to YouTube changing the format of the XML results 

= 1.5.0 =
* "All tags" and "Any tags" modes now merged into "YouTube search" mode
* Play videos with GreyBox (this replaces ThickBox)
* Play videos with lightWindow
* Pagination now uses modified Digg Style pagination
* Max player size is now 424px x 336px (removes thin white border from player)
* Input validation on options page
* All error messages are extremely detailed
* Debug mode is more comprehensive
* Option to disable debugging mode completely 
* Embedded video is XHTML compliant
* "Playlist" mode pages correctly (thanks, YouTube!)
* "Normal" mode displays title and runtime correctly after user click 
* Much better code documentation
* Code style is PEAR compliant
* XML parsing uses PEAR's XML_Unserializer instead of XMLIster
* Almost all functions moved to classes (helps namespace management) 
* WordPress-specific code is cleanly isolated
* Improved message-resources handling
* Revamped directory structure
* Improved naming conventions all around
* Quasi-private methods (PHP4 OOP isn't too sophisticated)
* Most HTML printing methods use sprintf (or variant) - improves readability 

= 1.2.0 =
* Fixed a bug that caused an incorrect count of YouTube video results
* Added debugging mode! See the documentation for how to use it. 

= 1.1.5 =
* Fixed major XML bugs that caused a fatal error when parsing YouTube's XML
* CSS bug fixes with the pagination links
* Fixed a bug in the options page related to the "popular" mode drop down menu 

= 1.1 =
* CSS bug fixes. Thanks to Scott for reporting it 

= 1.05 =
* Fixed huge IE display bug (thanks to Keane and Jojo for reporting it)
* Disabled Thickbox by default due to incompatibilities with themes and other jquery-enabled features 

= 1.0 =
* Now with seven modes of operation: videos by playlists (with paging), featured (the latest 25 featured videos from YouTube's homepage), popular (the most-viewed videos from the past day, week, or month), related (videos that match any tag that you specify), tag (videos that match all the tags you specify), favorite videos of any user, videos uploaded by any user 
* You no longer need to supply your own developer ID and YouTube username! This means that anyone can use TubePress, even if you don't have a YouTube account!
* Pagination. You control how many videos per page show up (supported modes only)
* Leaner and meaner. The speedups in this version are ridiculous. Makes literally dozens less of the expensive database calls per page that existed in previous versions. Reduces database space in your wordpress options table from about 40 rows to only one.
* Options page is more concise and XHTML compliant
* Improved documentation
* Bug fixes. Too many to list! 

= 0.9 =
* Now with five options on where to play the main video: Normally (at the top of your gallery), In a popup window, In a new window all by itself, In its original YouTube page, Using Thickbox (experimental, but very cool!) 
* Bug fix regarding HTML special characters in video titles 

= 0.8 =
* Major bug fix with PHP4. Thanks to Fabien and Paige for reporting it. 

= 0.7 =
* Override global settings on a per-page basis! This will allow you to have one page with your videos (that you've uploaded), another page with your YouTube favorites, etc.
* Choose which meta information (author, rating, description, etc) you want to display
* Lots of optimizations and bug fixes 

= 0.6 =
* Now works with PHP4
* CSS revisions
* Uses snoopy class instead of cURL libraries 
* Added configurable timeout parameter for contacting YouTube 

= 0.5 =
* Fixed small bug in time display 

= 0.4 =
* Takes full advantage of the YouTube API: List videos from "your favorites", List your videos (that you've uploaded), List videos that match some tag, List videos of another user 
* XHTML compliant 

= 0.3 =
* OK, now the bug is really fixed. Sorry about that! 

= 0.2 =
* Fixed major bug with CSS file path 

= 0.1 =
* Initial release 

== Screenshots ==

1. Sample TubePress gallery
