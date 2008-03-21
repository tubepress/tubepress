<?php
final class TubePressPlayerValue extends TubePressEnumValue {
   
	/**
	 * Default constructor
	 *
	 * @param string $theName
	 * @param string $theDefault
	 */
    public function __construct($theName, $theDefault) {
 
        parent::__construct($theName, array(
            TubePressPlayer::normal => "normally (at the top of your gallery)",
            TubePressPlayer::popup => "in a popup window",
            TubePressPlayer::youTube => "from the original YouTube page",
            TubePressPlayer::newWindow => "in a new window by itself",
            TubePressPlayer::lightWindow => "with lightWindow (experimental)",
            TubePressPlayer::greyBox => "with GreyBox (experimental)"
        ), TubePressPlayer::normal);
    }
  
}
?>