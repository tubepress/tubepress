function tubepress_attach_listeners()
{
	jQuery("a[id^='tubepress_']").click(function () {
		var rel_split    = jQuery(this).attr("rel").split("_");
		var galleryId    = rel_split[3];
		var playerName   = rel_split[2]
		var embeddedName = rel_split[1];
		var videoId = jQuery(this).attr("id").substring(16, 27);

        tubepress_swap_embedded(galleryId, videoId, embeddedName);
        tubepress_call_player_js(galleryId, videoId, embeddedName, playerName);
    });
}

function tubepress_load_embedded_js(baseUrl)
{
    var embeddedNames = tubepress_rel_parser(1);
    for(var i = 0; i < embeddedNames.length; i++) {
        jQuery.getScript(baseUrl + "/ui/embedded/" + embeddedNames[i] + "/" + embeddedNames[i] + ".js");
    }
}

function tubepress_load_player_js(baseUrl)
{
    var playerNames = tubepress_rel_parser(2);
    for(var i = 0; i < playerNames.length; i++) {
        var playerName = playerNames[i];
        jQuery.getScript(baseUrl + "/ui/players/" + playerName + "/" + playerName + ".js", function() {
            var playerName = this.url.match(/players\/([^\/]+)\/.*/)[1];
            window["tubepress_" + playerName + "_preload"](baseUrl);
        });
    }
    jQuery(document).ready(function() {
        for (var x = 0; x < playerNames.length; x++) {
            window["tubepress_" + playerNames[x] + "_postload"](baseUrl);
        }        
    });
}

function tubepress_rel_parser(index) {
    var returnValue = [];
    jQuery("a[rel^='tubepress_']").each( function() {
        var thisName = jQuery(this).attr("rel").split("_")[index];
        if (returnValue.indexOf(thisName) == -1) {
            returnValue.push(thisName);
        }
    });
    return returnValue;
}

function tubepress_call_player_js(galleryId, videoId, embeddedName, playerName) {
    var playerFunctionName = "tubepress_" + playerName + "_player";
    window[playerFunctionName](galleryId, videoId);
}

function tubepress_swap_embedded(galleryId, videoId, embeddedName) {
    var matcherFunctionName = "tubepress_" + embeddedName + "_matcher";

    var matcher = window[matcherFunctionName]();

    var embeddedWrapper = jQuery("#tubepress_embedded_object_" + galleryId);
    var oldVideoId = embeddedWrapper.html().match(matcher)[1];
    embeddedWrapper.html(embeddedWrapper.html().replace(oldVideoId, videoId));
}

