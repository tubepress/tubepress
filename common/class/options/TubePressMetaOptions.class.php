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
	    
	    $this->setTitle("Meta display");
	    
	    $this->setOptions(array(
    	    TubePressMetaOptions::title => new TubePressOption(TubePressMetaOptions::title,
    	    	"Title", " ",
    	        new TubePressBoolValue(TubePressMetaOptions::title, true)),
    	    
    	    TubePressMetaOptions::length => new TubePressOption(TubePressMetaOptions::length,
    	        "Length", " ",
    	        new TubePressBoolValue(TubePressMetaOptions::length, true)),
    	    
    	    TubePressMetaOptions::views => new TubePressOption(TubePressMetaOptions::views,
    	        "Views", " ",
    	        new TubePressBoolValue(TubePressMetaOptions::views, true)),
    	    
    	    TubePressMetaOptions::author => new TubePressOption(TubePressMetaOptions::author,
    	        "Author", " ",
    	        new TubePressBoolValue(TubePressMetaOptions::author, false)),
    	    
    	    TubePressMetaOptions::id => new TubePressOption(TubePressMetaOptions::id,
    	        "Video ID", " ",
    	        new TubePressBoolValue(TubePressMetaOptions::id, false)),
    	    
    	    TubePressMetaOptions::rating => new TubePressOption(TubePressMetaOptions::rating,
    	        "Rating", " ",
    	        new TubePressBoolValue(TubePressMetaOptions::rating, false)),
    	    
    	    TubePressMetaOptions::ratings => new TubePressOption(TubePressMetaOptions::ratings,
    	        "Ratings", " ",
    	        new TubePressBoolValue(TubePressMetaOptions::ratings, false)),
    	    
    	    TubePressMetaOptions::uploaded => new TubePressOption(TubePressMetaOptions::uploaded,
    	        "Uploaded date", " ",
    	        new TubePressBoolValue(TubePressMetaOptions::uploaded, false)),
    	    
    	    TubePressMetaOptions::tags => new TubePressOption(TubePressMetaOptions::tags,
    	        "Tags", " ",
    	        new TubePressBoolValue(TubePressMetaOptions::tags, false)),
    	    
    	    TubePressMetaOptions::URL => new TubePressOption(TubePressMetaOptions::URL,
    	        "URL", " ",
    	        new TubePressBoolValue(TubePressMetaOptions::URL, false)),
    	    
    	    TubePressMetaOptions::description => new TubePressOption(TubePressMetaOptions::description,
    	        "Description", " ",
    	        new TubePressBoolValue(TubePressMetaOptions::description, false)),
    	    
    	    TubePressMetaOptions::category => new TubePressOption(TubePressMetaOptions::category,
    	        "Category", " ",
    	        new TubePressBoolValue(TubePressMetaOptions::category, false))
    	));
	    
	}
}
?>                