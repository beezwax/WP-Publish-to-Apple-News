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
		return $this->group_body_components( $components );
	}

	/**
	 * Given an array of components in array format, group all the elements of
	 * role 'body'. Ignore body elements that have an ID, as they are used for
	 * anchoring.
	 *
	 * Grouping body like this allows the Apple Format interpreter to render
	 * proper paragraph spacing.
	 *
	 * @since 0.6.0
	 */
	private function group_body_components( $components ) {
		$new_components = array();
		$body_collector = null;

		foreach( $components as $component ) {
			if( 'body' != $component['role'] || isset( $component['identifier'] ) ) {
				// If we have something stored in the collector, add it to the new
				// components array and set to null again.
				if ( ! is_null( $body_collector ) ) {
					$body_collector['text'] = trim( $body_collector['text'] ) . "\n";
					$new_components[] = $body_collector;
					$body_collector   = null;
				}

				if( 'body' == $component['role'] && isset( $component['identifier'] ) ) {
					$component['text'] = trim( $component['text'] ) . "\n";
				}

				$new_components[] = $component;
				continue;
			}

			// We can now assume $component is of role body and has no identifier.
			// Let's collect the contents.
			if( is_null( $body_collector ) ) {
				$body_collector = $component;
			} else {
				$body_collector['text'] .= $component['text'];
			}
		}

		// Make a final check for the body collector, as it might not be empty
		if ( ! is_null( $body_collector ) ) {
			$body_collector['text'] = trim( $body_collector['text'] ) . "\n";
			$new_components[] = $body_collector;
		}

		return $new_components;
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

		// Add byline
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

		// Always position the advertisement in the middle
		$index     = ceil( count( $components ) / 2 );
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
			// Skip advertisement elements, they must span all width. If the previous
			// element is an ad, use next instead.
			if ( 'banner_advertisement' == $other_component->get_json( 'role' ) ) {
				$other_component = $components[ $i + 1 ];
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
