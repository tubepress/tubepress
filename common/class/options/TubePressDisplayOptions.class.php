<?php
class TubePressDisplayOptions extends TubePressOptionsCategory {
    
    const currentPlayerName = "playerLocation";
    const greyBoxEnabled = "greyBoxEnabled";
    const lightWindowEnabled = "lightWindowEnabled";
    const mainVidHeight = "mainVidHeight";
	const mainVidWidth = "mainVidWidth";
	const orderBy = "orderBy";
	const resultsPerPage = "resultsPerPage";
    const thumbHeight = "thumbHeight";
	const thumbWidth = "thumbWidth";

	public function __construct() {
	    
        $this->setTitle("Video Display Options");
        
        $thumbHeightValue = new TubePressIntValue(TubePressDisplayOptions::thumbHeight, 120);
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
            
           TubePressDisplayOptions::lightWindowEnabled => new TubePressOption(
                TubePressDisplayOptions::lightWindowEnabled,
                "Enable lightWindow",
                "Checking this box will load the lightWindow JS libraries" .
                  " in your blog. This <i>may</i> interfere with your theme and/or other plugins," .
                  " so it's good practice to leave this disabled if you're not using lightWindow.",
                new TubePressBoolValue(
                    TubePressDisplayOptions::lightWindowEnabled,
                    false
                )
            ),
            
            TubePressDisplayOptions::greyBoxEnabled => new TubePressOption(
                TubePressDisplayOptions::greyBoxEnabled,
                "Enable GreyBox",
                "Checking this box will load the GreyBox JS libraries" .
                  " in your blog. This <i>may</i> interfere with your theme and/or other plugins," .
                  " so it's good practice to leave this disabled if you're not using GreyBox.",
                new TubePressBoolValue(
                    TubePressDisplayOptions::greyBoxEnabled,
                    false
                )
            ),
            
            TubePressDisplayOptions::orderBy => new TubePressOption(
                TubePressDisplayOptions::orderBy,
                "Enable GreyBox",
                "Checking this box will load the GreyBox JS libraries" .
                  " in your blog. This <i>may</i> interfere with your theme and/or other plugins," .
                  " so it's good practice to leave this disabled if you're not using GreyBox.",
                new TubePressOrderValue(
                    TubePressDisplayOptions::orderBy,
                    TubePressOrderValue::views
                )
            )
        ));
	}
}
?>