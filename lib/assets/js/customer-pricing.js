(function( $ ) {
	$( 'select.it-exchange-customer-pricing-base-price-selector' ).live( 'change', function( event ) {
		event.preventDefault();
		var parent = $( this ).parent();
		$( '.it-exchange-base-price', parent ).html( $( 'option:selected', this ).attr( 'data-price' ) );
		if ( 'other' === $( 'option:selected', this ).val() )
			$( '.it-exchange-customer-pricing-base-price-nyop-input, .it-exchange-customer-pricing-base-price-nyop-description', parent ).removeClass( 'it-exchange-hidden' );
		else
			$( '.it-exchange-customer-pricing-base-price-nyop-input, .it-exchange-customer-pricing-base-price-nyop-description', parent ).addClass( 'it-exchange-hidden' );
	});
	$( 'input.it-exchange-customer-pricing-base-price-selector' ).live( 'change', function( event ) {
		event.preventDefault();
		var parent = $( this ).parent();
		$( '.it-exchange-base-price', parent ).html( $( this ).attr( 'data-price' ) );
		if ( 'other' === $( this ).val() )
			$( '.it-exchange-customer-pricing-base-price-nyop-input, .it-exchange-customer-pricing-base-price-nyop-description', parent ).removeClass( 'it-exchange-hidden' );
		else
			$( '.it-exchange-customer-pricing-base-price-nyop-input, .it-exchange-customer-pricing-base-price-nyop-description', parent ).addClass( 'it-exchange-hidden' );
	});
	$( 'input.it-exchange-customer-pricing-base-price-nyop-input' ).live('input keyup change', function(event) {
		event.preventDefault();
		var parent = $( this ).parent();
		var data = {
			'action': 'it-exchange-customer-pricing-format-nyop-input',
			'input':  $( this ).val(),
			'post_id': $( '.it-exchange-customer-pricing-product-id', parent ).val(),
		}
		$.post( it_exchange_customer_pricing_ajax_object.ajax_url, data, function( response ) {
			console.log( response );
			if ( '' != response )
				$( '.it-exchange-base-price', parent ).html( response );
		});
	});
})( jQuery );