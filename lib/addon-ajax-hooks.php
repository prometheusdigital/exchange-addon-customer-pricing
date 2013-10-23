<?php
/**
 * iThemes Exchange Customer Pricing Add-on
 * @package IT_Exchange_Addon_Customer_Pricing
 * @since 1.0.0
*/

/**
 * AJAX function called to add new price option rows
 *
 * @since 1.0.0
 * @return string HTML output of content access rule row div
*/
function it_exchange_customer_pricing_ajax_add_new_price() {
	$return = '';
	if ( isset( $_REQUEST['count'] ) ) { //use isset() in case count is 0
		$count = $_REQUEST['count'];
		$return .= it_exchange_customer_pricing_addon_build_price_option( false, $count );
	}
	die( $return );
}
add_action( 'wp_ajax_it-exchange-customer-pricing-add-new-price', 'it_exchange_customer_pricing_ajax_add_new_price' );

/**
 * AJAX function called to add new price option rows
 * Also updates the customer-pricing session data with customer's choice
 *
 * @since 1.0.0
 * @return string json encoded array with db_price and formated price
*/
function it_exchange_customre_pricing_ajax_format_nyop_input() {
	$return = '';
	if ( isset( $_POST['input'] ) && !empty( $_POST['post_id'] ) ) {
		$price = $_POST['input'];
		$price = it_exchange_convert_to_database_number( $price );
		$post_id = $_POST['post_id'];
		
		$nyop_min = it_exchange_get_product_feature( $post_id, 'customer-pricing', array( 'setting' => 'nyop_min' ) );
		$nyop_max = it_exchange_get_product_feature( $post_id, 'customer-pricing', array( 'setting' => 'nyop_max' ) );
		
		if ( !empty( $nyop_min ) && 0 < $nyop_min ) {
			if ( $price < $nyop_min )
				$price = $nyop_min;
		}
		
		if ( !empty( $nyop_max ) && 0 < $nyop_max ) {
			if ( $price > $nyop_max )
				$price = $nyop_max;
		}
		
		$return = array( 
			'db_price' => $price, 
			'price' => it_exchange_format_price( it_exchange_convert_from_database_number( $price ) ) 
		);
		
		$customer_prices = (array)it_exchange_get_session_data( 'customer-prices' );
		$customer_prices[$post_id] = $return['db_price'];
		it_exchange_update_session_data( 'customer-prices', $customer_prices );
	}
	die( json_encode( $return ) );
}
add_action( 'wp_ajax_it-exchange-customer-pricing-format-nyop-input', 'it_exchange_customre_pricing_ajax_format_nyop_input' );
add_action( 'wp_ajax_nopriv_it-exchange-customer-pricing-format-nyop-input', 'it_exchange_customre_pricing_ajax_format_nyop_input' );
/**
 * AJAX function called to update the customer-pricing session data with customer's choice
 *
 * @since 1.0.0
 * @return void
*/
function it_exchange_customer_pricing_session() {
	if ( isset( $_POST['input'] ) && !empty( $_POST['post_id'] ) ) {
		$price = $_POST['input'];
		$price = $price;
		$post_id = $_POST['post_id'];
		
		$nyop_min = it_exchange_get_product_feature( $post_id, 'customer-pricing', array( 'setting' => 'nyop_min' ) );
		$nyop_max = it_exchange_get_product_feature( $post_id, 'customer-pricing', array( 'setting' => 'nyop_max' ) );
		
		if ( !empty( $nyop_min ) && 0 < $nyop_min ) {
			if ( $price < $nyop_min )
				$price = $nyop_min;
		}
		
		if ( !empty( $nyop_max ) && 0 < $nyop_max ) {
			if ( $price > $nyop_max )
				$price = $nyop_max;
		}
		
		$customer_prices = (array)it_exchange_get_session_data( 'customer-prices' );
		$customer_prices[$post_id] = $price;
		it_exchange_update_session_data( 'customer-prices', $customer_prices );
	}
	die();
}
add_action( 'wp_ajax_it-exchange-customer-pricing-session', 'it_exchange_customer_pricing_session' );
add_action( 'wp_ajax_nopriv_it-exchange-customer-pricing-session', 'it_exchange_customer_pricing_session' );
