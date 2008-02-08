<?php
class TubePressDisplayOptions extends TubePressOptionsCategory {
    
    const currentPlayerName = "playerLocation";
    const mainVidHeight = "mainVidHeight";
	const mainVidWidth = "mainVidWidth";
	const orderBy = "orderBy";
	const resultsPerPage = "resultsPerPage";
    const thumbHeight = "thumbHeight";
	const thumbWidth = "thumbWidth";
	const playerColor = "playerColor";
	const autoplay = "autoplay";
	const showRelated = "showRelated";

	public function __construct() {
	    
        $this->setTitle("Video display");
        
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
            
            TubePressDisplayOptions::mainVidHeight => new TubePressOption(
                TubePressDisplayOptions::mainVidHeight,
                "Max height (px) of main video",
                "Default is 336",
                new TubePressIntValue(
                    TubePressDisplayOptions::mainVidHeight,
                    336
                )
            ),
            
            TubePressDisplayOptions::mainVidWidth => new TubePressOption(
                TubePressDisplayOptions::mainVidWidth,
                "Max width (px) of main video",
                "Default is 424",
                new TubePressIntValue(
                    TubePressDisplayOptions::mainVidWidth,
                    424
                )
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
           ),
           
           TubePressDisplayOptions::playerColor => new TubePressOption(
               TubePressDisplayOptions::playerColor,
               "Player frame color",
               "This is a tweak that YouTube released recently. FIXME",
               new TubePressColorValue(TubePressDisplayOptions::playerColor)
           ),
           
           TubePressDisplayOptions::autoplay => new TubePressOption(
               TubePressDisplayOptions::autoplay,
               "Auto-play videos after thumbnail click", "",
               new TubePressBoolValue(TubePressDisplayOptions::autoplay, false)
           ),
           
           TubePressDisplayOptions::showRelated => new TubePressOption(
               TubePressDisplayOptions::showRelated,
               "Enable 'show related' feature'",
               "Toggles the related videos feature that appears after you watch a video",
               new TubePressBoolValue(TubePressDisplayOptions::showRelated, true)
           )
        ));
	}
}
?>