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
        
        $ext = new ReflectionExtension('TubePressPlayer');
        
        $opts = array(
            new TubePressOption(
                TubePressDisplayOptions::currentPlayerName,
                "Play each video...", " ",
                new TubePressEnumValue(
                    TubePressDisplayOptions::currentPlayerName,
                    $ext->getConstants(),
                    TubePressPlayer::normal)
            ),
            
            new TubePressOption(
                TubePressDisplayOptions::mainVidHeight,
                "Max height (px) of main video",
                "Default is 336",
                new TubePressIntValue(
                    TubePressDisplayOptions::mainVidHeight,
                    336
                )
            ),
            
            new TubePressOption(
                TubePressDisplayOptions::mainVidWidth,
                "Max width (px) of main video",
                "Default is 424",
                new TubePressIntValue(
                    TubePressDisplayOptions::mainVidWidth,
                    424
                )
            )
        );
           
        $thumbHeightValue = new TubePressIntValue(TubePressDisplayOptions::thumbHeight, 120);
        $thumbHeightValue->setMax(90);
        array_push($opts, new TubePressOption(
            TubePressDisplayOptions::thumbHeight,
                "Height (px) of thumbs",
                "Default (and maximum) is 90",
                $thumbHeightValue
        ));
            
        $thumbWidthValue = new TubePressIntValue(TubePressDisplayOptions::thumbWidth, 120);
        $thumbWidthValue->setMax(120);
        array_push($opts, new TubePressOption(
                TubePressDisplayOptions::thumbWidth,
                "Width (px) of thumbs",
                "Default (and maximum) is 120",
                $thumbWidthValue
        ));    
            
        $resultsPerPageValue = new TubePressIntValue(TubePressDisplayOptions::resultsPerPage, 20);
        $resultsPerPageValue->setMax(50);
        array_push($opts, new TubePressOption(
                TubePressDisplayOptions::resultsPerPage,
                "Videos per page",
                "Default is 20, maximum is 50",
                $resultsPerPageValue
        ));
        
        array_push($opts,
            new TubePressOption(
                TubePressDisplayOptions::lightWindowEnabled,
                "Enable lightWindow",
                "Checking this box will load the lightWindow JS libraries" .
                  " in your blog. This <i>may</i> interfere with your theme and/or other plugins," .
                  " so it's good practice to leave this disabled if you're not using lightWindow.",
                new TubePressBoolOption(
                    TubePressDisplayOptions::lightWindowEnabled,
                    false
                )
            ),
            
            new TubePressOption(
                TubePressDisplayOptions::greyBoxEnabled,
                "Enable GreyBox",
                "Checking this box will load the GreyBox JS libraries" .
                  " in your blog. This <i>may</i> interfere with your theme and/or other plugins," .
                  " so it's good practice to leave this disabled if you're not using GreyBox.",
                new TubePressBoolOption(
                    TubePressDisplayOptions::greyBoxEnabled,
                    false
                )
            ),
            
            new TubePressOption(
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
        );
	}
}
?>