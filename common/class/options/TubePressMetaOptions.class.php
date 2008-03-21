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
    	        "ID", " ",
    	        new TubePressBoolValue(TubePressMetaOptions::id, false)),
    	    
    	    TubePressMetaOptions::rating => new TubePressOption(TubePressMetaOptions::rating,
    	        "Rating", " ",
    	        new TubePressBoolValue(TubePressMetaOptions::rating, false)),
    	    
    	    TubePressMetaOptions::ratings => new TubePressOption(TubePressMetaOptions::ratings,
    	        "Ratings", " ",
    	        new TubePressBoolValue(TubePressMetaOptions::ratings, false)),
    	    
    	    TubePressMetaOptions::uploaded => new TubePressOption(TubePressMetaOptions::uploaded,
    	        "Uploaded", " ",
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
	
    public function printForOptionsForm(HTML_Template_IT &$tpl) {

        $tpl->setVariable("OPTION_CATEGORY_TITLE", $this->getTitle());
        
        $colCount = 0;
        
        /* go through each option in the category */
        foreach($this->getOptions() as $option) {            
            $tpl->setVariable("EXTRA_STYLE", "; width: 15em"); 
            $tpl->setVariable("OPTION_TITLE", $option->getTitle());
            $tpl->setVariable("OPTION_DESC", $option->getDescription());
            $tpl->setVariable("OPTION_NAME", $option->getName());
            $option->getValue()->printForOptionsPage($tpl);

            if (++$colCount % 5 === 0) {
                $tpl->parse("optionRow");
            } else {
                $tpl->parse("option");
            }
            
        
        }
        $tpl->parse("optionCategory");
    }
}
?>                