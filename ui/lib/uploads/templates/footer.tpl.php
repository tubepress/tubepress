<?php 
/**
 * Copyright 2006 - 2010 Eric D. Hough (http://ehough.com)
 * 
 * This file is part of TubePress (http://tubepress.org)
 * 
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 * 
 * Uber simple/fast template for TubePress. Idea from here: http://seanhess.net/posts/simple_templating_system_in_php
 * Sure, maybe your templating system of choice looks prettier but I'll bet it's not faster :)
 */
?>
		</div>
	</body>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
	<script type="text/javascript">
		TubePressUploadsAdmin = (function () {

			/* add a click listener to each album row */
			init = function () {
				jQuery("tr[id^='album_']").click(toggleAlbum);
			};

			/* toggle the display of an album's videos */
			toggleAlbum = function () {
				var album_id = jQuery(this).attr('id'),
					rowSelector = "[rel=videos_for_" + album_id + ']',
					img = jQuery("tr[id='" + album_id + "'] > td > img").first();

				if (jQuery(rowSelector).length > 0 && jQuery(rowSelector).first().is(':visible')) {
					jQuery(img).attr('src', 'famfam/folder_closed.png');
				} else {
					jQuery(img).attr('src', 'famfam/folder_open.png');
				}

				jQuery(rowSelector).each(function () {
					if (jQuery(this).is(':visible')) {
						jQuery(this).fadeOut();
					} else {
						jQuery(this).fadeIn();
					}
				});
			};

			return {	init	: init	};
		}());
		jQuery(window).ready(function () {
			TubePressUploadsAdmin.init();
		});
	</script>
</html>
