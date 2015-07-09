<?php
namespace Exporter;

/**
 * This class in in charge of creating components. Manual component
 * instantiation should be avoided, use this instead.
 *
 * @since 0.2.0
 */
class Component_Factory {

	private static $components = array();
	private static $workspace  = null;
	private static $settings   = null;
	private static $styles     = null;
	private static $layouts    = null;

	public static function initialize( $workspace, $settings, $styles, $layouts ) {
		self::$workspace = $workspace;
		self::$settings  = $settings;
		self::$styles    = $styles;
		self::$layouts   = $layouts;

		// Order is important. Components are checked in the order they are added.
		self::register_component( 'gallery'      ,   '\\Exporter\\Components\\Gallery'         );
		self::register_component( 'tweet'        ,   '\\Exporter\\Components\\Tweet'           );
		self::register_component( 'instagram'    ,   '\\Exporter\\Components\\Instagram'       );
		self::register_component( 'img'          ,   '\\Exporter\\Components\\Image'           );
		self::register_component( 'iframe'       ,   '\\Exporter\\Components\\Embed_Web_Video' );
		self::register_component( 'video'        ,   '\\Exporter\\Components\\Video'           );
		self::register_component( 'audio'        ,   '\\Exporter\\Components\\Audio'           );
		self::register_component( 'heading'      ,   '\\Exporter\\Components\\Heading'         );
		self::register_component( 'blockquote'   ,   '\\Exporter\\Components\\Quote'           );
		self::register_component( 'p'            ,   '\\Exporter\\Components\\Body'            );
		self::register_component( 'hr'           ,   '\\Exporter\\Components\\Divider'         );
		// Non HTML-based components
		self::register_component( 'intro'        ,   '\\Exporter\\Components\\Intro'           );
		self::register_component( 'cover'        ,   '\\Exporter\\Components\\Cover'           );
		self::register_component( 'title'        ,   '\\Exporter\\Components\\Title'           );
		self::register_component( 'byline'       ,   '\\Exporter\\Components\\Byline'          );
		self::register_component( 'advertisement',   '\\Exporter\\Components\\Advertisement'   );
	}

	private static function register_component( $shortname, $classname ) {
		self::$components[ $shortname ] = $classname;
	}

	public static function get_component( $shortname, $html ) {
		$class = self::$components[ $shortname ];

		if ( is_null( $class ) ) {
			return null;
		}

		return new $class( $html, self::$workspace, self::$settings, self::$styles, self::$layouts );
	}

	/**
	 * Given a node, returns an array of all the components inside that node. If
	 * the node is a component itself, returns an array of only one element.
	 */
	public static function get_components_from_node( $node ) {
		$result = array();

		foreach ( self::$components as $shortname => $class ) {
			$matched_node = $class::node_matches( $node );

			// Nothing matched? Skip to next match.
			if ( ! $matched_node ) {
				continue;
			}

			// Did we match a list of nodes? For now, components that might return
			// DOMNodeList are Image, Video, EWV and Audio components.
			if ( $matched_node instanceof \DOMNodeList ) {
				foreach ( $matched_node as $item ) {
					$html     = $node->ownerDocument->saveXML( $item );
					$result[] = self::get_component( $shortname, $html );
				}
				return $result;
			}

			// Did we match several components? If so, a hash is returned. Right now
			// only the paragraph returns this.
			if ( is_array( $matched_node ) ) {
				foreach ( $matched_node as $base_component ) {
					$result[] = self::get_component( $base_component['name'], $base_component['value'] );
				}

				return $result;
			}

			// We matched a single node
			$html = $node->ownerDocument->saveXML( $matched_node );
			$result[] = self::get_component( $shortname, $html );
			return $result;
		}

		// Nothing found. Maybe it's a container element?
		if ( $node->hasChildNodes() ) {
			foreach ( $node->childNodes as $child ) {
				$result = array_merge( $result, self::get_components_from_node( $child ) );
			}
			// Remove all nulls from the array
			$result = array_filter( $result );
		}

		// Nothing was found, return null.
		return $result;
	}

}
