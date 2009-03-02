function tubepress_attach_listeners()
{
	jQuery("a[id^='tubepress_']").click(function () {
		var rel_split    = jQuery(this).attr("rel").split("_");
		var galleryId    = rel_split[3];
		var playerName   = rel_split[2]
		var embeddedName = rel_split[1];
		
		var id = jQuery(this).attr("id");
		id = id.replace("tubepress_")
		
		var obj = jQuery("#tubepress_embedded_object_" + galleryId);
        alert(obj.html()); 
    });
}
