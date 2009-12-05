jQuery.getScript = function(url, callback, cache) {
    jQuery.ajax({ type: "GET", url: url, success: callback, dataType: "script", cache: cache }); 
}; 

jQuery(document).ready(function() {_tubepress_init(getTubePressBaseUrl());})

function _tubepress_init(baseUrl) {
    _tubepress_load_embedded_js(baseUrl);
    _tubepress_load_players(baseUrl);
    jQuery("a[id^='tubepress_']").click(tubepress_click_listener);
}

function tubepress_click_listener() {
	var rel_split    = jQuery(this).attr("rel").split("_"),
    galleryId    = rel_split[3],
    playerName   = rel_split[2],
    embeddedName = rel_split[1],
    videoId = jQuery(this).attr("id").substring(16, 27);

	/* swap the gallery's embedded object */
	_tubepress_swap_embedded(galleryId, videoId, embeddedName);
	
	/* then call the player to load up / play the video */
	_tubepress_call_player_js(galleryId, videoId, embeddedName, playerName);
}

/**
 * This function is very carefully constructed to work with both IE 7-8 and FF.
 * Modify at your own risk!!
 * 
 * @param galleryId    The unique gallery ID containing the embedded object
 *                      to swap
 * @param videoId      The "new" YouTube video ID
 * @param embeddedName Longtail, YouTube, etc
 * @return void
 */
function _tubepress_swap_embedded(galleryId, videoId, embeddedName) {
	
	var wrapperId = "#tubepress_embedded_object_" + galleryId,
	    wrapper = jQuery(wrapperId);

	/* if we can't find the embedded object, just bail */
	if (wrapper.length == 0) {
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
	newHtml = tubepress_deep_construct_object(wrapper, params).replace(new RegExp(oldVideoId, 'g'), videoId);
	
	/* add it back in */
	wrapper.html(newHtml);

	/* now pat yourself on the back */    
}

function tubepress_deep_construct_object(wrapper, params) {

	var newHtml = wrapper.html();
		
	/* chop off the closing </object>. Don't change this unless you want to break IE */
	var newHtml = newHtml.substring(0, newHtml.length - 9);

	/* now add back the params, but this time with the new video ID */
	params.each(function() {
    	newHtml += '<param name="' + this.name + '" value="' + this.value + '" />';
    });
	
	/* re-close the object */
	newHtml += '</object>';
	return newHtml;
}
 
function _tubepress_call_player_js(galleryId, videoId, embeddedName, playerName) {
	/* always use the YouTube player for iPhone/iPod */
    if((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i))) {
    	window.location = "http://www.youtube.com/watch?v=" + videoId;
    	return;
    }
    var playerFunctionName = "tubepress_" + playerName + "_player";
    window[playerFunctionName](galleryId, videoId);
}


function _tubepress_load_players(baseUrl) {
	var playerNames = _tubepress_rel_parser(2), i;
    for(i = 0; i < playerNames.length; i++) {
    	var name = playerNames[i];
        jQuery.getScript(baseUrl + "/ui/players/" + name + "/" + name + ".js", 
        	_tubepress_player_loaded(name, baseUrl) , true);
    }
}

function _tubepress_player_loaded(playerName, baseUrl) {
	var funcName = 'tubepress_' + playerName + '_player_init',
	    f = function() { window[funcName](baseUrl); }
	_tubepress_call_when_true(function() { return typeof window[funcName] == 'function'; }, f);
}

function _tubepress_load_embedded_js(baseUrl) {
    var embeddedNames = _tubepress_rel_parser(1), i;
    for(i = 0; i < embeddedNames.length; i++) {
        jQuery.getScript(baseUrl + "/ui/embedded_flash/" + embeddedNames[i] + "/" + embeddedNames[i] + ".js", function() {}, true);
    }
}

/**
 * Utility function to do some DOM parsing for other functions here
 * @param index
 * @return
 */
function _tubepress_rel_parser(index) {
    var returnValue = [];
    jQuery("a[rel^='tubepress_']").each( function() {
        var thisName = jQuery(this).attr("rel").split("_")[index];
        if (jQuery.inArray(thisName, returnValue) == -1) {
            returnValue.push(thisName);
        }
    });
    return returnValue;
}

/**
 * Waits until the given test is true (tests every .4 seconds),
 * and then executes the given callback.
 * @param test
 * @param callback
 * @return
 */
function _tubepress_call_when_true(test, callback) {
    /* if the test doesn't pass, try again in .4 seconds */	
    if (!test()) {
        var futureTest = function() {_tubepress_call_when_true(test, callback);}
        setTimeout(futureTest, 400);
	return;
    }
    /* the test passed, so call the callback */
    callback();
}

/**
 * Convenience method to wait for a script to load, then call some function
 * from that script.
 * @param scriptPath
 * @param test
 * @param callback
 * @return
 */
function tubepress_get_wait_call(scriptPath, test, callback) {
    var futureCallback = function() { _tubepress_call_when_true(test, callback); }
    jQuery.getScript(scriptPath, futureCallback, true);
}

function tubepress_load_css(path) {
    var fileref=document.createElement("link")
    fileref.setAttribute("rel", "stylesheet")
    fileref.setAttribute("type", "text/css")
    fileref.setAttribute("href", path)
    document.getElementsByTagName("head")[0].appendChild(fileref)
}

function tubepress_center_thumbs(gallerySelector) {
	jQuery(document).ready(function() {
		jQuery(gallerySelector + " div.tubepress_thumb").children().each(function() {
			var myWidth = jQuery(this).width(),
				parentWidth = jQuery(this).parent().width(),
				offset = (parentWidth - myWidth) / 2;
				jQuery(this).css("margin-left", offset);
		;})
	;});
}

/**
 * Adds Ajax functionality to pagination links
 *
 */
function tubepress_ajaxifyPaginationForGallery(galleryId) {
    var clickCallback = function() { _tubepress_processAjaxRequest(jQuery(this), galleryId); };
    jQuery("#tubepress_gallery_" + galleryId + " div.pagination a").click(clickCallback);
}

function _tubepress_processAjaxRequest(anchor, galleryId) {
    var baseUrl = getTubePressBaseUrl(), 
        shortcode = window["getUrlEncodedShortcodeForTubePressGallery" + galleryId](),
        page = anchor.attr("rel"),
        thumbnailArea = "#tubepress_gallery_" + galleryId + "_thumbnail_area",
        postLoadCallback = function() { _tubepress_postAjaxGallerySetup(thumbnailArea, galleryId) },
        pageToLoad = baseUrl + "/env/pro/lib/ajax/responder.php?shortcode=" + shortcode + "&tubepress_" + page + "&tubepress_galleryId=" + galleryId,
        remotePageSelector = thumbnailArea + " > *",
        loadFunction= function() { jQuery(thumbnailArea).load(pageToLoad + " " + remotePageSelector, postLoadCallback); };

    /* fade out the old stuff */
    jQuery(thumbnailArea).fadeTo('fast', .01);
    
    /* use a tiny delay here to prevent the new content from showing up before we're done fading */
    setTimeout(loadFunction, 100);
}

function _tubepress_postAjaxGallerySetup(thumbnailArea, galleryId) {
	tubepress_center_thumbs("#tubepress_gallery_" + galleryId);
	jQuery("a[id^='tubepress_']").click(tubepress_click_listener);
    tubepress_ajaxifyPaginationForGallery(galleryId);
    jQuery(thumbnailArea).fadeTo('fast', 1);
}
