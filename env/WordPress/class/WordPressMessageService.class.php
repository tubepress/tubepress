<?php
/**
 * Copyright 2006, 2007, 2008, 2009 Eric D. Hough (http://ehough.com)
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
 */

/**
 * Message service that uses gettext (via WordPress)
 */
class WordPressMessageService extends AbstractTubePressMessageService
{
	/**
	 * Retrieves a message for TubePress
	 *
	 * @param string $msgId The message ID
	 *
	 * @return string The corresponding message, or "" if not found
	 */
	public function _($msgId)
	{
	    $message = $this->_keyToMessage($msgId);
	    return $message == "" ? "" : 
		    __($message, "tubepress");
	}
}
