/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
 * 
 * This file is part of TubePress (http://tubepress.org) and is released 
 * under the General Public License (GPL) version 3
 *
 * Shrink your JS: http://developer.yahoo.com/yui/compressor/
 */
function tubepress_popup_player(galleryId, videoId) {
	var wWidth		= TubePressEmbedded.getWidthOfCurrentEmbed(galleryId),
		wHeight		= TubePressEmbedded.getHeightOfCurrentEmbed(galleryId),
		top			= (screen.height / 2) - (wHeight / 2),
		left		= (screen.width / 2) - (wWidth / 2),
		win			= window.open('', '', 'location=0,directories=0,menubar=0,scrollbars=0,status=0,toolbar=0,width=' + wWidth + ',height=' + wHeight + ',top=' + top + ',left=' + left),
		preamble	= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">\n<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html;charset=utf-8" /><title>' +  jQuery("#tubepress_title_" + 
			videoId + '_' + galleryId).html() + '</title></head><body style="margin: 0pt; background-color: black;">',
		newHtml		= TubePressEmbedded.getHtmlForCurrentEmbed(galleryId),
		postAmble	= '</body></html>';

	win.document.write(preamble + newHtml + postAmble);
	win.document.close();
}

function tubepress_popup_player_init(baseUrl) {  }
