<?php
class TubePressOptionsManager
{
    private $_customOptions = array();
    
    private $_tpsm;
    
    private $_tagString;
    
    public function __construct(TubePressStorageManager $tpsm)
    {
        $this->_tpsm = $tpsm;
    }
    
    public function get($optionName)
    {
        if (array_key_exists($optionName, $this->_customOptions)) {
            return $this->_customOptions[$optionName];
        }
        return $this->_tpsm->get($optionName);
    }
    
    public function setCustomOptions(array $customOpts) {
        $this->_customOptions = $customOpts;
    }
    
    public function setTagString($newTagString) {
        $this->_tagString = $newTagString;
    }
    
    public function getTagString() {
        return $this->_tagString;
    }
}
