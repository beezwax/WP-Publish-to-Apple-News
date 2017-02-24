<?php

//use Apple_Exporter\Exporter_Content as Exporter_Content;
use Apple_Exporter\Settings as Settings;
use Apple_Exporter\Builders\Component_Layouts as Component_Layouts;
use Apple_Exporter\Builders\Component_Text_Styles as Component_Text_Styles;
use Apple_Exporter\Components\Advertisement as Advertisement;

class Admin_Apple_JSON_Test extends WP_UnitTestCase {

	public function setup() {
		parent::setup();

		$this->settings = new Settings();
//		$this->content  = new Exporter_Content( 1, __( 'My Title', 'apple-news' ), '<p>' . __( 'Hello, World!', 'apple-news' ) . '</p>' );
		$this->styles   = new Component_Text_Styles( $this->content, $this->settings );
		$this->layouts  = new Component_Layouts( $this->content, $this->settings );
	}

	public function testJSONSaveCustomSpec() {
		$json = <<<JSON
{
    "role": "banner_advertisement",
    "bannerType": "double_height"
}
JSON;
		$nonce = wp_create_nonce( 'apple_news_json' );
		$_POST['apple_news_component'] = 'Advertisement';
		$_POST['apple_news_action'] = 'apple_news_save_json';
		$_POST['apple_news_json_json'] = $json;
		$_POST['page'] = 'apple-news-json';
		$_POST['redirect'] = false;
		$_REQUEST['_wp_http_referer'] = '/wp-admin/admin.php?page=apple-news-json';
		$_REQUEST['_wpnonce'] = $nonce;

		$admin_json = new \Admin_Apple_JSON();
		$admin_json->action_router();

		$stored_json_specs = get_option( 'apple_news_json_advertisement' );
		$stored_json = json_encode( $stored_json_specs['apple_news_json_json'], JSON_PRETTY_PRINT );
		$this->assertEquals( $stored_json, $json );
	}

	public function testJSONUseCustomSpec() {
		// Just manually create a custom spec
		update_option( 'apple_news_json_advertisement', array(
			'apple_news_json_json' => array(
				'role' => 'banner_advertisement',
   				'bannerType' => 'double_height',
			),
		) );

		$component = new Advertisement( null, null, $this->settings, $this->styles,
			$this->layouts );
		$json = $component->to_array();

		$this->assertEquals( 'banner_advertisement', $json['role'] );
		$this->assertEquals( 'double_height', $json['bannerType'] );
	}
}

