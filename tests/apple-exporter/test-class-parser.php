<?php

use Apple_Exporter\Parser as Parser;

class Parser_Test extends WP_UnitTestCase {

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

	public function testCleanHTMLMarkdown() {
		update_option( 'sieturl', 'http://wp.dev' );
		update_option( 'home', 'http://wp.dev' );

		// Create a basic HTML post
		$post = '<html><body><a name="anchor"><h2>A heading</h2></a><p><a href="http://avalidurl.com">A valid URL</a><br/><a href="http://someurl.com ">Invalid spaces</a><br/><a href="#anchor">Anchors don\'t work!</a><br/><a href="/relative_url">Some relative url</a></p></body></html>';

		// Convert to Markdown
		$parser = new Parser( 'markdown' );
		$markdown = $parser->parse( $post );

		// Verify
		$this->assertEquals( $markdown, "## A heading\n[A valid URL](http://avalidurl.com)\n[Invalid spaces](http://someurl.com)\nAnchors don't work\!\n[Some relative url](http://wp.dev/relative_url)\n\n" );
	}

	public function testCleanHTML() {
		update_option( 'sieturl', 'http://wp.dev' );
		update_option( 'home', 'http://wp.dev' );

		// Create a basic HTML post
		$post = '<a name="anchor"><h2>A heading</h2></a><p><a href="http://avalidurl.com">A valid URL</a><br/><a href="http://someurl.com ">Invalid spaces</a><br/><a href="#anchor">Anchors don\'t work!</a><br/><a href="/relative_url">Some relative url</a></p>';

		// Convert to Markdown
		$parser = new Parser( 'html' );
		$markdown = $parser->parse( $post );

		// Verify
		$this->assertEquals( $markdown, 'A heading<p><a href="http://avalidurl.com">A valid URL</a><br /><a href="http://someurl.com">Invalid spaces</a><br />Anchors don\'t work!<br /><a href="http://wp.dev/relative_url">Some relative url</a></p>' );
	}
}

