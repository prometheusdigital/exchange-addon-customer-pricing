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
			
		$return  = '<div class="it-exchange-customer-pricing-option columns-wrapper">';
		$return .= it_exchange_customer_pricing_addon_build_price_option( false, $count );
		$return .= '</div>';
	
	}
	
	die( $return );
}
add_action( 'wp_ajax_it-exchange-customer-pricing-add-new-price', 'it_exchange_customer_pricing_ajax_add_new_price' );

/**
 * AJAX function called to add new price option rows
 *
 * @since 1.0.0
 * @return string HTML output of content access rule row div
*/
function it_exchange_customre_pricing_ajax_format_nyop_input() {
	
	$return = '';
	error_log( $_POST['input'] );
	
	if ( isset( $_POST['input'] ) && !empty( $_POST['post_id'] ) ) {
		
		$price = $_POST['input'];
		
		$nyop_min = it_exchange_get_product_feature( $_POST['post_id'], 'customer-pricing', array( 'setting' => 'nyop_min' ) );
		$nyop_max = it_exchange_get_product_feature( $_POST['post_id'], 'customer-pricing', array( 'setting' => 'nyop_max' ) );
		
		if ( !empty( $nyop_min ) && 0 < $nyop_min ) {
			
			$min = it_exchange_convert_from_database_number( $nyop_min );
			if ( $price < $min )
				$price = $min;
			
		}
		
		
		if ( !empty( $nyop_max ) && 0 < $nyop_max ) {
			
			$max = it_exchange_convert_from_database_number( $nyop_max );
			if ( $price > $max )
				$price = $max;
			
		}
		
		$return = it_exchange_format_price( $price );
		
	}
	
	die( $return );
}
add_action( 'wp_ajax_it-exchange-customer-pricing-format-nyop-input', 'it_exchange_customre_pricing_ajax_format_nyop_input' );
add_action( 'wp_ajax_nopriv_it-exchange-customer-pricing-format-nyop-input', 'it_exchange_customre_pricing_ajax_format_nyop_input' );