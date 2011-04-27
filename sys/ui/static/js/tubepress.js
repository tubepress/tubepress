/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
 * 
 * This file is part of TubePress (http://tubepress.org) and is released 
 * under the General Public License (GPL) version 3
 *
 * Shrink your JS: http://developer.yahoo.com/yui/compressor/
 */

/*global jQuery, getTubePressBaseUrl, alert */
/*jslint white: true, onevar: true, undef: true, newcap: true, nomen: true, regexp: true, plusplus: true, bitwise: true, continue: true, browser: true, maxerr: 50, indent: 4 */

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
		loadAndStyle		: loadAndStyle
	};
}());

/**
 * Handles some DOM and network related tasks
 */
var TubePressJS = (function () {
	
	/**
	 * Waits until the given test is true (tests every .4 seconds),
	 * and then executes the given callback.
	 */
	var callWhenTrue = function (test, callback) {

		/* if the test doesn't pass, try again in .4 seconds */	
		if (!test()) {
			setTimeout(function () { callWhenTrue(test, callback); }, 400);
			return;
		}

		/* the test passed, so call the callback */
		callback();
	},
	
		/*
		 * If test passes right away, this will invoke callback. If not,
		 * it will load the script, wait for the test to pass, then invoke
		 * callback.
		 */ 
		getWaitCall = function (scriptPath, test, callback) {
	
			/* short circuit */
			if (test()) {
				return callback();
			}
	
			jQuery.getScript(scriptPath, function () { callWhenTrue(test, callback); }, true);
		},
	
		/*
		 * Dynamically load CSS into the DOM.
		 */
		loadCss = function (path) {
			
			var fileref = document.createElement('link');
			
			fileref.setAttribute('rel', 'stylesheet');
			fileref.setAttribute('type', 'text/css');
			fileref.setAttribute('href', path);
			document.getElementsByTagName('head')[0].appendChild(fileref);
		};
	
	return { 
		callWhenTrue	: callWhenTrue,
		getWaitCall		: getWaitCall,
		loadCss			: loadCss
	};
}());

var TubePressEvents = (function () {
	
	return {
		NEW_THUMBS_LOADED	: 'tubepressNewThumbnailsLoaded',
		NEW_GALLERY_LOADED	: 'tubepressNewGalleryLoaded'
	};
}());

var TubePressGallery = (function () {

	var galleries = {},
	
		isFluidThumbs = function (galleryId) {
			return galleries[galleryId].fluidThumbs;
		},
	
		getShortcode = function (galleryId) {
			return galleries[galleryId].shortcode;
		},
	
		getPlayerLocationName = function (galleryId) {
			return galleries[galleryId].playerLocationName;
		},
	
		isAjaxPagination = function (galleryId) {
			return galleries[galleryId].ajaxPagination;
		},
	
		getEmbeddedHeight = function (galleryId) {
			return galleries[galleryId].embeddedHeight;
		},
	
		getEmbeddedWidth = function (galleryId) {
			return galleries[galleryId].embeddedWidth;
		},
		
		init = function (galleryId, params) {
			
			/* save the params */
			galleries[galleryId] = params;

			/* init the thumbs */
			jQuery(document).trigger(TubePressEvents.NEW_GALLERY_LOADED, galleryId);
		};

	return {
		isAjaxPagination		: isAjaxPagination,
		isFluidThumbs			: isFluidThumbs,
		getShortcode			: getShortcode,
		getPlayerLocationName	: getPlayerLocationName,
		getEmbeddedHeight		: getEmbeddedHeight,
		getEmbeddedWidth		: getEmbeddedWidth,
		init					: init
	};
}());

/* handles player-related functionality (popup, Shadowbox, etc) */
var TubePressPlayers = (function () {
	
	/* record of the players we've already loaded */
	var loadedPlayers = {},
	
		/* initialize the player */
		playerInit = function (playerName, baseUrl) {
			
			/* Call tubepress_<playername>_init() when the player JS is loaded */
			var playerInitFunctionName	= 'tubepress_' + playerName + '_player_init',
				playerInitFunction	= function () { window[playerInitFunctionName](baseUrl); },
				playerReadyTest		= function () { return typeof window[playerInitFunctionName] === 'function'; };
	
			TubePressJS.callWhenTrue(playerReadyTest, playerInitFunction);	
		},
	
		/* find the player required for a gallery and load the JS. */
		loadPlayerAndInit = function (baseUrl, galleryId) {
			
			var playerName	= TubePressGallery.getPlayerLocationName(galleryId),
				path	= baseUrl + '/sys/ui/static/players/' + playerName + '/' + playerName + '.js';
	
			/* don't load a player twice */
			if (loadedPlayers.playerName === true) {
				return;
			} else {
				loadedPlayers.playerName = true;
			}
	
			jQuery.getScript(path, playerInit(playerName, baseUrl));
		},
	
		invokePlayer = function (galleryId, videoId) {
			
			var playerFunctionName	= 'tubepress_' +  TubePressGallery.getPlayerLocationName(galleryId) + '_player',
				height				= TubePressGallery.getEmbeddedHeight(galleryId),
				width				= TubePressGallery.getEmbeddedWidth(galleryId),
				shortcode			= TubePressGallery.getShortcode(galleryId),
				callback			= function (data) { 
				
					var title = decodeURIComponent(data.title),
						html = decodeURIComponent(data.html);
					
					window[playerFunctionName](title, html, height, width, videoId, galleryId); 
				},
				dataToSend			= { tubepress_video : videoId, tubepress_shortcode : shortcode },
				url					= getTubePressBaseUrl() + '/sys/scripts/ajax/embeddedHtml.php';
		
			jQuery.get(url, dataToSend, callback, 'json');
		},
		
		playerBinder = function (e, galleryId) {

			/* load up its player */
			loadPlayerAndInit(getTubePressBaseUrl(), galleryId);
		};
	
	/* when we see a new gallery... */
	jQuery(document).bind(TubePressEvents.NEW_GALLERY_LOADED, playerBinder);

	return { invokePlayer : invokePlayer };
}());

/**
 * Main TubePress gallery module.
 */
var TubePressThumbs = (function () {

	/* thumbnail click listener */
	var getThumbAreaSelector = function (galleryId) {
		
			return "#tubepress_gallery_" + galleryId + "_thumbnail_area";
		},
		
		getThumbArea = function (galleryId) {
			
			return jQuery(getThumbAreaSelector(galleryId));
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
			
			var rel_split	= jQuery(this).attr('rel').split('_'),
				galleryId	= getGalleryIdFromRelSplit(rel_split),
				videoId		= getVideoIdFromIdAttr(jQuery(this).attr('id'));
		
			/* then call the player to load up / play the video */
			TubePressPlayers.invokePlayer(galleryId, videoId);
		},

		/* http://www.sohtanaka.com/web-design/smart-columns-w-css-jquery/ */
		makeThumbsFluid = function (galleryId) {
			
			getThumbArea(galleryId).css({ 'width' : "100%" });
			
			var gallerySelector	= getThumbAreaSelector(galleryId),
				columnWidth		= getThumbWidth(galleryId),
				gallery			= jQuery(gallerySelector),
				colWrap			= gallery.width(), 
				colNum			= Math.floor(colWrap / columnWidth), 
				colFixed		= Math.floor(colWrap / colNum),
				thumbs			= jQuery(gallerySelector + ' div.tubepress_thumb');
			
			gallery.css({ 'width' : '100%'});
			gallery.css({ 'width' : colWrap });
			thumbs.css({ 'width' : colFixed});
		},
		
		getCurrentPageNumber = function (galleryId) {
			
			var page = 1, 
				paginationSelector = 'div#tubepress_gallery_' + galleryId
					+ ' div.tubepress_thumbnail_area:first > div.pagination:first > span.current',
				current = jQuery(paginationSelector);
	
			if (current.length > 0) {
				page = current.html();
			}
			
			return page;
		},
		
		thumbBinder = function (e, galleryId) {

			/* add a click handler to each link in this gallery */
			jQuery("#tubepress_gallery_" + galleryId + " a[id^='tubepress_']").click(clickListener);

			/* fluid thumbs if we need it */
			if (TubePressGallery.isFluidThumbs(galleryId)) {
				makeThumbsFluid(galleryId);
			}
		},
		
		eventsToBindTo = TubePressEvents.NEW_THUMBS_LOADED + ' ' + TubePressEvents.NEW_GALLERY_LOADED;
	
	jQuery(document).bind(eventsToBindTo, thumbBinder);

	/* return only public functions */
	return {
		
		getThumbAreaSelector	: getThumbAreaSelector,
		getCurrentPageNumber	: getCurrentPageNumber
	};
}());

/**
 * Functions for handling Ajax pagination.
 */
var TubePressAjaxPagination = (function () {
	
	/* post thumbnail load setup */
	var postLoad = function (galleryId) {
		
		jQuery(document).trigger(TubePressEvents.NEW_THUMBS_LOADED, galleryId);
	},
		
		/* Handles an ajax pagination click. */
		processClick = function (anchor, galleryId) {
			
			var baseUrl			= getTubePressBaseUrl(), 
				shortcode		= TubePressGallery.getShortcode(galleryId),
				page			= anchor.attr('rel'),
				thumbnailArea		= TubePressThumbs.getThumbAreaSelector(galleryId),
				postLoadCallback	= function () { postLoad(galleryId); },
				pageToLoad		= baseUrl + '/sys/scripts/ajax/shortcode_printer.php?shortcode=' + shortcode + '&tubepress_' + page + '&tubepress_galleryId=' + galleryId,
				remotePageSelector	= thumbnailArea + ' > *';
				
			TubePressAjax.loadAndStyle(pageToLoad, thumbnailArea, remotePageSelector, '', postLoadCallback);
		},
		
		/* initializes pagination HTML for Ajax. */
		addClickHandlers = function (galleryId) {
			
			var clickCallback = function () {
				processClick(jQuery(this), galleryId);
			};
			
			jQuery('#tubepress_gallery_' + galleryId + ' div.pagination a').click(clickCallback);
		},

		paginationBinder = function (e, galleryId) {
			
			if (TubePressGallery.isAjaxPagination(galleryId)) {
				addClickHandlers(galleryId);
			}
		};

	/* sets up new thumbnails for ajax pagination */
	jQuery(document).bind(TubePressEvents.NEW_THUMBS_LOADED, paginationBinder);
}());

/**
 * Browser quirks and small performance improvements.
 */
var TubePressCompat = (function () {

	var init = function () {

		/* caching script loader */
		jQuery.getScript = function (url, callback, cache) {
			jQuery.ajax({ type: 'GET', url: url, success: callback, dataType: 'script', cache: cache }); 
		};

		/* http://jquery.malsup.com/fadetest.html */
		jQuery.fn.fadeTo = function (speed, to, callback) {
			
			return this.animate({opacity: to}, speed, function () { 
			
				if (to === 1 && jQuery.browser.msie) {
					this.style.removeAttribute('filter');
				}
				
				if (jQuery.isFunction(callback)) {
					callback();
				}
			});
		};
	};

	return { init: init };

}());

/**
 * This is here for backwards compatability only.
 */
function safeTubePressInit() {
	TubePressCompat.init();
}

/* append our init method to after all the other (potentially full of errors) ready blocks have 
 * run. http://stackoverflow.com/questions/1890512/handling-errors-in-jquerydocument-ready */
if (!jQuery.browser.msie) {
	
	var oldReady = jQuery.ready;

	jQuery.ready = function () {
	
		try {
			oldReady.apply(this, arguments);
		} catch (e) { }
		
		TubePressCompat.init();
	};
	
} else {
	
	jQuery(document).ready(function () {
		TubePressCompat.init();
	});
}
