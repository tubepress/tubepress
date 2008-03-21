<?php
class TubePressEmbeddedOptions extends TubePressOptionsCategory {
    
    const embeddedHeight = "embeddedHeight";
	const embeddedWidth = "embeddedWidth";
	const playerColor = "playerColor";
	const autoplay = "autoplay";
	const showRelated = "showRelated";
	const loop = "loop";
	const genie = "genie";
	const border = "border";

	public function __construct() {
	    
        $this->setTitle("Embedded player");
        
        $this->setOptions(array(
            
            TubePressEmbeddedOptions::embeddedHeight => new TubePressOption(
                TubePressEmbeddedOptions::embeddedHeight,
                "Max height (px)",
                "Default is 355",
                new TubePressIntValue(
                    TubePressEmbeddedOptions::embeddedHeight,
                    355
                )
            ),
            
            TubePressEmbeddedOptions::embeddedWidth => new TubePressOption(
                TubePressEmbeddedOptions::embeddedWidth,
                "Max width (px)",
                "Default is 425",
                new TubePressIntValue(
                    TubePressEmbeddedOptions::embeddedWidth,
                    425
                )
            ),
           
           TubePressEmbeddedOptions::playerColor => new TubePressOption(
               TubePressEmbeddedOptions::playerColor,
               "Color",
               "",
               new TubePressColorValue(TubePressEmbeddedOptions::playerColor)
           ),
           
           TubePressEmbeddedOptions::autoplay => new TubePressOption(
               TubePressEmbeddedOptions::autoplay,
               "Auto-play videos", "",
               new TubePressBoolValue(TubePressEmbeddedOptions::autoplay, false)
           ),
           
           TubePressEmbeddedOptions::showRelated => new TubePressOption(
               TubePressEmbeddedOptions::showRelated,
               "Show related videos",
               "Toggles the display of related videos after a video finishes",
               new TubePressBoolValue(TubePressEmbeddedOptions::showRelated, true)
           ),
           
           TubePressEmbeddedOptions::loop => new TubePressOption(
               TubePressEmbeddedOptions::loop,
               "Loop",
               "Continue playing the video until the user stops it",
               new TubePressBoolValue(TubePressEmbeddedOptions::loop, false)
           ),
           
           TubePressEmbeddedOptions::genie => new TubePressOption(
               TubePressEmbeddedOptions::genie,
               "Enhanced genie menu",
               "Show the genie menu (if present) when the mouse enters the video area (as opposed to only when the user pushes the \"menu\" button",
               new TubePressBoolValue(TubePressEmbeddedOptions::genie, false)
           ),
           
           TubePressEmbeddedOptions::border => new TubePressOption(
               TubePressEmbeddedOptions::border,
               "Show border",
               "",
               new TubePressBoolValue(TubePressEmbeddedOptions::border, false)
           )
        ));
	}
}
?>