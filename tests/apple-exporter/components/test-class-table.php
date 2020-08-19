<?php
/**
 * Publish to Apple News tests: Table_Test class
 *
 * @package Apple_News
 * @subpackage Tests
 */

use Apple_Exporter\Exporter;
use Apple_Exporter\Exporter_Content;
use Apple_Exporter\Components\Table;

/**
 * A class to test the behavior of the
 * Apple_Exporter\Components\Table class.
 *
 * @package Apple_News
 * @subpackage Tests
 */
class Table_Test extends Component_TestCase {

	/**
	 * Instructions to be executed before each test.
	 *
	 * @access public
	 */
	public function setUp() {

		// Run the parent setup function (not done automatically).
		parent::setup();

		// Create an example table to use in tests.
		$this->html = <<<HTML
<table>
	<thead>
		<tr>
			<th>Column Header 1</th>
			<th>Column Header 2</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>Column Data 1</td>
			<td>Column Data 2</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td>Column Footer 1</td>
			<td>Column Footer 2</td>
		</tr>
	</tfoot>
</table>
HTML;

		$this->html_caption = '<figure class="wp-block-table"><table><thead><tr><th>Column Header 1</th><th>Column Header 2</th></tr></thead><tbody><tr><td>Column Data 1</td><td>Column Data 2</td></tr></tbody><tfoot><tr><td>Column Footer 1</td><td>Column Footer 2</td></tr></tfoot></table><figcaption>Caption</figcaption></figure>';
	}

	/**
	 * Tests HTML formatting with captions.
	 *
	 * @access public
	 */
	public function testCaptions() {
		$component = new Table(
			$this->html_caption,
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts,
			null,
			$this->component_styles
		);

		// Test.
		$this->assertEquals(
			array(
				'role' => 'container',
				'components' => array(
					array(
						'role' => 'htmltable',
						'html' => '<table><thead><tr><th>Column Header 1</th><th>Column Header 2</th></tr></thead><tbody><tr><td>Column Data 1</td><td>Column Data 2</td></tr></tbody><tfoot><tr><td>Column Footer 1</td><td>Column Footer 2</td></tr></tfoot></table>',
						'layout' => 'table-layout',
						'style' => 'default-table',
					),
					array(
						'role' => 'caption',
						'text' => 'Caption',
						'format' => 'html',
					)
				)
			),
			$component->to_array()
		);
	}

	/**
	 * Tests HTML formatting.
	 *
	 * @access public
	 */
	public function testHTML() {

		// Setup.
		$component = new Table(
			$this->html,
			$this->workspace,
			$this->settings,
			$this->styles,
			$this->layouts,
			null,
			$this->component_styles
		);

		// Test.
		$this->assertEquals(
			array(
				'html' => $this->html,
				'layout' => 'table-layout',
				'role' => 'htmltable',
				'style' => 'default-table',
			),
			$component->to_array()
		);
	}


	/**
	 * Tests table settings.
	 *
	 * @access public
	 */
	public function testDarkColors() {

		// Setup.
		$content = new Exporter_Content(
			3,
			'Title',
			$this->html
		);

		// Set table settings.
		$this->set_theme_settings(
			[
				'table_border_color_dark'                => '#abcdef',
				'table_body_background_color_dark'       => '#fedcba',
				'table_body_color_dark'                  => '#123456',
				'table_header_background_color_dark'     => '#654321',
				'table_header_color_dark'                => '#987654',
			]
		);

		// Run the export.
		$exporter = new Exporter( $content, $this->workspace, $this->settings );
		$json = $exporter->export();
		$this->ensure_tokens_replaced( $json );
		$json = json_decode( $json, true );

		// Ensure conditionals are set
		// Outer Table Border.
		$this->assertTrue( isset( $json['componentStyles']['default-table']['conditional'] ) );
		// Background Color, Text Color.
		$this->assertTrue( isset( $json['componentStyles']['default-table']['tableStyle']['cells']['conditional'] ) );
		// Column Border.
		$this->assertTrue( isset( $json['componentStyles']['default-table']['tableStyle']['columns']['conditional'] ) );
		// Row Border.
		$this->assertTrue( isset( $json['componentStyles']['default-table']['tableStyle']['rows']['conditional'] ) );
		// Header Border
		$this->assertTrue( isset( $json['componentStyles']['default-table']['tableStyle']['headerRows']['conditional'] ) );
		// Header Background, Header Text Color
		$this->assertTrue( isset( $json['componentStyles']['default-table']['tableStyle']['headerCells']['conditional'] ) );

		// Ensure Color Values match
		$this->assertEquals(
			'#abcdef',
			$json['componentStyles']['default-table']['conditional']['border']['all']['color']
		);
		$this->assertEquals(
			'#fedcba',
			$json['componentStyles']['default-table']['tableStyle']['cells']['conditional'][0]['backgroundColor']
		);
		$this->assertEquals(
			'#123456',
			$json['componentStyles']['default-table']['tableStyle']['cells']['conditional'][0]['textStyle']['textColor']
		);

		$this->assertEquals(
			'#abcdef',
			$json['componentStyles']['default-table']['tableStyle']['columns']['conditional'][0]['divider']['color']
		);
		$this->assertEquals(
			'#abcdef',
			$json['componentStyles']['default-table']['tableStyle']['rows']['conditional'][0]['divider']['color']
		);
		$this->assertEquals(
			'#abcdef',
			$json['componentStyles']['default-table']['tableStyle']['headerRows']['conditional'][0]['divider']['color']
		);

		$this->assertEquals(
			'#654321',
			$json['componentStyles']['default-table']['tableStyle']['headerCells']['conditional'][0]['backgroundColor']
		);
		$this->assertEquals(
			'#987654',
			$json['componentStyles']['default-table']['tableStyle']['headerCells']['conditional'][0]['textStyle']['textColor']
		);
	}

	/**
	 * Tests table settings.
	 *
	 * @access public
	 */
	public function testSettings() {

		// Setup.
		$content = new Exporter_Content(
			3,
			'Title',
			$this->html
		);

		// Set table settings.
		$this->set_theme_settings(
			[
				'table_border_color'                => '#abcdef',
				'table_border_style'                => 'dashed',
				'table_border_width'                => 5,
				'table_body_background_color'       => '#fedcba',
				'table_body_color'                  => '#123456',
				'table_body_font'                   => 'AmericanTypewriter',
				'table_body_horizontal_alignment'   => 'center',
				'table_body_line_height'            => 1,
				'table_body_padding'                => 2,
				'table_body_size'                   => 3,
				'table_body_tracking'               => 4,
				'table_body_vertical_alignment'     => 'bottom',
				'table_header_background_color'     => '#654321',
				'table_header_color'                => '#987654',
				'table_header_font'                 => 'Menlo-Regular',
				'table_header_horizontal_alignment' => 'right',
				'table_header_line_height'          => 5,
				'table_header_padding'              => 6,
				'table_header_size'                 => 7,
				'table_header_tracking'             => 8,
				'table_header_vertical_alignment'   => 'top',
			]
		);

		// Run the export.
		$exporter = new Exporter( $content, $this->workspace, $this->settings );
		$json = $exporter->export();
		$this->ensure_tokens_replaced( $json );
		$json = json_decode( $json, true );

		// Validate table layout in generated JSON.
		$this->assertEquals(
			array(
				'margin' => array(
					'bottom' => 1,
				),
			),
			$json['componentLayouts']['table-layout']
		);

		// Ensure conditionals are not set
		// Outer Table Border.
		$this->assertFalse( isset( $json['componentStyles']['default-table']['conditional'] ) );
		// Background Color, Text Color.
		$this->assertFalse( isset( $json['componentStyles']['default-table']['tableStyle']['cells']['conditional'] ) );
		// Column Border.
		$this->assertFalse( isset( $json['componentStyles']['default-table']['tableStyle']['columns']['conditional'] ) );
		// Row Border.
		$this->assertFalse( isset( $json['componentStyles']['default-table']['tableStyle']['rows']['conditional'] ) );
		// Header Border
		$this->assertFalse( isset( $json['componentStyles']['default-table']['tableStyle']['headerRows']['conditional'] ) );
		// Header Background, Header Text Color
		$this->assertFalse( isset( $json['componentStyles']['default-table']['tableStyle']['headerCells']['conditional'] ) );

		// Validate table settings in generated JSON.
		$this->assertEquals(
			array(
				'border' => array(
					'all' => array(
						'color' => '#abcdef',
						'style' => 'dashed',
						'width' => 5,
					),
				),
				'tableStyle' => array(
					'cells' => array(
						'backgroundColor' => '#fedcba',
						'horizontalAlignment' => 'center',
						'padding' => 2,
						'textStyle' => array(
							'fontName' => 'AmericanTypewriter',
							'fontSize' => 3,
							'lineHeight' => 1,
							'textColor' => '#123456',
							'tracking' => 4,
						),
						'verticalAlignment' => 'bottom',
					),
					'columns' => array(
						'divider' => array(
							'color' => '#abcdef',
							'style' => 'dashed',
							'width' => 5,
						),
					),
					'headerCells' => array(
						'backgroundColor' => '#654321',
						'horizontalAlignment' => 'right',
						'padding' => 6,
						'textStyle' => array(
							'fontName' => 'Menlo-Regular',
							'fontSize' => 7,
							'lineHeight' => 5,
							'textColor' => '#987654',
							'tracking' => 8,
						),
						'verticalAlignment' => 'top',
					),
					'headerRows' => array(
						'divider' => array(
							'color' => '#abcdef',
							'style' => 'dashed',
							'width' => 5,
						),
					),
					'rows' => array(
						'divider' => array(
							'color' => '#abcdef',
							'style' => 'dashed',
							'width' => 5,
						),
					),
				),
			),
			$json['componentStyles']['default-table']
		);
	}
}
