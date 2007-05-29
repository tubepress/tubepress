function playVideo(id, height, width, title, time, location, url) {
	switch (location) {
		case "popup":
			var newurl = url + "/wp/wp-content/plugins/tubepress/common/popup.php?name=" + title + "&id=" + id + "&w=" + width + "&h=" + height;
			window.open(newurl, "newwin", "width=" + width + ",height=" + height + ",toolbar=false,locationbar=false,directories=false,status=false,menubar=false,scrollbars=false,resizable=true,copyhistory=false");
			break;
		default:
			document.getElementById('tubepress_mainvideo').innerHTML = ' \
					<span class="tubepress_title">' + decodeURIComponent(title) + '</span> \
					<span class="tubepress_runtime">(' + time + ')</span><br /> \
	<object type="application/x-shockwave-flash" style="width:' + Math.min(width, 424) + 'px;height:' + Math.min(height, 336) + 'px;" data="http://www.youtube.com/v/' + id + '"> \
        		<param name="movie" value="http://www.youtube.com/v/' + id + '" /> \
        		</object> \
			';
			document.location.hash = '#tubepress_video';
			break;
	}
}