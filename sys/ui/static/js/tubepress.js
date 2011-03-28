/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
 * 
 * This file is part of TubePress (http://tubepress.org) and is released 
 * under the General Public License (GPL) version 3
 *
 * Shrink your JS: http://developer.yahoo.com/yui/compressor/
 */

/*global jQuery, getTubePressBaseUrl, alert */

var TubePressAjax = (function () {

	var load, loadAndStyle, applyLoadingStyle, removeLoadingStyle;
	
	load = function (url, targetDiv, selector, preLoadFunction, postLoadFunction) {
		if (typeof preLoadFunction === 'function') {
			preLoadFunction();
		}
		
		jQuery.ajax({
			url: url,
			type: 'GET',
			dataType: 'html',
			complete: function (res) {
				var html = selector ? jQuery('<div>').append(res.responseText).find(selector) :
					res.responseText;
				jQuery(targetDiv).html(html);
				if (typeof postLoadFunction === 'function') {
					postLoadFunction();
				}
			}
		});
	};
	
	loadAndStyle = function (url, targetDiv, selector, preLoadFunction, postLoadFunction) {
		applyLoadingStyle(targetDiv);
		var post = function () { removeLoadingStyle(targetDiv); };
		if (typeof postLoadFunction === 'function') {
			post = function () {
				removeLoadingStyle(targetDiv);
				postLoadFunction();
			};
		}
		load(url, targetDiv, selector, preLoadFunction, post);
	};
	
	applyLoadingStyle = function (targetDiv) {
		jQuery(targetDiv).fadeTo(0, 0.3);
	};
	
	removeLoadingStyle = function (targetDiv) {
		jQuery(targetDiv).fadeTo(0, 1);
	};
	
	return {
		load			: load,
		applyLoadingStyle	: applyLoadingStyle,
		removeLoadingStyle	: removeLoadingStyle,
		loadAndStyle		: loadAndStyle
	};
	
}());

/**
 * Handles some DOM and network related tasks
 */
var TubePressJS = (function () {
	
	var callWhenTrue, getWaitCall, loadCss;
	
	/**
	 * Waits until the given test is true (tests every .4 seconds),
	 * and then executes the given callback.
	 */
	callWhenTrue = function (test, callback) {

		/* if the test doesn't pass, try again in .4 seconds */	
		if (!test()) {
			var futureTest = function () {
				callWhenTrue(test, callback);
			};
			setTimeout(futureTest, 400);
			return;
		}
		/* the test passed, so call the callback */
		callback();
	};
	
	getWaitCall = function (scriptPath, test, callback) {
		var futureCallback = function () {
			callWhenTrue(test, callback);
		};
		jQuery.getScript(scriptPath, futureCallback, true);
	};

	loadCss = function (path) {
		var fileref = document.createElement('link');
		
		fileref.setAttribute('rel', 'stylesheet');
		fileref.setAttribute('type', 'text/css');
		fileref.setAttribute('href', path);
		document.getElementsByTagName('head')[0].appendChild(fileref);
	};
	
	/* return only public functions */
	return { 
		callWhenTrue	: callWhenTrue,
		getWaitCall		: getWaitCall,
		loadCss			: loadCss
	};
}());

var TubePressEvents = (function () {
	
	return {
		NEW_THUMBS_LOADED	: 'tubepressNewThumbnailsLoaded'
	};
	
}());

/* analyzes HTML anchor objects */
var TubePressAnchors = (function () {
	
	var findAllEmbeddedNames, findAllPlayerNames, getEmbeddedNameFromRelSplit,
		getPlayerNameFromRelSplit, getGalleryIdFromRelSplit, getVideoIdFromIdAttr,
		parseRels;
	
	findAllEmbeddedNames = function () {
		return parseRels(1);	
	};
	
	findAllPlayerNames = function () {
		return parseRels(2);	
	};
	
	getEmbeddedNameFromRelSplit = function (relSplit) {
		return relSplit[1];
	};
	
	getPlayerNameFromRelSplit = function (relSplit) {
		return relSplit[2];
	};
	
	getGalleryIdFromRelSplit = function (relSplit) {
		return relSplit[3];
	};
	
	getVideoIdFromIdAttr = function (id) {
		var end = id.lastIndexOf('_');
		return id.substring(16, end);
	};
	
	parseRels = function (index) {
		var returnValue = [];
		jQuery("a[rel^='tubepress_']").each(function () {
			var thisName = jQuery(this).attr('rel').split('_')[index];
			if (jQuery.inArray(thisName, returnValue) === -1) {
				returnValue.push(thisName);
			}
		});
		return returnValue;
	};
	
	return {
		findAllEmbeddedNames		: findAllEmbeddedNames,
		findAllPlayerNames		: findAllPlayerNames,
		getEmbeddedNameFromRelSplit	: getEmbeddedNameFromRelSplit,
		getPlayerNameFromRelSplit	: getPlayerNameFromRelSplit,
		getGalleryIdFromRelSplit	: getGalleryIdFromRelSplit,
		getVideoIdFromIdAttr		: getVideoIdFromIdAttr
	};
	
}());

/* handles player-related functionality (popup, Shadowbox, etc) */
var TubePressPlayers = (function () {
	
	var loadPlayerJs, playerInit, invokePlayer, loadedPlayers;
	
	loadPlayerJs = function (baseUrl) {
		
		/* loads up JS necessary for dealing with TubePress players that we find on the page */
		var playerNames = TubePressAnchors.findAllPlayerNames(), i, name;
		for (i = 0; i < playerNames.length; i = i + 1) {
			name = playerNames[i];

			/* don't load a player twice */
			if (loadedPlayers[name] === true) {
				continue;
			}

			jQuery.getScript(baseUrl + '/sys/ui/static/players/' + name + '/' + name + '.js', 
				playerInit(name, baseUrl));
		}
	};
	
	invokePlayer = function (galleryId, videoId, embeddedName, playerName) {
		var playerFunctionName = 'tubepress_' + playerName + '_player';
		window[playerFunctionName](galleryId, videoId);
	};
	
	playerInit = function (name, baseUrl) {
		
		/* remember that we already loaded this */
		loadedPlayers[name] = true;

		/* Call tubepress_<playername>_init() when the player JS is loaded */
		var funcName = 'tubepress_' + name + '_player_init',
			f = function () {
				window[funcName](baseUrl);
			};	
		TubePressJS.callWhenTrue(function () {
			return typeof window[funcName] === 'function'; 
		}, f);	
	};
	
	jQuery().bind(TubePressEvents.NEW_THUMBS_LOADED, function (e, galleryId) {

		loadPlayerJs(getTubePressBaseUrl());

	});

	return { invokePlayer : invokePlayer };
	
}());

/* deals with the embedded video player */
var TubePressEmbedded = (function () {

	var isIframe, getHtmlForCurrentEmbed, getWidthOfCurrentEmbed, getHeightOfCurrentEmbed,
		objCss, getIframe, dealingWithiframe, swap, swapIframe, swapEmbeddedObject,
		getEmbeddedObjectClone, getIdMatcher, getEmbedParam;
	
	isIframe = function (embeddedName) {
		return embeddedName !== 'longtail';
	};
	
	getHtmlForCurrentEmbed = function (galleryId) {

		if (dealingWithiframe(galleryId)) {
			return jQuery('div#tubepress_embedded_object_' + galleryId).html();
		}
		
		var wrapperId		= '#tubepress_embedded_object_' + galleryId,
			wrapper		= jQuery(wrapperId),
			obj		= jQuery(wrapperId + ' > object'),
			params		= obj.children('param');

		return getEmbeddedObjectClone(wrapper, params);
	};
	
	getWidthOfCurrentEmbed = function (galleryId) {
		if (dealingWithiframe(galleryId)) {
			return parseInt(getIframe(galleryId).attr('width'), 10);
		}
		return objCss(galleryId, 'width');
	};
	
	getHeightOfCurrentEmbed = function (galleryId) {
		if (dealingWithiframe(galleryId)) {
			return parseInt(getIframe(galleryId).attr('height'), 10);
		}
		return objCss(galleryId, 'height');
	};
	
	objCss = function (galleryId, attribute) {
		var wrapperId	= '#tubepress_embedded_object_' + galleryId,
			obj	= jQuery(wrapperId + ' > object'),
			regex   = new RegExp(attribute + '[\\s]*:[\\s]*([\\d]+)', 'i');
		return parseInt(obj.attr('style').match(regex)[1], 10);
	};
	
	getIframe = function (galleryId) {
		return jQuery('div#tubepress_embedded_object_' + galleryId + ' > iframe:first');
	};
	
	dealingWithiframe = function (galleryId) {
		return getIframe(galleryId).length !== 0;
	};
	
	/**
	 * Swaps out the embedded Flash player with a replacement. 
	 * This function is very carefully constructed to work with both IE 7-8 and FF.
	 * Modify at your own risk!!
	*/
	swap = function (galleryId, videoId, embeddedName) {
		var wrapperId = '#tubepress_embedded_object_' + galleryId,
			wrapper = jQuery(wrapperId),
			matcher = getIdMatcher(embeddedName);

		/* if we can't find the embedded object, just bail */
		if (wrapper.length === 0) {
			return;
		}

		/* swap technique depends if iframe or not */
		if (TubePressEmbedded.isIframe(embeddedName)) {
			swapIframe(embeddedName, wrapper, matcher, videoId);
		} else {
			swapEmbeddedObject(embeddedName, wrapperId, matcher, wrapper, videoId);
		}
	};

	/**
	 * Swaps an iframe for the new video
	 */
	swapIframe = function (embeddedName, wrapper, matcher, videoId) {
		var oldHtml	= wrapper.html(),
			oldId	= oldHtml.match(matcher)[1];

		wrapper.html(oldHtml.replace(oldId, videoId) + ' ');
			
		/* add the random digits here to prevent browser from caching iframe */
		wrapper.children('iframe')[0].src = wrapper.children('iframe')[0].src + Math.random();
	
		/* you've earned yourself a beer */
	};
	
	/**
	 * Swaps an embedded object for a new video
	 */
	swapEmbeddedObject = function (embeddedName, wrapperId, matcher, wrapper, videoId) {
		var paramName		= getEmbedParam(embeddedName),
			obj		= jQuery(wrapperId + ' > object'),
			oldVideoId	= obj.children("param[name='" + paramName + "']").attr('value').match(matcher)[1],
			params,
			newHtml;

		/* remove anything AdBlock plus sticks in there */
		obj.siblings().remove();
	
		/* save the params but remove them from the DOM for now */
		params = obj.children('param');
		params.remove();

		/* create the new embedded object */
		newHtml = getEmbeddedObjectClone(wrapper, params).replace(new RegExp(oldVideoId, 'g'), videoId);
	
		/* add it back in */
		wrapper.html(newHtml);

		/* now pat yourself on the back */	
	};
	
	getEmbeddedObjectClone = function (wrapper, params) {
		
		//http://blog.stevenlevithan.com/archives/faster-trim-javascript
		var newHtml = wrapper.html().replace(/\s\s*$/, '');
		
		/* chop off the closing </object>. Don't change this unless you want to break IE */
		newHtml = newHtml.substring(0, newHtml.length - 9);

		/* now add back the params, but this time with the new video ID */
		params.each(function () {
			newHtml += '<param name="' + this.name + '" value="' + this.value + '" />';
		});
	
		/* re-close the object */
		newHtml += '</object>';
		return newHtml;
	};
	
	getIdMatcher = function (embeddedName) {
		switch (embeddedName) {
		case 'longtail':
			return (/youtube\.com\/watch\?v=(\S{11})\S*/);
		case 'vimeo':
			return (/\/video\/([0-9]+)\S*/);
		default:
			return (/youtube\.com\/embed\/(\S{11})\S*/);
		}
	};
	
	getEmbedParam = function (embeddedName) {
		switch (embeddedName) {
		case 'longtail':
			return 'flashvars';
		default:
			return 'movie';
		}
	};

	return {
		isIframe		: isIframe,
		swap			: swap,
		getHtmlForCurrentEmbed	: getHtmlForCurrentEmbed,
		getHeightOfCurrentEmbed	: getHeightOfCurrentEmbed,
		getWidthOfCurrentEmbed	: getWidthOfCurrentEmbed
	};
	
}());

var TubePressGallery = (function () {

	var isFluidThumbs, getShortcode;

	isFluidThumbs = function (galleryId) {
		return window['TubePressGallery' + galleryId].fluidThumbs;
	};

	getShortcode = function (galleryId) {
		return window['getUrlEncodedShortcodeForTubePressThumbs' + galleryId]();
	};

	return {
		isFluidThumbs	: isFluidThumbs,
		getShortcode	: getShortcode
	};
}());

/**
 * Main TubePress gallery module.
 */
var TubePressThumbs = (function () {

	var makeThumbsFluid, clickListener, getCurrentPageNumber, getThumbWidth, getThumbArea, getThumbAreaSelector;
	
	
	/* thumbnail click listener */
	clickListener = function () {
		var rel_split		= jQuery(this).attr('rel').split('_'),
			galleryId	= TubePressAnchors.getGalleryIdFromRelSplit(rel_split),
			playerName	= TubePressAnchors.getPlayerNameFromRelSplit(rel_split),
			embeddedName	= TubePressAnchors.getEmbeddedNameFromRelSplit(rel_split),
			videoId		= TubePressAnchors.getVideoIdFromIdAttr(jQuery(this).attr('id'));

		/* swap the gallery's embedded object */
		TubePressEmbedded.swap(galleryId, videoId, embeddedName);
	
		/* then call the player to load up / play the video */
		TubePressPlayers.invokePlayer(galleryId, videoId, embeddedName, playerName);
	};

	/* http://www.sohtanaka.com/web-design/smart-columns-w-css-jquery/ */
	makeThumbsFluid = function (galleryId) {
		
		getThumbArea(galleryId).css({ 'width' : "100%" });
		
		var gallerySelector	= getThumbAreaSelector(galleryId),
			columnWidth	= getThumbWidth(galleryId),
			gallery		= jQuery(gallerySelector),
			colWrap		= gallery.width(), 
			colNum		= Math.floor(colWrap / columnWidth), 
			colFixed	= Math.floor(colWrap / colNum),
			thumbs		= jQuery(gallerySelector + ' div.tubepress_thumb');
		
		gallery.css({ 'width' : '100%'});
		gallery.css({ 'width' : colWrap });
		thumbs.css({ 'width' : colFixed});
	};
	
	getCurrentPageNumber = function (galleryId) {
		var page = 1, 
			paginationSelector = 'div#tubepress_gallery_' + galleryId
				+ ' div.tubepress_thumbnail_area:first > div.pagination:first > span.current',
			current = jQuery(paginationSelector);

		if (current.length > 0) {
			page = current.html();
		}
		
		return page;
	};

	getThumbWidth = function (galleryId) {
		return getThumbArea(galleryId).find('img:first').width();
	};

	getThumbArea = function (galleryId) {
		return jQuery(getThumbAreaSelector(galleryId));
	};

	getThumbAreaSelector = function (galleryId) {
		return "#tubepress_gallery_" + galleryId + "_thumbnail_area";
	};
	
	jQuery().bind(TubePressEvents.NEW_THUMBS_LOADED, function (e, galleryId) {

		/* add a click handler to each link in this gallery */
		jQuery("#tubepress_gallery_ " + galleryId + " a[id^='tubepress_']").click(clickListener);

		/* fluid thumbs if we need it */
		if (TubePressGallery.isFluidThumbs(galleryId)) {
			makeThumbsFluid(galleryId);
		}
	});

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
	
	var addClickHandlers, processClick, postLoad;
	
	/* initializes pagination HTML for Ajax. */
	addClickHandlers = function (galleryId) {
		var clickCallback = function () {
			processClick(jQuery(this), galleryId);
		};
		jQuery('#tubepress_gallery_' + galleryId + ' div.pagination a').click(clickCallback);
	};

	processClick = function (anchor, galleryId) {
		var baseUrl			= getTubePressBaseUrl(), 
			shortcode		= TubePressGallery.getShortcode(galleryId),
			page			= anchor.attr('rel'),
			thumbnailArea		= TubePressThumbs.getThumbAreaSelector(galleryId),
			postLoadCallback	= function () { postLoad(galleryId); },
			pageToLoad		= baseUrl + '/sys/scripts/ajax/shortcode_printer.php?shortcode=' + shortcode + '&tubepress_' + page + '&tubepress_galleryId=' + galleryId,
			remotePageSelector	= thumbnailArea + ' > *';
		TubePressAjax.loadAndStyle(pageToLoad, thumbnailArea, remotePageSelector, '', postLoadCallback);
	};

	/* post thumbnail load setup */
	postLoad = function (galleryId) {
		jQuery().trigger(TubePressEvents.NEW_THUMBS_LOADED, galleryId);
	};

	jQuery().bind(TubePressEvents.NEW_THUMBS_LOADED, function (e, galleryId) {
		addClickHandlers(galleryId);
	});
}());

var TubePress = (function () {

	/* this is meant to be called from the user's HTML page */
	var init, compat, alreadyInited = false;

	init = function () {
		if (!window.getTubePressBaseUrl || alreadyInited) {
			return;
		}
		try {
			compat();
		
			/* init each gallery we find */
			jQuery("div[id^=tubepress_gallery]").each(function () {
				var galleryId = jQuery(this).attr('id').replace('tubepress_gallery_', '');
				jQuery().trigger(TubePressEvents.NEW_THUMBS_LOADED, [galleryId]);
			});

			alreadyInited = true;

		} catch (f) {
			alert('TubePress failed to initialize: ' + f.message);
		}
	};

	compat = function () {

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


/* append our init method to after all the other (potentially full of errors) ready blocks have 
 * run. http://stackoverflow.com/questions/1890512/handling-errors-in-jquerydocument-ready */
if (!jQuery.browser.msie) {
	var oldReady = jQuery.ready;
	jQuery.ready = function () {
		try {
			oldReady.apply(this, arguments);
		} catch (e) { }
		TubePress.init();
	};
} else {
	jQuery().ready(function () {
		TubePress.init();
	});
}

