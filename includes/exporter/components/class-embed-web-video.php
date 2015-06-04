<?php
namespace Exporter\Components;

class Embed_Web_Video extends Component {

	protected function build( $text ) {
		$attributes = array();
		preg_match_all( '/(\w+)="([^"]*?)"/im', $text, $matches, PREG_SET_ORDER );
		foreach ( $matches as $match ) {
			$attributes[ $match[1] ] = $match[2];
		}

		$aspect_ratio = (float) substr( ( $attributes['width'] / $attributes['height'] ), 0, 5 );

		$this->json = array(
			'role' => 'embedwebvideo',
			'aspectRatio' => $aspect_ratio,
			'URL' => $attributes['src'],
			'caption' => 'test',
			'accessibilityCaption' => 'test',
		);
	}

}

