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
 * A video that TubePress can play around with
 */
class TubePressVideo
{
    private $_author;
    private $_category;
    private $_description;
    private $_id;
    private $_rating;
    private $_ratings;
    private $_length;
    private $_tags;
    private $_thumbUrls;
    private $_title;
    private $_uploadTime;
    private $_youTubeUrl;
    private $_views;
    
    public function getAuthor() {      return $this->_author; }
    public function getCategory() {    return $this->_category; }
    public function getDescription() { return $this->_description; }
    public function getId() {          return $this->_id; }
    public function getRating() {      return $this->_rating; }
    public function getRatings() {     return $this->_ratings; }
    public function getLength() {      return $this->_length; }
    public function getTags() {        return $this->_tags; }
	public function getThumbUrls() {   return $this->_thumbUrls; }
    public function getTitle() {       return $this->_title; }
    public function getUploadTime() {  return $this->_uploadTime; }
    public function getYouTubeUrl() {  return $this->_youTubeUrl; }
    public function getViews() {       return $this->_views; }
    
    public function setAuthor($author) {             $this->_author = $author; }
    public function setCategory($category) {         $this->_category = $category; }
    public function setDescription($description) {   $this->_description = $description; }
    public function setId($id) {                     $this->_id = $id; }
    public function setRating($rating) {             $this->_rating = $rating; }
    public function setRatings($ratings) {           $this->_ratings = $ratings; }
    public function setLength($length) {             $this->_length = $length; }
    public function setTags(array $tags) {           $this->_tags = $tags; }
	public function setThumbUrls(array $thumbUrls) { $this->_thumbUrls = $thumbUrls; }
    public function setTitle($title) {               $this->_title = $title; }
    public function setUploadTime($uploadTime) {     $this->_uploadTime = $uploadTime; }
    public function setYouTubeUrl($youTubeUrl) {     $this->_youTubeUrl = $youTubeUrl; }
    public function setViews($views) {               $this->_views = $views; }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    public function getRandomThumbURL()
    {
    	$thumbs = $this->getThumbUrls();
        return $thumbs[array_rand($this->getThumbUrls())];
    }
    
    /**
     * Enter description here...
     *
     * @return unknown
     */
    public function getDefaultThumbURL()
    {
        return "http://img.youtube.com/vi/" . $this->getId() . "/default.jpg";
    }
    
}
?>
