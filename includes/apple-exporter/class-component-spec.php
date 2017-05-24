<?php
/**
 * Publish to Apple News Includes: Apple_Exporter\Component_Spec class
 *
 * Defines a JSON spec for a component.
 *
 * @package Apple_News
 * @subpackage Apple_Exporter
 * @since 1.2.4
 */

namespace Apple_Exporter;

/**
 * A class that defines a JSON spec for a component.
 *
 * @since 1.2.4
 */
class Component_Spec {

	/**
	 * The component for this spec.
	 *
	 * @access public
	 * @var string
	 */
	public $component;

	/**
	 * The name for this spec.
	 *
	 * @access public
	 * @var string
	 */
	public $name;

	/**
	 * The label for this spec.
	 *
	 * @access public
	 * @var string
	 */
	public $label;

	/**
	 * The spec.
	 *
	 * @access public
	 * @var array
	 */
	public $spec;

	/**
	 * Initializes the object with the name, label and the spec.
	 *
	 * @param string $component The component name.
	 * @param string $name The spec name.
	 * @param string $label The human-readable label for the spec.
	 * @param array $spec The spec definition.
	 *
	 * @access public
	 */
	public function __construct( $component, $name, $label, $spec ) {
		$this->component = $component;
		$this->name = $name;
		$this->label = $label;
		$this->spec = $spec;
	}

	/**
	 * Using the provided spec and array of values, build the component's JSON.
	 *
	 * @param array $values Values to substitute into the spec.
	 *
	 * @access public
	 * @return array The component JSON with placeholders in the spec replaced.
	 */
	public function substitute_values( $values ) {
		return $this->value_iterator( $this->get_spec(), $values );
	}

	/**
	 * Substitute values recursively for a given spec.
	 *
	 * @param array $spec The spec to use as a template.
	 * @param array $values Values to substitute in the spec.
	 *
	 * @access public
	 * @return array The spec with placeholders replaced by values.
	 */
	public function value_iterator( $spec, $values ) {

		// Go through this level of the iterator.
		foreach ( $spec as $key => $value ) {

			// If the current element has children, call this recursively.
			if ( is_array( $value ) ) {

				// Call this function recursively to handle the substitution on this child array.
				$spec[ $key ] = $this->value_iterator( $spec[ $key ], $values );
			} elseif ( ! is_array( $value ) && $this->is_token( $value ) ) {

				// This element is a token, so substitute its value.
				// If no value exists, it should be removed to not produce invalid JSON.
				if ( isset( $values[ $value ] ) ) {
					$spec[ $key ] = $values[ $value ];
				} else {
					unset( $spec[ $key ] );
				}
			}
		}

		return $spec;
	}

	/**
	 * Validate the provided spec against the built-in spec.
	 *
	 * @param array $spec The spec to validate.
	 *
	 * @access public
	 * @return boolean True if validation was successful, false otherwise.
	 */
	public function validate( $spec ) {

		// Iterate recursively over the built-in spec and get all the tokens.
		// Do the same for the provided spec.
		$new_tokens = $default_tokens = array();
		$this->find_tokens( $spec, $new_tokens );
		$this->find_tokens( $this->spec, $default_tokens );

		// Removing tokens is fine, but new tokens cannot be added.
		foreach ( $new_tokens as $token ) {
			if ( ! in_array( $token, $default_tokens, true ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Recursively find tokens in the spec.
	 *
	 * @param array $spec The spec to iterate over to look for tokens.
	 * @param array $tokens A list of found tokens.
	 *
	 * @access public
	 */
	public function find_tokens( $spec, &$tokens ) {

		// Find all tokens in the spec.
		foreach ( $spec as $key => $value ) {

			// If the current element has children, call this recursively.
			if ( is_array( $value ) ) {
				$this->find_tokens( $spec[ $key ], $tokens );
			} elseif ( ! is_array( $value ) && $this->is_token( $value ) ) {
				$tokens[] = $value;
			}
		}
	}

	/**
	 * Save the provided spec override.
	 *
	 * @param array $spec The spec definition to save.
	 * @param string $theme_name Optional. Theme name to save to if other than default.
	 *
	 * @access public
	 * @return boolean True on success, false on failure.
	 */
	public function save( $spec, $theme_name = '' ) {

		// Validate the JSON.
		$json = json_decode( $spec, true );
		if ( empty( $json ) ) {
			\Admin_Apple_Notice::error( sprintf(
				__( 'The spec for %s was invalid and cannot be saved', 'apple-news' ),
				$this->label
			) );

			return false;
		}

		// Compare this JSON to the built-in JSON.
		// If they are the same, there is no reason to save this.
		$custom_json = $this->format_json( $json );
		$default_json = $this->format_json( $this->spec );
		if ( $custom_json === $default_json ) {
			// Delete the spec in case we've reverted back to default.
			// No need to keep it in storage.
			return $this->delete( $theme_name );
		}

		// Validate the JSON.
		$result = $this->validate( $json );
		if ( false === $result ) {
			\Admin_Apple_Notice::error( sprintf(
				__(
					'The spec for %s had invalid tokens and cannot be saved',
					'apple-news'
				),
				$this->label
			) );

			return $result;
		}

		// Negotiate the theme name.
		if ( empty( $theme_name ) ) {
			$theme_name = \Apple_Exporter\Theme::get_active_theme_name();
		}

		// Attempt to load the theme to be saved.
		$theme = new \Apple_Exporter\Theme;
		$theme->set_name( $theme_name );
		if ( ! $theme->load() ) {
			\Admin_Apple_Notice::error( sprintf(
				__( 'Unable to load theme %s to save spec', 'apple-news' ),
				$theme_name
			) );

			return false;
		}

		// Ensure that json_templates is set in the theme and is an array.
		$theme_settings = $theme->all_settings();
		if ( empty( $theme_settings['json_templates'] )
			|| ! is_array( $theme_settings['json_templates'] )
		) {
			$theme_settings['json_templates'] = array();
		}

		// Try to load the custom JSON into the theme.
		$component_key = $this->key_from_name( $this->component );
		$spec_key = $this->key_from_name( $this->name );
		$theme_settings['json_templates'][ $component_key ][ $spec_key ] = $json;
		if ( ! $theme->load( $theme_settings ) ) {
			\Admin_Apple_Notice::error( sprintf(
				__( 'The spec for %s could not be loaded into the theme', 'apple-news' ),
				$this->label
			) );

			return false;
		}

		// Try to save the theme.
		if ( ! $theme->save() ) {
			\Admin_Apple_Notice::error( sprintf(
				__( 'The spec for %s could not be saved to the theme', 'apple-news' ),
				$this->label
			) );

			return false;
		}

		// Indicate success.
		return true;
	}

	/**
	 * Delete the current spec override.
	 *
	 * @param string $theme_name Optional. Theme to delete from if not the default.
	 *
	 * @access public
	 * @return bool True on success, false on failure.
	 */
	public function delete( $theme_name = '' ) {

		// Negotiate theme name.
		if ( empty( $theme_name ) ) {
			$theme_name = \Apple_Exporter\Theme::get_active_theme_name();
		}

		// Compute component and spec keys.
		$component_key = $this->key_from_name( $this->component );
		$spec_key = $this->key_from_name( $this->name );

		// Try to load theme settings.
		$theme = new \Apple_Exporter\Theme;
		$theme->set_name( $theme_name );
		if ( ! $theme->load() ) {
			return false;
		}

		// Determine if this spec override is defined in the theme.
		$theme_settings = $theme->all_settings();
		if ( ! isset( $theme_settings['json_templates'][ $component_key ][ $spec_key ] ) ) {
			return false;
		}

		// Remove this spec from the theme.
		unset( $theme_settings['json_templates'][ $component_key ][ $spec_key ] );

		// If there are no more overrides for this component, remove it.
		if ( empty( $theme_settings['json_templates'][ $component_key ] ) ) {
			unset( $theme_settings['json_templates'][ $component_key ] );

			// If there are no more JSON templates, remove the block.
			if ( empty( $theme_settings['json_templates'] ) ) {
				unset( $theme_settings['json_templates'] );
			}
		}

		// Update the theme.
		if ( ! $theme->load( $theme_settings ) ) {
			return false;
		}

		return $theme->save();
	}

	/**
	 * Get the spec for this component as JSON.
	 *
	 * @access public
	 * @return array The configuration for the spec.
	 */
	public function get_spec() {

		// Determine if there is an override for this spec.
		$override = $this->get_override();
		if ( ! empty( $override ) ) {
			return $override;
		}

		return $this->spec;
	}

	/**
	 * Get the spec for this component as JSON.
	 *
	 * @param string $spec
	 * @return string
	 * @access public
	 */
	public function format_json( $spec ) {
		return wp_json_encode( $spec, JSON_PRETTY_PRINT );
	}

	/**
	 * Get the override for this component spec.
	 *
	 * @access public
	 * @return array|null An array of values if an override is present, else null.
	 */
	public function get_override() {

		// Try to get JSON templates.
		$theme = \Apple_Exporter\Theme::get_used();
		$json_templates = $theme->get_value( 'json_templates' );
		if ( empty( $json_templates ) || ! is_array( $json_templates ) ) {
			return null;
		}

		// Determine if there is an override in the theme.
		$component = $this->key_from_name( $this->component );
		$spec = $this->key_from_name( $this->name );
		if ( ! empty( $json_templates[ $component ][ $spec ] ) ) {
			return $json_templates[ $component ][ $spec ];
		}

		return null;
	}

	/**
	 * Determines whether or not the spec value is a token.
	 *
	 * @param string $value The value to check against the token format.
	 *
	 * @access public
	 * @return boolean True if the value is a token, false otherwise.
	 */
	public function is_token( $value ) {
		return ( 1 === preg_match( '/#[^%]+#/', $value ) );
	}

	/**
	 * Generates a key for the JSON from the provided component or spec.
	 *
	 * @param string $name The name to turn into a key.
	 *
	 * @access public
	 * @return string The name converted into a key.
	 */
	public function key_from_name( $name ) {
		return sanitize_key( $name );
	}
}