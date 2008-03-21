<?php
class TubePressDisplayOptions extends TubePressOptionsCategory {
    
    const currentPlayerName = "playerLocation";
	const orderBy = "orderBy";
	const resultsPerPage = "resultsPerPage";
    const thumbHeight = "thumbHeight";
	const thumbWidth = "thumbWidth";

	public function __construct() {
	    
        $this->setTitle("Gallery options");
        
        $thumbHeightValue = new TubePressIntValue(TubePressDisplayOptions::thumbHeight, 90);
        $thumbHeightValue->setMax(90);
        
        $thumbWidthValue = new TubePressIntValue(TubePressDisplayOptions::thumbWidth, 120);
        $thumbWidthValue->setMax(120);
        
        $resultsPerPageValue = new TubePressIntValue(TubePressDisplayOptions::resultsPerPage, 20);
        $resultsPerPageValue->setMax(50);
        
        $this->setOptions(array(
            TubePressDisplayOptions::currentPlayerName => new TubePressOption(
                TubePressDisplayOptions::currentPlayerName,
                "Play each video...", " ",
                new TubePressPlayerValue(
                    TubePressDisplayOptions::currentPlayerName,
                    new TPNormalPlayer())
            ),
            
            TubePressDisplayOptions::thumbHeight => new TubePressOption(
                TubePressDisplayOptions::thumbHeight,
                "Height (px) of thumbs",
                "Default (and maximum) is 90",
                $thumbHeightValue
            ),
                
            TubePressDisplayOptions::thumbWidth => new TubePressOption(
                TubePressDisplayOptions::thumbWidth,
                "Width (px) of thumbs",
                "Default (and maximum) is 120",
                $thumbWidthValue
           ),
           
           TubePressDisplayOptions::resultsPerPage => new TubePressOption(
                TubePressDisplayOptions::resultsPerPage,
                "Videos per page",
                "Default is 20, maximum is 50",
                $resultsPerPageValue
            ),
            
           TubePressDisplayOptions::orderBy => new TubePressOption(
               TubePressDisplayOptions::orderBy,
               "Order videos by",
               "",
               new TubePressOrderValue(TubePressDisplayOptions::orderBy)
           )
        ));
	}
}
?>