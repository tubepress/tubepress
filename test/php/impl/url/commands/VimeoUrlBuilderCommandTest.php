<?php

require_once BASE . '/sys/classes/org/tubepress/impl/url/commands/VimeoUrlBuilderCommand.class.php';

class org_tubepress_impl_url_commands_VimeoUrlBuilderCommandTest extends TubePressUnitTest {

    const PRE = "/http:\/\/vimeo.com\/api\/rest\/v2\?";
    const POST = "&format=php&oauth_consumer_key=vimeokey&oauth_nonce=[a-zA-Z0-9]+&oauth_signature_method=HMAC-SHA1&oauth_timestamp=[0-9]+&oauth_version=1.0&oauth_signature=[a-zA-Z0-9%]+/";

    private $_sut;

    function setUp()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_url_commands_VimeoUrlBuilderCommand();

        $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();

        $pc           = $ioc->get('org_tubepress_api_provider_ProviderCalculator');
        $pc->shouldReceive('calculateProviderOfVideoId')->zeroOrMoreTimes()->andReturn(org_tubepress_api_provider_Provider::VIMEO);

        $execContext = $ioc->get('org_tubepress_api_exec_ExecutionContext');
        $execContext->shouldReceive('get')->zeroOrMoreTimes()->with(org_tubepress_api_const_options_names_Display::RESULTS_PER_PAGE)->andReturn(20);
    }

    /**
    * @expectedException Exception
    */
    function testNoVimeoKeyGallery()
    {
        $this->_expectOptions(array(
        org_tubepress_api_const_options_names_Feed::VIMEO_KEY => '',
        org_tubepress_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));

        $context = self::_buildContext(org_tubepress_api_provider_Provider::VIMEO, true, '444333');

        $this->_sut->execute($context);
    }

    /**
     * @expectedException Exception
     */
    function testNoVimeoKeySingleVideo()
    {
        $this->_expectOptions(array(
        org_tubepress_api_const_options_names_Feed::VIMEO_KEY => '',
        org_tubepress_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));

        $context = self::_buildContext(org_tubepress_api_provider_Provider::VIMEO, true, '444333');

        $this->_sut->execute($context);
    }

    /**
    * @expectedException Exception
    */
    function testNoVimeoSecretGallery()
    {
        $this->_expectOptions(array(
        org_tubepress_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
        org_tubepress_api_const_options_names_Feed::VIMEO_SECRET => ''
        ));

        $context = self::_buildContext(org_tubepress_api_provider_Provider::VIMEO, true, '444333');

        $this->_sut->execute($context);
    }

    /**
     * @expectedException Exception
     */
    function testNoVimeoSecretSingleVideo()
    {
        $this->_expectOptions(array(
        org_tubepress_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
        org_tubepress_api_const_options_names_Feed::VIMEO_SECRET => ''
        ));

        $context = self::_buildContext(org_tubepress_api_provider_Provider::VIMEO, true, '444333');

        $this->_sut->execute($context);
    }

    function testSingleVideoUrl()
    {
        $this->_expectOptions(array(
           org_tubepress_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
           org_tubepress_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));

        $context = self::_buildContext(org_tubepress_api_provider_Provider::VIMEO, true, '444333');

        self::assertTrue($this->urlMatches('method=vimeo.videos.getInfo&video_id=444333', $context));
    }

    function testexecuteGroup()
    {
        $this->_expectOptions(array(
           org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::VIMEO_GROUP,
           org_tubepress_api_const_options_names_Output::VIMEO_GROUP_VALUE => 'eric',
           org_tubepress_api_const_options_names_Display::ORDER_BY => 'random',
           org_tubepress_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
           org_tubepress_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));

        $context = self::_buildContext(org_tubepress_api_provider_Provider::VIMEO, false, 1);

        self::assertTrue($this->urlMatches('method=vimeo.groups.getVideos&group_id=eric&full_response=true&page=1&per_page=20&sort=random', $context));
    }

    function testExecuteCreditedTo()
    {
        $this->_expectOptions(array(
        org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::VIMEO_CREDITED,
        org_tubepress_api_const_options_names_Output::VIMEO_CREDITED_VALUE => 'eric',
        org_tubepress_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
        org_tubepress_api_const_options_names_Display::ORDER_BY => 'random',
        org_tubepress_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));

        $context = self::_buildContext(org_tubepress_api_provider_Provider::VIMEO, false, 1);

        self::assertTrue($this->urlMatches('method=vimeo.videos.getAll&user_id=eric&full_response=true&page=1&per_page=20', $context));
    }

    function testexecuteAlbum()
    {
        $this->_expectOptions(array(
           org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::VIMEO_ALBUM,
           org_tubepress_api_const_options_names_Output::VIMEO_ALBUM_VALUE => 'eric',
           org_tubepress_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
           org_tubepress_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));

        $context = self::_buildContext(org_tubepress_api_provider_Provider::VIMEO, false, 1);

        self::assertTrue($this->urlMatches('method=vimeo.albums.getVideos&album_id=eric&full_response=true&page=1&per_page=20', $context));
    }

    function testexecuteChannel()
    {
        $this->_expectOptions(array(
           org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::VIMEO_CHANNEL,
           org_tubepress_api_const_options_names_Output::VIMEO_CHANNEL_VALUE => 'eric',
           org_tubepress_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
           org_tubepress_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));

        $context = self::_buildContext(org_tubepress_api_provider_Provider::VIMEO, false, 1);

        self::assertTrue($this->urlMatches('method=vimeo.channels.getVideos&channel_id=eric&full_response=true&page=1&per_page=20', $context));
    }


    function testexecuteSearch()
    {
        $this->_expectOptions(array(
           org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::VIMEO_SEARCH,
           org_tubepress_api_const_options_names_Output::VIMEO_SEARCH_VALUE => 'eric hough',
           org_tubepress_api_const_options_names_Display::ORDER_BY => 'relevance',
           org_tubepress_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
           org_tubepress_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret',
           org_tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER => '',
        ));

        $context = self::_buildContext(org_tubepress_api_provider_Provider::VIMEO, false, 1);

        self::assertTrue($this->urlMatches('method=vimeo.videos.search&query=eric\+hough&full_response=true&page=1&per_page=20&sort=relevant', $context));
    }

    function testexecuteSearchWithUser()
    {
        $this->_expectOptions(array(
           org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::VIMEO_SEARCH,
           org_tubepress_api_const_options_names_Output::VIMEO_SEARCH_VALUE => 'eric hough',
           org_tubepress_api_const_options_names_Display::ORDER_BY => 'relevance',
           org_tubepress_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
           org_tubepress_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret',
           org_tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER => 'ehough'
        ));

        $context = self::_buildContext(org_tubepress_api_provider_Provider::VIMEO, false, 1);

        self::assertTrue($this->urlMatches('method=vimeo.videos.search&query=eric\+hough&user_id=ehough&full_response=true&page=1&per_page=20&sort=relevant', $context));
    }

    function testexecuteAppearsIn()
    {
        $this->_expectOptions(array(
           org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::VIMEO_APPEARS_IN,
           org_tubepress_api_const_options_names_Output::VIMEO_APPEARS_IN_VALUE => 'eric',
           org_tubepress_api_const_options_names_Display::ORDER_BY => 'oldest',
           org_tubepress_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
           org_tubepress_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));

        $context = self::_buildContext(org_tubepress_api_provider_Provider::VIMEO, false, 1);

        self::assertTrue($this->urlMatches('method=vimeo.videos.getAppearsIn&user_id=eric&full_response=true&page=1&per_page=20&sort=oldest', $context));
    }

    function testexecuteLikes()
    {
        $this->_expectOptions(array(
           org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::VIMEO_LIKES,
           org_tubepress_api_const_options_names_Output::VIMEO_LIKES_VALUE => 'eric',
           org_tubepress_api_const_options_names_Display::ORDER_BY => 'rating',
           org_tubepress_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
           org_tubepress_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));

        $context = self::_buildContext(org_tubepress_api_provider_Provider::VIMEO, false, 1);

        self::assertTrue($this->urlMatches('method=vimeo.videos.getLikes&user_id=eric&full_response=true&page=1&per_page=20&sort=most_liked', $context));
    }

    function testexecuteUploadedBy()
    {
        $this->_expectOptions(array(
           org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::VIMEO_UPLOADEDBY,
           org_tubepress_api_const_options_names_Output::VIMEO_UPLOADEDBY_VALUE => 'eric',
           org_tubepress_api_const_options_names_Display::ORDER_BY => 'commentCount',
           org_tubepress_api_const_options_names_Feed::VIMEO_KEY => 'vimeokey',
           org_tubepress_api_const_options_names_Feed::VIMEO_SECRET => 'vimeosecret'
        ));

        $context = self::_buildContext(org_tubepress_api_provider_Provider::VIMEO, false, 1);

        self::assertTrue($this->urlMatches('method=vimeo.videos.getUploaded&user_id=eric&full_response=true&page=1&per_page=20&sort=most_commented', $context));
    }

    private function _expectOptions($opts)
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
        $execContext = $ioc->get('org_tubepress_api_exec_ExecutionContext');

        foreach ($opts as $name => $value) {
            $execContext->shouldReceive('get')->zeroOrMoreTimes()->with($name)->andReturn($value);
        }
    }

    private static function _buildContext($provider, $single, $arg)
    {
        $context = new stdClass();
        $context->arg = $arg;
        $context->single = $single;
        $context->providerName = $provider;
        return $context;
    }

    private function urlMatches($url, $context)
    {
        $status = $this->_sut->execute($context);
        self::assertTrue($status);

        $full = $context->returnValue;

        $pattern = self::PRE . $url . self::POST;
        $result = 1 === preg_match($pattern, $full);
        if (!$result) {
            echo "\n\n$full\n    does not match\n$pattern\n\n";
        }
        return $result;
    }
}


