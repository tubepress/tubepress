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
<div class="span-24 last">
	<div class="error">
		<strong>Access denied: No password set</strong>
	</div>
	<div>
		You don't have a password set for the TubePress uploads administration page.
		<ul>
			<li>WordPress users: Go to <strong>WP Admin > Settings > TubePress > Uploads</strong> to set a password</li>
			<li>Standalone PHP users: Do somethign awesome</li>
		</ul>
		Sorry for the inconvenience, but the Internet can be a nasty place!
	</div>
</div>
