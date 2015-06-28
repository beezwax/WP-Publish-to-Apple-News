<?php

use \Exporter\Components\Tweet as Tweet;
use \Exporter\Settings as Settings;
use \Exporter\Component_Layouts as Component_Layouts;
use \Exporter\Component_Styles as Component_Styles;

class Tweet_Test extends PHPUnit_Framework_TestCase {

	protected function setup() {
		$this->settings = new Settings();
		$this->styles   = new Component_Styles();
		$this->layouts  = new Component_Layouts();
	}

	public function testInvalidMarkup() {
		$component = new Tweet( '<blockquote class="twitter-tweet" lang="en">Invalid content. No URL.</blockquote>',
			null, $this->settings, $this->styles, $this->layouts );

		$this->assertEquals(
			null,
			$component->value()
		);
	}

	public function testGetsURLFromNewFormat() {
		$component = new Tweet( '<blockquote class="twitter-tweet"
			lang="en"><p lang="en" dir="ltr">Swift will be open source later this
			year, available for iOS, OS X, and Linux. <a
			href="http://t.co/yQhyzxukTn">http://t.co/yQhyzxukTn</a></p>&mdash;
		Federico Ramirez (@gosukiwi) <a
			href="https://twitter.com/gosukiwi/status/608069908044390400">June 9,
			2015</a></blockquote>', null, $this->settings, $this->styles, $this->layouts );

		$this->assertEquals(
			array(
				'role' => 'tweet',
				'URL' => 'https://twitter.com/gosukiwi/status/608069908044390400',
		 	),
			$component->value()
		);
	}

	public function testGetsURLFromOldFormat() {
		$component = new Tweet( '<blockquote class="twitter-tweet"
			lang="en">WordPress.com (@wordpressdotcom) <a
			href="http://twitter.com/#!/wordpressdotcom/status/204557548249026561"
			data-datetime="2012-05-21T13:01:34+00:00">May 21, 2012</a></blockquote>',
			null, $this->settings, $this->styles, $this->layouts );

		$this->assertEquals(
			array(
				'role' => 'tweet',
				'URL' => 'https://twitter.com/wordpressdotcom/status/204557548249026561',
		 	),
			$component->value()
		);
	}

	public function testGetUsingWWW() {
		$component = new Tweet( '<blockquote class="twitter-tweet"
			lang="en">WordPress.com (@wordpressdotcom) <a
			href="https://www.twitter.com/#!/wordpressdotcom/status/204557548249026561"
			data-datetime="2012-05-21T13:01:34+00:00">May 21, 2012</a></blockquote>',
			null, $this->settings, $this->styles, $this->layouts );

		$this->assertEquals(
			array(
				'role' => 'tweet',
				'URL' => 'https://twitter.com/wordpressdotcom/status/204557548249026561',
		 	),
			$component->value()
		);
	}

	public function testGetUsingStatuses() {
		$component = new Tweet( '<blockquote class="twitter-tweet"
			lang="en">WordPress.com (@wordpressdotcom) <a
			href="https://twitter.com/#!/wordpressdotcom/statuses/204557548249026561"
			data-datetime="2012-05-21T13:01:34+00:00">May 21, 2012</a></blockquote>',
			null, $this->settings, $this->styles, $this->layouts );

		$this->assertEquals(
			array(
				'role' => 'tweet',
				'URL' => 'https://twitter.com/wordpressdotcom/status/204557548249026561',
		 	),
			$component->value()
		);
	}

	public function testGetLastLink() {
		$component = new Tweet( '<blockquote class="twitter-tweet"
			lang="en"><p><a
			href="https://twitter.com/foo/status/1111">twitter.com/foo/status/1111</a></p>&mdash;
		<br />WordPress.com (@wordpressdotcom) <a
			href="http://twitter.com/#!/wordpressdotcom/status/123"
			data-datetime="2012-05-21T13:01:34+00:00">May 21, 2012</a></blockquote>',
			null, $this->settings, $this->styles, $this->layouts );

		$this->assertEquals(
			array(
				'role' => 'tweet',
				'URL' => 'https://twitter.com/wordpressdotcom/status/123',
		 	),
			$component->value()
		);
	}

}

