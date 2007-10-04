<?php
class TubePressOption implements TubePressHasValue,
    TubePressHasDescription, TubePressHasName, TubePressHasTitle {
	
	const storageIdentifier = "tubepress";
	
	const currentModeName = "mode";

	const currentPlayerName = "playerLocation";
	

	
	const author = "author";
	const category ="category";
	const description = "description";
	const id = "id";
	const length = "length";
	const rating = "rating";
	const ratings = "ratings";
	const tags = "tags";
	const thumbURL ="thumburl";
	const title = "title";
	const uploaded = "uploaded";
	const URL = "url";
	const views = "views";
	
    private $name;
    private $title;
    private $description;
    private $value;
	
	public function __construct($theName, $theTitle, $theDescription, $theDefault) {
		$this->name = $theName;
		$this->title = $theTitle;
		$this->description = $theDescription;
		$this->value = $theDefault;
	}
}
?>