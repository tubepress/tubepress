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

/**
 * Options that control which meta info is displayed below video
 * thumbnails
 *
 */
class TubePressMetaOptions extends TubePressOptionsCategory
{
    
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
    
	/**
	 * Default constructor
	 *
	 */
	function __construct()
	{
	    
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
    	        "Posted", " ",
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
	
    public function printForOptionsForm(HTML_Template_IT &$tpl) 
    {

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
              