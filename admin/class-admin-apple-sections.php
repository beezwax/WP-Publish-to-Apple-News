<?php
/**
 * Publish to Apple News Admin Screens: Admin_Apple_Sections class
 *
 * Contains a class which is used to manage the Sections admin settings page.
 *
 * @package Apple_News
 * @since 1.2.2
 */

use \Apple_Actions\Index\Section;

/**
 * This class is in charge of handling the management of Apple News sections.
 *
 * @since 1.2.2
 */
class Admin_Apple_Sections extends Apple_News {

	/**
	 * The option name for section/taxonomy mappings.
	 */
	const TAXONOMY_MAPPING_KEY = 'apple_news_section_taxonomy_mappings';

	/**
	 * Returns a taxonomy object representing the taxonomy to be mapped to sections.
	 *
	 * @access public
	 * @return WP_Taxonomy|false A WP_Taxonomy object on success; false on failure.
	 */
	public static function get_mapping_taxonomy() {

		/**
		 * Allows for modification of the taxonomy used for section mapping.
		 *
		 * @since 1.2.2
		 *
		 * @param string $taxonomy The taxonomy slug to be filtered.
		 */
		$taxonomy = apply_filters( 'apple_news_section_taxonomy', 'category' );

		return get_taxonomy( $taxonomy );
	}

	/**
	 * Returns an array of section data without requiring an instance of the object.
	 *
	 * @access public
	 * @return array An array of section data.
	 */
	public static function get_sections() {

		// Try to load from cache.
		$sections = get_transient( 'apple_news_sections' );
		if ( false !== $sections ) {
			return $sections;
		}

		// Try to get sections. The get_sections call sets the transient.
		$admin_settings = new Admin_Apple_Settings();
		$section_api    = new Section( $admin_settings->fetch_settings() );
		$sections       = $section_api->get_sections();
		if ( empty( $sections ) || ! is_array( $sections ) ) {
			$sections = [];
			Admin_Apple_Notice::error(
				__( 'Unable to fetch a list of sections.', 'apple-news' )
			);
		}

		return $sections;
	}

	/**
	 * Given a post ID, returns an array of section URLs based on applied taxonomy.
	 *
	 * Supports overrides for manual section selection and fallback to postmeta
	 * when no mappings are set.
	 *
	 * @param int    $post_id The ID of the post to query.
	 * @param string $format The return format to use. Can be 'url' or 'raw'.
	 *
	 * @access public
	 * @return array An array of section data according to the requested format.
	 */
	public static function get_sections_for_post( $post_id, $format = 'url' ) {

		// Try to load sections from postmeta.
		$meta_value = get_post_meta( $post_id, 'apple_news_sections', true );
		if ( ! empty( $meta_value ) && is_array( $meta_value ) ) {
			return $meta_value;
		}

		// Determine if there are taxonomy mappings configured.
		$mappings = get_option( self::TAXONOMY_MAPPING_KEY );
		if ( empty( $mappings ) ) {
			return [];
		}

		// Convert sections returned from the API into the requested format.
		$sections     = [];
		$sections_raw = self::get_sections();
		foreach ( $sections_raw as $section ) {

			// Ensure we have an ID to key off of.
			if ( empty( $section->id ) ) {
				continue;
			}

			// Fork for format.
			switch ( $format ) {
				case 'raw':
					$sections[ $section->id ] = $section;
					break;
				case 'url':
					if ( ! empty( $section->links->self ) ) {
						$sections[ $section->id ] = $section->links->self;
					}
					break;
			}
		}

		// Try to get configured taxonomy.
		$taxonomy = self::get_mapping_taxonomy();
		if ( empty( $taxonomy ) || is_wp_error( $taxonomy ) ) {
			wp_die( esc_html__( 'Unable to get a valid mapping taxonomy.', 'apple-news' ) );
		}

		// Try to get terms for the post.
		$terms = get_the_terms( $post_id, $taxonomy->name );
		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return [];
		}

		// Loop through the mappings to determine sections.
		$post_sections = [];
		$term_ids      = wp_list_pluck( $terms, 'term_id' );
		$mappings      = get_option( self::TAXONOMY_MAPPING_KEY );
		foreach ( $mappings as $section_id => $section_term_ids ) {
			foreach ( $section_term_ids as $section_term_id ) {
				if ( in_array( $section_term_id, $term_ids, true ) ) {
					$post_sections[] = $sections[ $section_id ];
				}
			}
		}

		// Eliminate duplicates.
		$post_sections = array_unique( $post_sections );

		// If we get here and no sections are specified, fall back to Main.
		if ( empty( $post_sections ) ) {
			$post_sections[] = reset( $sections );
		}

		/**
		 * Filters the sections for a post.
		 *
		 * @since 2.1.0
		 *
		 * @param array  $post_sections The sections for the post.
		 * @param int    $post_id       The post ID.
		 * @param string $format        The section format (e.g., 'url').
		 */
		return apply_filters( 'apple_news_get_sections_for_post', $post_sections, $post_id, $format );
	}
}
