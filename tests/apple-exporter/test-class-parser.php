<?php

use Apple_Exporter\Parser as Parser;

class Parser_Test extends WP_UnitTestCase {

	public function testCleanInvalidLinks() {

	}

	public function testParseMarkdown() {
		// Create a basic HTML post
		$post = '<html><body><h2>A heading</h2><p><strong>This is strong.</strong><br><a href="http://apple.com">This is a link</a></p></body></html>';

		// Convert to Markdown
		$parser = new Parser( 'markdown' );
		$markdown = $parser->parse( $post );

		// Verify
		$this->assertEquals( $markdown, "## A heading\n**This is strong.**\n[This is a link](http://apple.com)\n\n" );
	}

	public function testParseHTML() {
		// Create a basic HTML post
		$post = '<h2 class="someClass">A heading</h2><p><strong>This is strong.</strong><br><a href="http://apple.com" target="_blank">This is a link</a></p><div>The div tags will disappear.</div>';

		// Parse only HTML that's valid for Apple News
		$parser = new Parser( 'html' );
		$markdown = $parser->parse( $post );

		// Verify
		$this->assertEquals( $markdown, 'A heading<p><strong>This is strong.</strong><br><a href="http://apple.com">This is a link</a></p>The div tags will disappear.' );
	}
}

