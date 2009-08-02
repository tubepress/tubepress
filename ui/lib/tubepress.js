jQuery.getScript = function(url, callback, cache) {
    jQuery.ajax({ type: "GET", url: url, success: callback, dataType: "script", cache: cache }); 
}; 

function tubepress_init(baseUrl) {
	tubepress_load_embedded_js(baseUrl);
    tubepress_load_players(baseUrl);
    tubepress_attach_listeners();
}

function tubepress_attach_listeners()
{
    /* any link with an id that starts with "tubepress_" */
	jQuery("a[id^='tubepress_']").click(function () {
		var rel_split    = jQuery(this).attr("rel").split("_"),
            galleryId    = rel_split[3],
		    playerName   = rel_split[2],
		    embeddedName = rel_split[1],
            videoId = jQuery(this).attr("id").substring(16, 27);

        /* swap the gallery's embedded object */
        _tubepress_swap_embedded(galleryId, videoId, embeddedName);

        /* then call the player to load up / play the video */
        _tubepress_call_player_js(galleryId, videoId, embeddedName, playerName);
    });
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
	
	/* chop off the closing </object> */
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


function tubepress_load_players(baseUrl) {
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

function tubepress_load_embedded_js(baseUrl) {
    var embeddedNames = _tubepress_rel_parser(1), i;
    for(i = 0; i < embeddedNames.length; i++) {
        jQuery.getScript(baseUrl + "/ui/embedded/" + embeddedNames[i] + "/" + embeddedNames[i] + ".js", function() {}, true);
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
		setTimeout(function() {_tubepress_call_when_true(test, callback);}, 400);
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
function _tubepress_get_wait_call(scriptPath, test, callback) {
	jQuery.getScript(scriptPath, function() {
		_tubepress_call_when_true(test, callback);
    }, true);
}
 
 /*
  * includeMany 1.1.0
  *
  * Copyright (c) 2009 Arash Karimzadeh (arashkarimzadeh.com)
  * Licensed under the MIT (MIT-LICENSE.txt)
  * http://www.opensource.org/licenses/mit-license.php
  *
  * Date: Feb 28 2009
  */
 (function(a){a.include=function(e,b){var f=a.include.luid++;var d=function(h,g){if(a.isFunction(h)){h(g);}if(--a.include.counter[f]==0&&a.isFunction(b)){b();}};if(typeof e=="object"&&typeof e.length=="undefined"){a.include.counter[f]=0;for(var c in e){a.include.counter[f]++;}return a.each(e,function(g,h){a.include.load(g,d,h);
 });}e=a.makeArray(e);a.include.counter[f]=e.length;a.each(e,function(){a.include.load(this,d,null);});};a.extend(a.include,{luid:0,counter:[],load:function(b,c,d){if(a.include.exist(b)){return c(d);}if(/.css$/.test(b)){a.include.loadCSS(b,c,d);}else{if(/.js$/.test(b)){a.include.loadJS(b,c,d);}else{a.get(b,function(e){c(d,e);
 });}}},loadCSS:function(b,d,e){var c=document.createElement("link");c.setAttribute("type","text/css");c.setAttribute("rel","stylesheet");c.setAttribute("href",b);a("head").get(0).appendChild(c);a.browser.msie?a.include.IEonload(c,d,e):d(e);},loadJS:function(b,d,e){var c=document.createElement("script");
 c.setAttribute("type","text/javascript");c.setAttribute("src",b);a.browser.msie?a.include.IEonload(c,d,e):c.onload=function(){d(e);};a("head").get(0).appendChild(c);},IEonload:function(d,b,c){d.onreadystatechange=function(){if(this.readyState=="loaded"||this.readyState=="complete"){b(c);}};},exist:function(c){var b=false;
 a("head script").each(function(){if(/.css$/.test(c)&&this.href==c){return b=true;}else{if(/.js$/.test(c)&&this.src==c){return b=true;}}});return b;}});})(jQuery);
