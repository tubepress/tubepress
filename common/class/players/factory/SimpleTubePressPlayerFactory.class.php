<?php
/**
 * Copyright 2006, 2007, 2008 Eric D. Hough (http://ehough.com)
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

class SimpleTubePressPlayerFactory implements TubePressPlayerFactory {
	
	public function getInstance($playerName)
	{
		switch ($playerName) {
            
	        case TubePressPlayer::NORMAL:
	            return new TPNormalPlayer();
	            break;
	
	        case TubePressPlayer::GREYBOX:
	            return new TPGreyBoxPlayer();
	            break;
	
	        case TubePressPlayer::POPUP:
	            return new TPPopupPlayer();
	            break;
	
	        case TubePressPlayer::YOUTUBE:
	            return new TPYouTubePlayer();
	            break;
	
	        case TubePressPlayer::LIGHTWINDOW:
	            return new TPlightWindowPlayer();
	
	        case TubePressPlayer::SHADOWBOX:
	            return new TPShadowBoxPlayer();
	
	        default:
	            throw new Exception("No such player with name '$playerName'");
        }
		
	}
}
?>
