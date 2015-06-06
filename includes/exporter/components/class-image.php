<?php
namespace Exporter\Components;

class Image extends Component {

	protected function build( $text ) {
		$matches = array();
		preg_match( '/src="([^"]*?)"/im', $text, $matches );
		$url = $matches[1];
		$filename = basename( $url );

		// Save image into bundle
		$content = $this->get_file_contents( $url );
		$this->write_to_workspace( $filename, $content );

		$this->json = array(
			'role' => 'photo',
			'URL'  => 'bundle://' . $filename,
		);
	}

}

