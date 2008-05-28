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
 * Handles the parsing of the meta info below each video thumbnail
 *
 */
class TubePressMetaProcessor
{
	/**
	 * Parses a single video's meta information into a template
	 *
	 * @param TubePressVideo        $vid    The video to parse
	 * @param TubePressStorage_v160 $stored The TubePressStorage object
	 * @param string                $link   The link that will play the video
	 * @param HTML_Template_IT      &$tpl   The HTML template to parse into
	 * 
	 * @return void
	 */
    public static function process(TubePressVideo $vid, 
        TubePressOptionsManager $tpom, $link, HTML_Template_IT &$tpl) {
        
        $class = new ReflectionClass("TubePressMetaOptions");	

        /* go through each option in the category */
        foreach($class->getConstants() as $constant) {

            if ($tpom->get($constant) != 1) {
                continue;
            }
            
            switch ($constant) {
                
                case TubePressMetaOptions::TITLE:
                    $tpl->setVariable('PLAYLINK', $link);
                    $tpl->setVariable('TITLE', $vid->getTitle());
                    $tpl->parse('title');
                    break;
                    
                case TubePressMetaOptions::LENGTH:
                    $tpl->setVariable('RUNTIME', $vid->getRuntime());
                    $tpl->parse('runtime');
                    break;
                    
                case TubePressMetaOptions::DESCRIPTION:
                    $tpl->setVariable('DESCRIPTION', $vid->getDescription());
                    $tpl->parse('description');
                    break;
                
                case TubePressMetaOptions::AUTHOR:
                    $opt = $metaOpts[TubePressMetaOptions::AUTHOR];
                    $tpl->setVariable('METANAME', TpMsg::_("video-" . $constant));
                    $tpl->setVariable('AUTHOR', $vid->getAuthor());
                    $tpl->parse('author');
                    break;
                    
                case TubePressMetaOptions::TAGS:
                    $tags = explode(" ", $vid->getTags());
                    $tags = implode("%20", $tags);
                    $opt = $metaOpts[TubePressMetaOptions::TAGS];
                    $tpl->setVariable('METANAME', TpMsg::_("video-" . $constant));
                    $tpl->setVariable('SEARCHSTRING', $tags);
                    $tpl->setVariable('TAGS', $vid->getTags());
                    $tpl->parse('tags');
                    break;
                    
                case TubePressMetaOptions::URL:
                    $opt = $metaOpts[TubePressMetaOptions::URL];
                    $tpl->setVariable('LINKVALUE', $vid->getURL());
                    $tpl->setVariable('LINKTEXT', TpMsg::_("video-" . $constant));
                    $tpl->parse('url');
                    break;
                
                default:
                    $tpl->setVariable('METANAME', TpMsg::_("video-" . $constant));
                       
                    switch ($constant) {
                           case TubePressMetaOptions::VIEWS:
                               $tpl->setVariable('METAVALUE', 
                                   $vid->getViewCount());
                               break;
                           case TubePressMetaOptions::ID:
                               $tpl->setVariable('METAVALUE', $vid->getId());
                               break;
                           case TubePressMetaOptions::RATING:
                               $tpl->setVariable('METAVALUE', $vid->getRatingAverage());
                               break;
                           case TubePressMetaOptions::RATINGS:
                               $tpl->setVariable('METAVALUE', $vid->getRatingCount());
                               break;
                           case TubePressMetaOptions::UPLOADED:
                               $niceDate = $vid->getUploadTime();
                            if ($niceDate != "N/A") {
                                $niceDate = date($tpom->
                                	get(TubePressAdvancedOptions::DATEFORMAT), 
                                	$vid->getUploadTime());
                            }
                            $tpl->setVariable('METAVALUE', $niceDate);
                            break;
                        case TubePressMetaOptions::CATEGORY:
                            $tpl->setVariable('METAVALUE', $vid->getCategory());
                       }
            }
            
            $tpl->parse('meta');
        }
            
    }
}