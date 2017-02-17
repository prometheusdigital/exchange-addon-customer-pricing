<?php
/**
 * iThemes Exchange Customer Pricing Add-on
 *
 * @package IT_Exchange_Addon_Customer_Pricing
 * @since   1.0.0
 */

/**
 * Builds pricing option row for WP Dashboard Product Edit Page
 *
 * @since 1.0.0
 *
 * @param array $price_option  Current Price Option
 * @param int   $count         Current Row count
 * @param bool  $add_new_first (optional) If this is the first rule being added, set to true
 *
 * @return string HTML formated price option row
 */
function it_exchange_customer_pricing_addon_build_price_option( $price_option, $count, $add_new_first = false ) {

	$price   = empty( $price_option['price'] ) ? '' : it_exchange_format_price( it_exchange_convert_from_database_number( $price_option['price'] ) );
	$label   = empty( $price_option['label'] ) ? '' : $price_option['label'];
	$default = empty( $price_option['default'] ) ? 'unchecked' : $price_option['default'];

	$return = '<div class="it-exchange-customer-pricing-option">';

	$return .= '<div class="it-exchange-customer-pricing-content-price-option columns-wrapper" data-count="' . $count . '">';

	$return .= '<div class="it-exchange-customer-pricing-content-price column">';
	$return .= '<div class="column-inner">';
	$return .= '<input type="text" class="it-exchange-customer-pricing-price" name="it-exchange-customer-pricing-options[' . $count . '][price]" value="' . $price . '" />';
	$return .= '</div>';
	$return .= '</div>';

	$return .= '<div class="it-exchange-customer-pricing-content-label column">';
	$return .= '<div class="column-inner">';
	$return .= '<input type="text" class="it-exchange-customer-pricing-label" name="it-exchange-customer-pricing-options[' . $count . '][label]" value="' . $label . '" />';
	$return .= '<div class="it-exchange-customer-pricing-content-default">';
	if ( 'checked' == $default || $add_new_first ) {
		$return .= '<span class="it-exchange-customer-pricing-content-default-checkmark it-exchange-customer-pricing-content-default-checkmark-checked"></span>';
		$return .= '<input type="hidden" class="it-exchange-customer-pricing-default" name="it-exchange-customer-pricing-options[' . $count . '][default]" value="checked" />';
	} else {
		$return .= '<span class="it-exchange-customer-pricing-content-default-checkmark"></span>';
		$return .= '<input type="hidden" class="it-exchange-customer-pricing-default" name="it-exchange-customer-pricing-options[' . $count . '][default]" value="unchecked" />';
	}
	$return .= '</div>';
	$return .= '</div>';
	$return .= '</div>';

	$return .= '<div class="it-exchange-customer-pricing-remove-option it-exchange-remove-item-wrapper column">';
	$return .= '<div class="column-inner">';
	$return .= '<a href="#" class="it-exchange-remove-item">×</a>';
	$return .= '</div>';
	$return .= '</div>';

	$return .= '</div>';

	$return .= '</div>';

	return $return;
}

/**
 * Is the selected customer price valid for a given product.
 *
 * @since 1.3.3
 *
 * @param float                   $price   The chosen price.
 * @param int|IT_Exchange_Product $product The product.
 * @param bool                    $coerce  Whether to coerce the given value to a valid price.
 *
 * @return bool|float False if invalid product or invalid price. True if valid price. Float when coercing the price.
 */
function it_exchange_is_customer_pricing_product_selected_price_valid( $price, $product, $coerce = false ) {

	$product = it_exchange_get_product( $product );
	$default = it_exchange_get_product_default_customer_price( $product );

	if ( ! $product ) {
		return false;
	}

	if ( $default === false ) {
		return $coerce ? $product->get_feature( 'base-price' ) : false;
	}

	if ( ! is_numeric( $price ) ) {
		return $coerce ? $default : false;
	}

	$price = (float) $price;

	if ( $price === $default ) {
		return $coerce ? $default : false;
	}

	$price_options = $product->get_feature( 'customer-pricing', array( 'setting' => 'pricing-options' ) );

	if ( ! empty( $price_options ) && is_array( $price_options ) ) {
		foreach ( $price_options as $price_option ) {
			if ( (float) it_exchange_convert_from_database_number( $price_option['price'] ) === $price ) {
				return $coerce ? $price : true;
			}
		}
	}

	if ( $product->get_feature( 'customer-pricing', array( 'setting' => 'nyop_enabled' ) ) === 'yes' ) {
		$min = (float) it_exchange_convert_from_database_number(
			$product->get_feature( 'customer-pricing', array( 'setting' => 'nyop_min' ) )
		);
		$max = (float) it_exchange_convert_from_database_number(
			$product->get_feature( 'customer-pricing', array( 'setting' => 'nyop_max' ) )
		);

		$_price = $price;

		if ( ! empty( $min ) && $min > 0 ) {
			$_price = max( $min, $_price );
		}

		if ( ! empty( $max ) && $max > 0 ) {
			$_price = min( $max, $_price );
		}

		if ( $price === $_price ) {
			return $coerce ? $price : true;
		} else {
			if ( $coerce ) {
				return $price > $_price ? $max : $min;
			} else {
				return false;
			}
		}
	}

	return $coerce ? $default : false;
}

/**
 * Get the default customer price for a product.
 *
 * @since 1.3.3
 *
 * @param int|IT_Exchange_Product $product
 *
 * @return float|false
 */
function it_exchange_get_product_default_customer_price( $product ) {

	$product = it_exchange_get_product( $product );

	if ( ! $product ) {
		return false;
	}

	if ( ! $product->has_feature( 'customer-pricing', array( 'setting' => 'enabled' ) ) ) {
		return false;
	}

	$enabled = $product->get_feature( 'customer-pricing', array( 'setting' => 'enabled' ) );

	if ( $enabled !== 'yes' ) {
		return false;
	}

	if ( $product->get_feature( 'customer-pricing', array( 'setting' => 'nyop_enabled' ) ) === 'yes' ) {
		$min  = it_exchange_convert_from_database_number(
			$product->get_feature( 'customer-pricing', array( 'setting' => 'nyop_min' ) )
		);
		$max  = it_exchange_convert_from_database_number(
			$product->get_feature( 'customer-pricing', array( 'setting' => 'nyop_max' ) )
		);
		$base = (float) $product->get_feature( 'base-price' );

		if ( $min < $base && $max > $base ) {
			return $base;
		}

		return ( $min + $max ) / 2;
	}

	$price_options = $product->get_feature( 'customer-pricing', array( 'setting' => 'pricing-options' ) );

	if ( empty( $price_options ) || ! is_array( $price_options ) ) {
		return (float) $product->get_feature( 'base-price' );
	}

	$most_expensive = 0.0;

	foreach ( $price_options as $price_option ) {
		$price = it_exchange_convert_from_database_number( $price_option['price'] );

		if ( 'checked' === $price_option['default'] ) {
			return $price;
		}

		if ( $price > $most_expensive ) {
			$most_expensive = $price;
		}
	}

	if ( $most_expensive ) {
		return $most_expensive;
	}

	return (float) $product->get_feature( 'base-price' );
}