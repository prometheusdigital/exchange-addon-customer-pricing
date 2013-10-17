<?php
/**
 * iThemes Exchange Customer Pricing Add-on
 * @package IT_Exchange_Addon_Customer_Pricing
 * @since 1.0.0
*/

function it_exchange_customer_pricing_addon_build_price_option( $price_option, $count ) {
	
	$price = empty( $price_option['price'] ) ? '' : it_exchange_format_price( it_exchange_convert_from_database_number( $price_option['price'] ) );
	$label  = empty( $price_option['label'] ) ? '' : $price_option['label'];
	$default  = empty( $price_option['default'] ) ? 'unchecked' : $price_option['default'];
	
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
	if ( 'checked' == $default ) {
		$return .= '<span class="it-exchange-customer-pricing-content-default-checkmark it-exchange-customer-pricing-content-default-checkmark-checked"></span>';	
	} else {
		$return .= '<span class="it-exchange-customer-pricing-content-default-checkmark"></span>';
	}
	$return .= '<input type="hidden" class="it-exchange-customer-pricing-default" name="it-exchange-customer-pricing-options[' . $count . '][default]" value="' . $default . '" />';
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