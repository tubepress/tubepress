<?php

require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/plugin/filters/providerresult/PerPageSorter.class.php';

class org_tubepress_impl_plugin_filters_providerresult_PerPageSorterTest extends TubePressUnitTest
{
	private $_sut;

	private $_videos;

	function setup()
	{
		parent::setUp();
		$this->_sut = new org_tubepress_impl_plugin_filters_providerresult_PerPageSorter();
		$this->_buildVideos();
	}

	function testSortOrderNone()
	{
	    $providerResult = \Mockery::mock('org_tubepress_api_provider_ProviderResult');

        $this->_setSortAndPerPageOrder(org_tubepress_api_const_options_values_OrderByValue::COMMENT_COUNT, org_tubepress_api_const_options_values_PerPageSortValue::NONE);

        $this->_sut->alter_providerResult($providerResult);
	}

	function testSortBothRandom()
	{
	    $providerResult = \Mockery::mock('org_tubepress_api_provider_ProviderResult');
	    $providerResult->shouldReceive('getVideoArray')->once()->andReturn($this->_videos);
	    $providerResult->shouldReceive('setVideoArray')->once();

	    $this->_setSortAndPerPageOrder(org_tubepress_api_const_options_values_OrderByValue::RANDOM, org_tubepress_api_const_options_values_PerPageSortValue::RANDOM);

	    $this->_sut->alter_providerResult($providerResult);
	}

	function testCommentCount()
	{
	    $providerResult = new org_tubepress_api_provider_ProviderResult();
	    $providerResult->setVideoArray($this->_videos);

	    $this->_setSortAndPerPageOrder(org_tubepress_api_const_options_values_OrderByValue::RANDOM, org_tubepress_api_const_options_values_PerPageSortValue::COMMENT_COUNT);

	    $result = $this->_sut->alter_providerResult($providerResult);

	    $this->_verifySort($result->getVideoArray(), 3, 2, 1);
	}

	function testDuration()
	{
	    $providerResult = new org_tubepress_api_provider_ProviderResult();
	    $providerResult->setVideoArray($this->_videos);

	    $this->_setSortAndPerPageOrder(org_tubepress_api_const_options_values_OrderByValue::RANDOM, org_tubepress_api_const_options_values_PerPageSortValue::DURATION);

	    $result = $this->_sut->alter_providerResult($providerResult);

	    $this->_verifySort($result->getVideoArray(), 2, 3, 1);
	}

	function testNewest()
	{
	    $providerResult = new org_tubepress_api_provider_ProviderResult();
	    $providerResult->setVideoArray($this->_videos);

	    $this->_setSortAndPerPageOrder(org_tubepress_api_const_options_values_OrderByValue::RANDOM, org_tubepress_api_const_options_values_PerPageSortValue::NEWEST);

	    $result = $this->_sut->alter_providerResult($providerResult);

	    $this->_verifySort($result->getVideoArray(), 1, 2, 3);
	}

	function testOldest()
	{
	    $providerResult = new org_tubepress_api_provider_ProviderResult();
	    $providerResult->setVideoArray($this->_videos);

	    $this->_setSortAndPerPageOrder(org_tubepress_api_const_options_values_OrderByValue::RANDOM, org_tubepress_api_const_options_values_PerPageSortValue::OLDEST);

	    $result = $this->_sut->alter_providerResult($providerResult);

	    $this->_verifySort($result->getVideoArray(), 3, 2, 1);
	}

	function testRating()
	{
	    $providerResult = new org_tubepress_api_provider_ProviderResult();
	    $providerResult->setVideoArray($this->_videos);

	    $this->_setSortAndPerPageOrder(org_tubepress_api_const_options_values_OrderByValue::RANDOM, org_tubepress_api_const_options_values_PerPageSortValue::RATING);

	    $result = $this->_sut->alter_providerResult($providerResult);

	    $this->_verifySort($result->getVideoArray(), 2, 3, 1);
	}

	function testTitle()
	{
	    $providerResult = new org_tubepress_api_provider_ProviderResult();
	    $providerResult->setVideoArray($this->_videos);

	    $this->_setSortAndPerPageOrder(org_tubepress_api_const_options_values_OrderByValue::RANDOM, org_tubepress_api_const_options_values_PerPageSortValue::TITLE);

	    $result = $this->_sut->alter_providerResult($providerResult);

	    $this->_verifySort($result->getVideoArray(), 1, 3, 2);
	}

	function testViewCount()
	{
	    $providerResult = new org_tubepress_api_provider_ProviderResult();
	    $providerResult->setVideoArray($this->_videos);

	    $this->_setSortAndPerPageOrder(org_tubepress_api_const_options_values_OrderByValue::RANDOM, org_tubepress_api_const_options_values_PerPageSortValue::VIEW_COUNT);

	    $result = $this->_sut->alter_providerResult($providerResult);

	    $this->_verifySort($result->getVideoArray(), 3,2,1);
	}

	private function _verifySort($videos, $first, $second, $third)
	{
        $this->assertTrue($videos[0]->xxx == $first, "First video should have been $first but was " . $videos[0]->xxx);
        $this->assertTrue($videos[1]->xxx == $second, "Second video should have been $second but was " . $videos[1]->xxx);
        $this->assertTrue($videos[2]->xxx == $third, "Third video should have been $third but was " . $videos[2]->xxx);
	}

	private function _setSortAndPerPageOrder($feed, $perPage)
	{
	    $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
	    $exec = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);

	    $exec->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Feed::ORDER_BY)->andReturn($feed);
	    $exec->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Feed::PER_PAGE_SORT)->andReturn($perPage);
	}

	private function _buildVideos()
	{
	    $v1 = new org_tubepress_api_video_Video();
	    $v1->xxx = 1;
	    $v1->setCommentCount(100);
	    $v1->durationInSeconds = 10;
	    $v1->timePublishedInUnixTime = 4000;
	    $v1->setRatingAverage('1.5');
	    $v1->setTitle('one');
	    $v1->setViewCount(500);

	    $v2 = new org_tubepress_api_video_Video();
	    $v2->xxx = 2;
	    $v2->setCommentCount(200);
	    $v2->durationInSeconds = 30;
	    $v2->timePublishedInUnixTime = 3000;
	    $v2->setRatingAverage('3.5');
	    $v2->setTitle('two');
	    $v2->setViewCount(600);

	    $v3 = new org_tubepress_api_video_Video();
	    $v3->xxx = 3;
	    $v3->setCommentCount(300);
	    $v3->durationInSeconds = 20;
	    $v3->timePublishedInUnixTime = 2000;
	    $v3->setRatingAverage('2.5');
	    $v3->setTitle('three');
	    $v3->setViewCount(700);

        $this->_videos = array(

            $v1, $v2, $v3
        );

        shuffle($this->_videos);

	}
}

