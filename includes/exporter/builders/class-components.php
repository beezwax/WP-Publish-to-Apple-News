<?php
namespace Exporter\Builders;

use \Exporter\Component_Factory as Component_Factory;

/**
 * @since 0.4.0
 */
class Components extends Builder {

	/**
	 * Builds an array with all the components of this WordPress content.
	 */
	protected function build() {
		$components = $this->meta_components();
		foreach ( $this->split_into_components() as $component ) {
			$components[] = $component->to_array();
		}
		return $components;
	}

	/**
	 * Meta components are those which were not created from HTML, instead, they
	 * contain only text. This text is normally created from the article
	 * metadata.
	 */
	private function meta_components() {
		$components = array();

		// The content's cover is optional. In WordPress, it's a post's thumbnail
		// or featured image.
		if ( $this->content_cover() ) {
			$components[] = $this->get_component_from_shortname( 'cover', $this->content_cover() )->to_array();
		}

		// Add title
		$components[] = $this->get_component_from_shortname( 'title', $this->content_title() )->to_array();

		// Add title
		if ( $this->content_byline() ) {
			$components[] = $this->get_component_from_shortname( 'byline', $this->content_byline() )->to_array();
		}

		return $components;
	}

	/**
	 * Split components from the source WordPress content.
	 */
	private function split_into_components() {
		// Loop though the first-level nodes of the body element. Components
		// might include child-components, like an Cover and Image.
		$result = array();
		foreach ( $this->content_nodes() as $node ) {
			$components = $this->get_components_from_node( $node );
			$result     = array_merge( $result, $components );
		}

		// Process the result some more. It gets passed by reference for efficiency.
		// It's not like it's a big memory save but still relevant.
		// FIXME: Maybe this could have been done in a better way?
		$this->add_pullquote_if_needed( $result );
		$this->add_advertisement_if_needed( $result );
		$this->anchor_components( $result );

		return $result;
	}

	private function add_advertisement_if_needed( &$components ) {
		if ( 'yes' != $this->get_setting( 'enable_advertisement' ) ) {
			return;
		}

		$position  = $this->get_setting( 'advertisement_position' );
		$index     = 'middle' == $position ? ceil( count( $components ) / 2 ) : 0;
		$component = $this->get_component_from_shortname( 'advertisement' );
		// Add component in position
		array_splice( $components, $index, 0, array( $component ) );
	}

	/**
	 * Anchor components that are marked as anchorable
	 */
	private function anchor_components( &$components ) {
		$len = count( $components );

		for ( $i = 0; $i < $len; $i++ ) {
			$component = $components[ $i ];

			if ( ! $component->is_anchorable ) {
				continue;
			}

			// Anchor this component to previous component
			$other_component = $components[ $i - 1 ];
			// Skip advertisement elements, they must span all width
			if ( 'banner_advertisement' == $other_component->get_json( 'role' ) ) {
				$other_component = $components[ $i - 1 ];
			}

			$component->set_json( 'anchor', array(
				'targetComponentIdentifier' => $other_component->uid(),
				'targetAnchorPosition'      => 'center',
				'rangeStart'                => 0,
				'rangeLength'               => 1,
			) );
		}
	}

	private function add_pullquote_if_needed( &$components ) {
		// Must we add a pullquote?
		$pullquote          = $this->content_setting( 'pullquote' );
		$pullquote_position = $this->content_setting( 'pullquote_position' );

		if ( empty( $pullquote ) || $pullquote_position <= 0 || $pullquote_position >= count( $components ) ) {
			return;
		}

		$component = $this->get_component_from_shortname( 'blockquote', "<blockquote>$pullquote</blockquote>" );
		$component->set_anchorable( true );
		// Add component in position
		array_splice( $components, $pullquote_position, 0, array( $component ) );
	}

	private function get_component_from_shortname( $shortname, $html = null ) {
		return Component_Factory::get_component( $shortname, $html );
	}

	private function get_components_from_node( $node ) {
		return Component_Factory::get_components_from_node( $node );
	}

}
