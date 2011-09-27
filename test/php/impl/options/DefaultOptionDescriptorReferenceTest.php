<?php

require_once BASE . '/sys/classes/org/tubepress/impl/options/DefaultOptionDescriptorReference.class.php';

class org_tubepress_impl_options_DefaultOptionDescriptorReferenceTest extends TubePressUnitTest {

	private $_sut;

	public function setup()
	{
		parent::setUp();
		$this->_sut = new org_tubepress_impl_options_DefaultOptionDescriptorReference();
	}

	public function testFindAll()
	{
	    $all = $this->_sut->findAll();

	    $this->assertTrue(count($all) === 83);
	}

	/**
	 * @expectedException Exception
	 */
	public function testRegisterDuplicate()
	{
	    $od = \Mockery::mock(org_tubepress_api_options_OptionDescriptor::_);
	    $od->shouldReceive('getName')->atLeast()->once()->andReturn('name');

	    $this->_sut->register($od);
	    $this->_sut->register($od);
	}

	public function testOptions()
	{
    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Advanced::CACHE_CLEAN_FACTOR);
    	$this->assertTrue($option->getDefaultValue() === 20, $option->getName() . ' should have default value of 20');
    	$this->assertTrue($option->getLabel() === 'Cache cleaning factor', $option->getName());
    	$this->assertTrue($option->getDescription() === 'If you enter X, the entire cache will be cleaned every 1/X cache writes. Enter 0 to disable cache cleaning.', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName() . ' should not be pro only');

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Advanced::CACHE_DIR);
    	$this->assertTrue($option->getLabel() === 'Cache directory', $option->getName());
    	$this->assertTrue($option->getDescription() === 'Leave blank to attempt to use system temp directory. Otherwise enter the absolute path of a writeable directory.', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Advanced::CACHE_LIFETIME_SECONDS);
    	$this->assertTrue($option->getDefaultValue() === 3600, $option->getName());
    	$this->assertTrue($option->getLabel() === 'Cache expiration time (seconds)', $option->getName());
    	$this->assertTrue($option->getDescription() === 'Cache entries will be considered stale after the specified number of seconds. Default is 3600 (one hour).', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Advanced::DATEFORMAT);
    	$this->assertTrue($option->getDefaultValue() === 'M j, Y', $option->getName());
    	$this->assertTrue($option->getLabel() === 'Date format', $option->getName());
    	$this->assertTrue($option->getDescription() === 'Set the textual formatting of date information for videos. See <a href="http://us.php.net/date">date</a> for examples.', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Advanced::DEBUG_ON);
    	$this->assertTrue($option->getDefaultValue() === true, $option->getName());
    	$this->assertTrue($option->getLabel() === 'Enable debugging', $option->getName());
    	$this->assertTrue($option->getDescription() === 'If checked, anyone will be able to view your debugging information. This is a rather small privacy risk. If you\'re not having problems with TubePress, or you\'re worried about revealing any details of your TubePress pages, feel free to disable the feature.', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_CURL);
    	$this->assertTrue($option->getLabel() === 'Disable <a href="http://php.net/manual/en/function.curl-exec.php">cURL</a> HTTP transport', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Do not attempt to use cURL to fetch remote feeds. Leave enabled unless you know what you are doing.', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_EXTHTTP);
    	$this->assertTrue($option->getLabel() === 'Disable <a href="http://php.net/http_request">HTTP extension</a> transport', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Do not attempt to use the PHP HTTP extension to fetch remote feeds. Leave enabled unless you know what you are doing.', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_FOPEN);
    	$this->assertTrue($option->getLabel() === 'Disable <a href="http://php.net/manual/en/function.fopen.php">fopen</a> HTTP transport', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Do not attempt to use fopen to fetch remote feeds. Leave enabled unless you know what you are doing.', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_FSOCKOPEN);
    	$this->assertTrue($option->getLabel() === 'Disable <a href="http://php.net/fsockopen">fsockopen</a> HTTP transport', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Do not attempt to use fsockopen to fetch remote feeds. Leave enabled unless you know what you are doing.', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_STREAMS);
    	$this->assertTrue($option->getLabel() === 'Disable <a href="http://php.net/manual/en/intro.stream.php">PHP streams</a> HTTP transport', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Do not attempt to use PHP streams to fetch remote feeds. Leave enabled unless you know what you are doing.', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Advanced::GALLERY_ID);
    	$this->assertTrue($option->getValidValueRegex() === '/\w+/', $option->getName());
    	$this->assertTrue($option->isMeantToBePersisted() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Advanced::KEYWORD);
    	$this->assertTrue($option->getLabel() === 'Shortcode keyword', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === 'tubepress', $option->getName());
    	$this->assertTrue($option->getDescription() === 'The word you insert (in plaintext, between square brackets) into your posts/pages to display a gallery.', $option->getName());
    	$this->assertTrue($option->isAbleToBeSetViaShortcode() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Advanced::VIDEO_BLACKLIST);
    	$this->assertTrue($option->getLabel() === 'Videos blacklist', $option->getName());
    	$this->assertTrue($option->getDescription() === 'List of video IDs that should never be displayed', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Display::AJAX_PAGINATION);
    	$this->assertTrue($option->getLabel() === '<a href="http://wikipedia.org/wiki/Ajax_(programming)">Ajax</a>-enabled pagination', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === true, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Display::CURRENT_PLAYER_NAME);
    	$this->assertTrue($option->getLabel() === 'Play each video', $option->getName() . ' has incorrect label');
    	$this->assertTrue($option->getDefaultValue() === org_tubepress_api_const_options_values_PlayerValue::NORMAL, $option->getName() . ' has incorrect default value');
    	$this->assertTrue($option->getAcceptableValues() === array(
        	org_tubepress_api_const_options_values_PlayerValue::NORMAL    => 'normally (at the top of your gallery)',
        	org_tubepress_api_const_options_values_PlayerValue::POPUP     => 'in a popup window',
        	org_tubepress_api_const_options_values_PlayerValue::YOUTUBE   => 'from the video\'s original YouTube page',
        	org_tubepress_api_const_options_values_PlayerValue::VIMEO     => 'from the video\'s original Vimeo page',
        	org_tubepress_api_const_options_values_PlayerValue::SHADOWBOX => 'with Shadowbox',
        	org_tubepress_api_const_options_values_PlayerValue::JQMODAL   => 'with jqModal',
        	org_tubepress_api_const_options_values_PlayerValue::TINYBOX   => 'with TinyBox',
        	org_tubepress_api_const_options_values_PlayerValue::FANCYBOX  => 'with FancyBox',
        	org_tubepress_api_const_options_values_PlayerValue::STATICC   => 'statically (page refreshes on each thumbnail click)',
        	org_tubepress_api_const_options_values_PlayerValue::SOLO      => 'in a new window on its own',
        	org_tubepress_api_const_options_values_PlayerValue::DETACHED  => 'in a "detached" location (see the documentation)'
    	), $option->getName() . ' has incorrect value map');
    	$this->assertTrue($option->isProOnly() === false, $option->getName() . ' should not be pro only');

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Display::DESC_LIMIT);
    	$this->assertTrue($option->getLabel() === 'Maximum description length', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === 80, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Maximum number of characters to display in video descriptions. Set to 0 for no limit.', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Display::FLUID_THUMBS);
    	$this->assertTrue($option->getLabel() === 'Use "fluid" thumbnails', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === true, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Dynamically set thumbnail spacing based on the width of their container.', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Display::HQ_THUMBS);
    	$this->assertTrue($option->getLabel() === 'Use high-quality thumbnails', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Note: this option cannot be used with the "randomize thumbnails" feature', $option->getName());
    	$this->assertTrue($option->isProOnly() === true, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Display::ORDER_BY);
    	$this->assertTrue($option->getLabel() === 'Order videos by', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === org_tubepress_api_const_options_values_OrderValue::VIEW_COUNT, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Not all sort orders can be applied to all gallery types. See the <a href="http://tubepress.org/documentation">documentation</a> for more info.', $option->getName());
    	$this->assertTrue($option->getAcceptableValues() === array(
        	org_tubepress_api_const_options_values_OrderValue::RELEVANCE      => 'relevance',
        	org_tubepress_api_const_options_values_OrderValue::VIEW_COUNT     => 'view count',
        	org_tubepress_api_const_options_values_OrderValue::RATING         => 'rating',
        	org_tubepress_api_const_options_values_OrderValue::DATE_PUBLISHED => 'date published',
        	org_tubepress_api_const_options_values_OrderValue::RANDOM         => 'randomly',
        	org_tubepress_api_const_options_values_OrderValue::POSITION       => 'position in a playlist',
        	org_tubepress_api_const_options_values_OrderValue::COMMENT_COUNT  => 'comment count',
        	org_tubepress_api_const_options_values_OrderValue::DURATION       => 'length',
        	org_tubepress_api_const_options_values_OrderValue::TITLE          => 'title',
        	org_tubepress_api_const_options_values_OrderValue::NEWEST         => 'newest',
        	org_tubepress_api_const_options_values_OrderValue::OLDEST         => 'oldest',
    	), $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Display::PAGINATE_ABOVE);
    	$this->assertTrue($option->getLabel() === 'Show pagination above thumbnails', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === true, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Only applies to galleries that span multiple pages', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Display::PAGINATE_BELOW);
    	$this->assertTrue($option->getLabel() === 'Show pagination below thumbnails', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === true, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Only applies to galleries that span multiple pages', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Display::RANDOM_THUMBS);
    	$this->assertTrue($option->getLabel() === 'Randomize thumbnails', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === true, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Most videos come with several thumbnails. By selecting this option, each time someone views your gallery they will see the same videos with each video\'s thumbnail randomized. Note: this option cannot be used with the "high quality thumbnails" feature', $option->getName());
    	$this->assertTrue($option->isApplicableToVimeo() === false, $option->getName() . ' should not be applicable to vimeo');
    	$this->assertTrue($option->isProOnly() === false, $option->getName() . ' should be set to pro only');

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Display::RELATIVE_DATES);
    	$this->assertTrue($option->getLabel() === 'Use relative dates', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->getDescription() === 'e.g. "yesterday" instead of "November 3, 1980"', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Display::RESULTS_PER_PAGE);
    	$this->assertTrue($option->getLabel() === 'Videos per Page', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === 20, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Default is 20. Maximum is 50', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Display::THEME);
    	$this->assertTrue($option->getLabel() === 'Theme', $option->getName());
    	$this->assertTrue($option->getDescription() === 'The TubePress theme to use for this gallery. Your themes can be found at <tt>%s</tt>, and default themes can be found at <tt>%s</tt>.', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Display::THUMB_HEIGHT);
    	$this->assertTrue($option->getLabel() === 'Height (px) of thumbs', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === 90, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Default is 90', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Display::THUMB_WIDTH);
    	$this->assertTrue($option->getLabel() === 'Width (px) of thumbs', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === 120, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Default is 120', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Embedded::AUTOPLAY);
    	$this->assertTrue($option->getLabel() === 'Auto-play all videos', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT);
    	$this->assertTrue($option->getLabel() === 'Max height (px)', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === 350, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Default is 350', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH);
    	$this->assertTrue($option->getLabel() === 'Max width (px)', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === 425, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Default is 425', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Embedded::FULLSCREEN);
    	$this->assertTrue($option->getLabel() === 'Allow fullscreen playback', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === true, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Embedded::HIGH_QUALITY);
    	$this->assertTrue($option->getLabel() === 'Show videos in high definition when available', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->isApplicableToVimeo() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Embedded::LAZYPLAY);
    	$this->assertTrue($option->getLabel() === '"Lazy" play videos', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === true, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Auto-play each video after thumbnail click', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Embedded::LOOP);
    	$this->assertTrue($option->getLabel() === 'Loop', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Continue playing the video until the user stops it', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Embedded::PLAYER_COLOR);
    	$this->assertTrue($option->getLabel() === 'Main color', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === '999999', $option->getName());
    	$this->assertTrue($option->getDescription() === 'Default is 999999', $option->getName());
    	$this->assertTrue($option->getValidValueRegex() === '/^([0-9a-f]{1,2}){3}$/i', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Embedded::PLAYER_HIGHLIGHT);
    	$this->assertTrue($option->getLabel() === 'Highlight color', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === 'FFFFFF', $option->getName());
    	$this->assertTrue($option->getDescription() === 'Default is FFFFFF', $option->getName());
    	$this->assertTrue($option->getValidValueRegex() === '/^([0-9a-f]{1,2}){3}$/i', $option->getName());
    	$this->assertTrue($option->isApplicableToVimeo() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Embedded::PLAYER_IMPL);
    	$this->assertTrue($option->getLabel() === 'Implementation', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === org_tubepress_api_const_options_values_PlayerImplementationValue::PROVIDER_BASED, $option->getName());
    	$this->assertTrue($option->getDescription() === 'The brand of the embedded player. Default is the provider\'s player (YouTube, Vimeo, etc)', $option->getName());
    	$this->assertTrue($option->isApplicableToVimeo() === false, $option->getName());
    	$this->assertTrue($option->getAcceptableValues() === array(
    	org_tubepress_api_const_options_values_PlayerImplementationValue::EMBEDPLUS      => 'EmbedPlus',
    	org_tubepress_api_const_options_values_PlayerImplementationValue::LONGTAIL       => 'JW FLV Media Player (by Longtail Video)',
    	org_tubepress_api_const_options_values_PlayerImplementationValue::PROVIDER_BASED => 'Provider default',
    	), $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Embedded::SHOW_INFO);
    	$this->assertTrue($option->getLabel() === 'Show title and rating before video starts', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Embedded::SHOW_RELATED);
    	$this->assertTrue($option->getLabel() === 'Show related videos', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === true, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Toggles the display of related videos after a video finishes', $option->getName());
    	$this->assertTrue($option->isApplicableToVimeo() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Feed::CACHE_ENABLED);
    	$this->assertTrue($option->getLabel() === 'Enable request cache', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Store network responses locally for 1 hour. Each response is on the order of a few hundred KB, so leaving the cache enabled will significantly reduce load times for your galleries at the slight expense of freshness.', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Feed::DEV_KEY);
    	$this->assertTrue($option->getLabel() === 'YouTube API Developer Key', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === 'AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg', $option->getName());
    	$this->assertTrue($option->getDescription() === 'YouTube will use this developer key for logging and debugging purposes if you experience a service problem on their end. You can register a new client ID and developer key <a href="http://code.google.com/apis/youtube/dashboard/">here</a>. Don\'t change this unless you know what you\'re doing.', $option->getName());
    	$this->assertTrue($option->isApplicableToVimeo() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Feed::EMBEDDABLE_ONLY);
    	$this->assertTrue($option->getLabel() === 'Only retrieve embeddable videos', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === true, $option->getName());
    	$this->assertTrue($option->getDescription() === 'Some videos have embedding disabled. Checking this option will exclude these videos from your galleries.', $option->getName());
    	$this->assertTrue($option->isApplicableToVimeo() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Feed::FILTER);
    	$this->assertTrue($option->getLabel() === 'Filter "racy" content', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === org_tubepress_api_const_options_values_SafeSearchValue::MODERATE, $option->getName());
    	$this->assertTrue($option->isApplicableToVimeo() === false, $option->getName());
    	$this->assertTrue($option->getAcceptableValues() === array(
        	org_tubepress_api_const_options_values_SafeSearchValue::NONE     => 'none',
        	org_tubepress_api_const_options_values_SafeSearchValue::MODERATE => 'moderate',
        	org_tubepress_api_const_options_values_SafeSearchValue::STRICT   => 'strict',
    	), $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP);
    	$this->assertTrue($option->getLabel() === 'Maximum total videos to retrieve', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === 300, $option->getName());
    	$this->assertTrue($option->getDescription() === 'This can help to reduce the number of pages in your gallery. Set to "0" to remove any limit.', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER);
    	$this->assertTrue($option->getLabel() === 'Restrict search results to videos from this user', $option->getName());
    	$this->assertTrue($option->getDescription() === 'Only applies to search-based galleries', $option->getName());
    	$this->assertTrue($option->getValidValueRegex() === '/\w+/', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Feed::VIMEO_KEY);
    	$this->assertTrue($option->getLabel() === 'Vimeo API "Consumer Key"', $option->getName());
    	$this->assertTrue($option->getDescription() === '<a href="http://vimeo.com/api/applications/new">Click here</a> to register for a consumer key and secret.', $option->getName());
    	$this->assertTrue($option->isApplicableToYouTube() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Feed::VIMEO_SECRET);
    	$this->assertTrue($option->getLabel() === 'Vimeo API "Consumer Secret"', $option->getName());
    	$this->assertTrue($option->getDescription() === '<a href="http://vimeo.com/api/applications/new">Click here</a> to register for a consumer key and secret.', $option->getName());
    	$this->assertTrue($option->isApplicableToYouTube() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Meta::AUTHOR);
    	$this->assertTrue($option->getLabel() === 'Author', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Meta::CATEGORY);
    	$this->assertTrue($option->getLabel() === 'Category', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Meta::DESCRIPTION);
    	$this->assertTrue($option->getLabel() === 'Description', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Meta::ID);
    	$this->assertTrue($option->getLabel() === 'ID', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Meta::LENGTH);
    	$this->assertTrue($option->getLabel() === 'Runtime', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === true, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Meta::LIKES);
    	$this->assertTrue($option->getLabel() === 'Likes', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->isApplicableToYouTube() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Meta::RATING);
    	$this->assertTrue($option->getLabel() === 'Rating', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->isApplicableToVimeo() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Meta::RATINGS);
    	$this->assertTrue($option->getLabel() === 'Ratings', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->isApplicableToVimeo() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Meta::TAGS);
    	$this->assertTrue($option->getLabel() === 'Keywords', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Meta::TITLE);
    	$this->assertTrue($option->getLabel() === 'Title', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === true, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Meta::UPLOADED);
    	$this->assertTrue($option->getLabel() === 'Posted', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Meta::URL);
    	$this->assertTrue($option->getLabel() === 'URL', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Meta::VIEWS);
    	$this->assertTrue($option->getLabel() === 'Views', $option->getName());
    	$this->assertTrue($option->getDefaultValue() === true, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Output::FAVORITES_VALUE);
    	$this->assertTrue($option->getDefaultValue() === 'mrdeathgod', $option->getName());
    	$this->assertTrue($option->isApplicableToVimeo() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Output::MODE);
    	$this->assertTrue($option->getDefaultValue() === org_tubepress_api_const_options_values_ModeValue::FEATURED, $option->getName());
    	$this->assertTrue($option->getAcceptableValues() === array(
        	org_tubepress_api_const_options_values_ModeValue::FAVORITES        => 'This YouTube user\'s "favorites"',
        	org_tubepress_api_const_options_values_ModeValue::FEATURED         => 'The latest "featured" videos on YouTube\'s homepage',
        	org_tubepress_api_const_options_values_ModeValue::MOST_DISCUSSED   => 'Most-discussed videos',
        	org_tubepress_api_const_options_values_ModeValue::MOST_RECENT      => 'Most-recently added videos',
        	org_tubepress_api_const_options_values_ModeValue::MOST_RESPONDED   => 'Most-responded to videos',
        	org_tubepress_api_const_options_values_ModeValue::PLAYLIST         => 'This playlist',
        	org_tubepress_api_const_options_values_ModeValue::POPULAR          => 'Most-viewed videos from',
        	org_tubepress_api_const_options_values_ModeValue::TAG              => 'YouTube search for...',
        	org_tubepress_api_const_options_values_ModeValue::TOP_FAVORITES    => 'Videos most frequently flagged as favorites from...',
        	org_tubepress_api_const_options_values_ModeValue::TOP_RATED        => 'Top rated videos from...',
        	org_tubepress_api_const_options_values_ModeValue::USER             => 'Videos from this YouTube user',
        	org_tubepress_api_const_options_values_ModeValue::VIMEO_ALBUM      => 'Videos from this Vimeo album',
        	org_tubepress_api_const_options_values_ModeValue::VIMEO_APPEARS_IN => 'Videos this Vimeo user appears in',
        	org_tubepress_api_const_options_values_ModeValue::VIMEO_CHANNEL    => 'Videos in this Vimeo channel',
        	org_tubepress_api_const_options_values_ModeValue::VIMEO_CREDITED   => 'Videos credited to this Vimeo user (either appears in or uploaded by)',
        	org_tubepress_api_const_options_values_ModeValue::VIMEO_GROUP      => 'Videos from this Vimeo group',
        	org_tubepress_api_const_options_values_ModeValue::VIMEO_LIKES      => 'Videos this Vimeo user likes',
        	org_tubepress_api_const_options_values_ModeValue::VIMEO_SEARCH     => 'Vimeo search for...',
        	org_tubepress_api_const_options_values_ModeValue::VIMEO_UPLOADEDBY => 'Videos uploaded by this Vimeo user'
    	), $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Output::MOST_VIEWED_VALUE);
    	$this->assertTrue($option->getDefaultValue() === org_tubepress_api_const_options_values_TimeFrameValue::TODAY, $option->getName());
    	$this->assertTrue($option->isApplicableToVimeo() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Output::OUTPUT);
    	$this->assertTrue($option->isMeantToBePersisted() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Output::PLAYLIST_VALUE);
    	$this->assertTrue($option->getDefaultValue() === 'D2B04665B213AE35', $option->getName());
    	$this->assertTrue($option->getDescription() === 'Limited to 200 videos per playlist. Will usually look something like this: D2B04665B213AE35. Copy the playlist id from the end of the URL in your browser\'s address bar (while looking at a YouTube playlist). It comes right after the "p=". For instance: http://youtube.com/my_playlists?p=D2B04665B213AE35', $option->getName());
    	$this->assertTrue($option->isApplicableToVimeo() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Output::SEARCH_PROVIDER);
    	$this->assertTrue($option->isProOnly() === false, $option->getName());
    	$this->assertTrue($option->getAcceptableValues() === array(
            org_tubepress_api_provider_Provider::YOUTUBE,
            org_tubepress_api_provider_Provider::VIMEO,
        ));

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Output::SEARCH_RESULTS_DOM_ID);
    	$this->assertTrue($option->isProOnly() === true, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Output::SEARCH_RESULTS_ONLY);
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Output::SEARCH_RESULTS_URL);
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Output::TAG_VALUE);
    	$this->assertTrue($option->getDefaultValue() === 'pittsburgh steelers', $option->getName());
    	$this->assertTrue($option->getDescription() === 'YouTube limits this mode to 1,000 results', $option->getName());
    	$this->assertTrue($option->isApplicableToVimeo() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Output::TOP_RATED_VALUE);
    	$this->assertTrue($option->getDefaultValue() === org_tubepress_api_const_options_values_TimeFrameValue::TODAY, $option->getName());
    	$this->assertTrue($option->isApplicableToVimeo() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Output::TOP_FAVORITES_VALUE);
    	$this->assertTrue($option->getDefaultValue() === org_tubepress_api_const_options_values_TimeFrameValue::TODAY, $option->getName());
    	$this->assertTrue($option->isApplicableToVimeo() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Output::USER_VALUE);
    	$this->assertTrue($option->getDefaultValue() === '3hough', $option->getName());
    	$this->assertTrue($option->isApplicableToVimeo() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Output::VIMEO_UPLOADEDBY_VALUE);
    	$this->assertTrue($option->getDefaultValue() === 'mattkaar', $option->getName());
    	$this->assertTrue($option->isApplicableToYouTube() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Output::VIMEO_LIKES_VALUE);
    	$this->assertTrue($option->getDefaultValue() === 'coiffier', $option->getName());
    	$this->assertTrue($option->isApplicableToYouTube() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Output::VIMEO_APPEARS_IN_VALUE);
    	$this->assertTrue($option->getDefaultValue() === 'royksopp', $option->getName());
    	$this->assertTrue($option->isApplicableToYouTube() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Output::VIMEO_SEARCH_VALUE);
    	$this->assertTrue($option->getDefaultValue() === 'cats playing piano', $option->getName());
    	$this->assertTrue($option->isApplicableToYouTube() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Output::VIMEO_CREDITED_VALUE);
    	$this->assertTrue($option->getDefaultValue() === 'patricklawler', $option->getName());
    	$this->assertTrue($option->isApplicableToYouTube() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Output::VIMEO_CHANNEL_VALUE);
    	$this->assertTrue($option->getDefaultValue() === 'splitscreenstuff', $option->getName());
    	$this->assertTrue($option->isApplicableToYouTube() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Output::VIMEO_GROUP_VALUE);
    	$this->assertTrue($option->getDefaultValue() === 'hdxs', $option->getName());
    	$this->assertTrue($option->isApplicableToYouTube() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Output::VIMEO_ALBUM_VALUE);
    	$this->assertTrue($option->getDefaultValue() === '140484', $option->getName());
    	$this->assertTrue($option->isApplicableToYouTube() === false, $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Widget::TITLE);
    	$this->assertTrue($option->getDefaultValue() === 'TubePress', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());

    	$option = $this->_sut->findOneByName(org_tubepress_api_const_options_names_Widget::TAGSTRING);
    	$this->assertTrue($option->getDefaultValue() === '[tubepress thumbHeight=\'105\' thumbWidth=\'135\']', $option->getName());
    	$this->assertTrue($option->isProOnly() === false, $option->getName());
	}
}

