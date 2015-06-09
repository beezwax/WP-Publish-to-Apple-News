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

	public function testGetsURL() {
		$tweet_component = new Tweet( '<blockquote class="twitter-tweet" lang="en"><p lang="en" dir="ltr">Swift will be open source later this year, available for iOS, OS X, and Linux. <a href="http://t.co/yQhyzxukTn">http://t.co/yQhyzxukTn</a></p>&mdash; Federico Ramirez (@gosukiwi) <a href="https://twitter.com/gosukiwi/status/608069908044390400">June 9, 2015</a></blockquote>', null );

		$this->assertEquals(
			array(
				'role' => 'tweet',
				'URL' => 'https://twitter.com/gosukiwi/status/608069908044390400',
		 	),
			$tweet_component->value()
		);
	}

}

