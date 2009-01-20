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

class org_tubepress_player_factory_SimplePlayerFactory implements org_tubepress_player_factory_PlayerFactory {
	
	public function getInstance($playerName)
	{
		switch ($playerName) {
            
	        case org_tubepress_player_Player::NORMAL:
	            return new org_tubepress_player_impl_NormalPlayer();
	            break;
	
	        case org_tubepress_player_Player::GREYBOX:
	            return new org_tubepress_player_impl_GreyBoxPlayer();
	            break;
	
	        case org_tubepress_player_Player::POPUP:
	            return new org_tubepress_player_impl_PopupPlayer();
	            break;
	
	        case org_tubepress_player_Player::YOUTUBE:
	            return new org_tubepress_player_impl_YouTubePlayer();
	            break;
	
	        case org_tubepress_player_Player::LIGHTWINDOW:
	            return new org_tubepress_player_impl_LightWindowPlayer();
	
	        case org_tubepress_player_Player::SHADOWBOX:
	            return new org_tubepress_player_impl_ShadowBoxPlayer();
	
	        default:
	            throw new Exception("No such player with name '$playerName'");
        }
		
	}
}
?>
