(function( $ ) {
	$( 'select.it-exchange-customer-pricing-base-price-selector' ).live( 'change', function( event ) {
		event.preventDefault();
		var this_parent = $( this ).parent();
		var price       = $( 'option:selected', this ).attr( 'data-price' );
		var db_price    = $( 'option:selected', this ).val();
		
		if ( 'other' === db_price ) {
			
			$( '.it-exchange-customer-pricing-base-price-nyop-input, .it-exchange-customer-pricing-base-price-nyop-description', this_parent ).removeClass( 'it-exchange-hidden' );
			
		} else {
			$( '.it-exchange-customer-pricing-base-price-nyop-input, .it-exchange-customer-pricing-base-price-nyop-description', this_parent ).addClass( 'it-exchange-hidden' );
			$( '.it-exchange-customer-pricing-base-price-nyop-input' ).val( '' );
			
			var data = {
				'action':  'it-exchange-customer-pricing-session',
				'input':   db_price,
				'post_id': $( '.it-exchange-customer-pricing-product-id' ).val(),
			}
			$.post( it_exchange_customer_pricing_ajax_object.ajax_url, data, function( response ) {
				$( '.it-exchange-base-price', this_parent ).html( price );
				$( '.it-exchange-customer-pricing-new-base-price', this_parent ).val( db_price );
			});
		}
	});
	
	$( 'input.it-exchange-customer-pricing-base-price-selector' ).live( 'change', function( event ) {
		event.preventDefault();
		var this_parent = $( this ).parent().parent().parent();
		var price       = $( this ).attr( 'data-price' );
		var db_price    = $( this ).val();
			
		if ( 'other' === db_price ) {
			
			$( '.it-exchange-customer-pricing-base-price-nyop-input, .it-exchange-customer-pricing-base-price-nyop-description', this_parent ).removeClass( 'it-exchange-hidden' );
			
		} else {
			$( '.it-exchange-customer-pricing-base-price-nyop-input, .it-exchange-customer-pricing-base-price-nyop-description', this_parent ).addClass( 'it-exchange-hidden' );
			$( '.it-exchange-customer-pricing-base-price-nyop-input' ).val( '' );
			
				
			var data = {
				'action':  'it-exchange-customer-pricing-session',
				'input':   db_price,
				'post_id': $( '.it-exchange-customer-pricing-product-id' ).val(),
			}
			$.post( it_exchange_customer_pricing_ajax_object.ajax_url, data, function( response ) {
				$( '.it-exchange-base-price', this_parent ).html( price );
				$( '.it-exchange-customer-pricing-new-base-price', this_parent ).val( db_price );
			});
		}
	});
	
	$( 'input.it-exchange-customer-pricing-base-price-nyop-input' ).live( 'input keyup change', function(event) {
		event.preventDefault();
		var this_parent = $( this ).parent().parent();
		var data = {
			'action': 'it-exchange-customer-pricing-format-nyop-input',
			'input':  $( this ).val(),
			'post_id': $( '.it-exchange-customer-pricing-product-id' ).val(),
		}
		$.post( it_exchange_customer_pricing_ajax_object.ajax_url, data, function( response ) {
			console.log( response );
			if ( '' != response ) {
				price_obj = $.parseJSON( response );
				$( '.it-exchange-base-price', this_parent ).html( price_obj['price'] );
				$( '.it-exchange-customer-pricing-new-base-price', this_parent ).val( price_obj['db_price'] );
			}
		});
	});
})( jQuery );