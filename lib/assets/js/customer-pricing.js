(function( $ ) {
	$( '.it-exchange-customer-pricing-options' ).on( 'change', 'select.it-exchange-customer-pricing-base-price-selector', function( event ) {
		event.preventDefault();
		var this_parent = $( this ).parent().parent();
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
	
	$( '.it-exchange-customer-pricing-options' ).on( 'change', 'input.it-exchange-customer-pricing-base-price-selector', function( event ) {
		event.preventDefault();
		var this_parent = $( this ).parent().parent().parent().parent();
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
	
	var nyop_keyup_timer = 0;
	$( '.it-exchange-customer-pricing-options' ).on( 'keydown', 'input.it-exchange-customer-pricing-base-price-nyop-input', function() {
        clearTimeout( nyop_keyup_timer );
	});
	// Format base price
	$( '.it-exchange-customer-pricing-options' ).on( 'keyup', 'input.it-exchange-customer-pricing-base-price-nyop-input', function() {
		var this_obj = this;
		var this_parent = $( this ).parent().parent().parent();
		var input = $( this ).val();
		if ( nyop_keyup_timer ) {
	        clearTimeout( nyop_keyup_timer );
	    }
		nyop_keyup_timer = setTimeout(function(){ 
			var data = {
				'action': 'it-exchange-customer-pricing-format-nyop-input',
				'input':  input,
				'post_id': $( '.it-exchange-customer-pricing-product-id' ).val(),
			}
			$.post( it_exchange_customer_pricing_ajax_object.ajax_url, data, function( response ) {
				if ( '' != response ) {
					price_obj = $.parseJSON( response );
					$( this_obj ).val( price_obj['price'] );
					$( '.it-exchange-base-price', this_parent ).html( price_obj['price'] );
					$( '.it-exchange-customer-pricing-new-base-price', this_parent ).val( price_obj['db_price'] );
				}
			});
		}, 500 );
	});
})( jQuery );