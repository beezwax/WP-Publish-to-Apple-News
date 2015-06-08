<?php
namespace Exporter\Components;

class Gallery extends Component {

	protected function build( $text ) {
		preg_match_all( '/src="([^"]+)"/', $text, $matches );
		$urls  = $matches[1];
		$items = array();

		foreach ( $urls as $url ) {
			// TODO: get_file_contents and write_to_workspace used one after another
			// is used quite a lot. Maybe make a function save_to_workspace to
			// make things more DRY.
			$filename = basename( $url );
			$content = $this->get_file_contents( $url );
			$this->write_to_workspace( $filename, $content );
			$items[] = array(
				'URL' => 'bundle://' . $filename,
			);
		}

		$this->json = array(
			'role' => 'gallery',
			'items' => $items,
		);
	}

}

