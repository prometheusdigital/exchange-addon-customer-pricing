jQuery(document).ready(function($) {
	$( '#it-exchange-customer-pricing-enable' ).on( 'click', function( event ) {
		$( '.it-exchange-pricing-enabled' ).toggle();
		if  ( $( this ).attr('checked') ) {
			$( '#base-price-customer-pricing-disabled' ).hide();
			$( '#base-price-customer-pricing-enabled' ).show();
		} else {
			$( '#base-price-customer-pricing-enabled' ).hide();
			$( '#base-price-customer-pricing-disabled' ).show();
		}
	});
	
	$( '#it-exchange-customer-pricing-enable-nyop' ).on( 'click', function( event ) {
		$( '.it-exchange-customer-pricing-nyop-min-max' ).toggle();
	});
	
	$( '.it-exchange-customer-pricing-add-new-price' ).on( 'click', function( event ) {
		event.preventDefault();
		var parent = $( this ).parent();
		var data = {
			'action': 'it-exchange-customer-pricing-add-new-price',
			'count':  it_exchange_customer_pricing_addon_price_options_interation,
		}
		it_exchange_customer_pricing_addon_price_options_interation++;
		$.post( ajaxurl, data, function( response ) {
			console.log( response );
			$( '.it-exchange-customer-pricing-list' ).removeClass('hidden');
			$( '.it-exchange-customer-pricing-no-prices' ).addClass('hidden');
			$( '.it-exchange-customer-pricing-addon-pricing' ).append( response );
		});
	});
	
	$( '.it-exchange-customer-pricing-remove-option' ).live( 'click', function( event ) {
		event.preventDefault();
		var parent = $( this ).parent().parent();
		parent.remove();
	});
	
	$( '.it-exchange-customer-pricing-content-default' ).live( 'click', function( event ) {
		event.preventDefault();
		$( '.it-exchange-customer-pricing-content-default-checkmark' ).removeClass( 'it-exchange-customer-pricing-content-default-checkmark-checked' );
		$( '.it-exchange-customer-pricing-default' ).val( 'unchecked' );
		$( '.it-exchange-customer-pricing-content-default-checkmark', this ).addClass( 'it-exchange-customer-pricing-content-default-checkmark-checked' );
		$( '.it-exchange-customer-pricing-default', this ).val( 'checked' );
	});
});