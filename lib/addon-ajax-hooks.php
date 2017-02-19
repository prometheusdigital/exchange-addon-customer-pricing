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
		if ( 0 == $count )
			$add_new_first = true;
		else
			$add_new_first = false;
		$return .= it_exchange_customer_pricing_addon_build_price_option( false, $count, $add_new_first );
	}
	die( $return );
}
add_action( 'wp_ajax_it-exchange-customer-pricing-add-new-price', 'it_exchange_customer_pricing_ajax_add_new_price' );

/**
 * AJAX function called to format price inputs on customer pricing options meta box
 *
 * @since 1.0.0
 * @return string formated price
*/
function it_exchange_customer_pricing_ajax_format_prices() {
	$price = 0;
	if ( isset( $_POST['price'] ) )
		$price = it_exchange_convert_to_database_number( $_POST['price'] );
	
	if ( !empty( $_POST['max'] ) && 'true' == $_POST['max'] && 0 == $price )
		die( __( 'No Limit', 'LION' ) );

	die( esc_html( it_exchange_format_price( it_exchange_convert_from_database_number( $price ) ) ) );
}
add_action( 'wp_ajax_it-exchange-customer-pricing-format-prices', 'it_exchange_customer_pricing_ajax_format_prices' );

/**
 * AJAX function called to add new price option rows
 * Also updates the customer-pricing session data with customer's choice
 *
 * @since 1.0.0
 * @return string json encoded array with db_price and formated price
*/
function it_exchange_customer_pricing_ajax_format_nyop_input() {
	$return = '';

	if ( ! isset( $_POST['input'] ) || empty( $_POST['post_id'] ) ) {
		die( json_encode( $return ) );
	}

	$post_id = $_POST['post_id'];
	$price   = it_exchange_convert_from_database_number( it_exchange_convert_to_database_number( $_POST['input'] ) );
	$price   = it_exchange_is_customer_pricing_product_selected_price_valid( $price, $post_id, true );

	$formatted = it_exchange_format_price( $price );

	/**
	 * Filter the selected price for a product.
	 *
	 * @since 1.0.0
	 *
	 * @param string $formatted
	 * @param int    $post_id
	 */
	$formatted = apply_filters( 'it_exchange_customer_pricing_product_price', $formatted, $post_id );

	$return = array(
		'db_price' => it_exchange_convert_to_database_number( $price ),
		'price'    => esc_html( $formatted ),
	);

	die( json_encode( $return ) );
}
add_action( 'wp_ajax_it-exchange-customer-pricing-format-nyop-input', 'it_exchange_customer_pricing_ajax_format_nyop_input' );
add_action( 'wp_ajax_nopriv_it-exchange-customer-pricing-format-nyop-input', 'it_exchange_customer_pricing_ajax_format_nyop_input' );

/**
 * AJAX function called to update the customer-pricing session data with customer's choice
 *
 * @since 1.0.0
 *
 * @return void
*/
function it_exchange_customer_pricing_session() {
	$price = 0;

	if ( ! isset( $_POST['input'] ) || empty( $_POST['post_id'] ) ) {
		die( $price );
	}

	$post_id  = $_POST['post_id'];
	$price    = it_exchange_convert_from_database_number( $_POST['input'] );
	$price    = it_exchange_is_customer_pricing_product_selected_price_valid( $price, $post_id, true );

	$formatted = it_exchange_format_price( $price );

	// This filter is documented in lib/addon-ajax-hooks.php
	$formatted = apply_filters( 'it_exchange_customer_pricing_product_price', $formatted, $post_id );

	die( $formatted );
}
add_action( 'wp_ajax_it-exchange-customer-pricing-session', 'it_exchange_customer_pricing_session' );
add_action( 'wp_ajax_nopriv_it-exchange-customer-pricing-session', 'it_exchange_customer_pricing_session' );
