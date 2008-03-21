<?php
abstract class TubePressOptionsCategory implements TubePressHasTitle {

    /* the title of this options category */
    private $title;
    
    /* an array of TubePressOptions */
    private $options;
    
    public final function getTitle() { return $this->title; }
    public final function &getOptions() { return $this->options; }
    
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
    
    public final function &get($optionName) {
        
        if (!array_key_exists($optionName, $this->options)) {
            throw new Exception("No such option in this category");
        }
        return $this->options[$optionName];
    }
    
    public function printForOptionsForm(HTML_Template_IT &$tpl) {

        $tpl->setVariable("OPTION_CATEGORY_TITLE", $this->title);
            
        /* go through each option in the category */
        foreach($this->options as $option) {             
            $tpl->setVariable("OPTION_TITLE", $option->getTitle());
            $tpl->setVariable("OPTION_DESC", $option->getDescription());
            $tpl->setVariable("OPTION_NAME", $option->getName());
            $option->getValue()->printForOptionsPage($tpl);
            $tpl->parse("optionRow");
        }
        $tpl->parse("optionCategory");
    }
}
?>