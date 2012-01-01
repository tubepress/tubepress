<?php
require_once BASE . '/sys/classes/org/tubepress/impl/bootstrap/TubePressBootstrapper.class.php';

use \Mockery as m;

class org_tubepress_impl_bootstrap_TubePressBootstrapperTest extends TubePressUnitTest {

    private $_sut;

    function setUp()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_bootstrap_TubePressBootstrapper();
    }

    function testBoot()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $pm                    = $ioc->get(org_tubepress_api_plugin_PluginManager::_);
        $expectedSystemFilters = array(
            array(org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_EMBEDDED    , 'org_tubepress_impl_plugin_filters_embeddedtemplate_CoreVariables'),
            array(org_tubepress_api_const_plugin_FilterPoint::HTML_GALLERY         , 'org_tubepress_impl_plugin_filters_galleryhtml_GalleryInitJs'),
            array(org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_GALLERY     , 'org_tubepress_impl_plugin_filters_gallerytemplate_CoreVariables'),
            array(org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_GALLERY     , 'org_tubepress_impl_plugin_filters_gallerytemplate_EmbeddedPlayerName'),
            array(org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_GALLERY     , 'org_tubepress_impl_plugin_filters_gallerytemplate_Pagination'),
            array(org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_GALLERY     , 'org_tubepress_impl_plugin_filters_gallerytemplate_Player'),
            array(org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_GALLERY     , 'org_tubepress_impl_plugin_filters_gallerytemplate_VideoMeta'),
            array(org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_PLAYER      , 'org_tubepress_impl_plugin_filters_playertemplate_CoreVariables'),
            array(org_tubepress_api_const_plugin_FilterPoint::PROVIDER_RESULT      , 'org_tubepress_impl_plugin_filters_providerresult_ResultCountCapper'),
            array(org_tubepress_api_const_plugin_FilterPoint::PROVIDER_RESULT      , 'org_tubepress_impl_plugin_filters_providerresult_VideoBlacklist'),
            array(org_tubepress_api_const_plugin_FilterPoint::PROVIDER_RESULT      , 'org_tubepress_impl_plugin_filters_providerresult_Shuffler'),
            array(org_tubepress_api_const_plugin_FilterPoint::PROVIDER_RESULT      , 'org_tubepress_impl_plugin_filters_providerresult_VideoPrepender'),
            array(org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_SEARCHINPUT , 'org_tubepress_impl_plugin_filters_searchinputtemplate_CoreVariables'),
            array(org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_SINGLEVIDEO , 'org_tubepress_impl_plugin_filters_singlevideotemplate_CoreVariables'),
            array(org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_SINGLEVIDEO , 'org_tubepress_impl_plugin_filters_singlevideotemplate_VideoMeta'),
        );
        foreach ($expectedSystemFilters as $filter) {

            $pm->shouldReceive('registerFilter')->with($filter[0], anInstanceOf($filter[1]))->once();
        }
        $pm->shouldReceive('registerListener')->with(org_tubepress_api_const_plugin_EventName::BOOT, anInstanceOf('org_tubepress_impl_plugin_listeners_WordPressBoot'));
        $pm->shouldReceive('registerListener')->with(org_tubepress_api_const_plugin_EventName::BOOT, anInstanceOf('org_tubepress_impl_plugin_listeners_SkeletonExistsListener'));
        
        $pm->shouldReceive('notifyListeners')->with(org_tubepress_api_const_plugin_EventName::BOOT)->once();

        $envD = $ioc->get(org_tubepress_api_environment_Detector::_);
        $envD->shouldReceive('isWordPress')->once()->andReturn(false);

        $context = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $context->shouldReceive('get')->with(org_tubepress_api_const_options_names_Advanced::DEBUG_ON)->andReturn(false);

        $fe = $ioc->get(org_tubepress_api_filesystem_Explorer::_);
        $fe->shouldReceive('getDirectoriesInDirectory')->once()->with('<<user-content-dir>>/plugins', anything())->andReturn(array('fakedirectory'));
        $fe->shouldReceive('getFilenamesInDirectory')->once()->with('fakedirectory', anything())->andReturn(array(dirname(__FILE__) . '/../../../resources/simplePhpFile.php'));

        $th         = $ioc->get(org_tubepress_api_theme_ThemeHandler::_);
        $th->shouldReceive('getUserContentDirectory')->once()->andReturn('<<user-content-dir>>');
        
        $this->_sut->boot();
    }
}