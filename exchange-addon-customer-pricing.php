<?php
/*
 * Plugin Name: iThemes Exchange - Customer Pricing Add-on
 * Version: 1.0.5
 * Description: Adds the customer pricing to iThemes Exchange
 * Plugin URI: http://ithemes.com/exchange/customer-pricing/
 * Author: iThemes
 * Author URI: http://ithemes.com
 * iThemes Package: exchange-addon-customer-pricing
 
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
		'author'            => 'iThemes',
		'author_url'        => 'http://ithemes.com/exchange/customer-pricing/',
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
function ithemes_exchange_addon_customer_pricing_updater_register( $updater ) { 
	    $updater->register( 'exchange-addon-customer-pricing', __FILE__ );
}
add_action( 'ithemes_updater_register', 'ithemes_exchange_addon_customer_pricing_updater_register' );
require( dirname( __FILE__ ) . '/lib/updater/load.php' );
