<?php

require_once __DIR__ . '/../../includes/push-api/class-api.php';
require_once __DIR__ . '/../../includes/push-api/class-credentials.php';

use \Push_API\API as API;
use \Push_API\Credentials as Credentials;

class API_Test extends WP_UnitTestCase {

	public function setup() {
		// Whether or not to set requests to debug mode, enabling the use or
		// reverse proxies such as Charles.
		$debug_mode = false;

		$key        = getenv( 'WP_PLUGIN_KEY' );
		$secret     = getenv( 'WP_PLUGIN_SECRET' );
		$endpoint   = 'https://u48r14.digitalhub.com';
		$this->channel_id = getenv( 'WP_PLUGIN_CHANNEL' );

		$credentials = new Credentials( $key, $secret );
		$this->api   = new API( $endpoint, $credentials, $debug_mode );
	}

	public function testPostSimpleArticle() {
		$article = '{"version":"0.10","identifier":"post-1","language":"en","title":"\u00a1Hola mundo!","components":[{"role":"intro","text":"\u00a1Hola mundo!"},{"role":"body","text":"Bienvenido a WordPress. Esta es tu primera entrada. Ed\u00c3\u00adtala o b\u00c3\u00b3rrala, \u00c2\u00a1y comienza a publicar!."},{"role":"body","text":"Now this is the second paragraph. And it\u2019s in english!"}],"layout":{"columns":7,"width":1024,"margin":30,"gutter":20},"documentStyle":{"backgroundColor":"#F7F7F7"},"componentTextStyles":{"default":{"fontName":"Helvetica","fontSize":13,"linkStyle":{"textColor":"#428bca"}},"title":{"fontName":"Helvetica-Bold","fontSize":30,"hyphenation":false},"default-body":{"fontName":"Helvetica","fontSize":13}},"componentLayouts":{"headerContainerLayout":{"columnStart":0,"columnSpan":7,"ignoreDocumentMargin":true,"minimumHeight":"50vh"}}}';

		$this->assertNotNull( $this->api->post_article_to_channel( $article, $this->channel_id ) );

		// Test article with invalid json
		$this->setExpectedException( 'Push_API\\Request\\Request_Exception', 'INVALID_DOCUMENT' );
		$this->api->post_article_to_channel( '{invalid json}', $this->channel_id );
	}

	public function testPostWithImages() {
		$article = file_get_contents( __DIR__ . '/resources/article.json' );
		$files = array(
			realpath( __DIR__ . '/resources/367f66381fd0be912e6d1744135e528b.jpg' ),
			realpath( __DIR__ . '/resources/54dd603249541ae4dc6356aeb186e47b.jpg' ),
		);
		$this->assertNotNull( $this->api->post_article_to_channel( $article, $this->channel_id, $files ) );

		// Test article with no files
		$this->setExpectedException( 'Push_API\\Request\\Request_Exception', 'INVALID_DOCUMENT' );
		$this->api->post_article_to_channel( $article, $this->channel_id );
	}

	public function testGetChannelInfo() {
		$info = $this->api->get_channel( $this->channel_id );
		$this->assertEquals( $this->channel_id, $info->data->id );
	}

	public function testGetSections() {
		$sections = $this->api->get_sections( $this->channel_id );
		$this->assertTrue( count( $sections->data ) > 0 );

		// Test for an invalid channel ID
		$this->setExpectedException( 'Push_API\\Request\\Request_Exception', 'INVALID_TYPE' );
		$fetched_section = $this->api->get_sections( 'some-invalid-id' );
	}

	public function testGetSection() {
		$all_sections    = $this->api->get_sections( $this->channel_id );
		$first_section   = $all_sections->data[0];
		$fetched_section = $this->api->get_section( $first_section->id )->data;

		$this->assertEquals(
			$first_section->id,
			$fetched_section->id
		);

		$this->assertEquals(
			$first_section->name,
			$fetched_section->name
		);

		// Test for an invalid channel ID
		$this->setExpectedException( 'Push_API\\Request\\Request_Exception', 'INVALID_TYPE' );
		$fetched_section = $this->api->get_section( 'some-invalid-id' );
	}

	public function testUpdateArticle() {
		$article = '{"version":"0.10","identifier":"post-1","language":"en","title":"\u00a1Hola mundo!","components":[{"role":"intro","text":"\u00a1Hola mundo!"},{"role":"body","text":"Bienvenido a WordPress. Esta es tu primera entrada. Ed\u00c3\u00adtala o b\u00c3\u00b3rrala, \u00c2\u00a1y comienza a publicar!."},{"role":"body","text":"Now this is the second paragraph. And it\u2019s in english!"}],"layout":{"columns":7,"width":1024,"margin":30,"gutter":20},"documentStyle":{"backgroundColor":"#F7F7F7"},"componentTextStyles":{"default":{"fontName":"Helvetica","fontSize":13,"linkStyle":{"textColor":"#428bca"}},"title":{"fontName":"Helvetica-Bold","fontSize":30,"hyphenation":false},"default-body":{"fontName":"Helvetica","fontSize":13}},"componentLayouts":{"headerContainerLayout":{"columnStart":0,"columnSpan":7,"ignoreDocumentMargin":true,"minimumHeight":"50vh"}}}';
		$result = $this->api->post_article_to_channel( $article, $this->channel_id );

		$updated = $this->api->update_article( $result->data->id, $result->data->revision, $article );
		$this->assertEquals( $result->data->id, $updated->data->id );
	}

}

