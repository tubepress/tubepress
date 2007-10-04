<?php
abstract class TubePressOptionsCategory implements TubePressHasTitle {

    protected $title;
    protected $options;
    
    public function getTitle() { return $this->title; }
    
    public function getOptions();
}
?>