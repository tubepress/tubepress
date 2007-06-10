function playVideo(id, height, width, title, time, location, url) {
    switch (location) {
        case "popup":
            var newurl = url + "/common/popup.php?name=" + title + "&id=" + id + "&w=" + width + "&h=" + height;
            window.open(newurl, "newwin", "width=" + width + ",height=" + height + ",toolbar=false,locationbar=false,directories=false,status=false,menubar=false,scrollbars=false,resizable=true,copyhistory=false");
            break;
        default:
            document.getElementById('tubepress_mainvideo').innerHTML = ' \
                    <div id="tubepress_inner" style="width: ' + Math.min(width, 424) + 'px"> \
                    <div id="tubepress_btitle">' + decodeURIComponent(title) + ' \
    <object type="application/x-shockwave-flash" style="width:' + Math.min(width, 424) + 'px;height:' + Math.min(height, 336) + 'px;" data="http://www.youtube.com/v/' + id + '"> \
                <param name="movie" value="http://www.youtube.com/v/' + id + '" /> \
                </object></div> \
            ';
            document.location.hash = '#tubepress_video';
            break;
    }
}