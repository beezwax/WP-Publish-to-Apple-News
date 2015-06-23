<?php
namespace Exporter;

/**
 * The grid takes an array of components and sorts them into Containers,
 * aligned as columns, as specified in the settings.
 *
 * @since 0.4.0
 */
class Component_Grid {

	/**
	 * If a grid is specified, holds columns containers.
	 *
	 * @since 0.4.0
	 */
	private $columns;

	/**
	 * Whether or not a valid grid is beeing used.
	 *
	 * @since 0.4.0
	 */
	private $has_grid;

	/**
	 * Ammount of columns specified in the settings.
	 *
	 * @since 0.4.0
	 */
	private $total_columns;

	function __construct( $settings ) {
		$this->columns  = null;
		$this->has_grid = false;
		$this->total_columns = intval( $settings->get( 'layout_columns' ) );

		// Check for a custom "grid", if it exists, register columns.
		if ( $this->total_columns != $settings->get( 'grid' ) ) {
			preg_match_all( '#(\d+)#m', $settings->get( 'grid' ), $columns );
			$this->register_columns( $columns[1] );
		}
	}


	/**
	 * Split an array of components into the defined columns (containers)
	 * configured for this article. If no columns are set, just return the input
	 * array of components.
	 *
	 * @since 0.4.0
	 */
	public function split_components_into_columns( $components ) {
		if ( ! $this->has_grid ) {
			return $components;
		}

		// Completely fill first column, then fill second, and so on.
		$total_cols  = count( $this->columns );
		$per_column  = ceil( count( $components ) / $total_cols );
		$in_curr_col = 0;
		$column_index     = 0;

		foreach ( $components as $component ) {
			if ( $in_curr_col >= $per_column ) {
				$in_curr_col  = 0;
				$column_index = ( $column_index + 1 ) % $total_cols;
			}

			// Use full-width layout if there's no layout defined.
			if ( ! $component['layout'] && $column_index > 0 ) {
				$component['layout'] = 'full-width';
			}

			$this->columns[ $column_index ]['components'][] = $component;
			$in_curr_col += 1;
		}

		return $this->columns;
	}

	/**
	 * Given an array of columns (eg [2 4 2]) creates appropriate containers.
	 *
	 * @since 0.4.0
	 */
	private function register_columns( $cols ) {
		// If columns are invalid, ignore silently.
		// TODO: Show warning.
		if ( $this->total_columns != array_sum( $cols ) ) {
			return;
		}

		// Generate columns. Each column is a container which will hold components
		// inside.
		$this->columns  = array();
		$this->has_grid = true;
		$start = 0;
		foreach ( $cols as $col ) {
			$col = intval( $col );
			$this->columns[] = array(
				'role' => 'container',
				'layout' => array(
					'columnStart' => $start,
					'columnSpan'  => $col,
				),
				'components' => array(),
			);
			$start += $col;
		}
	}

}
