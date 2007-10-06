<?php
class TubePressMetaOptions extends TubePressOptionsCategory {
    
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
    
	function __construct() {
	    
	    $this->title = "Meta display";
	    
	    new TubePressOption(TubePressMetaOptions::title,
	    	"Title", " ",
	        new TubePressBoolValue(TubePressMetaOptions::title, true));
	    
	    new TubePressOption(TubePressMetaOptions::length,
	        "Length", " ",
	        new TubePressBoolValue(TubePressMetaOptions::length, true));
	    
	    new TubePressOption(TubePressMetaOptions::views,
	        "Views", " ",
	        new TubePressBoolValue(TubePressMetaOptions::views, true));
	    
	    new TubePressOption(TubePressMetaOptions::author,
	        "Author", " ",
	        new TubePressBoolValue(TubePressMetaOptions::author, false));
	    
	    new TubePressOption(TubePressMetaOptions::id,
	        "Video ID", " ",
	        new TubePressBoolValue(TubePressMetaOptions::id, false));
	    
	    new TubePressOption(TubePressMetaOptions::rating,
	        "Rating", " ",
	        new TubePressBoolValue(TubePressMetaOptions::rating, false));
	    
	    new TubePressOption(TubePressMetaOptions::ratings,
	        "Ratings", " ",
	        new TubePressBoolValue(TubePressMetaOptions::ratings, false));
	    
	    new TubePressOption(TubePressMetaOptions::uploaded,
	        "Uploaded date", " ",
	        new TubePressBoolValue(TubePressMetaOptions::uploaded, false));
	    
	    new TubePressOption(TubePressMetaOptions::tags,
	        "Tags", " ",
	        new TubePressBoolValue(TubePressMetaOptions::tags, false));
	    
	    new TubePressOption(TubePressMetaOptions::URL,
	        "URL", " ",
	        new TubePressBoolValue(TubePressMetaOptions::URL, false));
	    
	    new TubePressOption(TubePressMetaOptions::description,
	        "Description", " ",
	        new TubePressBoolValue(TubePressMetaOptions::description, false));
	    
	    new TubePressOption(TubePressMetaOptions::category,
	        "Category", " ",
	        new TubePressBoolValue(TubePressMetaOptions::category, false));
	    
	}
}
?>                