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
 * A class that parses raw HTML into either Apple News HTML or Markdown format.
 *
 * @since 1.2.1
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
	 * @param array $values
	 * @return array
	 * @access public
	 */
	public function substitute_values( $values ) {
		// Call a recursive function to substitute the values
		return $this->value_iterator( $this->spec, $values );
	}

	/**
	 * Substitute values recursively for a given spec
	 *
	 * @param array $spec
	 * @param array $values
	 * @return array
	 * @access public
	 */
	public function value_iterator( $spec, $values ) {
		// Go through this level of the iterator
		foreach ( $spec as $key => $value ) {

			// If the current element has children, call this recursively
			if ( is_array( $value ) ) {
				// Pass through only the values for this child array
				$child_values = ( isset( $values[ $key ] ) ) ? $values[ $key ] : array();

				// Call this function recursively to handle the substitution on this child array
				$spec[ $key ] = $this->value_iterator( $spec[ $key ], $child_values );
			} else if ( ! is_array( $value ) && $this->is_token( $value ) ) {
				// This element is a token, so substitute its value
				// If no value exists, it should be removed to not produce invalid JSON
				if ( isset( $values[ $key ] ) ) {
					$spec[ $key ] = $values[ $key ];
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
	 * @param array $spec
	 * @return boolean
	 * @access public
	 */
	public function validate( $spec ) {
		// Iterate recursively over the built-in spec and get all the tokens
		// Do the same for the provided spec and ensure the tokens are the same
		// Provide an error for unexpected tokens or missing tokens
	}

	/**
	 * Save the provided spec override.
	 *
	 * @param array $spec
	 * @return boolean
	 * @access public
	 */
	public function save( $spec ) {
		// Save as part of a single option value array
		// TODO - should components handle this maybe since they have multiple specs?
		// Picturing a dropdown or nav that changes between components with form fields
		// with JSON pretty print for each spec for that component.
	}

	/**
	 * Determines whether or not the spec value is a token.
	 *
	 * @param string $value
	 * @return boolean
	 * @access public
	 */
	public function is_token( $value ) {
		return ( 1 === preg_match( '/%%(.*?)%%/', $value ) );
	}

	// TODO - need a function for pulling in spec overrides from the database?
	// TODO - how will validation work for overrides on save?
	// http://stackoverflow.com/questions/6054033/pretty-printing-json-with-php

	// TODO should remove items from spec that don't have values set
}
