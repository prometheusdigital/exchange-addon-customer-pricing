(function( $ ) {
	$( 'select.it-exchange-customer-pricing-base-price-selector' ).live( 'change', function( event ) {
		event.preventDefault();
		var parent = $( this ).parent();
		$( '.it-exchange-base-price', parent ).html( $( 'option:selected', this ).attr( 'data-price' ) );
		$( 'input.it-exchange-customer-pricing-new-base-price' ).val( $( this ).val() );
		if ( 'other' === $( 'option:selected', this ).val() )
			$( '.it-exchange-customer-pricing-base-price-nyop-input, .it-exchange-customer-pricing-base-price-nyop-description', parent ).removeClass( 'it-exchange-hidden' );
		else
			$( '.it-exchange-customer-pricing-base-price-nyop-input, .it-exchange-customer-pricing-base-price-nyop-description', parent ).addClass( 'it-exchange-hidden' );
	});
	$( 'input.it-exchange-customer-pricing-base-price-selector' ).live( 'change', function( event ) {
		event.preventDefault();
		var parent = $( this ).parent();
		$( '.it-exchange-base-price', parent ).html( $( this ).attr( 'data-price' ) );
		$( 'input.it-exchange-customer-pricing-new-base-price' ).val( $( this ).val() );
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
			if ( '' != response ) {
				price_obj = $.parseJSON( response );
				$( '.it-exchange-base-price', parent ).html( price_obj['price'] );
				$( 'input.it-exchange-customer-pricing-new-base-price' ).val( price_obj['db_price'] );
			}
		});
	});
	
	// Register Buy Now and Add to Cart event
	$( 'form.it-exchange-sw-buy-now, form.it-exchange-sw-add-to-cart' ).live('submit', function(event) {
		event.preventDefault();
		var data = {
			'action': 'it-exchange-customer-pricing-session',
			'input':  $( '.it-exchange-customer-pricing-new-base-price' ).val(),
			'post_id': $( '.it-exchange-customer-pricing-product-id' ).val(),
		}
		$.post( it_exchange_customer_pricing_ajax_object.ajax_url, data, function( response ) {
			console.log( response );
		});
	});
})( jQuery );