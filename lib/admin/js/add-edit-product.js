jQuery(document).ready(function($) {
	$( '#it-exchange-customer-pricing-enable' ).on( 'click', function( event ) {
		$( '.base-price-customer-pricing-toggle' ).toggleClass( 'hide-if-js ' );
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
			$( '.it-exchange-customer-pricing-list' ).removeClass('hidden');
			$( '.it-exchange-customer-pricing-no-prices' ).addClass('hidden');
			$( '.it-exchange-customer-pricing-addon-pricing' ).append( response );
		});
	});
	
	$( '.it-exchange-customer-pricing-enable' ).on( 'click', '.it-exchange-customer-pricing-remove-option', function( event ) {
		event.preventDefault();
		var parent = $( this ).parent().parent();
		parent.remove();
	});
	
	$( '.it-exchange-customer-pricing-content-default-checkmark' ).tooltip({
		items: 'span',
		content: function() {
			var checkmark = $( this );
			if ( $( this ).hasClass( 'it-exchange-customer-pricing-content-default-checkmark-checked' ) ) {
				return 'Current Default';
			} else {
				return 'Set as Default';
			}
		},
		position: {
			my: 'left+25 center',
			at: 'left center'
		}
	});
	
	$( '.it-exchange-customer-pricing-enable' ).on( 'click', '.it-exchange-customer-pricing-content-default', function( event ) {
		event.preventDefault();
		$( '.it-exchange-customer-pricing-content-default-checkmark' ).removeClass( 'it-exchange-customer-pricing-content-default-checkmark-checked' );
		$( '.it-exchange-customer-pricing-default' ).val( 'unchecked' );
		$( '.it-exchange-customer-pricing-content-default-checkmark', this ).addClass( 'it-exchange-customer-pricing-content-default-checkmark-checked' );
		$( '.it-exchange-customer-pricing-default', this ).val( 'checked' );
	});
	
	$( '.it-exchange-customer-pricing-enable' ).on( 'focusout', '.it-exchange-customer-pricing-price, .it-exchange-customer-pricing-nyop-min-max', function( event ) {
		var this_obj = this;
		if ( 'it-exchange-customer-pricing-nyop-max' === event.target.id ) {
			nyop_max = true;
		} else {
			nyop_max = false;
		}
		var data = {
			'action': 'it-exchange-customer-pricing-format-prices',
			'price':  $( this ).val(),
			'max':    nyop_max,
		}
		$.post( ajaxurl, data, function( response ) {
			if ( '' != response ) {
				$( this_obj ).val( response );
			}
		});
	});
});