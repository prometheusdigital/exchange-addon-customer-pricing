<?php
/*
 * Plugin Name: ExchangeWP - Customer Pricing Add-on
 * Version: 0.0.1
 * Description: Adds the customer pricing to ExchangeWP
 * Plugin URI: https://exchangewp.com/downloads/customer-pricing/
 * Author: ExchangeWP
 * Author URI: http://exchangewp.com
 * ExchangeWP Package: exchange-addon-customer-pricing

 * Installation:
 * 1. Download and unzip the latest release zip file.
 * 2. If you use the WordPress plugin uploader to install this plugin skip to step 4.
 * 3. Upload the entire plugin directory to your `/wp-content/plugins/` directory.
 * 4. Activate the plugin through the 'Plugins' menu in WordPress Administration.
 *
*/

/**
 * This registers our plugin as a customer pricing addon
 *
 * @since 1.0.0
 *
 * @return void
*/
function it_exchange_register_customer_pricing_addon() {
	$options = array(
		'name'              => __( 'Customer Pricing', 'LION' ),
		'description'       => __( 'Let customers choose their price from a list of price options you create or let them enter their own price.', 'LION' ),
		'author'            => 'ExchangeWP',
		'author_url'        => 'https://exchangewp.com/downloads/customer-pricing/',
		'icon'              => ITUtility::get_url_from_file( dirname( __FILE__ ) . '/lib/images/customerpricing50px.png' ),
		'file'              => dirname( __FILE__ ) . '/init.php',
		'category'          => 'pricing',
		'basename'          => plugin_basename( __FILE__ ),
		'labels'      => array(
			'singular_name' => __( 'Customer Pricing', 'LION' ),
		),
		'settings-callback' => 'it_exchange_customer_pricing_settings_callback',
	);
	it_exchange_register_addon( 'customer-pricing', $options );
}
add_action( 'it_exchange_register_addons', 'it_exchange_register_customer_pricing_addon' );

/**
 * Loads the translation data for WordPress
 *
 * @uses load_plugin_textdomain()
 * @since 1.0.0
 * @return void
*/
function it_exchange_customer_pricing_set_textdomain() {
	load_plugin_textdomain( 'LION', false, dirname( plugin_basename( __FILE__  ) ) . '/lang/' );
}
add_action( 'plugins_loaded', 'it_exchange_customer_pricing_set_textdomain' );

/**
 * Registers Plugin with iThemes updater class
 *
 * @since 1.0.0
 *
 * @param object $updater ithemes updater object
 * @return void
*/
function exchange_customer_pricing_plugin_updater() {

	$license_check = get_transient( 'exchangewp_license_check' );

	if ($license_check->license == 'valid' ) {
		$license_key = it_exchange_get_option( 'exchangewp_licenses' );
		$license = $license_key['exchange_license'];

		$edd_updater = new EDD_SL_Plugin_Updater( 'https://exchangewp.com', __FILE__, array(
				'version' 		=> '0.0.1', 				// current version number
				'license' 		=> $license, 		// license key (used get_option above to retrieve from DB)
				'item_name' 	=> urlencode('Customer Pricing'), 	  // name of this plugin
				'author' 	  	=> 'ExchangeWP',    // author of this plugin
				'url'       	=> home_url(),
				'wp_override' => true,
				'beta'		  	=> false
			)
		);
	}

}

add_action( 'admin_init', 'exchange_customer_pricing_plugin_updater', 0 );
