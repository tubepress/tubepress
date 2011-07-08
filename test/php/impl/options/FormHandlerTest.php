<?php

require_once BASE . '/sys/classes/org/tubepress/impl/options/FormHandler.class.php';

class org_tubepress_impl_options_FormHandlerTest extends TubePressUnitTest {

    private $_stpom;

    public function setup()
    {
        parent::setUp();
        $this->_stpom = new org_tubepress_impl_options_FormHandler();
    }

    public function testDisplay()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $messageService = $ioc->get('org_tubepress_api_message_MessageService');
        $messageService->shouldReceive('_')->atLeast()->once()->andReturnUsing( function ($key) {
            return "[[$key]]";
        });

        $storageManager = $ioc->get('org_tubepress_api_options_StorageManager');
        $storageManager->shouldReceive('get')->atLeast()->once()->andReturnUsing( function ($key) {
            return "<value of $key>";
        });

        $fs = $ioc->get('org_tubepress_api_filesystem_Explorer');
        $fs->shouldReceive('getTubePressBaseInstallationPath')->atLeast()->once()->andReturn('basePath');
        $fs->shouldReceive('getDirectoriesInDirectory')->atLeast()->once()->with('basePath/sys/ui/themes', 'Options Reference')->andReturn(array());
        $fs->shouldReceive('getDirectoriesInDirectory')->atLeast()->once()->with('basePath/content/themes', 'Options Reference')->andReturn(array());

        $mockTemplate = \Mockery::mock('org_tubepress_api_template_Template');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::OPTIONS_PAGE_TITLE, '[[options-page-title]]');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::OPTIONS_PAGE_INTRO, '[[options-page-intro-text]]');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::OPTIONS_PAGE_SAVE, '[[options-page-save-button]]');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::OPTIONS_PAGE_OPTIONS_FILTER, '[[options-page-options-filter]]');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::TUBEPRESS_BASE_URL, '<tubepress_base_url>');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::OPTIONS_PAGE_CATEGORIES, \Mockery::any());
        $mockTemplate->shouldReceive('toString')->once()->andReturn('fooey');

        $templateBldr   = $ioc->get('org_tubepress_api_template_TemplateBuilder');
        $templateBldr->shouldReceive('getNewTemplateInstance')->once()->with('basePath/sys/ui/templates/wordpress/options_page.tpl.php')->andReturn($mockTemplate);

        $this->assertEquals('fooey', $this->_stpom->getHtml());
    }

    public function testCollect()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $storageManager = $ioc->get('org_tubepress_api_options_StorageManager');
        $storageManager->shouldReceive('exists')->atLeast()->once()->andReturn(true);
        $storageManager->shouldReceive('set')->atLeast()->once();

        $fakePostVars = array('test', 'two', 'poo');
        $this->assertNull($this->_stpom->collect($fakePostVars));
    }

    private function expected()
    {
        return file_get_contents(dirname(__FILE__) . '/expected.txt');
    }
}

