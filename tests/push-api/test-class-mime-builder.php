<?php

require_once __DIR__ . '/../../includes/push-api/class-credentials.php';

use \Push_API\MIME_Builder as MIME_Builder;

class MIME_Builder_Test extends WP_UnitTestCase {

	public function setup() {
		$this->builder = new MIME_Builder();
	}

	public function testAddJSON() {
		$eol      = "\r\n";
		$name     = 'some-name';
		$filename = 'article.json';
		$json     = '{"hello": "world"}';
		$size     = strlen( $json );

		$expected = '--' . $this->builder->boundary() . $eol .
			'Content-Type: application/json' . $eol .
			"Content-Disposition: form-data; name=$name; filename=$filename; size=$size" . $eol .
		 	$eol . $json . $eol;

		$this->assertEquals(
			$expected,
			$this->builder->add_json_string( $name, $filename, $json )
		);
	}

}

