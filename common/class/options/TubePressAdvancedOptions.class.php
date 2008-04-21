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
class TubePressAdvancedOptions extends TubePressOptionsCategory {
    
    const debugEnabled = "debugging_enabled";
    const filter = "filter_racy";
	const randomThumbs = "randomize_thumbnails";
	const timeout = "timeout";
	const triggerWord = "keyword";
	const dateFormat = "dateFormat";

    public function __construct() {
        
        $this->setTitle("Advanced");
        $this->setOptions(array(
        
            TubePressAdvancedOptions::triggerWord => new TubePressOption(
                TubePressAdvancedOptions::triggerWord,
                "Trigger keyword",
                "The word you insert (in plaintext, between square brackets) into your posts to display your YouTube gallery.",
                new TubePressTextValue(
                    TubePressAdvancedOptions::triggerWord,
                    "tubepress"
                )
            ),
            
            TubePressAdvancedOptions::debugEnabled => new TubePressOption(
                TubePressAdvancedOptions::debugEnabled,
                "Enable debugging",
                "If checked, " .
                             "anyone will be able to view your debugging " .
                             "information. This is a rather small privacy " .
                             "risk. If you're not having problems with " .
                             "TubePress, or you're worried about revealing " .
                             "any details of your TubePress pages, feel free to " .
                             "disable the feature.",
                new TubePressBoolValue(
                    TubePressAdvancedOptions::debugEnabled,
                    true
                )
            ),
            
            TubePressAdvancedOptions::randomThumbs => new TubePressOption(
                TubePressAdvancedOptions::randomThumbs,
                "Randomize thumbnails",
                "Most videos come with several thumbnails. By selecting this option, each time someone views your gallery they will see the same videos with each video's thumbnail randomized",
                new TubePressBoolValue(
                    TubePressAdvancedOptions::randomThumbs,
                    true
                )
            ),
            
            TubePressAdvancedOptions::filter => new TubePressOption(
                TubePressAdvancedOptions::filter,
            	"Filter \"racy\" content",
                "Don't show videos that may not be suitable for minors.",
                new TubePressBoolValue(
                    TubePressAdvancedOptions::filter,
                    true
                )
            ),
            
            TubePressAdvancedOptions::dateFormat => new TubePressOption(
                TubePressAdvancedOptions::dateFormat,
            	"Date format",
                "Set the textual formatting of date information for videos. See <a href=\"http://us.php.net/date\">date</a> for examples.",
                new TubePressTextValue(
                    TubePressAdvancedOptions::dateFormat,
                    "M j, Y"
                )
            )
        ));
    }
}
?>
                  