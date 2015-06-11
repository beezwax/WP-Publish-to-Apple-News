<?php

require_once __DIR__ . '/../../includes/push-api/class-api.php';

use \Push_API\API as API;

class API_Test extends WP_UnitTestCase {

	public function setup() {
		$this->key        = getenv( 'WP_PLUGIN_KEY' );
		$this->secret     = getenv( 'WP_PLUGIN_SECRET' );
		$this->channel_id = getenv( 'WP_PLUGIN_CHANNEL' );
		$this->endpoint   = 'https://u48r14.digitalhub.com';
	}

	public function testPostSimpleArticle() {
		$api = new API( $this->endpoint, $this->key, $this->secret, false );

		$article = '{"version":"0.1","identifier":"post-1","language":"en","title":"\u00a1Hola mundo!","components":[{"role":"intro","text":"\u00a1Hola mundo!"},{"role":"body","text":"Bienvenido a WordPress. Esta es tu primera entrada. Ed\u00c3\u00adtala o b\u00c3\u00b3rrala, \u00c2\u00a1y comienza a publicar!."},{"role":"body","text":"Now this is the second paragraph. And it\u2019s in english!"}],"layout":{"columns":7,"width":1024,"margin":30,"gutter":20},"documentStyle":{"backgroundColor":"#F7F7F7"},"componentTextStyles":{"default":{"fontName":"Helvetica","fontSize":13,"linkStyle":{"textColor":"#428bca"}},"title":{"fontName":"Helvetica-Bold","fontSize":30,"hyphenation":false},"default-body":{"fontName":"Helvetica","fontSize":13}},"componentLayouts":{"headerContainerLayout":{"columnStart":0,"columnSpan":7,"ignoreDocumentMargin":true,"minimumHeight":"50vh"}}}';

		$this->assertNotNull( $api->post_article_to_channel( $article, $this->channel_id ) );
	}

}

