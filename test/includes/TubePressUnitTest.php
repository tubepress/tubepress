<?php
require_once 'PHPUnit/Framework.php';

require_once 'env/fake_wordpress_functions.inc.php';
require_once 'env/tubepressBaseUrl.php';

require_once 'utils/arrays.php';
require_once 'utils/chain.php';
require_once 'utils/constants.php';

class_exists('TubePressClassloader') || require dirname(__FILE__) . '/../../sys/classes/org/tubepress/impl/classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
	'org_tubepress_impl_ioc_IocContainer',
    'org_tubepress_impl_options_OptionsReference',
    'org_tubepress_impl_template_SimpleTemplate',
));

abstract class TubePressUnitTest extends PHPUnit_Framework_TestCase
{
    private $options = array();
    
    public function setUp()
    {
        $ioc = $this->getMock('org_tubepress_api_ioc_IocService');
        
        $ioc->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(array($this, 'getMock')));
        
        org_tubepress_impl_ioc_IocContainer::setInstance($ioc);
    }
    
    public function getMock($className)
    {
        $mock = parent::getMock($className);
        
        switch ($className) {
            
            case 'org_tubepress_api_exec_ExecutionContext':
                $this->setupOptionsManagerMock($mock);
                break;
                
            case 'org_tubepress_api_message_MessageService':
            case 'org_tubepress_api_options_StorageManager':
                $this->setupMessageMock($mock);
                break;
                
            case 'org_tubepress_api_theme_ThemeHandler':
                $this->setupThemeHandlerMock($mock);
                break;
                
            case 'org_tubepress_api_filesystem_Explorer':
                $this->setupExplorerMock($mock);
                break;
                
            default:
                break;
        }
        return $mock;
    }
    
    public function setupThemeHandlerMock($mock)
    {
        $mock->expects($this->any())
             ->method('getTemplateInstance')
             ->will($this->returnCallback(array($this, 'templateCallback')));
             
        $mock->expects($this->any())
             ->method('getCssPath')
             ->will($this->returnCallback(array($this, 'cssPathCallback')));
    }

    public function cssPathCallback()
    {
        $args = func_get_args();
        if (count($args) === 1 || !$args[1]) {
            return dirname(__FILE__) . '/../../sys/ui/themes/' . $args[0] . '/style.css';
        }
        return 'sys/ui/themes/' . $args[0] . '/style.css';
    }


    public function templateCallback()
    {
        $template = new org_tubepress_impl_template_SimpleTemplate();
        $args = func_get_args();
        $template->setPath(dirname(__FILE__) . '/../../sys/ui/themes/default/' .$args[0]);
        return $template;
    }
    
    public function setupOptionsManagerMock($mock)
    {
        $mock->expects($this->any())
             ->method('get')
             ->will($this->returnCallback(array($this, 'optionsCallback')));
             
        $mock->expects($this->any())
             ->method('setCustomOptions')
             ->will($this->returnCallback(array($this, 'setOptions')));
             
        $mock->expects($this->any())
             ->method('getCustomOptions')
             ->will($this->returnValue($this->options));
    }

    public function setOptions($options)
    {
        $this->options = array();

        foreach ($options as $key => $value) {
            $this->options[$key] = $value;
        }
    }

    public function optionsCallback() {

        $args = func_get_args();

        if (array_key_exists($args[0], $this->options)) {
            return $this->options[$args[0]];
        }

        return org_tubepress_impl_options_OptionsReference::getDefaultValue($args[0]);
    }
    
    public function setupMessageMock($mock)
    {
        $mock->expects($this->any())
             ->method('_')
             ->will($this->returnCallback(array($this, 'echoCallback')));
    }
    
    public function echoCallback()
    {
        $args = func_get_args();
        return $args[0];
    }
    
    public function setupExplorerMock($mock)
    {
        $mock->expects($this->any())
              ->method('getTubePressBaseInstallationPath')
              ->will($this->returnValue(realpath(dirname(__FILE__) . '/../../')));
              
        $mock->expects($this->any())
             ->method('getFilenamesInDirectory')
             ->will($this->returnCallback(array($this, 'readfiles')));
             
        $mock->expects($this->any())
             ->method('getDirectoriesInDirectory')
             ->will($this->returnCallback(array($this, 'readdir')));
             
        $mock->expects($this->any())
             ->method('getSystemTempDirectory')
             ->will($this->returnCallback('sys_get_temp_dir'));
    }
    
    public function readfiles($path)
    {
        $raw = scandir($path);
        return array_filter($raw, array($this, 'isfile'));
    }
    
    public function isfile($element)
    {
        return is_file($element);
    }
    
    public function readdir($path)
    {
        $raw = scandir($path);
        return array_filter($raw, array($this, 'isdir'));
    }
    
    public function isdir($element)
    {
        return is_dir($element);
    }
}