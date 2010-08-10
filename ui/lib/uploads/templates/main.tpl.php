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
<div class="span-8" id="album_list">
	<table class="album">
	<?php foreach (${org_tubepress_uploads_admin_AdminPageHandler::ADMIN_ALBUM_ARRAY} as $album) : ?>	
        	<tr id="album_<?php echo md5($album->getRelativeContainerPath()); ?>">
			<td><img src="famfam/folder_closed.png" /></td>
			<td><img src="famfam/folder.png" /> <span><?php echo $album->getRelativeContainerPath(); ?> (<?php echo sizeof($album->getRelativeVideoPaths()); ?>)</span></td>
		</tr>
		<?php foreach ($album->getRelativeVideoPaths() as $relativeVideoPath) : ?>
		<tr rel="videos_for_album_<?php echo md5($album->getRelativeContainerPath()); ?>" class="video">
			<td>&nbsp;</td>
			<td>&nbsp;&nbsp;&nbsp;&nbsp;<img src="famfam/film.png" /> <span><?php echo basename($relativeVideoPath); ?></span></td>
		</tr>
		<?php endforeach; ?>

	<?php endforeach; ?>
	</table>
</div>
<div class="span-16 last" id="video_editing_pane">
Hello
</div>
