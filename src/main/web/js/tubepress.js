/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 *
 * @author Eric D. Hough (eric@tubepress.org)
 * @author Bill Jackson (genalgo@tubepress.org)
 */

/*global jQuery, TubePressGlobalJsConfig, YT, Froogaloop, console */
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
		isLoggingAvailable		= windowConsole !== undefined,

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
}()),

TubePressJson = (function () {

	'use strict';

	var jquery			= jQuery,
		version			= jquery.fn.jquery,
		modernJquery	= /1\.6|7|8|9\.[0-9]+/.test(version) !== false,
		parser,
		parse = function (msg) {

			return parser(msg);
		};

	if (modernJquery) {

		parser = function (msg) {

			return jquery.parseJSON(msg);
		};

	} else {

		parser = function (data) {

			if (typeof data !== 'string' || !data) {

				return null;
			}

			data = jquery.trim(data);

			if (/^[\],:{}\s]*$/.test(data.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, "@")
				.replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, "]")
				.replace(/(?:^|:|,)(?:\s*\[)+/g, ""))) {

				//noinspection JSHint,JSHint
				return window.JSON && window.JSON.parse ?

					window.JSON.parse(data) :

					(new Function("return " + data))();

			} else {

				throw 'Invalid JSON: ' + data;
			}
		};
	}

	return {

		parse : parse
	};
}()),

/**
 * Various Ajax utilities.
 */
TubePressAjax = (function () {

	/** http://www.yuiblog.com/blog/2010/12/14/strict-mode-is-coming-to-town/ */
	'use strict';

	var
		/** These variable declarations aide in compression. */
		jquery = jQuery,

		/**
		 * Similar to jQuery's "load" but tolerates non-200 status codes.
		 * https://github.com/jquery/jquery/blob/master/src/ajax.js#L168.
		 */
		load = function (method, url, targetDiv, selector, preLoadFunction, postLoadFunction) {

			var completeCallback = function (res) {

				var responseText	= res.responseText,
					html			= selector ? jquery('<div>').append(responseText).find(selector) : responseText;

				jquery(targetDiv).html(html);

				/* did the user supply a post-load function? */
				if (jquery.isFunction(postLoadFunction)) {

					postLoadFunction();
				}
			};

			/** did the user supply a pre-load function? */
			if (jquery.isFunction(preLoadFunction)) {

				preLoadFunction();
			}

			jquery.ajax({

				url			: url,
				type		: method,
				dataType	: 'html',
				complete	: completeCallback
			});
		},

		/**
		 * Similar to jQuery's "get" but ignores response code.
		 */
		get = function (method, url, data, success, dataType) {

			jquery.ajax({

				url			: url,
				type		: method,
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
		loadAndStyle = function (method, url, targetDiv, selector, preLoadFunction, postLoadFunction) {

			applyLoadingStyle(targetDiv);

			/** one way or another, we're removing the loading style when we're done... */
			var post = function () {

				removeLoadingStyle(targetDiv);
			};

			/** ... but maybe we want to do something else too */
			if (jquery.isFunction(postLoadFunction)) {

				post = function () {

					removeLoadingStyle(targetDiv);
					postLoadFunction();
				};
			}

			/** do the load. do it! */
			load(method, url, targetDiv, selector, preLoadFunction, post);
		};

	return {

		applyLoadingStyle	: applyLoadingStyle,
		removeLoadingStyle	: removeLoadingStyle,
		loadAndStyle		: loadAndStyle,
		get					: get
	};
}()),

/**
 * Handles dynamic loading of CSS. This should only really be used for TubePress players
 * and NOT TubePress themes.
 */
TubePressCss = (function () {

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
}()),

/**
 * Events that TubePress fires.
 */
TubePressEvents = (function () {

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
}()),

TubePressGallery = (function () {

	/** http://www.yuiblog.com/blog/2010/12/14/strict-mode-is-coming-to-town/ */
	'use strict';

	var galleries	= {},
		docElement	= jQuery(document),
		events		= TubePressEvents,
		nvpMap		= 'nvpMap',
		jsMap		= 'jsMap',

		/**
		 * Does the gallery use Ajax pagination?
		 */
		isAjaxPagination = function (galleryId) {

			//noinspection JSUnresolvedVariable
			return galleries[galleryId][jsMap].ajaxPagination;
		},

		/**
		 * Does the gallery use auto-next?
		 */
		isAutoNext = function (galleryId) {

			//noinspection JSUnresolvedVariable
			return galleries[galleryId][jsMap].autoNext;
		},

		/**
		 * Does the gallery use fluid thumbs?
		 */
		isFluidThumbs = function (galleryId) {

			//noinspection JSUnresolvedVariable
			return galleries[galleryId][jsMap].fluidThumbs;
		},

		/**
		 * What's the embedded height for the video player of this gallery?
		 */
		getEmbeddedHeight = function (galleryId) {

			//noinspection JSUnresolvedVariable
			return galleries[galleryId][nvpMap].embeddedHeight;
		},

		/**
		 * What's the embedded width for the video player of this gallery?
		 */
		getEmbeddedWidth = function (galleryId) {

			//noinspection JSUnresolvedVariable
			return galleries[galleryId][nvpMap].embeddedWidth;
		},

		/**
		 * Which HTTP method (GET or POST) does this gallery want to use?
		 */
		getHttpMethod = function (galleryId) {

			//noinspection JSUnresolvedVariable
			return galleries[galleryId][jsMap].httpMethod;
		},

		getNvpMap = function (galleryId) {

			return galleries[galleryId][nvpMap];
		},

		/**
		 * What's the gallery's player location name?
		 */
		getPlayerLocationName = function (galleryId) {

			//noinspection JSUnresolvedVariable
			return galleries[galleryId][nvpMap].playerLocation;
		},

		/**
		 * Where is the JS init code for this player?
		 */
		getPlayerJsUrl = function (galleryId) {

			//noinspection JSUnresolvedVariable
			return galleries[galleryId][jsMap].playerJsUrl;
		},

		/**
		 * Does this player produce HTML?
		 */
		getPlayerProducesHtml = function (galleryId) {

			//noinspection JSUnresolvedVariable
			return galleries[galleryId][jsMap].playerLocationProducesHtml;
		},

		/**
		 * What's the sequence of videos for this gallery?
		 */
		getSequence = function (galleryId) {

			//noinspection JSUnresolvedVariable
			return galleries[galleryId][jsMap].sequence;
		},

		/**
		 * Performs gallery initialization on jQuery(document).ready().
		 */
		docReadyInit = function (galleryId, params) {

			/** Save the params. */
			galleries[galleryId] = params;

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
		getHttpMethod			: getHttpMethod,
		getNvpMap				: getNvpMap,
		getPlayerLocationName	: getPlayerLocationName,
		getPlayerLocationProducesHtml : getPlayerProducesHtml,
		getPlayerJsUrl          : getPlayerJsUrl,
		getSequence				: getSequence,
		init					: init
	};
}()),

/**
 * Handles player-related functionality (popup, Shadowbox, etc)
 */
TubePressPlayers = (function () {

	/** http://www.yuiblog.com/blog/2010/12/14/strict-mode-is-coming-to-town/ */
	'use strict';

	var
		/** These variable declarations help compression. */
		jquery				= jQuery,
		documentElement		= jquery(document),
		tubepressGallery	= TubePressGallery,
		tubepressEvents		= TubePressEvents,

		/** Keep track of the players we've loaded. */
		loadedPlayers = {},

		/**
		 * Find the player required for a gallery and load the JS.
		 */
		bootPlayer = function (e, galleryId) {

			var playerName	= tubepressGallery.getPlayerLocationName(galleryId),
				path		= tubepressGallery.getPlayerJsUrl(galleryId);

			/** don't load a player twice... */
			if (loadedPlayers[playerName] !== true) {

				loadedPlayers[playerName] = true;
				jquery.getScript(path);
			}
		},

		/**
		 * Load up a TubePress player with the given video ID.
		 */
		invokePlayer = function (e, galleryId, videoId) {


			var playerName	= tubepressGallery.getPlayerLocationName(galleryId),
				height		= tubepressGallery.getEmbeddedHeight(galleryId),
				width		= tubepressGallery.getEmbeddedWidth(galleryId),
				nvpMap		= tubepressGallery.getNvpMap(galleryId),
				callback	= function (data) {

					var result	= TubePressJson.parse(data.responseText),
						title	= result.title,
						html	= result.html;

					documentElement.trigger(tubepressEvents.PLAYER_POPULATE + playerName, [ title, html, height, width, videoId, galleryId ]);
				},

				dataToSend = {

					'action'			: 'playerHtml',
					'tubepress_video'	: videoId
				},

				url = TubePressGlobalJsConfig.baseUrl + '/src/main/php/scripts/ajaxEndpoint.php',
				method;

			/**
			 * Add the NVPs for TubePress to the data.
			 */
			jquery.extend(dataToSend, nvpMap);

			/** Announce we're gonna invoke the player... */
			documentElement.trigger(tubepressEvents.PLAYER_INVOKE + playerName, [ videoId, galleryId, width, height ]);

			/** If this player requires population, go fetch the HTML for it. */
			if (tubepressGallery.getPlayerLocationProducesHtml(galleryId)) {

				method = tubepressGallery.getHttpMethod(galleryId);

				/* ... and fetch the HTML for it */
				TubePressAjax.get(method, url, dataToSend, callback, 'json');
			}
		};

	/** When we see a new gallery... */
	documentElement.bind(tubepressEvents.NEW_GALLERY_LOADED, bootPlayer);

	/** When a user clicks a thumbnail... */
	documentElement.bind(tubepressEvents.GALLERY_VIDEO_CHANGE, invokePlayer);
}()),

/**
 * Sequencing support for TubePress.
 */
TubePressSequencer = (function () {

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

				return false;
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
			docElement.trigger(events.GALLERY_VIDEO_CHANGE, [galleryId, videoId]);
		},

		/**
		 * Go to the next video in the gallery.
		 */
		next = function (galleryId) {

			/** Get the gallery's sequence. This is an array of video ids. */
			var sequence	= tubepressGallery.getSequence(galleryId),
				vidId		= galleries[galleryId][currentVideoId],
				index		= jquery.inArray(vidId, sequence),
				lastIndex	= sequence ? sequence.length - 1 : index;

			/** Sorry, we don't know anything about this video id, or we've reached the end of the gallery. */
			if (index === -1 || index === lastIndex) {

				return;
			}

			/** Start the next video in line. */
			changeToVideo(galleryId, sequence[index + 1]);
		},

		/** Play the previous video in the gallery. */
		prev = function (galleryId) {

			/** Get the gallery's sequence. This is an array of video ids. */
			var sequence	= tubepressGallery.getSequence(galleryId),
				vidId		= galleries[galleryId][currentVideoId],
				index		= jquery.inArray(vidId, sequence);

			/** Sorry, we don't know anything about this video id, or we're at the start of the gallery. */
			if (index === -1 || index === 0) {

				return;
			}

			/** Start the previous video in line. */
			changeToVideo(galleryId, sequence[index + 1]);
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

}()),

/**
 * Handles basic thumbnail tasks.
 */
TubePressThumbs = (function () {

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
		//this is hard-coded - need to get rid of that.
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
		 * Get the thumbnail width. Usually this is just a static thumbnail
		 * image, but *may* be an actual embed or something like that.
		 *
		 * Fallback value is 120.
		 */
		getThumbWidth = function (galleryId) {

			var thumbArea			= getThumbArea(galleryId),
				firstVisualElement	= thumbArea.find('img:first');

			if (firstVisualElement.length === 0) {

				firstVisualElement = thumbArea.find('div.tubepress_thumb:first > div.tubepress_embed');

				if (firstVisualElement.length === 0) {

					return 120;
				}
			}

			return firstVisualElement.width();
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
		//this is way too fragile.
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
}()),

/**
 * Functions for handling Ajax pagination.
 */
TubePressAjaxPagination = (function () {

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

			var baseUrl				= TubePressGlobalJsConfig.baseUrl,
				nvpMap				= gallery.getNvpMap(galleryId),
				page				= anchor.attr('rel'),
				thumbnailArea		= TubePressThumbs.getThumbAreaSelector(galleryId),
				postLoadCallback	= function () {

					postLoad(galleryId);
				},
				pageToLoad			= baseUrl + '/src/main/php/scripts/ajaxEndpoint?tubepress_' + page + '&tubepress_galleryId=' + galleryId + jquery.param(nvpMap),
				remotePageSelector	= thumbnailArea + ' > *',
				httpMethod			= gallery.getHttpMethod(galleryId);

			TubePressAjax.loadAndStyle(httpMethod, pageToLoad, thumbnailArea, remotePageSelector, '', postLoadCallback);
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

}()),

/**
 * Browser quirks and small performance improvements.
 */
TubePressCompat = (function () {

	/** http://www.yuiblog.com/blog/2010/12/14/strict-mode-is-coming-to-town/ */
	'use strict';

	var jquery = jQuery,

		init = function () {

			/* caching script loader */
			jquery.getScript = function (url, callback, cache) {

				return jquery.ajax({ type: 'GET', url: url, success: callback, dataType: 'script', cache: cache });
			};
		};

	return { init: init };

}()),

/**
 * Provides auto-sequencing capability for TubePress.
 */
TubePressPlayerApi = (function () {

	/** http://www.yuiblog.com/blog/2010/12/14/strict-mode-is-coming-to-town/ */
	'use strict';

	var

		/** These variable declarations aide in compression. */
		jquery				= jQuery,
		documentElement		= jquery(document),
		events				= TubePressEvents,
		logger				= TubePressLogger,

		/** YouTube variables. */
		loadingYouTubeApi	= false,
		youTubePrefix		= 'tubepress-youtube-player-',
		youTubePlayers		= {},
		youTubeIdPattern	= /[a-z0-9\-_]{11}/i,

		/** Vimeo variables. */
		loadingVimeoApi		= false,
		vimeoPrefix			= 'tubepress-vimeo-player-',
		vimeoPlayers		= {},
		vimeoIdPattern		= /[0-9]+/,

		httpScheme			= 'http',
		httpsScheme			= 'https',

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

			//noinspection JSUnresolvedVariable
			if (!jquery.isFunction(player.getVideoUrl)) {

				return null;
			}

			//noinspection JSUnresolvedFunction
			url					= player.getVideoUrl();
			loadedId			= url.split('v=')[1];
			ampersandPosition	= loadedId.indexOf('&');

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

			//noinspection JSUnresolvedVariable
			return window.YT !== undefined && YT.Player !== undefined;
		},

		/**
		 * Is the Vimeo API available yet?
		 */
		isVimeoApiAvailable = function () {

			return window.Froogaloop !== undefined;
		},

		/**
		 * Load the YouTube API, if necessary.
		 */
		loadYouTubeApi = function () {

			//noinspection JSUnresolvedVariable
			var scheme = TubePressGlobalJsConfig.https ? httpsScheme : httpScheme;

			if (! loadingYouTubeApi && ! isYouTubeApiAvailable()) {

				if (logger.on()) {

					logger.log('Loading YT API');
				}

				loadingYouTubeApi = true;
				jquery.getScript(scheme + '://www.youtube.com/player_api');
			}
		},

		/**
		 * Load the Vimeo API, if necessary.
		 */
		loadVimeoApi = function () {

			//noinspection JSUnresolvedVariable
			var scheme = TubePressGlobalJsConfig.https ? httpsScheme : httpScheme;

			if (! loadingVimeoApi && ! isVimeoApiAvailable()) {

				if (logger.on()) {

					logger.log('Loading Vimeo API');
				}

				loadingVimeoApi = true;
				jquery.getScript(scheme + '://a.vimeocdn.com/js/froogaloop2.min.js');
			}
		},

		/**
		 * The YouTube player will call this method when a player event
		 * fires.
		 */
		onYouTubeStateChange = function (event) {

			//noinspection JSUnresolvedVariable
			var videoId		= getVideoIdFromYouTubeEvent(event),
				eventData	= event.data,
				playerState	= YT.PlayerState;

			/**
			 * If we can't parse the event, just bail.
			 */
			if (videoId === null) {

				return;
			}

			//noinspection JSUnresolvedVariable
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

				//noinspection JSUnresolvedFunction
				youTubePlayers[videoId] = new YT.Player(youTubePrefix + videoId, {

					events: {

						'onError'		: onYouTubeError,
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

}()),

/**
 * Handles Ajax interactive searching.
 */
TubePressAjaxSearch = (function () {

	/** http://www.yuiblog.com/blog/2010/12/14/strict-mode-is-coming-to-town/ */
	'use strict';

	var performSearch = function (nvpMap, rawSearchTerms, targetDomSelector, galleryId) {

		/** These variable declarations aide in compression. */
		var jquery		= jQuery,
			logger		= TubePressLogger,
			httpMethod	= TubePressGallery.getHttpMethod(galleryId),

			/** Some vars we'll need later. */
			callback,
			ajaxResultSelector,
			finalAjaxContentDestination,
			urlParams,

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

		urlParams = {

			action				: 'ajaxInteractiveSearch',
			tubepress_search	: rawSearchTerms
		};

		jquery.extend(urlParams, nvpMap);

		TubePressAjax.loadAndStyle(

			httpMethod,

			TubePressGlobalJsConfig.baseUrl + '/src/main/php/scripts/ajaxEndpoint.php?' + jquery.param(urlParams),

			finalAjaxContentDestination,

			ajaxResultSelector,

			null,

			callback
		);
	};

	return { performSearch : performSearch };

}()),

/**
 * Primary TubePress boot function.
 */
tubePressBoot = function () {

	/** http://www.yuiblog.com/blog/2010/12/14/strict-mode-is-coming-to-town/ */
	'use strict';

	TubePressCompat.init();
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
			if (window.console !== undefined) {

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