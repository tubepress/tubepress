/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
 * 
 * This file is part of TubePress (http://tubepress.org) and is released 
 * under the General Public License (GPL) version 3
 *
 * Shrink your JS: http://developer.yahoo.com/yui/compressor/
 */

/*global jQuery, getTubePressBaseUrl, alert */
/*jslint sloppy: true, white: true, vars: false, undef: true, newcap: true, nomen: true, regexp: true, plusplus: true, bitwise: true, continue: true, browser: true, maxerr: 50, indent: 4 */

var TubePressAjax = (function () {

	/*
	 * Similar to jQuery's "load" but tolerates non-200 status codes.
	 * https://github.com/jquery/jquery/blob/master/src/ajax.js#L168.
	 */
	var load = function (url, targetDiv, selector, preLoadFunction, postLoadFunction) {
		
		/* did the user supply a pre-load function? */
		if (typeof preLoadFunction === 'function') {
			preLoadFunction();
		}
		
		jQuery.ajax({
			url	: url,
			type	: 'GET',
			dataType: 'html',
			complete: function (res) {

				var html = selector ? jQuery('<div>').append(res.responseText).find(selector) : res.responseText;

				jQuery(targetDiv).html(html);

				/* did the user supply a post-load function? */
				if (typeof postLoadFunction === 'function') {
					postLoadFunction();
				}
			}
		});
	},
	
		/* Similar to jQuery's "get" but ignores response code. */
		get = function (url, data, success, dataType) {
		
			jQuery.ajax({
				url: url,
				type: 'GET',
				data: data,
				dataType: dataType,
				complete: success
			});
		
		},
	
		/* fade to "white" */
		applyLoadingStyle = function (targetDiv) {
		
			jQuery(targetDiv).fadeTo(0, 0.3);
		},
		
		/* fade to full opacity */
		removeLoadingStyle = function (targetDiv) {
			
			jQuery(targetDiv).fadeTo(0, 1);
		},
	
		/*
		 * Calls "load", but does some additional styling on the target element while it's processing.
		 */
		loadAndStyle = function (url, targetDiv, selector, preLoadFunction, postLoadFunction) {
		
			applyLoadingStyle(targetDiv);
	
			/* one way or another, we're removing the loading style when we're done... */
			var post = function () { removeLoadingStyle(targetDiv); };
	
			/* ... but maybe we want to do something else too */
			if (typeof postLoadFunction === 'function') {
				post = function () {
					removeLoadingStyle(targetDiv);
					postLoadFunction();
				};
			}
	
			/* do the load. do it! */
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
 * Handles dynamic loading of CSS.
 */
var TubePressCss = (function () {

	/*
	 * Dynamically load CSS into the DOM.
	 */
	var load = function (path) {
			
		var fileref = document.createElement('link');
			
		fileref.setAttribute('rel', 'stylesheet');
		fileref.setAttribute('type', 'text/css');
		fileref.setAttribute('href', path);
		document.getElementsByTagName('head')[0].appendChild(fileref);
	};
	
	return { 
		load	: load
	};
}());

var TubePressEvents = (function () {
	
	return {
		
		/** Playback of a video started. */
		PLAYBACK_STARTED	: 'tubepressPlaybackStarted',
		
		/** Playback of a video stopped. */
		PLAYBACK_STOPPED	: 'tubepressPlaybackStopped',
		
		/** Playback of a video is buffering. */
		PLAYBACK_BUFFERING	: 'tubepressPlaybackBuffering',
		
		/** Playback of a video is paused. */
		PLAYBACK_PAUSED		: 'tubepressPlaybackPaused',
		
		/** Playback of a video has errored out. */
		PLAYBACK_ERROR		: 'tubepressPlaybackError',
		
		/** A new set of thumbnails has entered the DOM. */
		NEW_THUMBS_LOADED	: 'tubepressNewThumbnailsLoaded',
		
		/** An entirely new gallery has entered the DOM. */
		NEW_GALLERY_LOADED	: 'tubepressNewGalleryLoaded',
		
		/** A TubePress thumbnail has been clicked. */
		THUMBNAIL_CLICKED   : 'tubepressThumbnailClicked',
		
		/** A TubePress player is being invoked. */
		PLAYER_INVOKE		: 'tubepressPlayerInvoke',
		
		/** A TubePress player is being populated. */
		PLAYER_POPULATE		: 'tubepressPlayerPopulate'
	};
}());

var TubePressGallery = (function () {

	var galleries = {},
	
		cssLoaded = {},
	
		isAjaxPagination = function (galleryId) {
			
			return galleries[galleryId].ajaxPagination;
		},
		
		isAutoNext = function (galleryId) {
		
			return galleries[galleryId].autoNext;
		},
		
		isFluidThumbs = function (galleryId) {
			
			return galleries[galleryId].fluidThumbs;
		},
		
		isJsApiEnabled = function (galleryId) {
			
			return galleries[galleryId].jsApiEnabled;
		},
	
		getShortcode = function (galleryId) {
			
			return galleries[galleryId].shortcode;
		},
	
		getPlayerLocationName = function (galleryId) {
			
			return galleries[galleryId].playerLocationName;
		},
	
		getEmbeddedHeight = function (galleryId) {
			
			return galleries[galleryId].embeddedHeight;
		},
	
		getEmbeddedWidth = function (galleryId) {
			
			return galleries[galleryId].embeddedWidth;
		},
		
		getSequence = function (galleryId) {
			
			return galleries[galleryId].sequence;
		},
		
		getThemeCss = function (galleryId) {
			
			return galleries[galleryId].themeCSS;
		},
		
		delayedTrigger = function (galleryId) {
			
			/** Trigger callback. */
			var callback = function () {
				
				jQuery(document).trigger(TubePressEvents.NEW_GALLERY_LOADED, galleryId);
			};
			
			TubePressBoot.runAfterBoot(callback);
		},
		
		init = function (galleryId, params) {

			/* save the params */
			galleries[galleryId] = params;
			
			var theme = decodeURIComponent(getThemeCss(galleryId));
			
			if (theme !== '' && cssLoaded[theme] !== true) {
			
				TubePressCss.load(getTubePressBaseUrl() + theme);
				cssLoaded[theme] = true;
			}
			
			/** Trigger an event after we've booted. */
			delayedTrigger(galleryId);
		};

	return {
		
		isAjaxPagination		: isAjaxPagination,
		isAutoNext				: isAutoNext,
		isFluidThumbs			: isFluidThumbs,
		isJsApiEnabled			: isJsApiEnabled,
		getShortcode			: getShortcode,
		getPlayerLocationName	: getPlayerLocationName,
		getEmbeddedHeight		: getEmbeddedHeight,
		getEmbeddedWidth		: getEmbeddedWidth,
		getSequence				: getSequence,
		init					: init
	};
}());

/* handles player-related functionality (popup, Shadowbox, etc) */
var TubePressPlayers = (function () {
	
	/* record of the players we've already loaded */
	var loadedPlayers = {},
	
		/* helps YUI compressor */
		jquery           = jQuery,
		documentElement  = jquery(document),
		tubepressGallery = TubePressGallery,
		tubepressEvents  = TubePressEvents,
	
		/* find the player required for a gallery and load the JS. */
		bootPlayer = function (e, galleryId) {
			
			var baseUrl		= getTubePressBaseUrl(),
				playerName	= tubepressGallery.getPlayerLocationName(galleryId),
				path		= baseUrl + '/sys/ui/static/players/' + playerName + '/' + playerName + '.js';
	
			/* don't load a player twice */
			if (loadedPlayers[playerName] === true) {
				
				return;
			
			} else {
			
				loadedPlayers[playerName] = true;
			}
	
			jquery.getScript(path);
		},
		
		requiresPopulation = function (playerName) {
			
			return playerName !== 'vimeo' && playerName !== 'youtube' && playerName !== 'solo' && playerName !== 'static';
		},
	
		invokePlayer = function (e, videoId, galleryId) {
			
			var playerName			= tubepressGallery.getPlayerLocationName(galleryId),
				height				= tubepressGallery.getEmbeddedHeight(galleryId),
				width				= tubepressGallery.getEmbeddedWidth(galleryId),
				shortcode			= tubepressGallery.getShortcode(galleryId),
				callback			= function (data) { 
				
					var result = jQuery.parseJSON(data.responseText),
						title  = decodeURIComponent(result.title),
						html   = decodeURIComponent(result.html);

					documentElement.trigger(tubepressEvents.PLAYER_POPULATE + playerName, [ title, html, height, width, videoId, galleryId ]); 
				},
				dataToSend			= { tubepress_video : videoId, tubepress_shortcode : shortcode },
				url					= getTubePressBaseUrl() + '/sys/scripts/ajax/playerHtml.php';
		
			/* announce we're gonna invoke the player... */
			documentElement.trigger(tubepressEvents.PLAYER_INVOKE + playerName, [ videoId, galleryId, width, height ]);
			
			if (requiresPopulation(playerName)) {
				
				/* ... and fetch the HTML for it */
				TubePressAjax.get(url, dataToSend, callback, 'json');
			}
		};
	
	/* when we see a new gallery... */
	documentElement.bind(tubepressEvents.NEW_GALLERY_LOADED, bootPlayer);
	
	/* when a user clicks a thumbnail... */
	documentElement.bind(tubepressEvents.THUMBNAIL_CLICKED, invokePlayer);
}());

/**
 * Main TubePress gallery module.
 */
var TubePressThumbs = (function () {

	var jquery = jQuery,
	
		getThumbAreaSelector = function (galleryId) {
		
			return "#tubepress_gallery_" + galleryId + "_thumbnail_area";
		},
		
		getThumbArea = function (galleryId) {
			
			return jquery(getThumbAreaSelector(galleryId));
		},
	
		getGalleryIdFromRelSplit = function (relSplit) {
			
			return relSplit[3];
		},
		
		getVideoIdFromIdAttr = function (id) {
			
			var end = id.lastIndexOf('_');
			
			return id.substring(16, end);
		},
		
		getThumbWidth = function (galleryId) {
			
			return getThumbArea(galleryId).find('img:first').width();
		},
		
		clickListener = function () {
			
			var rel_split	= jquery(this).attr('rel').split('_'),
				galleryId	= getGalleryIdFromRelSplit(rel_split),
				videoId		= getVideoIdFromIdAttr(jquery(this).attr('id'));
		
			jquery(document).trigger(TubePressEvents.THUMBNAIL_CLICKED, [ videoId, galleryId ]);
		},

		/* http://www.sohtanaka.com/web-design/smart-columns-w-css-jquery/ */
		makeThumbsFluid = function (galleryId) {
			
			getThumbArea(galleryId).css({ 'width' : '100%' });
			
			var gallerySelector	= getThumbAreaSelector(galleryId),
				columnWidth		= getThumbWidth(galleryId),
				gallery			= jquery(gallerySelector),
				colWrap			= gallery.width(), 
				colNum			= Math.floor(colWrap / columnWidth), 
				colFixed		= Math.floor(colWrap / colNum),
				thumbs			= jquery(gallerySelector + ' div.tubepress_thumb');
			
			gallery.css({ 'width' : '100%'});
			gallery.css({ 'width' : colWrap });
			thumbs.css({ 'width' : colFixed});
		},
		
		getCurrentPageNumber = function (galleryId) {
			
			var page = 1, 
				paginationSelector = 'div#tubepress_gallery_' + galleryId
					+ ' div.tubepress_thumbnail_area:first > div.pagination:first > span.current',
				current = jquery(paginationSelector);
	
			if (current.length > 0) {
				
				page = current.html();
			}
			
			return page;
		},
		
		thumbBinder = function (e, galleryId) {

			/* add a click handler to each link in this gallery */
			jquery("#tubepress_gallery_" + galleryId + " a[id^='tubepress_']").click(clickListener);

			/* fluid thumbs if we need it */
			if (TubePressGallery.isFluidThumbs(galleryId)) {
				
				makeThumbsFluid(galleryId);
			}
		},
		
		eventsToBindTo = TubePressEvents.NEW_THUMBS_LOADED + ' ' + TubePressEvents.NEW_GALLERY_LOADED;
	
	jquery(document).bind(eventsToBindTo, thumbBinder);

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
	
	/* post thumbnail load setup */
	var jquery = jQuery,
	
		postLoad = function (galleryId) {
		
			jquery(document).trigger(TubePressEvents.NEW_THUMBS_LOADED, galleryId);
		},
		
		/* Handles an ajax pagination click. */
		processClick = function (anchor, galleryId) {
			
			var baseUrl				= getTubePressBaseUrl(), 
				shortcode			= TubePressGallery.getShortcode(galleryId),
				page				= anchor.attr('rel'),
				thumbnailArea		= TubePressThumbs.getThumbAreaSelector(galleryId),
				postLoadCallback	= function () { postLoad(galleryId); },
				pageToLoad			= baseUrl + '/sys/scripts/ajax/shortcode_printer.php?shortcode=' + shortcode + '&tubepress_' + page + '&tubepress_galleryId=' + galleryId,
				remotePageSelector	= thumbnailArea + ' > *';
				
			TubePressAjax.loadAndStyle(pageToLoad, thumbnailArea, remotePageSelector, '', postLoadCallback);
		},
		
		/* initializes pagination HTML for Ajax. */
		addClickHandlers = function (galleryId) {
			
			var clickCallback = function () {
				processClick(jquery(this), galleryId);
			};
			
			jquery('#tubepress_gallery_' + galleryId + ' div.pagination a').click(clickCallback);
		},

		paginationBinder = function (e, galleryId) {
			
			if (TubePressGallery.isAjaxPagination(galleryId)) {
				
				addClickHandlers(galleryId);
			}
		};

	/* sets up new thumbnails for ajax pagination */
	jquery(document).bind(TubePressEvents.NEW_THUMBS_LOADED, paginationBinder);
	jquery(document).bind(TubePressEvents.NEW_GALLERY_LOADED, paginationBinder);
}());

/**
 * Browser quirks and small performance improvements.
 */
var TubePressCompat = (function () {

	var jquery = jQuery,
	
		init = function () {

			/* caching script loader */
			jquery.getScript = function (url, callback, cache) {
			
				jquery.ajax({ type: 'GET', url: url, success: callback, dataType: 'script', cache: cache }); 
			};

			/* http://jquery.malsup.com/fadetest.html */
			jquery.fn.fadeTo = function (speed, to, callback) {
			
				return this.animate({opacity: to}, speed, function () { 
			
					if (to === 1 && jquery.browser.msie) {
					
						this.style.removeAttribute('filter');
					}
				
					if (jquery.isFunction(callback)) {
					
						callback();
					}
				});
			};
		};

	return { init: init };

}());

/**
 * Provides auto-sequencing capability for TubePress.
 */
var TubePressPlayerApi = (function () {
	
	var jquery					= jQuery,
		documentElement			= jquery(document),
		tubepressEvents			= TubePressEvents,
		loadingYouTubeApi		= false,
		events					= TubePressEvents,
		players					= {},
		youTubePrefix			= 'tubepress-youtube-player-',
	
		triggerEvent = function (eventName, galleryId, videoId) {
		
			documentElement.trigger(eventName, galleryId, videoId);
		},
		
		fireVideoStartedEvent = function (galleryId, videoId) {
		
			triggerEvent(events.PLAYBACK_STARTED, galleryId, videoId);
		},
		
		fireVideoStoppedEvent = function (galleryId, videoId) {
			
			triggerEvent(events.PLAYBACK_STOPPED, galleryId, videoId);
		},
	
		fireVideoBufferingEvent = function (galleryId, videoId) {
			
			triggerEvent(events.PLAYBACK_BUFFERING, galleryId, videoId);
		},
		
		fireVideoPausedEvent = function (galleryId, videoId) {
			
			triggerEvent(events.PLAYBACK_PAUSED, galleryId, videoId);
		},
		
		fireVideoErrorEvent = function (galleryId, videoId) {
			
			triggerEvent(events.PLAYBACK_ERROR, galleryId, videoId);
		},
		
		parseYouTubeEvent = function (event) {
			
			var domId	= event.target.a.id,
				gId		= domId.replace(youTubePrefix, ''),
				player	= players[gId],
				videoId	= '';
			
			if (typeof player.getVideoData === 'undefined') {
				
				return null;
			}
			
			videoId = player.getVideoData().video_id;
			
			return {
				
				'vId'	:	videoId,
				'gId'	:	gId
			};
		},
		
		/** Utility to wait for test() to be true, then call callback() */
		callWhenTrue = function (callback, test, delay) {
		
			/** It's ready... */
			if (test() === true) {
				
				callback();
				return;
			}
			
			var func = function () {
				
				callWhenTrue(callback, test, delay);
			};

			/** Keep waiting... */
			setTimeout(func, delay);
		},
		
		isYouTubeApiAvailable = function () {
			
			return typeof YT !== 'undefined' && typeof YT.Player !== 'undefined';
		},
		
		loadYouTubeApi = function () {
	
			if (! loadingYouTubeApi && ! isYouTubeApiAvailable()) {
				
				loadingYouTubeApi = true;
				jquery.getScript('http://www.youtube.com/player_api');
			}
		},
		
		onYouTubeStateChange = function (event) {
			
			var eventData = parseYouTubeEvent(event);
			
			if (eventData === null) {
				
				return;
			}
			
			if (event.data === YT.PlayerState.PLAYING) {
				
				fireVideoStartedEvent(eventData.gId, eventData.vId);
				return;
			}
			
			if (event.data === YT.PlayerState.PAUSED) {
				
				fireVideoPausedEvent(eventData.gId, eventData.vId);
				return;
			}
			
			if (event.data === YT.PlayerState.ENDED) {
				
				fireVideoStoppedEvent(eventData.gId, eventData.vId);
				return;
			}
			
			if (event.data === YT.PlayerState.BUFFERING) {
				
				fireVideoBufferingEvent(eventData.gId, eventData.vId);
				return;
			}
		},
		
		onYouTubeError = function (event) {
			
			var eventData = parseYouTubeEvent(event);
			
			if (eventData === null) {
				
				return;
			}
			
			fireVideoErrorEvent(eventData.gId, eventData.vId);
		},
		
		newGalleryBindYouTube = function (galleryId) {
			
			/** Only load the YouTube API if the gallery has requested it. */
			if (! TubePressGallery.isJsApiEnabled(galleryId)) {
				
				return;
			}
			
			/** Load 'er up. */
			loadYouTubeApi();
			
			/** This stuff will execute once the TubePress API is loaded. */
			var callback = function () {
				
				players[galleryId] = new YT.Player(youTubePrefix + galleryId, {
					
					events: {
					      'onError'			: onYouTubeError,
					      'onStateChange'	: onYouTubeStateChange
					}
				});
			};
			
			/** Execute it when YouTube is ready. */
			callWhenTrue(callback, isYouTubeApiAvailable, 300);
		},
		
		newGalleryBind = function (e, galleryId) {
			
			newGalleryBindYouTube(galleryId);
		},
		
		playerPopulateBind = function () {
			
			//find the iframe and bind to it
		},
		
		init = function () {
			
			documentElement.bind(tubepressEvents.NEW_GALLERY_LOADED, newGalleryBind);
			documentElement.bind(tubepressEvents.PLAYER_POPULATE, playerPopulateBind);
		};
	
	return {
	
		init					:	init,
		onYouTubeStateChange	:	onYouTubeStateChange,
		onYouTubeError			:	onYouTubeError
	};
	
}());

var TubePressDepCheck = (function () {
	
	var init = function () {
		
		var version = jQuery.fn.jquery;

		if (/1\.6|7|8|9\.[0-9]+/.test(version) === false) {

			console.log("TubePress requires jQuery 1.6 or higher. This page is running version " + version);
		}
	};
	
	return { init : init };
	
}());

var TubePressBoot = (function () {
	
	var isBooted = false,
	
		/** A list of callbacks to run after we've booted. */
		queuedCallbacks = [],
	
		/** Runs all the queued callbacks. */
		runQueuedCallbacks = function () {
		
			var i = 0,
				func = queuedCallbacks[0];
		
			for (i; i < queuedCallbacks.length; i++) {
				
				func = queuedCallbacks[i];
				
				func();
			}
		},
		
		/** Boot up. */
		boot = function () {
		
			TubePressCompat.init();
			TubePressDepCheck.init();
			TubePressPlayerApi.init();
			
			isBooted = true;
			
			runQueuedCallbacks();
		},
		
		runAfterBoot = function (callback) {
			
			if (isBooted) {
				
				/** Just call it! */
				callback();
			
			} else {
				
				/** Queue it up for later. */
				queuedCallbacks.push(callback);
			}
		};
	
	return {
		
		boot			:	boot,
		runAfterBoot	:	runAfterBoot
	};
	
}());

var tubePressBoot = function () {
	
	TubePressBoot.boot();
};

/* append our init method to after all the other (potentially full of errors) ready blocks have 
 * run. http://stackoverflow.com/questions/1890512/handling-errors-in-jquerydocument-ready */
if (!jQuery.browser.msie) {
	
	var oldReady = jQuery.ready;

	jQuery.ready = function () {
	
		try {
			
			oldReady.apply(this, arguments);
		
		} catch (e) {
			
			console.log("Caught exception when booting TubePress: " + e);
		}
		
		tubePressBoot();
	};
	
} else {
	
	jQuery(document).ready(function () {
		
		tubePressBoot();
	});
}