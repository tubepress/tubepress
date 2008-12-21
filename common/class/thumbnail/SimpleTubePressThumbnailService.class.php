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

if (!function_exists("TubePressRelativeTime")) {
	include dirname(__FILE__) . "/../../../lib/relative_time.php";
}

/**
 * Handles the parsing of the meta info below each video thumbnail
 *
 */
class SimpleTubePressThumbnailService implements TubePressThumbnailService
{
	private $_tpl;
	private $_tpom;
	private $_msg;
	private $_tppf;
	private $_tpeps;
	
    public function getHtml($templateFile, TubePressVideo $vid, TubePressPlayer $player)
    {
    	$this->_tpl = new HTML_Template_IT(dirname(__FILE__) . "/../../ui");
        if (!$this->_tpl->loadTemplatefile($templateFile, true, true)) {
            throw new Exception("Couldn't load thumbnail template");
        }
    	//$this->_tpl->init();
        $this->_getCommonStuff($vid, $player);
        $this->_getMetaStuff($vid, $player);
        return $this->_tpl->get();
    }
    
    public function setOptionsManager(TubePressOptionsManager $tpom)
    {
    	$this->_tpom = $tpom;
    }
    
    private function _getCommonStuff(TubePressVideo $vid, TubePressPlayer $player)
    {
        $randomizeOpt = $this->_tpom->
            get(TubePressAdvancedOptions::RANDOM_THUMBS);
        $thumbWidth   = $this->_tpom->
            get(TubePressDisplayOptions::THUMB_WIDTH);
        $thumbHeight  = $this->_tpom->
            get(TubePressDisplayOptions::THUMB_HEIGHT);
        $height       = $this->_tpom->
            get(TubePressEmbeddedOptions::EMBEDDED_HEIGHT);
        $width        = $this->_tpom->
            get(TubePressEmbeddedOptions::EMBEDDED_WIDTH);
        
        $playLink = $player->getPlayLink($vid, $this->_tpom);

        $this->_tpl->setVariable('IMAGEPLAYLINK', $playLink);
        $this->_tpl->setVariable('IMAGETITLE', $vid->getTitle());

        if ($randomizeOpt) {
            $this->_tpl->setVariable('THUMBURL', $vid->getRandomThumbURL());
        } else {
             $this->_tpl->setVariable('THUMBURL', $vid->getDefaultThumbURL());
        }    
        
        $this->_tpl->setVariable('THUMBWIDTH', $thumbWidth);
        $this->_tpl->setVariable('THUMBHEIGHT', $thumbHeight);
    }
    
    private function _getMetaStuff(TubePressVideo $vid, TubePressPlayer $player)
    {
        $class = new ReflectionClass("TubePressMetaOptions");    

        $link = $playLink = $player->getPlayLink($vid, $this->_tpom);
        
        /* go through each option in the category */
        foreach ($class->getConstants() as $constant) {

            if ($this->_tpom->get($constant) != 1 
                && $this->_tpom->get($constant) != $constant) {
                continue;
            }
            
            $nofollow = $this->_tpom->get(TubePressAdvancedOptions::NOFOLLOW_LINKS);
            
            switch ($constant) {
                
            case TubePressMetaOptions::TITLE:
                $this->_tpl->setVariable('PLAYLINK', $link);
                $this->_tpl->setVariable('TITLE', $vid->getTitle());
                $this->_tpl->parse('title');
                break;
                    
            case TubePressMetaOptions::LENGTH:
                $this->_tpl->setVariable('RUNTIME', $vid->getLength());
                $this->_tpl->parse('runtime');
                break;
                    
            case TubePressMetaOptions::DESCRIPTION:
            	$limit = $this->_tpom->get(TubePressDisplayOptions::DESC_LIMIT);
            	$desc = $vid->getDescription();
            	if ($limit > 0 && strlen($desc) > $limit) {
            		$desc = substr($desc, 0, $limit) . "...";
            	}
                $this->_tpl->setVariable('DESCRIPTION', $desc);
                $this->_tpl->parse('description');
                break;
                
            case TubePressMetaOptions::AUTHOR:
                $this->_tpl->setVariable('METANAME', $this->_msg->_("video-" . $constant));
                $this->_tpl->setVariable('AUTHOR', $vid->getAuthor());
                if ($nofollow) { $this->_tpl->setVariable("NOFOLLOW", "rel=\"external nofollow\""); }
                $this->_tpl->parse('author');
                break;
                    
            case TubePressMetaOptions::TAGS:
                $tags = explode(" ", $vid->getTags());
                $tags = implode("%20", $tags);
                $this->_tpl->setVariable('METANAME', $this->_msg->_("video-" . $constant));
                $this->_tpl->setVariable('SEARCHSTRING', $tags);
                $this->_tpl->setVariable('TAGS', $vid->getTags());
             	if ($nofollow) { $this->_tpl->setVariable("NOFOLLOW", "rel=\"external nofollow\""); }
                $this->_tpl->parse('tags');
                break;
                    
            case TubePressMetaOptions::URL:
                $this->_tpl->setVariable('LINKVALUE', $vid->getYouTubeUrl());
                $this->_tpl->setVariable('LINKTEXT', $this->_msg->_("video-" . $constant));
                $this->_tpl->parse('url');
                break;
                
            default:
                $this->_tpl->setVariable('METANAME', $this->_msg->_("video-" . $constant));
                   
                switch ($constant) {

                case TubePressMetaOptions::VIEWS:
                    $this->_tpl->setVariable('METAVALUE', $vid->getViews());
                    break;
                           
                case TubePressMetaOptions::ID:            
                    $this->_tpl->setVariable('METAVALUE', $vid->getId());
                    break;

                case TubePressMetaOptions::RATING:
                    $this->_tpl->setVariable('METAVALUE', $vid->getRating());
                    break;
                           
                case TubePressMetaOptions::RATINGS:
                    $this->_tpl->setVariable('METAVALUE', $vid->getRatings());
                    break;
                           
                case TubePressMetaOptions::UPLOADED:
                    $niceDate = $vid->getUploadTime();
                    if ($niceDate != "N/A") {
                    	if ($this->_tpom->get(TubePressDisplayOptions::RELATIVE_DATES)) {
                    		$niceDate = 
                    		    TubePressRelativeTime($vid->getUploadTime());
                    	} else {
                            $niceDate = date($this->_tpom->
                                get(TubePressAdvancedOptions::DATEFORMAT), 
                                $vid->getUploadTime());
                    	}
                    }
                    $this->_tpl->setVariable('METAVALUE', $niceDate);
                    break;
                        
                case TubePressMetaOptions::CATEGORY:
                    $this->_tpl->setVariable('METAVALUE', $vid->getCategory());
                }
                $this->_tpl->parse('meta');
            }
        }
    }
    
    public function setMessageService(TubePressMessageService $messageService)
    { 
    	$this->_msg = $messageService; 
    }
}
