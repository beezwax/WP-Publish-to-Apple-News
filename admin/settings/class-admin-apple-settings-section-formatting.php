<?php

/**
 * Describes a WordPress setting section
 *
 * @since 0.6.0
 */
class Admin_Apple_Settings_Section_Formatting extends Admin_Apple_Settings_Section {

	/**
	 * Slug of the formatting settings section.
	 *
	 * @var string
	 * @access protected
	 */
	protected $slug = 'formatting-options';

	/**
	 * Constructor.
	 *
	 * @param string $page
	 */
	function __construct( $page ) {
		// Set the name
		$this->name =  __( 'Formatting', 'apple-news' );

		// Add the settings
		$this->settings = array(
			'layout_margin' => array(
				'label'   => __( 'Layout margin', 'apple-news' ),
				'type'    => 'integer',
			),
			'layout_gutter' => array(
				'label'   => __( 'Layout gutter', 'apple-news' ),
				'type'    => 'integer',
			),
			'body_font' => array(
				'label'   => '',
				'type'    => 'font',
			),
			'body_size' => array(
				'label'   => __( 'Body font size', 'apple-news' ),
				'type'    => 'integer',
			),
			'body_color' => array(
				'label'   => __( 'Body font color', 'apple-news' ),
				'type'    => 'color',
			),
			'body_link_color' => array(
				'label'   => __( 'Body font hyperlink color', 'apple-news' ),
				'type'    => 'color',
			),
			'body_background_color' => array(
				'label'   => __( 'Body background color', 'apple-news' ),
				'type'    => 'color',
			),
			'body_orientation' => array(
				'label'   => __( 'Body alignment', 'apple-news' ),
				'type'    => array( 'left', 'center', 'right' ),
			),
			'initial_dropcap' => array(
				'label'   => __( 'Use initial dropcap', 'apple-news' ),
				'type'    => array( 'yes', 'no' ),
			),
			'dropcap_font' => array(
				'label'   => '',
				'type'    => 'font',
			),
			'dropcap_color' => array(
				'label'   => __( 'Dropcap font color', 'apple-news' ),
				'type'    => 'color',
			),
			'byline_font' => array(
				'label'   => '',
				'type'    => 'font',
			),
			'byline_size' => array(
				'label'   => __( 'Byline font size', 'apple-news' ),
				'type'    => 'integer',
			),
			'byline_color' => array(
				'label'   => __( 'Byline font color', 'apple-news' ),
				'type'    => 'color',
			),
			'byline_format' => array(
				'label'				=> __( 'Byline format', 'apple-news' ),
				'type' 				=> 'text',
				'description' => __( 'Set the byline format. Two tokens can be present, #author# to denote the location of the author name and a <a href="http://php.net/manual/en/function.date.php" target="blank">PHP date format</a> string also encapsulated by #. The default format is "by #author# | #M j, Y | g:i A#".', 'apple-news' ),
				'size'				=> 40,
				'required'		=> false,
			),
			'header_font' => array(
				'label'   => '',
				'type'    => 'font',
			),
			'header_color' => array(
				'label'   => __( 'Header font color', 'apple-news' ),
				'type'    => 'color',
			),
			'header1_size' => array(
				'label'   => __( 'Header 1 font size', 'apple-news' ),
				'type'    => 'integer',
			),
			'header2_size' => array(
				'label'   => __( 'Header 2 font size', 'apple-news' ),
				'type'    => 'integer',
			),
			'header3_size' => array(
				'label'   => __( 'Header 3 font size', 'apple-news' ),
				'type'    => 'integer',
			),
			'header4_size' => array(
				'label'   => __( 'Header 4 font size', 'apple-news' ),
				'type'    => 'integer',
			),
			'header5_size' => array(
				'label'   => __( 'Header 5 font size', 'apple-news' ),
				'type'    => 'integer',
			),
			'header6_size' => array(
				'label'   => __( 'Header 6 font size', 'apple-news' ),
				'type'    => 'integer',
			),
			'pullquote_font' => array(
				'label'   => '',
				'type'    => 'font',
			),
			'pullquote_size' => array(
				'label'   => __( 'Pull quote font size', 'apple-news' ),
				'type'    => 'integer',
			),
			'pullquote_color' => array(
				'label'   => __( 'Pull quote color', 'apple-news' ),
				'type'    => 'color',
			),
			'pullquote_border_color' => array(
				'label'   => __( 'Pull quote border color', 'apple-news' ),
				'type'    => 'color',
			),
			'pullquote_border_style' => array(
				'label'   => __( 'Pull quote border style', 'apple-news' ),
				'type'    => array( 'solid', 'dashed', 'dotted' ),
			),
			'pullquote_border_width' => array(
				'label'   => __( 'Pull quote border width', 'apple-news' ),
				'type'    => 'integer',
			),
			'pullquote_transform' => array(
				'label'   => __( 'Pull quote transformation', 'apple-news' ),
				'type'    => array( 'none', 'uppercase' ),
			),
			'gallery_type' => array(
				'label'   => __( 'Gallery type', 'apple-news' ),
				'type'    => array( 'gallery', 'mosaic' ),
			),
			'enable_advertisement' => array(
				'label'   => __( 'Enable advertisements', 'apple-news' ),
				'type'    => array( 'yes', 'no' ),
			),
			'ad_frequency' => array(
				'label'   		=> __( 'Ad Frequency', 'apple-news' ),
				'type'    		=> 'integer',
				'description'	=> __( 'A number between 1 and 10 defining the frequency for automatically inserting Banner Advertisement components into articles. For more information, see the <a href="https://developer.apple.com/library/ios/documentation/General/Conceptual/Apple_News_Format_Ref/AdvertisingSettings.html#//apple_ref/doc/uid/TP40015408-CH93-SW1" target="_blank">Apple News Format Reference</a>.', 'apple-news' ),
			),
			'ad_margin' => array(
				'label'   => __( 'Ad Margin', 'apple-news' ),
				'type'    => 'integer',
				'description'	=> __( 'The margin to use above and below inserted ads.', 'apple-news' ),
			),
			'meta_component_order' => array(
				'callback'	=> array( get_class( $this ), 'render_meta_component_order' ),
				'sanitize' 	=> array( $this, 'sanitize_array' ),
			),
		);

		// Add the groups
		$this->groups = array(
			'layout' => array(
				'label'       => __( 'Layout Spacing', 'apple-news' ),
				'description' => __( 'The spacing for the base layout of the exported articles', 'apple-news' ),
				'settings'    => array( 'layout_margin', 'layout_gutter' ),
			),
			'body' => array(
				'label'       => __( 'Body', 'apple-news' ),
				'settings'    => array( 'body_font', 'body_size', 'body_color', 'body_link_color', 'body_background_color', 'body_orientation' ),
			),
			'dropcap' => array(
				'label'       => __( 'Dropcap', 'apple-news' ),
				'settings'    => array( 'dropcap_font', 'initial_dropcap', 'dropcap_color' ),
			),
			'byline' => array(
				'label'       => __( 'Byline', 'apple-news' ),
				'description' => __( "The byline displays the article's author and date", 'apple-news' ),
				'settings'    => array( 'byline_font', 'byline_size', 'byline_color', 'byline_format' ),
			),
			'headings' => array(
				'label'       => __( 'Headings', 'apple-news' ),
				'settings'    => array( 'header_font', 'header_color', 'header1_size',
				  'header2_size', 'header3_size', 'header4_size', 'header4_size',
				  'header5_size', 'header6_size' ),
			),
			'pullquote' => array(
				'label'       => __( 'Pull quote', 'apple-news' ),
				'description' => sprintf(
					'%s <a href="https://en.wikipedia.org/wiki/Pull_quote">%s</a>.',
					__( 'Articles can have an optional', 'apple-news' ),
					__( 'Pull quote', 'apple-news' )
				),
				'settings'    => array( 'pullquote_font', 'pullquote_size', 'pullquote_color', 'pullquote_border_color', 'pullquote_border_style', 'pullquote_border_width', 'pullquote_transform' ),
			),
			'gallery' => array(
				'label'       => __( 'Gallery', 'apple-news' ),
				'description' => __( 'Can either be a standard gallery, or mosaic.', 'apple-news' ),
				'settings'    => array( 'gallery_type' ),
			),
			'advertisement' => array(
				'label'       => __( 'Advertisement', 'apple-news' ),
				'settings'    => array( 'enable_advertisement', 'ad_frequency', 'ad_margin' ),
			),
			'component_order' => array(
				'label'       => __( 'Component Order', 'apple-news' ),
				'settings'    => array( 'meta_component_order' ),
			),
		);

		parent::__construct( $page );
	}

	/**
	 * Gets section info.
	 *
	 * @return string
	 * @access public
	 */
	public function get_section_info() {
		return __( 'Configuration for the visual appearance of the generated articles', 'apple-news' );
	}

	/**
	 * HTML to display before the section.
	 *
	 * @return string
	 * @access public
	 */
	public function before_section() {
		print_r( $this->settings );
		?>
		<div id="apple-news-formatting">
			<div class="apple-news-settings-left">
		<?php
	}

	/**
	 * HTML to display after the section.
	 *
	 * @return string
	 * @access public
	 */
	public function after_section() {
		?>
			</div>
			<div class="apple-news-settings-preview">
				<?php
					// Build sample content
					$title = __( 'Sample Article', 'apple-news' );
					$author = __( 'John Doe', 'apple-news' );
					$date = time();
					$pullquote = __( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit', 'apple-news' );
					$image = '<div class="apple-news-featured-image"></div>';
				?>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris sagittis, augue vitae iaculis euismod, libero nulla pellentesque quam, non venenatis massa odio id dolor. Vestibulum accumsan metus ut urna aliquet imperdiet. In malesuada lorem sed dapibus porta. Vivamus feugiat pellentesque feugiat. Donec id nunc ut orci tincidunt commodo. Sed turpis nunc, eleifend a odio quis, convallis vulputate leo. Aenean auctor ante lorem, sed consequat neque varius nec. Nullam semper, sapien sit amet cursus tristique, est leo sodales eros, a dignissim nisl augue et ligula. Nam malesuada nunc eros, vitae faucibus nibh tincidunt non.</p>
				<p>Praesent eget odio vel sapien scelerisque euismod. Phasellus eros sapien, rutrum ac nibh nec, tristique commodo neque. Cras non risus nec justo fringilla sodales. Donec ullamcorper quis nisi id egestas. Mauris ut enim risus. Cras porta, lectus sit amet vehicula auctor, tortor odio placerat sem, in mollis metus est id dolor. Vivamus ultricies justo eu sapien elementum tincidunt.</p>
				<p>Quisque efficitur sit amet ex et venenatis. Morbi nisi nisi, ornare id iaculis eget, pulvinar ac dolor. Nam rutrum eros non neque ornare semper. Proin urna ipsum, consectetur et interdum sed, faucibus sit amet orci. Mauris fermentum efficitur ligula sed blandit. In vel diam imperdiet, blandit metus et, suscipit ligula. Aliquam erat volutpat. Nunc dapibus in tellus vulputate volutpat.</p>
				<p>In eu lacus porttitor, pellentesque diam et, tristique elit. Mauris justo odio, efficitur sit amet aliquet id, aliquam placerat turpis. Donec sit amet sem dictum, pharetra lectus at, blandit diam. Maecenas posuere, nibh nec iaculis ultrices, leo eros faucibus arcu, vitae ullamcorper dui lacus ut ligula. Donec magna tortor, cursus id felis in, dignissim porta elit. Duis et malesuada nisi, sit amet lobortis est. Suspendisse vitae porta urna. Pellentesque vehicula pulvinar risus non commodo. Aenean ornare euismod velit a commodo. Ut dapibus scelerisque mi, dignissim volutpat ante semper quis. Quisque ipsum mi, mattis nec dapibus sed, vestibulum sed nisi.</p>
				<p>Nullam viverra magna elit, sit amet condimentum augue sagittis euismod. Curabitur sed eros velit. Sed aliquam augue id libero commodo, quis rhoncus mi porta. Praesent viverra condimentum est, efficitur malesuada ligula tincidunt sed. Nulla venenatis est eu mi placerat, a posuere ante rutrum. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla porttitor rhoncus sem, quis semper felis viverra fermentum. Integer faucibus, odio fringilla iaculis semper, magna leo ornare tellus, nec malesuada lorem massa a justo. Vestibulum vitae ex ipsum. Duis fringilla risus turpis, luctus consectetur dolor mattis nec. Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
			</div>
		</div>
		<?php
	}

	/**
	 * Renders the component order field.
	 *
	 * @static
	 * @access public
	 */
	public static function render_meta_component_order() {
		?>
		<ul id="meta-component-order-sort" class="component-order ui-sortable">
			<?php
				// Get the current order
				$component_order = self::get_value( 'meta_component_order' );
				if ( ! empty( $component_order ) && is_array( $component_order ) ) {
					foreach ( $component_order as $component_name ) {
						echo sprintf(
							'<li id="%s" class="ui-sortable-handle">%s</li>',
							esc_attr( $component_name ),
							esc_html( ucwords( $component_name ) )
						);
					}
				}
			?>
		</ul>
		<p class="description"><?php esc_html_e( 'Drag to set the order of the meta components at the top of the article. These include the title, the cover (i.e. featured image) and byline which also includes the date.', 'apple-news' ) ?></p>
		<?php
	}

}
