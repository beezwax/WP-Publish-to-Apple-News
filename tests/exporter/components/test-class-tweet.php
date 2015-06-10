<?php

use \Exporter\Components\Tweet as Tweet;

class Tweet_Test extends PHPUnit_Framework_TestCase {

	public function testInvalidMarkup() {
		$tweet_component = new Tweet( '<blockquote class="twitter-tweet" lang="en">Invalid content. No URL.</blockquote>', null );
		$this->assertEquals(
			null,
			$tweet_component->value()
		);
	}

	public function testGetsURLFromNewFormat() {
		$tweet_component = new Tweet( '<blockquote class="twitter-tweet"
			lang="en"><p lang="en" dir="ltr">Swift will be open source later this
			year, available for iOS, OS X, and Linux. <a
			href="http://t.co/yQhyzxukTn">http://t.co/yQhyzxukTn</a></p>&mdash;
		Federico Ramirez (@gosukiwi) <a
			href="https://twitter.com/gosukiwi/status/608069908044390400">June 9,
			2015</a></blockquote>', null );

		$this->assertEquals(
			array(
				'role' => 'tweet',
				'URL' => 'https://twitter.com/gosukiwi/status/608069908044390400',
		 	),
			$tweet_component->value()
		);
	}

	public function testGetsURLFromOldFormat() {
		$tweet_component = new Tweet( '<blockquote class="twitter-tweet"
			lang="en">WordPress.com (@wordpressdotcom) <a
			href="http://twitter.com/#!/wordpressdotcom/status/204557548249026561"
			data-datetime="2012-05-21T13:01:34+00:00">May 21, 2012</a></blockquote>',
			null );

		$this->assertEquals(
			array(
				'role' => 'tweet',
				'URL' => 'https://twitter.com/wordpressdotcom/status/204557548249026561',
		 	),
			$tweet_component->value()
		);
	}

	public function testGetUsingWWW() {
		$tweet_component = new Tweet( '<blockquote class="twitter-tweet"
			lang="en">WordPress.com (@wordpressdotcom) <a
			href="https://www.twitter.com/#!/wordpressdotcom/status/204557548249026561"
			data-datetime="2012-05-21T13:01:34+00:00">May 21, 2012</a></blockquote>',
			null );

		$this->assertEquals(
			array(
				'role' => 'tweet',
				'URL' => 'https://twitter.com/wordpressdotcom/status/204557548249026561',
		 	),
			$tweet_component->value()
		);
	}

	public function testGetUsingStatuses() {
		$tweet_component = new Tweet( '<blockquote class="twitter-tweet"
			lang="en">WordPress.com (@wordpressdotcom) <a
			href="https://twitter.com/#!/wordpressdotcom/statuses/204557548249026561"
			data-datetime="2012-05-21T13:01:34+00:00">May 21, 2012</a></blockquote>',
			null );

		$this->assertEquals(
			array(
				'role' => 'tweet',
				'URL' => 'https://twitter.com/wordpressdotcom/status/204557548249026561',
		 	),
			$tweet_component->value()
		);
	}

	public function testGetLastLink() {
		$tweet_component = new Tweet( '<blockquote class="twitter-tweet"
			lang="en"><p><a
			href="https://twitter.com/foo/status/1111">twitter.com/foo/status/1111</a></p>&mdash;
		<br />WordPress.com (@wordpressdotcom) <a
			href="http://twitter.com/#!/wordpressdotcom/status/123"
			data-datetime="2012-05-21T13:01:34+00:00">May 21, 2012</a></blockquote>',
			null );

		$this->assertEquals(
			array(
				'role' => 'tweet',
				'URL' => 'https://twitter.com/wordpressdotcom/status/123',
		 	),
			$tweet_component->value()
		);
	}

}

