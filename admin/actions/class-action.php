<?php
namespace Actions;

abstract class Action {

	protected $settings;

	function __construct( $settings ) {
		$this->settings = $settings;
	}

	public abstract function perform();

	/**
	 * Gets a setting by name which was loaded from WordPress options.
	 *
	 * @since 0.4.0
	 */
	protected function get_setting( $name ) {
		return $this->settings->get( $name );
	}

}
