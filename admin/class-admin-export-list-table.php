<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Use WordPress List_Table class to create a custom table displaying posts
 * information and actions.
 *
 * @since 0.4.0
 */
class Admin_Export_List_Table extends WP_List_Table {

	/**
	 * How many entries per page will be displayed.
	 *
	 * @since 0.4.0
	 */
	const PER_PAGE = 20;

	function __construct() {
		parent::__construct( array(
			'singular' => 'article',
			'plural'   => 'articles',
			'ajax'     => false,
		) );
	}

	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
		case 'title':
			return $item[ $column_name ];
		case 'sync':
			return $this->get_synced_status_for( $item );
		default:
			return print_r( $item, true ); // For debugging
		}
	}

	private function get_synced_status_for( $post ) {
		$remote_id = get_post_meta( $post->ID, 'apple_export_api_id', true );

		if ( ! $remote_id ) {
			// There is no remote id, check for a delete mark
			$deleted = get_post_meta( $post->ID, 'apple_export_api_deleted', true );
			if ( $deleted ) {
				return 'Deleted';
			}

			// No delete mark, this has not been published yet.
			return 'Not published';
		}

		$updated = get_post_meta( $post->ID, 'apple_export_api_modified_at', true );
		$updated = strtotime( $updated );
		$local   = strtotime( $post->post_modified );

		if ( $local > $updated ) {
			return 'Needs to be updated';
		}

		return 'Published';
	}

	/**
	 * This method is responsible for what is rendered in any column with a
	 * name/slug of 'title'.
	 *
	 * Every time the class needs to render a column, it first looks for a method
	 * named column_{$column_title}, if it exists, that method is run, otherwise,
	 * column_default() is called.
	 *
	 * Actions can be generated here.
	 */
	public function column_title( $item ) {
		$admin_url = get_admin_url() . 'admin.php';
		$base_url  = "$admin_url?page=%s&amp;action=%s&amp;post_id=%s";
		$page      = htmlentities( $_REQUEST['page'] );
		$actions   = array(
			'settings' => sprintf( "<a href='$base_url'>Options</a>", $page, 'settings', $item->ID ),
			'export'   => sprintf( "<a href='$base_url'>Download</a>", $page, 'export', $item->ID ),
			'push'     => sprintf( "<a href='$base_url'>Publish</a>", $page, 'push', $item->ID ),
		);

		if ( get_post_meta( $item->ID, 'apple_export_api_id', true ) ) {
			$actions['delete'] = sprintf( "<a title='This will NOT delete your local copy' href='$base_url'>Delete from Apple News</a>", $page, 'delete', $item->ID );
		}

		$share_url = get_post_meta( $item->ID, 'apple_export_api_share_url', true );
		if ( $share_url ) {
			$actions['share'] = sprintf( "<a title='Preview in News app' href='%s'>See in News app</a>", $share_url );
		}

		return sprintf( '%1$s <span>(id:%2$s)</span> %3$s',
			$item->post_title,             // %1$s
			$item->ID,                     // %2$s
			$this->row_actions( $actions ) // %3$s
		);
	}

	/**
	 * Dictates the table columns and titles. The 'cb' column is special and, if
	 * existant, there needs to be a `column_cb` method defined.
	 *
	 * @return array An array where the key is the column slug and the value is
	 * the title text.
	 */
	public function get_columns() {
		return array(
			'cb'    => '<input type="checkbox">',
			'title' => 'Title',
			'sync'  => 'Apple News Status',
		);
	}

	/**
	 * Required IF using checkboxes or bulk actions. The 'cb' column gets special
	 * treatment when columns are processed. It ALWAYS needs to have it's own
	 * method.
	 */
	public function column_cb( $item ) {
		return sprintf( '<input type="checkbox" name="%1$s[]" value="%2$s">',
			$this->_args['singular'], // %1$s
			$item->ID                 // %2$s
		);
	}

	public function get_bulk_actions() {
		return array(
			'push' => 'Publish',
		);
	}

	public function prepare_items() {
		// Set column headers. It expects an array of columns, and as second
		// argument an array of hidden columns, which in this case is empty.
		$columns = $this->get_columns();
		$this->_column_headers = array( $columns, array(), array() );

		// Data fetch
		$current_page = $this->get_pagenum();
		$data = get_posts( array(
			'posts_per_page' => self::PER_PAGE,
			'offset'         => ($current_page - 1) * self::PER_PAGE,
			'orderby'        => 'ID',
			'order'          => 'DESC',
		) );

		// Set data
		$this->items = $data;
		$total_items = wp_count_posts()->publish;
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => self::PER_PAGE,
			'total_pages' => ceil( $total_items / self::PER_PAGE ),
		) );
	}

}
