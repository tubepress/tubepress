<?php
class TubePressOptionsForm {
    
    public final function display(TubePressStorage_v157 $stored) {

        /* load up the template */
        $tpl = new HTML_Template_IT(dirname(__FILE__) . "/../../ui");
        if (!$tpl->loadTemplatefile("options_page.tpl.html", true, true)) {
            throw new Exception("Could not load options template");
        }
        
        $tpl->setVariable("PAGETITLE", "TubePress Options");
        $tpl->setVariable("INTROTEXT", "This is some intro text");
        $tpl->setVariable("SAVE", "Save");
        
        /* go through each category */
        foreach ($stored->getOptionPackages() as $optionCategory) {
            $optionCategory->printForOptionsForm($tpl);
        }
  
        $tpl->parse("options");
        print $tpl->get();
    }
    
    /**
     * Updates options from a keyed array
     *
     * @param TubePressStorage_v157 $stored
     * @param array $postVars
     */
    public final function collect(TubePressStorage_v157 &$stored, array $postVars) {
    	
        /* go through each category */
    	$packages =& $stored->getOptionPackages();
        foreach ($packages as &$optionCategory) {
        	
        	$options =& $optionCategory->getOptions();
        	
        	/* update each option */
            foreach ($options as &$option) {
            	$value =& $option->getValue();
            	$value->updateFromOptionsPage($postVars);
            }
        }
    }
}
?>