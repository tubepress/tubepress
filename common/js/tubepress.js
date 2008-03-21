function tubePress_normalPlayer(embed, width, title) {
	document.getElementById('tubepress_mainvideo').innerHTML =
    	'<div id="tubepress_inner" style="width: ' + Math.min(width, 425) + 'px">' +
		'	<div id="tubepress_btitle">' + decodeURIComponent(title) + '</div>' +
    	decodeURIComponent(embed);
}

function tubePress_popup(url, height, width) {
	window.open(url, "newwin",
		"toolbar=false," +
		"locationbar=false," +
		"directories=false," +
		"status=false," +
		"menubar=false," +
		"scrollbars=false," +
		"resizable=true," +
		"copyhistory=false," +
		"height=" + height + "," +
		"width=" + width
	);
}
