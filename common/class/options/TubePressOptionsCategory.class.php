<?php
abstract class TubePressOptionsCategory implements TubePressHasTitle {

    /* the title of this options category */
    private $title;
    
    /* an array of TubePressOptions */
    private $options;
    
    public final function getTitle() { return $this->title; }
    public final function getOptions() { return $this->options; }
    
    protected final function setTitle($newTitle) {
        $this->title = $newTitle;
    }
    
    protected final function setOptions($newOptions) {
        
        /* make sure that we're getting an array */
        if (!is_array($newOptions)) {
            throw new Exception("Options must be an array");   
        }
        
        /* make sure that each is a TubePressOption */
        foreach ($newOptions as $options) {
            if (!is_a($options, "TubePressOption")) {
                throw new Exception("Options must be TubePressOptions only");
            }
        }
        
        $this->options = $newOptions;
    }
    
    public final function get($optionName) {
        if (!in_array($optionName, $this->options)) {
            throw new Exception("No such option in this category");
        }
        return $this->options[$optionName];
    }
}
?>