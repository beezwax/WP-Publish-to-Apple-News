<?php
namespace Exporter\Components;

class Image extends Component {

	protected function build( $text ) {
		$matches = array();
		preg_match( '/src="([^"]*?)"/im', $text, $matches );
		$url = $matches[1];
		$filename = array_pop( explode( '/', $url ) );

		// Save image into bundle
		$this->workspace->write_tmp_file( $filename, file_get_contents( $url ) );

		$this->json = array(
			'role' => 'photo',
			'URL'  => 'bundle://' . $filename,
		);
	}

}

