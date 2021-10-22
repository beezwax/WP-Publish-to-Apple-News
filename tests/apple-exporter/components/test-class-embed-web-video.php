<?php
/**
 * Publish to Apple News tests: Embed_Web_Video_Test class
 *
 * @package Apple_News
 * @subpackage Tests
 */

/**
 * A class to test the behavior of the
 * Apple_Exporter\Components\Embed_Web_Video class.
 *
 * @package Apple_News
 * @subpackage Tests
 */
class Embed_Web_Video_Test extends Apple_News_Testcase {

	/**
	 * A data provider for the test_transform function.
	 *
	 * @return array An array of arrays representing function arguments.
	 */
	public function data_transform() {
		return [
			'Vimeo standard watch URL'           => [
				'vimeo',
				'https://vimeo.com/12819723',
				'https://player.vimeo.com/video/12819723',
			],
			'Vimeo standard watch URL, no https' => [
				'vimeo',
				'http://vimeo.com/12819723',
				'https://player.vimeo.com/video/12819723',
			],
			'YouTube standard watch URL'                   => [
				'youtube',
				'https://www.youtube.com/watch?v=0qwALOOvUik',
				'https://www.youtube.com/embed/0qwALOOvUik',
			],
			'YouTube embed URL'                            => [
				'youtube',
				'https://www.youtube.com/embed/0qwALOOvUik',
				'https://www.youtube.com/embed/0qwALOOvUik',
			],
			'YouTube standard watch URL, no www'           => [
				'youtube',
				'https://youtube.com/watch?v=0qwALOOvUik',
				'https://www.youtube.com/embed/0qwALOOvUik',
			],
			'YouTube embed URL, no www'                    => [
				'youtube',
				'https://youtube.com/embed/0qwALOOvUik',
				'https://www.youtube.com/embed/0qwALOOvUik',
			],
			'YouTube standard watch URL, no https'         => [
				'youtube',
				'http://www.youtube.com/watch?v=0qwALOOvUik',
				'https://www.youtube.com/embed/0qwALOOvUik',
			],
			'YouTube embed URL, no https'                  => [
				'youtube',
				'http://www.youtube.com/embed/0qwALOOvUik',
				'https://www.youtube.com/embed/0qwALOOvUik',
			],
			'YouTube standard watch URL, no https, no www' => [
				'youtube',
				'http://youtube.com/watch?v=0qwALOOvUik',
				'https://www.youtube.com/embed/0qwALOOvUik',
			],
			'YouTube embed URL, no https, no www'          => [
				'youtube',
				'http://youtube.com/embed/0qwALOOvUik',
				'https://www.youtube.com/embed/0qwALOOvUik',
			],
			'YouTube shortlink'                            => [
				'youtube',
				'https://youtu.be/0qwALOOvUik',
				'https://www.youtube.com/embed/0qwALOOvUik',
			],
			'YouTube shortlink, no https'                  => [
				'youtube',
				'http://youtu.be/0qwALOOvUik',
				'https://www.youtube.com/embed/0qwALOOvUik',
			],
		];
	}

	/**
	 * Tests transforming an embed to an Embed Web Video component.
	 *
	 * @dataProvider data_transform
	 *
	 * @param string $provider  The provider. One of 'youtube' or 'vimeo'.
	 * @param string $embed_url The user-entered oEmbed URL.
	 * @param string $expected  The expected embed URL that is rendered in the component.
	 */
	public function test_transform( $provider, $embed_url, $expected ) {
		// Test in Gutenberg.
		$post_content = <<<HTML
<!-- wp:embed {"url":"{$embed_url}","type":"video","providerNameSlug":"{$provider}","responsive":true,"className":"wp-embed-aspect-16-9 wp-has-aspect-ratio"} -->
<figure class="wp-block-embed is-type-video is-provider-{$provider} wp-block-embed-{$provider} wp-embed-aspect-16-9 wp-has-aspect-ratio"><div class="wp-block-embed__wrapper">
		{$embed_url}
</div></figure>
<!-- /wp:embed -->
HTML;
		$post_id      = self::factory()->post->create( [ 'post_content' => $post_content ] );
		$json         = $this->get_json_for_post( $post_id );
		$this->assertEquals( $expected, $json['components'][3]['URL'] );

		// Test in the classic editor.
		$post_content = <<<HTML
<p>{$embed_url}</p>
HTML;
		$post_id      = self::factory()->post->create( [ 'post_content' => $post_content ] );
		$json         = $this->get_json_for_post( $post_id );
		$this->assertEquals( $expected, $json['components'][3]['URL'] );
	}

	/**
	 * Tests the transformation of an iframe containing a Vimeo embed into an
	 * EmbedWebVideo component.
	 */
	public function test_transform_vimeo_iframe() {
		$this->become_admin();
		$post_content = <<<HTML
<iframe title="vimeo-player" src="https://player.vimeo.com/video/12819723?h=eafba1f705" width="640" height="360" frameborder="0" allowfullscreen></iframe>
HTML;
		$post_id      = self::factory()->post->create( [ 'post_content' => $post_content ] );
		$json         = $this->get_json_for_post( $post_id );
		$this->assertEquals( 'https://player.vimeo.com/video/12819723', $json['components'][3]['URL'] );
	}

	/**
	 * Tests the transformation of an iframe containing a YouTube embed into an
	 * EmbedWebVideo component.
	 */
	public function test_transform_youtube_iframe() {
		$this->become_admin();
		$post_content = <<<HTML
<iframe width="560" height="315" src="https://www.youtube.com/embed/0qwALOOvUik" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
HTML;
		$post_id      = self::factory()->post->create( [ 'post_content' => $post_content ] );
		$json         = $this->get_json_for_post( $post_id );
		$this->assertEquals( 'https://www.youtube.com/embed/0qwALOOvUik', $json['components'][3]['URL'] );
	}

	// TODO: REFACTOR LINE

	/**
	 * A filter function to modify the aspect ratio.
	 *
	 * @param array $json An array representing JSON for the component.
	 *
	 * @access public
	 * @return array The modified JSON.
	 */
	public function filter_apple_news_embed_web_video_json( $json ) {
		$json['aspectRatio'] = '1.4';

		return $json;
	}

	/**
	 * Test the `apple_news_embed_web_video_json` filter.
	 *
	 * @access public
	 */
	public function testFilter() {

		// Setup.
		add_filter(
			'apple_news_embed_web_video_json',
			array( $this, 'filter_apple_news_embed_web_video_json' )
		);
		$component = new Embed_Web_Video(
			'<p>https://vimeo.com/12819723</p>',
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);

		// Test.
		$this->assertEquals(
			array(
				'role'        => 'embedwebvideo',
				'URL'         => 'https://player.vimeo.com/video/12819723',
				'aspectRatio' => '1.4',
				'layout'      => 'embed-web-video-layout',
			),
			$component->to_array()
		);

		// Teardown.
		remove_filter(
			'apple_news_embed_web_video_json',
			array( $this, 'filter_apple_news_embed_web_video_json' )
		);
	}

	/**
	 * Tests the transformation process from a web video URL to an
	 * Embed_Web_Video component.
	 *
	 * Tests a variety of URL formats to ensure that they produce the
	 * proper output JSON using the dataProvider referenced below.
	 *
	 * @dataProvider dataTransformEmbedWebVideo
	 *
	 * @param string $html The HTML to be matched by the parser.
	 * @param string $final_url The final URL used in the JSON.
	 *
	 * @access public
	 */
	public function testTransformEmbedWebVideo( $html, $final_url ) {

		// Setup.
		$component = new Embed_Web_Video(
			$html,
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);

		// Test.
		$this->assertEquals(
			array(
				'role'        => 'embedwebvideo',
				'URL'         => $final_url,
				'aspectRatio' => '1.777',
				'layout'      => 'embed-web-video-layout'
			),
			$component->to_array()
		);
	}

	/**
	 * Tests an unsupported video provider.
	 *
	 * @access public
	 */
	public function testTransformUnsupportedProvider() {

		// Setup.
		$component = new Embed_Web_Video(
			'<iframe src="//player.notvimeo.com/video/12819723" width="560" height="315" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>',
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts
		);

		// Test.
		$this->assertNull( $component->to_array() );
	}
}
