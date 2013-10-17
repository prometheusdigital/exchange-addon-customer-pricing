(function( $ ) {
	$( 'select.it-exchange-customer-pricing-base-price-selector' ).live( 'change', function( event ) {
		event.preventDefault();
		var parent = $( this ).parent();
		$( '.it-exchange-base-price', parent ).html( $( 'option:selected', this ).attr( 'data-price' ) );
		if ( 'other' === $( 'option:selected', this ).val() )
			$( '.it-exchange-customer-pricing-base-price-nyop-input', parent ).removeClass( 'it-exchange-hidden' );
		else
			$( '.it-exchange-customer-pricing-base-price-nyop-input', parent ).addClass( 'it-exchange-hidden' );
	});
	$( 'input.it-exchange-customer-pricing-base-price-selector' ).live( 'change', function( event ) {
		event.preventDefault();
		var parent = $( this ).parent();
		$( '.it-exchange-base-price', parent ).html( $( this ).attr( 'data-price' ) );
		if ( 'other' === $( this ).val() )
			$( '.it-exchange-customer-pricing-base-price-nyop-input', parent ).removeClass( 'it-exchange-hidden' );
		else
			$( '.it-exchange-customer-pricing-base-price-nyop-input', parent ).addClass( 'it-exchange-hidden' );
	});
})( jQuery );