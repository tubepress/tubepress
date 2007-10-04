<?php
class TubePressOption extends TPAbstractHasValue {
	
	const storageIdentifier = "tubepress";
	
	const currentModeName = "mode";

	const currentPlayerName = "playerLocation";
	
	const thumbHeight = "thumbHeight";
	const thumbWidth = "thumbWidth";
	const mainVidHeight = "mainVidHeight";
	const mainVidWidth = "mainVidWidth";
	const resultsPerPage = "resultsPerPage";
	const greyBoxEnabled = "greyBoxEnabled";
	const lightWindowEnabled = "lightWindowEnabled";
	const orderBy = "orderBy";

	const debugEnabled = "debugging_enabled";
	const triggerWord = "keyword";
	const timeout = "timeout";
	const randomThumbs = "randomize_thumbnails";
	const filter = "filter_racy";
	
	const author = "author";
	const id = "id";
	const title = "title";
	const length = "length";
	const ratings = "ratings";
	const rating = "rating";
	const description = "description";
	const views = "views";
	const uploaded = "uploaded";
	const tags = "tags";
	const URL = "url";
	const thumbURL ="thumburl";
	const category ="category";
	
	public function __construct($name, $title, $description, $default) {
		parent::__construct($theName, $theTitle, $theDescription);
		$this->defaultValue = $default;
	}
	
}
?>