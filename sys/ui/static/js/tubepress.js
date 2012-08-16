/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 * 
 * This file is part of TubePress (http://tubepress.org) and is released 
 * under the General Public License (GPL) version 3
 *
 */

/*global jQuery, getTubePressBaseUrl, alert, YT, Froogaloop, console */
/*jslint devel: true, browser: true, sloppy: false, white: true, maxerr: 50, indent: 4 */

/**
 * Logger!
 */
var TubePressLogger = (function () {
	
	'use strict';
	
	/**
	 * Is the log on?
	 */
	var isLoggingRequested		= location.search.indexOf('tubepress_debug=true') !== -1,
		windowConsole			= window.console,
		isLoggingAvailable		= typeof windowConsole !== 'undefined',
	
	/**
	 * The log is on if it's been enabled and requested.
	 */
	isLoggingOn = function () {

		return isLoggingRequested && isLoggingAvailable;
	},
	
	/**
	 * Output a message.
	 */
	log = function (msg) {
		
		windowConsole.log(msg);
	},
	
	dir = function (obj) {
		
		windowConsole.dir(obj);
	};

	return {
	
		on	: isLoggingOn,
		log	: log,
		dir	: dir
	};
}());

/**
 * Various Ajax utilities.
 */
var TubePressAjax = (function () {

	/** http://www.yuiblog.com/blog/2010/12/14/strict-mode-is-coming-to-town/ */
	'use strict';

	var
		/** These variable declarations aide in compression. */
		jquery		= jQuery,
		getText		= 'GET',
		functionText	= 'function',

		/**
		 * Similar to jQuery's "load" but tolerates non-200 status codes.
		 * https://github.com/jquery/jquery/blob/master/src/ajax.js#L168.
		 */
		load = function (url, targetDiv, selector, preLoadFunction, postLoadFunction) {

			var completeCallback = function (res) {

				var responseText	= res.responseText,
					html			= selector ? jquery('<div>').append(responseText).find(selector) : responseText;

				jquery(targetDiv).html(html);

				/* did the user supply a post-load function? */
				if (typeof postLoadFunction === functionText) {

					postLoadFunction();
				}
			};

			/** did the user supply a pre-load function? */
			if (typeof preLoadFunction === functionText) {

				preLoadFunction();
			}

			jquery.ajax({
				
				url		: url,
				type		: getText,
				dataType	: 'html',
				complete	: completeCallback
			});
		},

		/**
		 *  Similar to jQuery's "get" but ignores response code.
		 */
		get = function (url, data, success, dataType) {

			jquery.ajax({
				
				url		: url,
				type		: getText,
				data		: data,
				dataType	: dataType,
				complete	: success
			});
		
		},
	
		/**
		 * Fade to "white".
		 */
		applyLoadingStyle = function (targetDiv) {
		
			jquery(targetDiv).fadeTo(0, 0.3);
		},
		
		/**
		 * Fade back to full opacity.
		 */
		removeLoadingStyle = function (targetDiv) {
			
			jquery(targetDiv).fadeTo(0, 1);
		},
	
		/**
		 * Calls "load", but does some additional styling on the target element while it's processing.
		 */
		loadAndStyle = function (url, targetDiv, selector, preLoadFunction, postLoadFunction) {
		
			applyLoadingStyle(targetDiv);
	
			/** one way or another, we're removing the loading style when we're done... */
			var post = function () {
				
				removeLoadingStyle(targetDiv);
			};
	
			/** ... but maybe we want to do something else too */
			if (typeof postLoadFunction === functionText) {
				
				post = function () {
				
					removeLoadingStyle(targetDiv);
					postLoadFunction();
				};
			}
	
			/** do the load. do it! */
			load(url, targetDiv, selector, preLoadFunction, post);
		};
		
	return {

		load				: load,
		applyLoadingStyle	: applyLoadingStyle,
		removeLoadingStyle	: removeLoadingStyle,
		loadAndStyle		: loadAndStyle,
		get					: get
	};
}());

/**
 * Handles dynamic loading of CSS. To be removed...
 */
var TubePressCss = (function () {

	/** http://www.yuiblog.com/blog/2010/12/14/strict-mode-is-coming-to-town/ */
	'use strict';
	
	/*
	 * Dynamically load CSS into the DOM.
	 */
	var load = function (path) {
			
		var	fileref = document.createElement('link');
			
		fileref.setAttribute('rel', 'stylesheet');
		fileref.setAttribute('type', 'text/css');
		fileref.setAttribute('href', path);
		document.getElementsByTagName('head')[0].appendChild(fileref);
	};
	
	return { 
		load	: load
	};
}());

/**
 * Events that TubePress fires.
 */
var TubePressEvents = (function () {
	
	/** http://www.yuiblog.com/blog/2010/12/14/strict-mode-is-coming-to-town/ */
	'use strict';
	
	return {
		
		/** A gallery's primary video has changed. */
		GALLERY_VIDEO_CHANGE	: 'tubepressGalleryVideoChange',
		
		/** Playback of a video started. */
		PLAYBACK_STARTED		: 'tubepressPlaybackStarted',
		
		/** Playback of a video stopped. */
		PLAYBACK_STOPPED		: 'tubepressPlaybackStopped',
		
		/** Playback of a video is buffering. */
		PLAYBACK_BUFFERING		: 'tubepressPlaybackBuffering',
		
		/** Playback of a video is paused. */
		PLAYBACK_PAUSED			: 'tubepressPlaybackPaused',
		
		/** Playback of a video has errored out. */
		PLAYBACK_ERROR			: 'tubepressPlaybackError',
		
		/** An embedded video has been loaded. */
		EMBEDDED_LOAD			: 'tubepressEmbeddedLoad',
		
		/** A new set of thumbnails has entered the DOM. */
		NEW_THUMBS_LOADED		: 'tubepressNewThumbnailsLoaded',
		
		/** An entirely new gallery has entered the DOM. */
		NEW_GALLERY_LOADED		: 'tubepressNewGalleryLoaded',
		
		/** A TubePress player is being invoked. */
		PLAYER_INVOKE			: 'tubepressPlayerInvoke',
		
		/** A TubePress player is being populated. */
		PLAYER_POPULATE			: 'tubepressPlayerPopulate'
	};
}());

var TubePressGallery = (function () {

	/** http://www.yuiblog.com/blog/2010/12/14/strict-mode-is-coming-to-town/ */
	'use strict';
	
	var galleries	= {},
		docElement	= jQuery(document),
		cssLoaded	= {},
		events		= TubePressEvents,
		
		/**
		 * Does the gallery use Ajax pagination?
		 */
		isAjaxPagination = function (galleryId) {
			
			return galleries[galleryId].ajaxPagination;
		},
		
		/**
		 * Does the gallery use auto-next?
		 */
		isAutoNext = function (galleryId) {
		
			return galleries[galleryId].autoNext;
		},
		
		/**
		 * Does the gallery use fluid thumbs?
		 */
		isFluidThumbs = function (galleryId) {
			
			return galleries[galleryId].fluidThumbs;
		},
		
		/**
		 * What's the embedded height for the video player of this gallery?
		 */
		getEmbeddedHeight = function (galleryId) {
			
			return galleries[galleryId].embeddedHeight;
		},
	
		/**
		 * What's the embedded width for the video player of this gallery?
		 */
		getEmbeddedWidth = function (galleryId) {
			
			return galleries[galleryId].embeddedWidth;
		},
		
		/**
		 * What's the gallery's player location name?
		 */
		getPlayerLocationName = function (galleryId) {
			
			return galleries[galleryId].playerLocationName;
		},
		
		/**
		 * What's the sequence of videos for this gallery?
		 */
		getSequence = function (galleryId) {
			
			return galleries[galleryId].sequence;
		},
		
		/**
		 * What's the shortcode for this gallery?
		 */
		getShortcode = function (galleryId) {
			
			return galleries[galleryId].shortcode;
		},
		
		/**
		 * Deprecated. To be removed...
		 */
		getThemeCss = function (galleryId) {
			
			return galleries[galleryId].themeCSS;
		},
		
		/**
		 * Performs gallery initialization on jQuery(document).ready().
		 */
		docReadyInit = function (galleryId, params) {
			
			/** Save the params. */
			galleries[galleryId] = params;

			/** Deprecated theme handling stuff. To be removed... */
			var theme = decodeURIComponent(getThemeCss(galleryId));
			if (theme !== '' && cssLoaded[theme] !== true) {
				TubePressCss.load(getTubePressBaseUrl() + theme);
				cssLoaded[theme] = true;
			}
			
			/** Trigger an event after we've booted. */
			docElement.trigger(events.NEW_GALLERY_LOADED, galleryId);
		},
		
		/**
		 * Register a TubePress gallery.
		 */
		init = function (galleryId, params) {
			
			docElement.ready(function () {
				
				docReadyInit(galleryId, params);
			});
		};
		
	return {
		
		isAjaxPagination		: isAjaxPagination,
		isAutoNext				: isAutoNext,
		isFluidThumbs			: isFluidThumbs,
		getEmbeddedHeight		: getEmbeddedHeight,
		getEmbeddedWidth		: getEmbeddedWidth,
		getPlayerLocationName	: getPlayerLocationName,
		getSequence				: getSequence,
		getShortcode			: getShortcode,
		init					: init
	};
}());

/**
 * Handles player-related functionality (popup, Shadowbox, etc)
 */
var TubePressPlayers = (function () {
	
	/** http://www.yuiblog.com/blog/2010/12/14/strict-mode-is-coming-to-town/ */
	'use strict';
	
	var 
		/** These variable declarations help compression. */
		jquery				= jQuery,
		documentElement		= jquery(document),
		tubepressGallery	= TubePressGallery,
		tubepressEvents		= TubePressEvents,
		decodeUri			= decodeURIComponent,

		/** Keep track of the players we've loaded. */
		loadedPlayers = {},
		
		/**
		 * Find the player required for a gallery and load the JS.
		 */
		bootPlayer = function (e, galleryId) {
			
			var playerName	= tubepressGallery.getPlayerLocationName(galleryId),
				path		= getTubePressBaseUrl() + '/sys/ui/static/players/' + playerName + '/' + playerName + '.js';
	
			/** don't load a player twice... */
			if (loadedPlayers[playerName] !== true) {
				
				loadedPlayers[playerName] = true;
				jquery.getScript(path);
			}
		},
		
		/**
		 * Does this player require population by TubePress?
		 */
		requiresPopulation = function (playerName) {
			
			return playerName !== 'vimeo' && playerName !== 'youtube' && playerName !== 'solo' && playerName !== 'static';
		},
	
		/**
		 * Load up a TubePress player with the given video ID.
		 */
		invokePlayer = function (e, galleryId, videoId) {
			
			var playerName	= tubepressGallery.getPlayerLocationName(galleryId),
				height		= tubepressGallery.getEmbeddedHeight(galleryId),
				width		= tubepressGallery.getEmbeddedWidth(galleryId),
				shortcode	= tubepressGallery.getShortcode(galleryId),
				callback	= function (data) { 
				
					var result = jquery.parseJSON(data.responseText),
						title  = decodeUri(result.title),
						html   = decodeUri(result.html);

					documentElement.trigger(tubepressEvents.PLAYER_POPULATE + playerName, [ title, html, height, width, videoId, galleryId ]); 
				},
				
				dataToSend = {
						
						tubepress_video		: videoId,
						tubepress_shortcode	: shortcode
				},
				
				url = getTubePressBaseUrl() + '/sys/scripts/ajax/playerHtml.php';
		
			/** Announce we're gonna invoke the player... */
			documentElement.trigger(tubepressEvents.PLAYER_INVOKE + playerName, [ videoId, galleryId, width, height ]);
			
			/** If this player requires population, go fetch the HTML for it. */
			if (requiresPopulation(playerName)) {

				/* ... and fetch the HTML for it */
				TubePressAjax.get(url, dataToSend, callback, 'json');
			}
		};

	/** When we see a new gallery... */
	documentElement.bind(tubepressEvents.NEW_GALLERY_LOADED, bootPlayer);
	
	/** When a user clicks a thumbnail... */
	documentElement.bind(tubepressEvents.GALLERY_VIDEO_CHANGE, invokePlayer);
}());

/**
 * Sequencing support for TubePress.
 */
var TubePressSequencer = (function () {

	/** http://www.yuiblog.com/blog/2010/12/14/strict-mode-is-coming-to-town/ */
	'use strict';
	
	var 
	
		/** These variable declarations aide in compression. */
		tubepressGallery		= TubePressGallery,
		jquery					= jQuery,
		docElement				= jquery(document),
		events					= TubePressEvents,
		logger					= TubePressLogger,
		isCurrentlyPlayingVideo	= 'isCurrentlyPlayingVideo',
		currentVideoId			= 'currentVideoId',
		
		/** Galleries that we track. */
		galleries = {},

		/**
		 * Searches through our galleries for one that matches the given.
		 */
		findGalleryThatMatchesTest = function (test) {
			
			var galleryId;
			
			for (galleryId in galleries) {
				
				if (galleries.hasOwnProperty(galleryId)) {
					
					if (test(galleryId)) {
						
						return galleryId;
					}
				}
			}
			
			return undefined;
		},
		
		/**
		 * Find the gallery with the given video loaded up.
		 */
		findGalleryIdWithVideoIdAsCurrent = function (videoId) {
			
			var test = function (galleryId) {
				
				var gall = galleries[galleryId];
				
				return gall[currentVideoId] === videoId;
			};
			
			return findGalleryThatMatchesTest(test);
		},
		
		/**
		 * Find the gallery that is currently playing the given video.
		 */
		findGalleryIdCurrentlyPlayingVideo = function (videoId) {
		
			var test = function (galleryId) {
				
				var gall			= galleries[galleryId],
					isCurrentlyPlaying	= gall[currentVideoId] === videoId && gall[isCurrentlyPlayingVideo];
				
				if (isCurrentlyPlaying) {
					
					return galleryId;
				}
			};
			
			return findGalleryThatMatchesTest(test);
		},
		
		/**
		 * When a new gallery is loaded...
		 */
		onNewGalleryLoaded = function (e, galleryId) {
			
			var gall		= {},
				sequence	= tubepressGallery.getSequence(galleryId);
			
			gall[isCurrentlyPlayingVideo] = false;
			
			/**
			 * If this gallery has a sequence,
			 * save the first video as the "current" video.
			 */
			if (sequence) {
				
				gall[currentVideoId] = sequence[0];
			}
			
			/** Record it. */
			galleries[galleryId] = gall;
			
			if (logger.on()) {
				
				logger.log('Gallery ' + galleryId + ' loaded');
			}
		},
		
		/**
		 * Set a video as "current" for a gallery.
		 */
		changeToVideo = function (galleryId, videoId) {
			
			/** Save it as current. */
			galleries[galleryId][currentVideoId] = videoId;
			
			/** Announce the change. */
			docElement.trigger(events.GALLERY_VIDEO_CHANGE, [ galleryId, videoId] );
		},
		
		/**
		 * Go to the next video in the gallery.
		 */
		next = function (galleryId) {
		
			/** Get the gallery's sequence. This is an array of video ids. */
			var sequence	= tubepressGallery.getSequence(galleryId),
				vidId		= galleries[galleryId][currentVideoId],
				index		= jquery.inArray(vidId, sequence),
				i			= index,
				lastIndex	= sequence ? sequence.length - 1 : index;
			
			/** Sorry, we don't know anything about this video id, or we've reached the end of the gallery. */
			if (index === -1 || index === lastIndex) {
				
				return;
			}
			
			/** Start the next video in line. */
			changeToVideo(galleryId, sequence[i + 1]);
		},
		
		/** Play the previous video in the gallery. */
		prev = function (galleryId) {
				
			/** Get the gallery's sequence. This is an array of video ids. */
			var sequence	= tubepressGallery.getSequence(galleryId),
				vidId		= galleries[galleryId][currentVideoId],
				index		= jquery.inArray(vidId, sequence),
				i			= index;
			
			/** Sorry, we don't know anything about this video id, or we're at the start of the gallery. */
			if (index === -1 || index === 0) {
				
				return;
			}
			
			/** Start the previous video in line. */
			changeToVideo(galleryId, sequence[i + 1]);
		},
		
		/**
		 * A video on the page has started.
		 */
		onPlaybackStarted = function (e, videoId) {
			
			var matchingGalleryId = findGalleryIdWithVideoIdAsCurrent(videoId);
			
			/**
			 * If we don't have a gallery assigned to this video, we don't really care.
			 */
			if (! matchingGalleryId) {
				
				return;
			}
			
			/**
			 * Record the video as playing.
			 */
			galleries[matchingGalleryId][isCurrentlyPlayingVideo]	= true;
			galleries[matchingGalleryId][currentVideoId]				= videoId;
			
			if (logger.on()) {
				
				logger.log('Playback of ' + videoId + ' started for gallery ' + matchingGalleryId);
			}
		},
		
		/**
		 * A video on the page has stopped.
		 */
		onPlaybackStopped = function (e, videoId) {
			
			var matchingGalleryId = findGalleryIdCurrentlyPlayingVideo(videoId);
			
			/**
			 * If we don't have a gallery assigned to this video, we don't really care.
			 */
			if (! matchingGalleryId) {
				
				return;
			}
			
			/**
			 * Record the video as not playing.
			 */
			galleries[matchingGalleryId][isCurrentlyPlayingVideo] = false;
			
			if (logger.on()) {
				
				logger.log('Playback of ' + videoId + ' stopped for gallery ' + matchingGalleryId);
			}
			
			if (tubepressGallery.isAutoNext(matchingGalleryId) && tubepressGallery.getSequence(matchingGalleryId)) {
				
				if (logger.on()) {
					
					logger.log('Auto-starting next for gallery ' + matchingGalleryId);
				}
				
				/** Go to the next one! */
				next(matchingGalleryId);
			}
		};

		/** We want to keep track of all galleries that are loaded. */
		docElement.bind(events.NEW_GALLERY_LOADED, onNewGalleryLoaded);
		
		/** We would like to be notified when a video starts */
		docElement.bind(events.PLAYBACK_STARTED, onPlaybackStarted);
		
		/** We would like to be notified when a video ends, in the case of auto-next. */
		docElement.bind(events.PLAYBACK_STOPPED, onPlaybackStopped);
		
	return {
		
		changeToVideo			: changeToVideo,
		next					: next,
		prev					: prev
	};
	
}());

/**
 * Handles basic thumbnail tasks.
 */
var TubePressThumbs = (function () {

	/** http://www.yuiblog.com/blog/2010/12/14/strict-mode-is-coming-to-town/ */
	'use strict';
	
	var
		/** These variables aide in compression. */
		jquery		= jQuery,
		events		= TubePressEvents,
		docElement	= jquery(document),
		math		= Math,
	
		/** Events we're interested in. */
		eventsToBindTo = events.NEW_THUMBS_LOADED + ' ' + events.NEW_GALLERY_LOADED,
		
		/**
		 * Get the jQuery selector where the thumbs live.
		 */
		//TODO: this is hard-coded - need to get rid of that.
		getThumbAreaSelector = function (galleryId) {
		
			return "#tubepress_gallery_" + galleryId + "_thumbnail_area";
		},
		
		/**
		 * Get the jQuery reference to where the thumbnails live.
		 */
		getThumbArea = function (galleryId) {
			
			return jquery(getThumbAreaSelector(galleryId));
		},
	
		/**
		 * Parse the gallery ID from the "rel" attribute.
		 */
		getGalleryIdFromRelSplit = function (relSplit) {
			
			return relSplit[3];
		},
		
		/**
		 * Parse the video ID from the "rel" attribute.
		 */
		getVideoIdFromIdAttr = function (id) {
			
			var end = id.lastIndexOf('_');
			
			return id.substring(16, end);
		},
		
		/**
		 * Get the thumbnail width.
		 */
		getThumbWidth = function (galleryId) {
			
			return getThumbArea(galleryId).find('img:first').width();
		},
		
		/**
		 * Click listener callback.
		 */
		clickListener = function () {
			
			var rel_split	= jquery(this).attr('rel').split('_'),
				galleryId	= getGalleryIdFromRelSplit(rel_split),
				videoId		= getVideoIdFromIdAttr(jquery(this).attr('id'));
		
			/** Tell the gallery to change it's video. */
			TubePressSequencer.changeToVideo(galleryId, videoId);
		},

		/** http://www.sohtanaka.com/web-design/smart-columns-w-css-jquery/ */
		makeThumbsFluid = function (galleryId) {
			
			getThumbArea(galleryId).css({ 'width' : '100%' });
			
			var gallerySelector	= getThumbAreaSelector(galleryId),
				columnWidth		= getThumbWidth(galleryId),
				gallery			= jquery(gallerySelector),
				colWrap			= gallery.width(), 
				colNum			= math.floor(colWrap / columnWidth), 
				colFixed		= math.floor(colWrap / colNum),
				thumbs			= jquery(gallerySelector + ' div.tubepress_thumb');
			
			gallery.css({ 'width' : '100%'});
			gallery.css({ 'width' : colWrap });
			thumbs.css({ 'width' : colFixed});
		},
		
		/**
		 * What page is the gallery on?
		 */
		//TODO: this is way too fragile.
		getCurrentPageNumber = function (galleryId) {
			
			var page = 1, 
				paginationSelector = 'div#tubepress_gallery_' + galleryId + ' div.tubepress_thumbnail_area:first > div.pagination:first > span.current',
				current = jquery(paginationSelector);
	
			if (current.length > 0) {
				
				page = current.html();
			}
			
			return page;
		},
		
		/**
		 * Callback for thumbnail loads.
		 */
		thumbBinder = function (e, galleryId) {

			/* add a click handler to each link in this gallery */
			jquery("#tubepress_gallery_" + galleryId + " a[id^='tubepress_']").click(clickListener);

			/* fluid thumbs if we need it */
			if (TubePressGallery.isFluidThumbs(galleryId)) {
				
				makeThumbsFluid(galleryId);
			}
		};

		docElement.bind(eventsToBindTo, thumbBinder);

	/* return only public functions */
	return {
		
		getCurrentPageNumber		: getCurrentPageNumber,
		getGalleryIdFromRelSplit	: getGalleryIdFromRelSplit,
		getThumbAreaSelector		: getThumbAreaSelector,
		getVideoIdFromIdAttr		: getVideoIdFromIdAttr
	};
}());

/**
 * Functions for handling Ajax pagination.
 */
var TubePressAjaxPagination = (function () {
	
	/** http://www.yuiblog.com/blog/2010/12/14/strict-mode-is-coming-to-town/ */
	'use strict';
	
	var
		/** These variable declarations aide in compression. */
		jquery		= jQuery,
		docElement	= jquery(document),
		events		= TubePressEvents,
		gallery		= TubePressGallery,
		
		/** Events we're interested in. */
		eventsToBindTo = events.NEW_THUMBS_LOADED + ' ' + events.NEW_GALLERY_LOADED,
	
		/**
		 * After we've loaded a new set of thumbs.
		 */
		postLoad = function (galleryId) {
		
			docElement.trigger(TubePressEvents.NEW_THUMBS_LOADED, galleryId);
		},
		
		/** Handles an ajax pagination click. */
		processClick = function (anchor, galleryId) {
			
			var baseUrl				= getTubePressBaseUrl(), 
				shortcode			= gallery.getShortcode(galleryId),
				page				= anchor.attr('rel'),
				thumbnailArea		= TubePressThumbs.getThumbAreaSelector(galleryId),
				postLoadCallback	= function () { postLoad(galleryId); },
				pageToLoad			= baseUrl + '/sys/scripts/ajax/shortcode_printer.php?shortcode=' + shortcode + '&tubepress_' + page + '&tubepress_galleryId=' + galleryId,
				remotePageSelector	= thumbnailArea + ' > *';
				
			TubePressAjax.loadAndStyle(pageToLoad, thumbnailArea, remotePageSelector, '', postLoadCallback);
		},
		
		/** Initializes pagination HTML for Ajax. */
		addClickHandlers = function (galleryId) {
			
			var clickCallback = function () {
				processClick(jquery(this), galleryId);
			};
			
			jquery('#tubepress_gallery_' + galleryId + ' div.pagination a').click(clickCallback);
		},

		/**
		 * Adds click handlers to galleries with Ajax pagination.
		 */
		paginationBinder = function (e, galleryId) {
			
			if (gallery.isAjaxPagination(galleryId)) {
				
				addClickHandlers(galleryId);
			}
		};

	/** Sets up new thumbnails for ajax pagination */
	docElement.bind(eventsToBindTo, paginationBinder);
	
}());

/**
 * Browser quirks and small performance improvements.
 */
var TubePressCompat = (function () {

	/** http://www.yuiblog.com/blog/2010/12/14/strict-mode-is-coming-to-town/ */
	'use strict';
	
	var jquery = jQuery,
	
		init = function () {

			/* caching script loader */
			jquery.getScript = function (url, callback, cache) {
			
				jquery.ajax({ type: 'GET', url: url, success: callback, dataType: 'script', cache: cache }); 
			};
		};

	return { init: init };

}());

/**
 * Provides auto-sequencing capability for TubePress.
 */
var TubePressPlayerApi = (function () {
	
	/** http://www.yuiblog.com/blog/2010/12/14/strict-mode-is-coming-to-town/ */
	'use strict';
	
	var 
	
		/** These variable declarations aide in compression. */
		jquery					= jQuery,
		documentElement			= jquery(document),
		events					= TubePressEvents,
		undef					= 'undefined',
		logger					= TubePressLogger,

		/** YouTube variables. */
		loadingYouTubeApi		= false,
		youTubePrefix			= 'tubepress-youtube-player-',
		youTubePlayers			= {},
		youTubeIdPattern		= /[a-z0-9\-_]{11}/i,

		/** Vimeo variables. */
		loadingVimeoApi			= false,
		vimeoPrefix				= 'tubepress-vimeo-player-',
		vimeoPlayers			= {},
		vimeoIdPattern			= /[0-9]+/,

		/**
		 * Is the given video ID from YouTube?
		 */
		isYouTubeVideoId = function (videoId) {
			
			return youTubeIdPattern.test(videoId);
		},
		
		/**
		 * Is the given video ID from Vimeo?
		 */
		isVimeoVideoId = function (videoId) {
			
			return vimeoIdPattern.test(videoId);
		},
	
		/**
		 * Helper method to trigger events on jQuery(document).
		 */
		triggerEvent = function (eventName, videoId) {
		
			if (logger.on()) {
				
				logger.log('Firing ' + eventName + ' for ' + videoId);
			}
			
			documentElement.trigger(eventName, videoId);
		},
		
		/**
		 * A video has started.
		 */
		fireVideoStartedEvent = function (videoId) {

			triggerEvent(events.PLAYBACK_STARTED, videoId);
		},
		
		/**
		 * A video has stopped.
		 */
		fireVideoStoppedEvent = function (videoId) {

			triggerEvent(events.PLAYBACK_STOPPED, videoId);
		},
	
		/**
		 * A video is buffering.
		 */
		fireVideoBufferingEvent = function (videoId) {
			
			triggerEvent(events.PLAYBACK_BUFFERING, videoId);
		},
		
		/**
		 * A video has paused.
		 */
		fireVideoPausedEvent = function (videoId) {

			triggerEvent(events.PLAYBACK_PAUSED, videoId);
		},
		
		/**
		 * A video has encountered an error.
		 */
		fireVideoErrorEvent = function (videoId) {
			
			triggerEvent(events.PLAYBACK_ERROR, videoId);
		},
		
		/**
		 * Pulls out the video ID from a YouTube event.
		 */
		getVideoIdFromYouTubeEvent = function (event) {
			
			var domId	= event.target.a.id,
				vId		= domId.replace(youTubePrefix, ''),
				player	= youTubePlayers[vId],
				url, loadedId, ampersandPosition;

			if (typeof player.getVideoUrl !== 'function') {

				return null;
			}

			//noinspection JSUnresolvedFunction
			url                 = player.getVideoUrl();
			loadedId            = url.split('v=')[1];
			ampersandPosition   = loadedId.indexOf('&');
		
			if (ampersandPosition !== -1) {

				loadedId = loadedId.substring(0, ampersandPosition);
			}

			return loadedId;	
		},
		
		/**
		 * Pulls out the video ID from a Vimeo event.
		 */
		getVideoIdFromVimeoEvent = function (event) {
			
			return event.replace(vimeoPrefix, '');
		},
		
		/**
		 * Utility to wait for test() to be true, then call callback()
		 */
		callWhenTrue = function (callback, test, delay) {
		
			/** It's ready... */
			if (test() === true) {
				
				callback();
				return;
			}
			
			/** Set up a timeout callback. */
			var func = function () {
				
				callWhenTrue(callback, test, delay);
			};

			/** Keep waiting... */
			setTimeout(func, delay);
		},
		
		/**
		 * Is the YouTube API available yet?
		 */
		isYouTubeApiAvailable = function () {
			
			return typeof YT !== undef && typeof YT.Player !== undef;
		},
		
		/**
		 * Is the Vimeo API available yet?
		 */
		isVimeoApiAvailable = function () {
			
			return typeof Froogaloop !== undef;
		},
		
		/**
		 * Load the YouTube API, if necessary.
		 */
		loadYouTubeApi = function () {
	
			if (! loadingYouTubeApi && ! isYouTubeApiAvailable()) {
				
				if (logger.on()) {
					
					logger.log('Loading YT API');
				}
				
				loadingYouTubeApi = true;
				jquery.getScript('http://www.youtube.com/player_api');
			}
		},
		
		/**
		 * Load the Vimeo API, if necessary.
		 */
		loadVimeoApi = function () {
			
			if (! loadingVimeoApi && ! isVimeoApiAvailable()) {
				
				if (logger.on()) {
					
					logger.log('Loading Vimeo API');
				}
				
				loadingVimeoApi = true;
				jquery.getScript('http://a.vimeocdn.com/js/froogaloop2.min.js');
			}
		},
		
		/**
		 * The YouTube player will call this method when a player event
		 * fires.
		 */
		onYouTubeStateChange = function (event) {
			
			var videoId		= getVideoIdFromYouTubeEvent(event),
				eventData	= event.data,
				playerState	= YT.PlayerState;
			
			/**
			 * If we can't parse the event, just bail.
			 */
			if (videoId === null) {
				
				return;
			}
			
			switch (eventData) {
			
				case playerState.PLAYING:
					
					fireVideoStartedEvent(videoId);
					break;
					
				case playerState.PAUSED:
					
					fireVideoPausedEvent(videoId);
					break;
					
				case playerState.ENDED:
					
					fireVideoStoppedEvent(videoId);
					break;
					
				case playerState.BUFFERING:
					
					fireVideoBufferingEvent(videoId);
					break;
					
				default:
					
					if (logger.on()) {
						
						logger.log('Unknown YT event');
						logger.dir(event);
					}
					
					//unknown event
					break;
			}
		},
		
		/**
		 * YouTube will call this when a player hits an error.
		 */
		onYouTubeError = function (event) {
			
			var videoId = getVideoIdFromYouTubeEvent(event);
			
			if (videoId === null) {
				
				return;
			}
			
			if (logger.on()) {
				
				logger.log('YT error');
				logger.dir(event);
			}
			
			fireVideoErrorEvent(videoId);
		},
		
		/**
		 * Vimeo will call then when a video starts.
		 */
		onVimeoPlay = function (event) {
			
			var videoId = getVideoIdFromVimeoEvent(event);
			
			fireVideoStartedEvent(videoId);
		},
		
		/**
		 * Vimeo will call then when a video pauses.
		 */
		onVimeoPause = function (event) {
			
			var videoId = getVideoIdFromVimeoEvent(event);
			
			fireVideoPausedEvent(videoId);
		},
		
		/**
		 * Vimeo will call then when a video ends.
		 */
		onVimeoFinish = function (event) {
			
			var videoId = getVideoIdFromVimeoEvent(event);
			
			fireVideoStoppedEvent(videoId);
		},
		
		/**
		 * A Vimeo player is ready for action.
		 */
		onVimeoReady = function (playerId) {
			
			var froog = vimeoPlayers[playerId];
			
			froog.addEvent('play', onVimeoPlay);
			froog.addEvent('pause', onVimeoPause);
			froog.addEvent('finish', onVimeoFinish);
		},

		/**
		 * Registers a YouTube player for use with the TubePress API.
		 */
		registerYouTubeVideo = function (videoId) {
			
			/** Load 'er up. */
			loadYouTubeApi();
			
			/** This stuff will execute once the TubePress API is loaded. */
			var callback = function () {
				
				if (logger.on()) {
					
					logger.log('Register YT video ' + videoId + ' with TubePress');
				}
				
				youTubePlayers[videoId] = new YT.Player(youTubePrefix + videoId, {
					
					events: {
						
					      'onError'			: onYouTubeError,
					      'onStateChange'	: onYouTubeStateChange
					}
				});
			};
			
			/** Execute it when YouTube is ready. */
			callWhenTrue(callback, isYouTubeApiAvailable, 300);
		},
		
		/**
		 * Registers a Vimeo player for use with the TubePress API.
		 */
		registerVimeoVideo = function (videoId) {
			
			/** Load up the API. */
			loadVimeoApi();
			
			var playerId	= vimeoPrefix + videoId,
				iframe		= document.getElementById(playerId),
				callback	= function () {
				
					var froog;
				
					if (logger.on()) {
					
						logger.log('Register Vimeo video ' + videoId + ' with TubePress');
					}
				
					/** Create and save the player. */
					froog = new Froogaloop(iframe);
					
					vimeoPlayers[playerId] = froog;
					
					froog.addEvent('ready', onVimeoReady);
				};
			
			/** Execute it when Vimeo is ready. */
			callWhenTrue(callback, isVimeoApiAvailable, 800);
		},
		
		/**
		 * Registers a play for use with the TubePress API, but only
		 * when jQuery(document).ready() has been called.
		 */
		docReadyRegister = function (videoId) {
			
			if (isYouTubeVideoId(videoId)) {
				
				registerYouTubeVideo(videoId);
				
			} else if (isVimeoVideoId(videoId)) {
				
				registerVimeoVideo(videoId);
			}
			
			/** Notify anyone that's interested. */
			triggerEvent(events.EMBEDDED_LOAD, videoId);
		},
		
		/**
		 * Registers an arbitrary video for use with the TubePress API.
		 */
		register = function (videoId) {

			documentElement.ready(function () {
				
				try {
				
					docReadyRegister(videoId);
				
				} catch (e) {
					
					logger.log('Error when registering: ' + e);
				}
			});
		};
		
	return {
	
		register				:	register,
		isYouTubeVideoId		:	isYouTubeVideoId,
		isVimeoVideoId			:	isVimeoVideoId,
		onYouTubeStateChange	:	onYouTubeStateChange,
		onYouTubeError			:	onYouTubeError,
		onVimeoPlay				:	onVimeoPlay,
		onVimeoPause			:	onVimeoPause,
		onVimeoFinish			:	onVimeoFinish,
		onVimeoReady			:	onVimeoReady
	};
	
}());

/**
 * Handles Ajax interactive searching.
 */
var TubePressAjaxSearch = (function () {
	
	/** http://www.yuiblog.com/blog/2010/12/14/strict-mode-is-coming-to-town/ */
	'use strict';
	
	var performSearch = function (urlEncodedShortcode, urlEncodedSearchTerms, targetDomSelector, galleryId) {
		
		/** These variable declarations aide in compression. */
		var jquery	= jQuery,
			logger	= TubePressLogger,
			
			/** Some vars we'll need later. */
			callback,
			ajaxResultSelector,
			finalAjaxContentDestination,
		
			/** The Ajax response results that we're interested in. */
			gallerySelector = '#tubepress_gallery_' + galleryId,
			
			/** Does a gallery with this ID already exist? */
			galleryExists = jquery(gallerySelector).length > 0,
			
			/** Does the target DOM exist? */
			targetDomExists = targetDomSelector && targetDomSelector !== '' && jquery(targetDomSelector).length > 0;
			
		/** We have three cases to handle... */
		if (galleryExists) {
			
			//CASE 1: gallery already exists

			/** Stick the thumbs into the existing thumb area. */
			finalAjaxContentDestination = TubePressThumbs.getThumbAreaSelector(galleryId);
			
			/** We want just the new thumbnails. */
			ajaxResultSelector = finalAjaxContentDestination + ' > *';
			
			/** Announce the new thumbs */
			callback = function () {
				
				jquery(document).trigger(TubePressEvents.NEW_THUMBS_LOADED, galleryId);
			};
			
		} else {
			
			if (targetDomExists) {
				
				//CASE 2: TARGET SELECTOR EXISTS AND GALLERY DOES NOT EXIST
				
				/** Stick the gallery into the target DOM. */
				finalAjaxContentDestination = targetDomSelector;
				
			} else {
				
				//CASE 3: TARGET SELECTOR DOES NOT EXIST AND GALLERY DOES NOT EXIST
				
				if (logger.on()) {
					
					logger.log('Bad target selector and missing gallery');
				}
				
				return;
			}
		}
		
		if (logger.on()) {
			
			logger.log('Final dest: ' + finalAjaxContentDestination);
			logger.log('Ajax selector: ' + ajaxResultSelector);
		}
		
		TubePressAjax.loadAndStyle(getTubePressBaseUrl() + '/sys/scripts/ajax/shortcode_printer.php?shortcode=' 
				+ urlEncodedShortcode + '&tubepress_search=' + urlEncodedSearchTerms, finalAjaxContentDestination, ajaxResultSelector, null, callback);
	};
	
	return { performSearch : performSearch };
	
}());

/**
 * Dependency checks for TubePress.
 */
var TubePressDepCheck = (function () {
	
	/** http://www.yuiblog.com/blog/2010/12/14/strict-mode-is-coming-to-town/ */
	'use strict';
	
	var init = function () {
		
		var version			= jQuery.fn.jquery,
			windowConsole	= window.console;

		if (/1\.6|7|8|9\.[0-9]+/.test(version) === false) {

			/** Try to log it... */
			if (typeof windowConsole !== 'undefined') {
				
				windowConsole.log("TubePress requires jQuery 1.6 or higher. This page is running version " + version);
			}
		}
	};
	
	return { init : init };
	
}());

/**
 * Primary TubePress boot function.
 */
var tubePressBoot = function () {
	
	/** http://www.yuiblog.com/blog/2010/12/14/strict-mode-is-coming-to-town/ */
	'use strict';
	
	TubePressCompat.init();
	TubePressDepCheck.init();
};

/**
 * Append our init method to after all the other (potentially full of errors) ready blocks have 
 * run. http://stackoverflow.com/questions/1890512/handling-errors-in-jquerydocument-ready
 */
if (!jQuery.browser.msie) {
	
	var oldReady = jQuery.ready;

	jQuery.ready = function () {
	
		/** http://www.yuiblog.com/blog/2010/12/14/strict-mode-is-coming-to-town/ */
		'use strict';
		
		try {
			
			oldReady.apply(this, arguments);
		
		} catch (e) {
			
			/** Try to log it. */
			if (typeof console !== 'undefined') {
			
				console.log("Caught exception when booting TubePress: " + e);
			}
		}
		
		tubePressBoot();
	};

    jQuery.ready.promise = oldReady.promise;
	
} else {
	
	jQuery(document).ready(function () {
		
		/** http://www.yuiblog.com/blog/2010/12/14/strict-mode-is-coming-to-town/ */
		'use strict';
		
		tubePressBoot();
	});
}
