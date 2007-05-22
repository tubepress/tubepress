<?php
/**
 * TubePressGallery.php
 * 
 * Copyright (C) 2007 Eric D. Hough (http://ehough.com)
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

class_exists("TubePressCSS") || require("TubePressCSS.php");
class_exists("TubePressXML") || require("TubePressXML.php");
class_exists("TubePressVideo") || require("TubePressVideo.php");

/**
 * Handles fetching and printing out a YouTube gallery. This is meant
 * to be a "static" class, but PHP 4 doesn't support them :(
 */
class TubePressGallery
{
    
    /**
     * Don't let anyone instantiate this class
     */
    function TubePressGallery()
    {
        die("This is a static utility class");
    }

    /**
     * This is the main method
     */
    function generate($options)
    {
        /* Print out the header */
        $newcontent = TubePressGallery::printHTML_videoheader();
    
        /* are we paging? */
        $paging = TubePressStatic::areWePaging($options);
    
        /* Grab the XML from YouTube's API */
        $youtube_xml = TubePressXML::fetchRawXML($options);

        /* Any HTTP errors? */
        if (PEAR::isError($youtube_xml)) {
            return $youtube_xml;
        }
    
        $videoArray = TubePressXML::parseRawXML($youtube_xml);

        /* Any parsing errors? Or errors from YouTube? */
        if (PEAR::isError($videoArray)) {
            return $videoArray;
        }
        
        /* keeps track of how many videos we've actually printed */
        $videoCount = 0;
        
        /* Next two lines figure out how many videos we're going to show */
        $vidLimit = ($paging?
            $options->get(TP_OPT_VIDSPERPAGE) : 
            $videoArray['total']);
            
        if ($videoArray['total'] < $vidLimit) {
            $vidLimit = $videoArray['total'];
        }

        for ($x = 0; $x < $vidLimit; $x++) {
            
            /* Create a TubePressVideo object from the XML (if we can) */
            $video = new TubePressVideo($videoArray['video'][$x]);
    
            if (PEAR::isError($video)) {
                continue;
            }
        
            /* If we're on the first video, see if we need to print a big one */
            if ($videoCount++ == 0) {
                $newcontent .= TubePressGallery::printHTML_bigvid($video, $options);
                if ($paging) {
                    $newcontent .= 
                        TubePressGallery::printHTML_pagination($videoArray['total'], 
                            $options);
                }
                $newcontent .= sprintf('<div class="%s">',
                    $css->thumb_container_class);
            }
            $newcontent .= TubePressGallery::printHTML_smallvid($video, $options);
        }
    
        $newcontent .= '</div>';
        if ($paging) {
            $newcontent .= 
                TubePressGallery::printHTML_pagination($videoArray['total'],
                    $options);
        }
    
        return $newcontent;
    }
    
     /**
     * Prints out an embedded video at the top of the gallery.
     * Used in "normal" video playing mode.
     * 
     * @param vid A TubePressVideo object of the video we're going to play
     * @param options A TubePressTag object holding all of our options
     */
    function printHTML_bigvid($vid, $options)
    {    
        $css = new TubePressCSS();
        
        /* we only do this stuff if we're operating in "normal" play mode */
        if ($options->getValue(TP_OPT_PLAYIN) != TP_PLAYIN_NORMAL) {
            return "";
        }
        
        $returnVal = <<<EOT
            <div id="$css->mainVid_id" class="$css->mainVid_class">
                <span class="$css->title_class">
                    {$vid->metaValues[TP_VID_TITLE]}</span>
                <span class="$css->runtime_class">
                    ({$vid->metaValues[TP_VID_LENGTH]})</span><br />
EOT;
        
        $returnVal .= 
            TubePressGallery::printHTML_embeddedVid($vid->metaValues[TP_VID_ID], $options);
        
        $returnVal .= sprintf('</div> <!--%s-->', $css->mainVid_class);
        
        return $returnVal;
    }
    
    /**
     * Handles the dirty work of printing out the embedded flash
     * 
     * @param id The YouTube video ID
     * @param options A TubePressTag object holding all of our options
     */
    function printHTML_embeddedVid($id, $options)
    {
        $height = $options->getValue(TP_OPT_VIDHEIGHT);
        $width = $options->getValue(TP_OPT_VIDWIDTH);
        
        return <<<EOT
            <object type="application/x-shockwave-flash" 
                style="width:{$width}px; height:{$height}px;" 
                data="http://www.youtube.com/v/$id" >
                    <param name="movie" value="http://www.youtube.com/v/$id" />
            </object>
EOT;
    }
    
    /**
     * Prints out video meta information below a video thumbnail.
     * The title and runtime are handled slightly differently than 
     * the rest since they output so differently.
     * 
     * @param vid A TubePressVideo object of the video in question
     * @param options A TubePressTag object holding all of our options
     * @param link The attributes of the anchor for the title text. 
     * This is generated from 
     * TubePressGallery::printHTML_smallVidLinkAttributes()
     */
    function printHTML_metaInfo($vid, $options, $link)
    {
    	$css = new TubePressCSS();
    
        /* first do the title */    //TODO: is this right???
        $content = sprintf('<div class="%s">', $css->title_class);
        if ($options->getValue(TP_VID_TITLE) == true) {
            $content .= sprintf('<a %s>%s</a><br/></div><!--%s-->', 
                $link, $vid->metaValues[TP_VID_TITLE], $css->title_class);
        }
        
        /* now do the runtime */
        if ($options->getValue(TP_VID_LENGTH) == true) {
            $content .= sprintf('<span class="%s">%s</span><br/>',
                $css->runtime_class, $vid->metaValues[TP_VID_LENGTH]);
        }
        
        $metaOptions = $options->getMetaOptionNames();
        /* now do the rest, since they all look alike */
        foreach ($metaOptions as $metaName) {
            
            /* ignore the title and runtime */
            if (($metaName == TP_VID_LENGTH) || ($metaName == TP_VID_TITLE)) {
                continue;
            }
            /* only bother with the ones the user wants to see */
            if ($options->getValue($metaName)) {
                $content .=  sprintf('<span class="%s">', $css->meta_class);
                switch ($metaName) {
                    
                    case TP_VID_DESC:
                        $content .= 
                            sprintf('</span>%s', $vid->metaValues[$metaName]);
                        break;
                        
                    case TP_VID_THUMBURL:
                        $content .= 
                            TubePressGallery::printHTML_metaLink($option->getTitle(),
                            $vid->metaValues[$metaName]);
                        break;
                        
                    case TP_VID_URL:
                        $content .=
                            TubePressGallery::printHTML_metaLink($option->getTitle(), 
                            $vid->metaValues[$metaName]);
                        break;
                        
                    case TP_VID_AUTHOR:
                        $content .= sprintf('%s: %s', $metaName,
                            TubePressGallery::printHTML_metaLink($vid->metaValues[$metaName],
                            'http://www.youtube.com/profile?user='
                            . $vid->metaValues[$metaName]));
                        break;
                        
                    case TP_VID_COMMENT_CNT:
                        $content .= sprintf('%s: %s', $metaName,
                            TubePressGallery::printHTML_metaLink($vid->metaValues[$metaName],
                           'http://youtube.com/comment_servlet?all_comments&amp;v='
                            . $vid->metaValues[$metaName]));
                        break;
                        
                    case TP_VID_TAGS:
                        $tags = explode(" ", $vid->metaValues[$metaName]);
                        $tags = implode("%20", $tags);
                        $content .= sprintf('%s: %s', $metaName,
                            TubePressGallery::printHTML_metaLink($vid->metaValues[$metaName],
                                sprintf('http://youtube.com/results?' .
                                    'search_query=%s&amp;search=Search',
                                    $tags)));
                        break;
                        
                    default:
                        $content .=  sprintf('%s: </span>%s', $metaName,
                            $vid->metaValues[$metaName]);
                }
                $content .= '<br/>';
            }
        }
        $content .= sprintf('</div><!--%s-->', $css->meta_group);
        
        return $content;
    }
    
    /**
     * Simple helper method for TubePressGallery::printHTML_metaInfo(). Prints
     * out a link for a line of meta information.
     * 
     * @param linkText The text of the link
     * @param linkvalue The anchor attributes
     */
    function printHTML_metaLink($linkText, $linkValue)
    {
        return sprintf('</span><a href="%s">%s</a>"', $linkValue, $linkText);
    }
        
    /**
     * Handles the logic and printing of pagination links ("next" and "prev")
     * 
     * @param vidCount How many videos we're supposed to print out per page 
     * (+ 1, unless we're on the last page)
     * @param options A TubePressTag object holding all of our options
     * @param css A CSS holder object
     */
    function printHTML_pagination($vidCount, $options)
    {
        $css = new TubePressCSS();
    
        /* if we're already on a page, save that value, otherwise assume 
         * we're on the first page */
        $currentPage = (isset($_GET[TP_PAGE_PARAM])? $_GET[TP_PAGE_PARAM] : 1);
    
        /* save our current full address */
        $url = TubePressTag::fullURL();
    
        /* print a previous button if we're not on the first page */
        $prevText = (($currentPage > 1)? 
            TubePressGallery::printHTML_paginationLink($url, $currentPage - 1, "< prev")
             : "&nbsp;");
    
        /* vidcount will always be one more than what the user wanted, 
         * unless we're on the last page */
        $nextText = (($vidCount < $options->getValue(TP_OPT_VIDSPERPAGE))? 
            "&nbsp;"
             : TubePressGallery::printHTML_paginationLink($url,
                 $currentPage + 1, "next >"));
    
        return sprintf('<div class="%s"><div class="%s">%s</div>' .
                '<div class="%s">%s</div></div>',
                $css->pagination, $css->prevlink, $prevText, 
                $css->nextlink, $nextText);
    }
    
    /**
     * Simple helper for TubePressGallery::printHTML_pagination(). 
     * Prints out the actual anchor tag
     * 
     * @param queryString The full URL of the page we're currently on
     * @param pageNum The page for which this method will print out a link
     * @param text The text of the link (always either "next" or "prev")
     */
    function printHTML_paginationLink($queryString, $pageNum, $text)
    {
        $url = new Net_URL($queryString);
        $url->removeQueryString(TP_PAGE_PARAM);
        $url->addQueryString(TP_PAGE_PARAM, $pageNum);
        
        return sprintf('<a href="%s">%s</a>"', 
            str_replace("&", "&amp;", $url->getURL()), $text);
    }
    
    /**
     * Used in "single video" mode to print out a single video and a 
     * "back to gallery" link
     *
     * @param options A TubePressTag object holding all of our options
     */
    function printHTML_singleVideo($options)
    {
        $css = new TubePressCSS();
        
        $url = new Net_URL(TubePressStatic::fullURL());
        $url->removeQueryString(TP_VID_PARAM);
        
        $returnVal = '<div id="' . $css->mainVid_id . '" class="' 
            . $css->mainVid_class . '">';
        $returnVal .= TubePressGallery::printHTML_embeddedVid($_GET[TP_VID_PARAM],
            $options);
        $returnVal .= '</div><a href="' . $url->getURL() . '">' 
            . _tpMsg("BACK2GALLERY") . '</a>';
        
        return $returnVal;
    }
    
    /**
     * The main wrapper method for printing out a single video 
     * thumbnail and the meta information for it.
     * 
     * @param vid A TubePressVideo object for which this method 
     * will print the thumb
     * @param options A TubePressTag object holding all of our options
     */
    function printHTML_smallvid($vid, $options)
    {
        $css = new TubePressCSS();
        
        $caption = sprintf('%s (%s)', $vid->metaValues[TP_VID_TITLE], 
            $vid->metaValues[TP_VID_LENGTH]);
        $thumbWidth = $options->getValue(TP_OPT_THUMBWIDTH);
        $thumbHeight = $options->getValue(TP_OPT_THUMBHEIGHT);
        $title = htmlspecialchars($vid->metaValues[TP_VID_TITLE]);
        $link = TubePressGallery::printHTML_smallVidLinkAttributes($vid,
            $options);
  
        $content = sprintf('<div class="%s"><div class="%s">', 
            $css->thumb_class, $css->thumbImg_class);
        $thumbSrc = $vid->metaValues[TP_VID_THUMBURL];
        $content .= <<<EOT
                <a $link>
                    <img alt="{$vid->metaValues[TP_VID_TITLE]}"  
                        src="$thumbSrc" width="$thumbWidth"  
                        height="$thumbHeight"  />
                </a>
            </div><!-- {$css->thumbImg_class} -->
            <div class="{$css->meta_group}">
EOT;
        $content .= TubePressGallery::printHTML_metaInfo($vid, $options,
            $link);
        $content .= sprintf('</div><!-- %s -->', $css->thumb_class);
        
        if ($options->getValue(TP_OPT_PLAYIN) == TP_PLAYIN_THICKBOX) {
            $content .= sprintf('<div id="tp%s" style="display:none">%s</div>',
                $vid->metaValues[TP_VID_ID],
                TubePressGallery::printHTML_embeddedVid($vid->metaValues[TP_VID_ID],
                    $options));
        }
        
        return $content;
    }
    
    /**
     * Prints out the "play" link attributes for a video thumbnail
     * 
     * @param vid A TubePressVideo object for the video in question
     * @param options A TubePressTag object holding all of our options
     */
    function printHTML_smallVidLinkAttributes($vid, $options)
    {
        $id = $vid->metaValues[TP_VID_ID];
        $height = $options->getValue(TP_OPT_VIDHEIGHT);
        $width = $options->getValue(TP_OPT_VIDWIDTH);
        
        switch ($options->getValue(TP_OPT_PLAYIN)) {
            case TP_PLAYIN_THICKBOX:
                return sprintf(
                    'href="#TB_inline?height=350&amp;width=425&amp;' .
                    'inlineId=tp%s" class="thickbox" title="%s"',
                    $id, $vid->metaValues[TP_VID_TITLE]);
                
            case TP_PLAYIN_NW:
                $url = new Net_URL(TubePressTag::fullURL());
                $url->addQueryString(TP_VID_PARAM, $id);
                return sprintf('href="%s"', $url->getURL());
            
            case TP_PLAYIN_YT:
                return sprintf('href="http://youtube.com/watch?v=%s', $id);
            
            case TP_PLAYIN_LWINDOW:
                //TODO: test me with relative quotes
                return sprintf('href="%s/wp-content/plugins/' .
                        'tubepress/tp_popup.php?' .
                        'name=%s&id=%s&w=%s&h=%s" class="lWOn" title="%s" ' .
                        'params="lWWidth=425,lWHeight=355"', 
                    $options->get('site_url'),
                    htmlspecialchars($vid->metaValues[TP_VID_TITLE]),
                    $id,
                    $options->getValue(TP_OPT_VIDWIDTH),
                    $options->getValue(TP_OPT_VIDHEIGHT),
                    htmlspecialchars($vid->metaValues[TP_VID_TITLE]));
        
            case TP_PLAYIN_NORMAL:
                return sprintf('href="#" onclick="javascript:playVideo(' .
                        '\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'normal\', \'%s\')"',
                    $id, $height, $width,
                    htmlspecialchars($vid->metaValues[TP_VID_TITLE]),
                    $vid->metaValues[TP_VID_LENGTH],
                    "http://localhost/wp");
    
            default:
                return sprintf('href="#" onclick="javascript:playVideo(' .
                        '\'%s\', \'%s\', \'%s\', \'%s\', \'%s\', \'popup\', \'%s\')"',
                    $id, $height, $width,
                    htmlspecialchars($vid->metaValues[TP_VID_TITLE]),
                    $vid->metaValues[TP_VID_LENGTH],
                    $options->get('site_url'));
        }
    }
    
    /**
     * Prints out the tail end of the gallery
     * 
     * @param css A CSS holder object
     */
    function printHTML_videofooter()
    {
    	$css = new TubePressCSS();
        return sprintf('</div><!-- %s -->', $css->container);
    }
    
    /**
     * Prints out the very beginning of the gallery
     * 
     * @param css A CSS holder object
     */
    function printHTML_videoheader()
    {
        $css = new TubePressCSS();
        return sprintf('<div class="%s">', $css->container);
    }
}
?>