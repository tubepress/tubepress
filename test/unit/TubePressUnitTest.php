<?php

function_exists('tubepress_load_classes')
    || require dirname(__FILE__) . '/../../classes/tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_api_options_OptionsManager',
    'org_tubepress_api_ioc_IocService',
    'org_tubepress_util_OptionsReference',
    'org_tubepress_api_message_MessageService',
    'org_tubepress_api_options_StorageManager',
    'org_tubepress_url_impl_YouTubeUrlBuilder',
    'org_tubepress_video_feed_retrieval_HTTPRequest2',
    'org_tubepress_video_factory_impl_YouTubeVideoFactory',
    'org_tubepress_embedded_impl_YouTubeEmbeddedPlayerService',
    'org_tubepress_video_feed_inspection_impl_YouTubeFeedInspectionService',
    'org_tubepress_api_cache_Cache',
    'org_tubepress_api_pagination_Pagination',
    'org_tubepress_template_SimpleTemplate',
    'org_tubepress_ioc_IocContainer',
    'org_tubepress_api_theme_ThemeHandler'));

abstract class TubePressUnitTest extends PHPUnit_Framework_TestCase
{
    private $options = array();
    
    protected function initFakeIoc()
    {
        $ioc  = $this->getMock('org_tubepress_api_ioc_IocService');
        $ioc->expects($this->any())
                   ->method('get')
                   ->will($this->returnCallback(array($this, 'getMock')));
        org_tubepress_ioc_IocContainer::setInstance($ioc);
    }
    
    public function getMock($className)
    {
        $mock = parent::getMock($className);
        
        switch ($className) {
            case 'org_tubepress_api_options_OptionsManager':
                $mock->expects($this->any())
                   ->method('get')
                   ->will($this->returnCallback(array($this, 'optionsCallback')));
		$mock->expects($this->any())
		   ->method('setCustomOptions')
		   ->will($this->returnCallback(array($this, 'setOptions')));
                break;
            case 'org_tubepress_api_message_MessageService':
            case 'org_tubepress_api_options_StorageManager':
                $mock->expects($this->any())
                   ->method('_')
                   ->will($this->returnCallback(array($this, 'echoCallback')));
                break;
            case 'org_tubepress_api_theme_ThemeHandler':
                $mock->expects($this->any())
                     ->method('getTemplateInstance')
                     ->will($this->returnCallback(array($this, 'templateCallback')));
            default:
                break;
        }
        return $mock;
    }
    
    public function templateCallback()
    {
        $template = new org_tubepress_template_SimpleTemplate();
        $args = func_get_args();
        $template->setPath(dirname(__FILE__) . '/../../ui/themes/default/' .$args[0]);
        return $template;
    }
    
    public function setOptions($options)
    {
        $this->options = array();
        foreach ($options as $key => $value) {
            $this->options[$key] = $value;
        }
    }
    
    public function echoCallback()
    {
        $args = func_get_args();
        return $args[0];
    }

    public function optionsCallback() {
        $args = func_get_args();
        
        if (array_key_exists($args[0], $this->options)) {
            return $this->options[$args[0]];
        }
        
        return org_tubepress_util_OptionsReference::getDefaultValue($args[0]);
    }
}
?>
