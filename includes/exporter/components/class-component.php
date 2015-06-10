<?php
namespace Exporter\Components;

/**
 * Base component class. All components must inherit from this class and
 * implement it's abstract method "build".
 *
 * @since 0.0.0
 */
abstract class Component {

	protected $workspace;
	protected $text;
	protected $json = null;

	function __construct( $text, $workspace ) {
		$this->text = $text;
		$this->workspace = $workspace;
	}

	/**
	 * Given a source (either a file path or an URL) gets the contents and writes
	 * them into a file with the given filename.
	 *
	 * @param string $filename  The name of the file to be created
	 * @param string $source    The path or URL of the resource which is going to
	 *                          be bundled
	 */
	protected function bundle_source( $filename, $source ) {
		$content = $this->workspace->get_file_contents( $source );
		$this->workspace->write_tmp_file( $filename, $content );
	}

	/**
	 * Lazily transforms HTML into an array that describes the component using
	 * the build function.
	 */
	public function value() {
		// Lazy value evaluation
		if ( is_null( $this->json ) ) {
			$this->build( $this->text );
		}

		return $this->json;
	}

	/**
	 * This function is in charge of transforming HTML into a Article Format
	 * valid array.
	 */
	abstract protected function build( $text );

}
