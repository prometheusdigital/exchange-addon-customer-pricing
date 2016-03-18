(function ( $ ) {

	$( document ).ready( function ( $ ) {

		$( '.it-exchange-customer-pricing-options' ).on( 'change', 'select.it-exchange-customer-pricing-base-price-selector', function ( event ) {
			event.preventDefault();
			var this_parent = $( this ).parent().parent();
			var price = $( 'option:selected', this ).attr( 'data-price' );
			var db_price = $( 'option:selected', this ).val();

			if ( 'other' === db_price ) {
				$( '.it-exchange-customer-pricing-base-price-nyop-input, .it-exchange-customer-pricing-base-price-nyop-description', this_parent ).removeClass( 'it-exchange-hidden' );
			} else {
				$( '.it-exchange-customer-pricing-base-price-nyop-input, .it-exchange-customer-pricing-base-price-nyop-description', this_parent ).addClass( 'it-exchange-hidden' );
				$( '.it-exchange-customer-pricing-base-price-nyop-input' ).val( '' );

				var data = {
					'action' : 'it-exchange-customer-pricing-session',
					'input'  : db_price,
					'post_id': $( '.it-exchange-customer-pricing-product-id' ).val(),
				};

				$.post( it_exchange_customer_pricing_ajax_object.ajax_url, data, function ( response ) {
					$( '.it-exchange-base-price', this_parent ).html( response );
					$( '.it-exchange-customer-pricing-new-base-price', this_parent ).val( db_price );
				} );
			}
		} );

		$( '.it-exchange-customer-pricing-options' ).on( 'change', 'input.it-exchange-customer-pricing-base-price-selector', function ( event ) {
			event.preventDefault();
			var this_parent = $( this ).parent().parent().parent().parent();
			var price = $( this ).attr( 'data-price' );
			var db_price = $( this ).val();

			if ( 'other' === db_price ) {
				$( '.it-exchange-customer-pricing-base-price-nyop-input, .it-exchange-customer-pricing-base-price-nyop-description', this_parent ).removeClass( 'it-exchange-hidden' );
			} else {
				$( '.it-exchange-customer-pricing-base-price-nyop-input, .it-exchange-customer-pricing-base-price-nyop-description', this_parent ).addClass( 'it-exchange-hidden' );
				$( '.it-exchange-customer-pricing-base-price-nyop-input' ).val( '' );

				var data = {
					'action' : 'it-exchange-customer-pricing-session',
					'input'  : db_price,
					'post_id': $( '.it-exchange-customer-pricing-product-id' ).val(),
				};

				$.post( it_exchange_customer_pricing_ajax_object.ajax_url, data, function ( response ) {
					$( '.it-exchange-base-price', this_parent ).html( response );
					$( '.it-exchange-customer-pricing-new-base-price', this_parent ).val( db_price );
				} );
			}
		} );

		var nyop_keyup_timer = 0;

		$( '.it-exchange-customer-pricing-options' ).on( 'keydown', 'input.it-exchange-customer-pricing-base-price-nyop-input', function () {
			clearTimeout( nyop_keyup_timer );
		} );

		var $basePrice = $( '.it-exchange-base-price' );
		var $nypoInput = $( '.it-exchange-customer-pricing-base-price-nyop-input' );

		var symbol = $nypoInput.data( 'symbol' ),
			position = $nypoInput.data( 'position' ),
			thousands = $nypoInput.data( 'thousands' ),
			decimals = $nypoInput.data( 'decimals' );

		// Format base price
		$( '.it-exchange-customer-pricing-options' ).on( 'keyup', 'input.it-exchange-customer-pricing-base-price-nyop-input', function () {

			var this_obj = this;
			var this_parent = $( this ).parent().parent().parent();
			var input = $( this ).val();

			$basePrice.text( formatPrice( input, symbol, position, decimals, thousands ) );

			if ( nyop_keyup_timer ) {
				clearTimeout( nyop_keyup_timer );
			}

			nyop_keyup_timer = setTimeout( function () {
				nyop_keyup( input, this_obj, this_parent );
			}, 500 );
		} );

		// Format base price
		$( '.it-exchange-customer-pricing-options' ).on( 'focusout', 'input.it-exchange-customer-pricing-base-price-nyop-input', function () {

			var this_obj = this;
			var this_parent = $( this ).parent().parent().parent();
			var input = $( this ).val();

			if ( nyop_keyup_timer ) {
				clearTimeout( nyop_keyup_timer );
			}

			nyop_keyup( input, this_obj, this_parent );
		} );

	} );

	function nyop_keyup( input, this_obj, this_parent ) {

		var data = {
			'action' : 'it-exchange-customer-pricing-format-nyop-input',
			'input'  : input,
			'post_id': $( '.it-exchange-customer-pricing-product-id' ).val()
		};

		$.post( it_exchange_customer_pricing_ajax_object.ajax_url, data, function ( response ) {
			if ( '' != response ) {

				var current = $( '.it-exchange-customer-pricing-base-price-nyop-input' ).val();

				if ( input != current ) {
					nyop_keyup( current, this_obj, this_parent );

					return;
				}

				var price_obj = $.parseJSON( response );

				$( this_obj ).val( price_obj[ 'price' ] );
				$( '.it-exchange-base-price', this_parent ).html( price_obj[ 'price' ] );
				$( '.it-exchange-customer-pricing-new-base-price', this_parent ).val( price_obj[ 'db_price' ] );
			}
		} );
	}

	function formatPrice( price, symbol, position, decimals, thousands ) {
		if ( position == 'before' )
			return symbol + it_exchange_number_format( price, 2, decimals, thousands );
		else
			return it_exchange_number_format( price, 2, decimals, thousands ) + symbol;
	}

	function it_exchange_number_format( number, decimals, dec_point, thousands_sep ) {
		number = (number + '').replace( thousands_sep, '' ); //remove thousands
		number = (number + '').replace( dec_point, '.' ); //turn number into proper float (if it is an improper float)
		number = (number + '').replace( /[^0-9+\-Ee.]/g, '' );
		var n = ! isFinite( + number ) ? 0 : + number;
		prec = ! isFinite( + decimals ) ? 0 : Math.abs( decimals );
		sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep;
		dec = (typeof dec_point === 'undefined') ? '.' : dec_point;
		s = '',
			toFixedFix = function ( n, prec ) {
				var k = Math.pow( 10, prec );
				return '' + Math.round( n * k ) / k;
			};
		// Fix for IE parseFloat(0.55).toFixed(0) = 0;
		s = (prec ? toFixedFix( n, prec ) : '' + Math.round( n )).split( '.' );
		if ( s[ 0 ].length > 3 ) {
			s[ 0 ] = s[ 0 ].replace( /\B(?=(?:\d{3})+(?!\d))/g, sep );
		}
		if ( (s[ 1 ] || '').length < prec ) {
			s[ 1 ] = s[ 1 ] || '';
			s[ 1 ] += new Array( prec - s[ 1 ].length + 1 ).join( '0' );
		}
		return s.join( dec );
	}

})( jQuery );