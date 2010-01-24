/**
 * Copyright 2006 - 2010 Eric D. Hough (http://ehough.com)
 * 
 * This file is part of TubePress (http://tubepress.org) and is released 
 * under the General Public License (GPL) version 3
 *
 * Shrink your JS: http://developer.yahoo.com/yui/compressor/
 */

/* caching script loader */
jQuery.getScript = function (url, callback, cache) {
	jQuery.ajax({ type: "GET", url: url, success: callback, dataType: "script", cache: cache }); 
}; 

var safeTubePressInit = function () {
	try {
        TubePress.init(getTubePressBaseUrl());
  	} catch (f) {
        alert("TubePress failed to initialize: " + f.message);
  	}
}

/* append our init method to after all the other (potentially full of errors) ready blocks have 
 * run. http://stackoverflow.com/questions/1890512/handling-errors-in-jquerydocument-ready */
if (!jQuery.browser.msie) {
	var oldReady = jQuery.ready, TubePress;
	jQuery.ready = function () {
	        try {
	            oldReady.apply(this, arguments);
	        } catch (e) { }
	        safeTubePressInit();
	};
} else {
    jQuery().ready(function () {
    	safeTubePressInit();
    });
}


TubePress = (function () {

	var init, loadEmbeddedJs, parseRels, loadPlayerJs, triggerPlayerLoadedEvent, clickListener,
			swapEmbedded, deepConstructObject, callPlayerJs, callWhenTrue, getWaitCall, loadCss, 
			centerThumbs, ajaxifyPaginationForGallery, processAjaxRequest, postAjaxGallerySetup;
	
	init = function (baseUrl) {
		jQuery().bind('tubepressPlayerLoaded', function (x, playerName, baseUrl) {
			var funcName = 'tubepress_' + playerName + '_player_init',
				f = function () {
					window[funcName](baseUrl);
				};
			callWhenTrue(function () {
				return typeof window[funcName] === 'function'; 
			}, f);
		});

		loadEmbeddedJs(baseUrl);
		loadPlayerJs(baseUrl);
		jQuery("a[id^='tubepress_']").click(clickListener);
	};

	/* loads up JS necessary for dealing with embedded Flash implementations that we find on the page */
	loadEmbeddedJs = function (baseUrl) {
		var embeddedNames = parseRels(1), i, emptyFunc = function () {};
		for (i = 0; i < embeddedNames.length; i = i + 1) {
			jQuery.getScript(baseUrl + "/ui/embedded_flash/" + embeddedNames[i] + "/" + embeddedNames[i] + ".js", emptyFunc, true);
		}
	};

	parseRels = function (index) {
		var returnValue = [];
		jQuery("a[rel^='tubepress_']").each(function () {
			var thisName = jQuery(this).attr("rel").split("_")[index];
			if (jQuery.inArray(thisName, returnValue) === -1) {
				returnValue.push(thisName);
			}
		});
		return returnValue;
	};

	/* loads up JS necessary for dealing with TubePress players that we find on the page */
	loadPlayerJs = function (baseUrl) {
		var playerNames = parseRels(2), i;
		for (i = 0; i < playerNames.length; i = i + 1) {
			var name = playerNames[i];
			jQuery.getScript(baseUrl + "/ui/players/" + name + "/" + name + ".js", 
				triggerPlayerLoadedEvent(name, baseUrl), true);
		}
	};
	
	triggerPlayerLoadedEvent = function (name, baseUrl) {
		jQuery().trigger('tubepressPlayerLoaded', [name, baseUrl]);
	};

	/* thumbnail click listener */
	clickListener = function () {
		var rel_split	= jQuery(this).attr("rel").split("_"),
		galleryId	= rel_split[3],
		playerName   = rel_split[2],
		embeddedName = rel_split[1],
		videoId = jQuery(this).attr("id").substring(16, 27);

		/* swap the gallery's embedded object */
		swapEmbedded(galleryId, videoId, embeddedName);
	
		/* then call the player to load up / play the video */
		callPlayerJs(galleryId, videoId, embeddedName, playerName);
	};

	/**
	 * This function is very carefully constructed to work with both IE 7-8 and FF.
	 * Modify at your own risk!!
	*/
	swapEmbedded = function (galleryId, videoId, embeddedName) {
		var wrapperId = "#tubepress_embedded_object_" + galleryId,
			wrapper = jQuery(wrapperId), newHtml;

		/* if we can't find the embedded object, just bail */
		if (wrapper.length === 0) {
			return;
		}

		var matcher = window["tubepress_" + embeddedName + "_matcher"](),
			paramName = window["tubepress_" + embeddedName + "_param"](),
			obj = jQuery(wrapperId + " > object"),
			oldVideoId = obj.children("param[name='" + paramName + "']").attr("value").match(matcher)[1];

		/* remove anything AdBlock plus sticks in there */
		obj.siblings().remove();
	
		/* save the params but remove them from the DOM for now */
		var params = obj.children("param");
		params.remove();

		/* create the new embedded object */
		newHtml = deepConstructObject(wrapper, params).replace(new RegExp(oldVideoId, 'g'), videoId);
	
		/* add it back in */
		wrapper.html(newHtml);

		/* now pat yourself on the back */	
	};

	callPlayerJs = function (galleryId, videoId, embeddedName, playerName) {
		if ((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i))) {
			return;
		}
		var playerFunctionName = "tubepress_" + playerName + "_player";
		window[playerFunctionName](galleryId, videoId);
	};

	deepConstructObject = function (wrapper, params) {
		
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
		var fileref = document.createElement("link");
		fileref.setAttribute("rel", "stylesheet");
		fileref.setAttribute("type", "text/css");
		fileref.setAttribute("href", path);
		document.getElementsByTagName("head")[0].appendChild(fileref);
	};

	centerThumbs = function (gallerySelector) {
		jQuery(document).ready(function () {
			jQuery(gallerySelector + " div.tubepress_thumb").children().each(function () {
				var myWidth = jQuery(this).width(),
					parentWidth = jQuery(this).parent().width(),
					offset = (parentWidth - myWidth) / 2;
				jQuery(this).css("margin-left", offset);
			});
		});
	};

	ajaxifyPaginationForGallery = function (galleryId) {
		var clickCallback = function () {
			processAjaxRequest(jQuery(this), galleryId);
		};
		jQuery("#tubepress_gallery_" + galleryId + " div.pagination a").click(clickCallback);
	};

	processAjaxRequest = function (anchor, galleryId) {
		var baseUrl = getTubePressBaseUrl(), 
			shortcode = window["getUrlEncodedShortcodeForTubePressGallery" + galleryId](),
			page = anchor.attr("rel"),
			thumbnailArea = "#tubepress_gallery_" + galleryId + "_thumbnail_area",
			postLoadCallback = function () {
				postAjaxGallerySetup(thumbnailArea, galleryId);
			},
			pageToLoad = baseUrl + "/env/pro/lib/ajax/responder.php?shortcode=" + shortcode + "&tubepress_" + page + "&tubepress_galleryId=" + galleryId,
			remotePageSelector = thumbnailArea + " > *",
			loadFunction = function () {
				jQuery(thumbnailArea).load(pageToLoad + " " + remotePageSelector, postLoadCallback);
			};

		/* fade out the old stuff */
		jQuery(thumbnailArea).fadeTo('fast', '.01');
	
		/* use a tiny delay here to prevent the new content from showing up before we're done fading */
		setTimeout(loadFunction, 100);
	};

	postAjaxGallerySetup = function (thumbnailArea, galleryId) {
		centerThumbs("#tubepress_gallery_" + galleryId);
		jQuery("a[id^='tubepress_']").click(clickListener);
		ajaxifyPaginationForGallery(galleryId);
		jQuery(thumbnailArea).fadeTo('fast', 1);
	};

	/* return only public functions */
	return {
		init : 							init,
		deepConstructObject : 			deepConstructObject,
		getWaitCall : 					getWaitCall,
		clickListener : 				clickListener,
		loadCss : 						loadCss,
		ajaxifyPaginationForGallery :   ajaxifyPaginationForGallery,
        centerThumbs :  				centerThumbs
	};
}());