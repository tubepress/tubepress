<?php
class TubePressDisplayOptions extends TubePressOptionsCategory {
    
    const greyBoxEnabled = "greyBoxEnabled";
    const lightWindowEnabled = "lightWindowEnabled";
    const mainVidHeight = "mainVidHeight";
	const mainVidWidth = "mainVidWidth";
	const orderBy = "orderBy";
	const resultsPerPage = "resultsPerPage";
    const thumbHeight = "thumbHeight";
	const thumbWidth = "thumbWidth";

	public function __construct() {
        $this->title = "Video Display Options";
        
        new TubePressOption(
            TubePressDisplayOptions::mainVidHeight,
            "Max height (px) of main video",
            "Default is 336",
            new TubePressIntValue(
                TubePressDisplayOptions::mainVidHeight,
                336
            )
        );
        
        new TubePressOption(
            TubePressDisplayOptions::mainVidWidth,
            "Max width (px) of main video",
            "Default is 424",
            new TubePressIntValue(
                TubePressDisplayOptions::mainVidWidth,
                424
            )
        );
        
        new TubePressOption(
            TubePressDisplayOptions::thumbHeight,
            "Max height (px) of main video",
            "Default is 90",
            new TubePressIntValue(
                TubePressDisplayOptions::thumbHeight,
                90
            )
        );
        
        new TubePressOption(
            TubePressDisplayOptions::mainVidWidth,
            "Max width (px) of main video",
            "Default is 424",
            new TubePressIntValue(
                TubePressDisplayOptions::mainVidWidth,
                424
            )
        );        
        
	}
}
?>

                  TP_OPT_ORDERBY => new TubePressEnumOpt(
                      "Order videos by", " ", "updated",
                      array("updated", "viewCount", "rating", "relevance")),
                  
                  TP_OPT_VIDSPERPAGE=>  new TubePressIntegerOpt(
                      _tpMsg("VIDSPERPAGE_TITLE"), _tpMsg("VIDSPERPAGE_DESC"), 20, 50),      

                  TP_OPT_THUMBWIDTH =>  new TubePressIntegerOpt(
                      _tpMsg("THUMBWIDTH_TITLE"), _tpMsg("THUMBWIDTH_DESC"), 120, 120),
                  TP_OPT_THUMBHEIGHT => new TubePressIntegerOpt(
                      _tpMsg("THUMBHEIGHT_TITLE"), _tpMsg("THUMBHEIGHT_DESC"), 90, 90),
                  TP_OPT_GREYBOXON => new TubePressBooleanOpt(
                      _tpMsg("TP_OPT_GREYBOXON_TITLE"), _tpMsg("TP_OPT_GREYBOXON_DESC"),
                       false),
                  TP_OPT_LWON => new TubePressBooleanOpt(
                      _tpMsg("TP_OPT_LWON_TITLE"), _tpMsg("TP_OPT_LWON_DESC")
                      , false),