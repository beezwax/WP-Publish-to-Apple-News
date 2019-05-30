<?php
/**
 * Contains functions for working with meta.
 *
 * @package Apple_News
 */

// Register action hooks and filters.
add_filter(
	'update_post_metadata',
	__NAMESPACE__ . '\filter_update_post_metadata',
	10,
	5
);

/**
 * A filter callback for update_post_metadata to fix a bug with WordPress
 * whereby meta values passed via the REST API that require slashing but are
 * otherwise the same as the existing value in the database will cause a failure
 * during post save.
 *
 * @see \update_metadata
 *
 * @param null|bool $check      Whether to allow updating metadata for the given type.
 * @param int       $object_id  Object ID.
 * @param string    $meta_key   Meta key.
 * @param mixed     $meta_value Meta value. Must be serializable if non-scalar.
 * @param mixed     $prev_value Optional. If specified, only update existing.
 * @return null|bool True if the conditions are ripe for the fix, otherwise the existing value of $check.
 */
function filter_update_post_metadata(
	$check,
	$object_id,
	$meta_key,
	$meta_value,
	$prev_value
) {
	if ( empty( $prev_value ) ) {
		$old_value = get_metadata( 'post', $object_id, $meta_key );
		if ( 1 === count( $old_value ) ) {
			if ( $old_value[0] === $meta_value ) {
				return true;
			}
		}
	}

	return $check;
}

/**
 * Register meta for posts or terms with sensible defaults and sanitization.
 *
 * @throws \InvalidArgumentException For unmet requirements.
 *
 * @see \register_post_meta
 * @see \register_term_meta
 *
 * @param string $object_type  The type of meta to register, which must be one of 'post' or 'term'.
 * @param array  $object_slugs The post type or taxonomy slugs to register with.
 * @param string $meta_key     The meta key to register.
 * @param array  $args         Optional. Additional arguments for register_post_meta or register_term_meta. Defaults to an empty array.
 * @return bool True if the meta key was successfully registered in the global array, false if not.
 */
function register_meta_helper(
	string $object_type,
	array $object_slugs,
	string $meta_key,
	array $args = []
) : bool {

	// Object type must be either post or term.
	if ( ! in_array( $object_type, [ 'post', 'term' ], true ) ) {
		throw new \InvalidArgumentException(
			__(
				'Object type must be one of "post", "term".',
				'apple-news'
			)
		);
	}

	// Merge provided arguments with defaults.
	$args = wp_parse_args(
		$args,
		[
			'sanitize_callback' => __NAMESPACE__ . '\sanitize_meta_by_type',
			'show_in_rest'      => true,
			'single'            => true,
			'type'              => 'string',
		]
	);

	// Fork for object type.
	switch ( $object_type ) {
		case 'post':
			foreach ( $object_slugs as $object_slug ) {
				if ( ! register_post_meta( $object_slug, $meta_key, $args ) ) {
					return false;
				}
			}
			break;
		case 'term':
			foreach ( $object_slugs as $object_slug ) {
				if ( ! register_term_meta( $object_slug, $meta_key, $args ) ) {
					return false;
				}
			}
			break;
		default:
			return false;
	}

	return true;
}

/**
 * A 'sanitize_callback' for a registered meta key that sanitizes based on type.
 *
 * @param mixed  $meta_value     Meta value to sanitize.
 * @param string $meta_key       Meta key.
 * @param string $object_type    Object type.
 * @param string $object_subtype Optional. Object subtype. Defaults to empty.
 * @return mixed Sanitized meta value.
 */
function sanitize_meta_by_type(
	$meta_value,
	string $meta_key,
	string $object_type,
	string $object_subtype = ''
) {

	// Ensure the meta key is registered.
	$registered = get_registered_meta_keys( $object_type, $object_subtype );
	if ( empty( $registered[ $meta_key ] ) ) {
		return $meta_value;
	}

	// Ensure a type is set.
	$args = $registered[ $meta_key ];
	if ( empty( $args['type'] ) ) {
		return $meta_value;
	}

	// Sanitize by type.
	switch ( $args['type'] ) {
		case 'boolean':
			return rest_sanitize_boolean( $meta_value );
		case 'integer':
			return (int) $meta_value;
		case 'number':
			return (float) $meta_value;
		case 'string':
			return sanitize_text_field( $meta_value );
	}

	return $meta_value;
}

/**
 * A 'sanitize_callback' for the apple_news_sections meta field.
 *
 * @param mixed $meta_value Meta value to sanitize.
 * @return array Sanitized meta value.
 */
function sanitize_selected_sections( $meta_value ) : array {
	// The meta value should be a stringified JSON array. Ensure that it is.
	$raw_meta_value = is_string( $meta_value ) ? json_decode( $meta_value, true ) : $meta_value;
	if ( ! is_array( $raw_meta_value ) ) {
		return [];
	}
	// Rebuild the data, sanitizing values, and validating keys.
	return array_values(
		$raw_meta_value
	);
}

// TODO - doc block
function prepare_sections_data( $value, $request, $args ) {
	return wp_json_encode( $value );
}

/**
 * A 'sanitize_callback' for the apple_news_coverart meta field.
 *
 * @param mixed $meta_value Meta value to sanitize.
 * @return array Sanitized meta value.
 */
function sanitize_coverart_data( $meta_value ) : array {
	return is_string( $meta_value ) ? json_decode( $meta_value, true ) : $meta_value;
}

// TODO - doc block
function prepare_coverart_data( $value, $request, $args ) {
	return wp_json_encode( $value );
}