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
class TubePressMetaProcessor {
    
    public static function process(TubePressVideo $vid, TubePressStorage_v160 $stored, $link, HTML_Template_IT &$tpl) {
        
        $metaOpts = $stored->getMetaOptions()->getOptions();

        foreach ($metaOpts as $metaOpt) {
            
            if ($metaOpt->getValue()->getCurrentValue() === false) {
                continue;
            }
            
            switch ($metaOpt->getName()) {
                
                case TubePressMetaOptions::title:
                    $tpl->setVariable('PLAYLINK', $link);
        	        $tpl->setVariable('TITLE', $vid->getTitle());
        	        $tpl->parse('title');
        	        break;
        	        
                case TubePressMetaOptions::length:
                    $tpl->setVariable('RUNTIME', $vid->getRuntime());
                    $tpl->parse('runtime');
                    break;
                    
                case TubePressMetaOptions::description:
                    $tpl->setVariable('DESCRIPTION', $vid->getDescription());
                    $tpl->parse('description');
                    break;
                
                case TubePressMetaOptions::author:
                    $opt = $metaOpts[TubePressMetaOptions::author];
                    $tpl->setVariable('METANAME', $opt->getTitle());
                    $tpl->setVariable('AUTHOR', $vid->getAuthor());
                    $tpl->parse('author');
                    break;
                    
                case TubePressMetaOptions::tags:
                    $tags = explode(" ", $vid->getTags());
                    $tags = implode("%20", $tags);
                    $opt = $metaOpts[TubePressMetaOptions::tags];
                    $tpl->setVariable('METANAME', $opt->getTitle());
                    $tpl->setVariable('SEARCHSTRING', $tags);
                    $tpl->setVariable('TAGS', $vid->getTags());
                    $tpl->parse('tags');
                    break;
                    
                case TubePressMetaOptions::URL:
                    $opt = $metaOpts[TubePressMetaOptions::URL];
        	        $tpl->setVariable('LINKVALUE', $vid->getURL());
        	        $tpl->setVariable('LINKTEXT', "YouTube link");
                    $tpl->parse('url');
                    break;
                
                default:
                    $tpl->setVariable('METANAME', $metaOpt->getTitle());
                   	
                    switch ($metaOpt->getName()) {
       	                case TubePressMetaOptions::views:
       	                    $tpl->setVariable('METAVALUE', $vid->getViewCount());
       	                    break;
       	                case TubePressMetaOptions::id:
       	                    $tpl->setVariable('METAVALUE', $vid->getId());
       	                    break;
           	            case TubePressMetaOptions::rating:
           	                $tpl->setVariable('METAVALUE', $vid->getRatingAverage());
           	                break;
           	            case TubePressMetaOptions::ratings:
           	                $tpl->setVariable('METAVALUE', $vid->getRatingCount());
           	                break;
           	            case TubePressMetaOptions::uploaded:
           	                $niceDate = $vid->getUploadTime();
                            if ($niceDate != "N/A") {
                                $niceDate = date($stored->getCurrentValue(TubePressAdvancedOptions::dateFormat), $vid->getUploadTime());
                            }
                            $tpl->setVariable('METAVALUE', $niceDate);
                            break;
                        case TubePressMetaOptions::category:
                            $tpl->setVariable('METAVALUE', $vid->getCategory());
       	            }
            }
            
            $tpl->parse('meta');
        }
            
    }
}
?>