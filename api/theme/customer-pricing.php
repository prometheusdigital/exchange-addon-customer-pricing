<?php
/**
 * Customer Pricing class for THEME API
 *
 * @since 1.0.0
*/

class IT_Theme_API_Customer_Pricing implements IT_Theme_API {
	
	/**
	 * API context
	 * @var string $_context
	 * @since 1.0.0
	*/
	private $_context = 'customer-pricing';

	/**u
	 * Maps api tags to methods
	 * @var array $_tag_map
	 * @since 1.0.0
	*/
	var $_tag_map = array(
		'baseprice'        => 'base_price',
		'customerpricing' => 'customer_pricing',
	);

	/**
	 * Current product in iThemes Exchange Global
	 * @var object $product
	 * @since 1.0.0
	*/
	private $product;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 *
	 * @return void
	*/
	function IT_Theme_API_Customer_Pricing() {
		// Set the current global product as a property
		$this->product = empty( $GLOBALS['it_exchange']['product'] ) ? false : $GLOBALS['it_exchange']['product'];
	}

	/**
	 * Returns the context. Also helps to confirm we are an iThemes Exchange theme API class
	 *
	 * @since 1.0.0
	 * 
	 * @return string
	*/
	function get_api_context() {
		return $this->_context;
	}

	/**
	 * The product base price
	 *
	 * @since 1.0.0
	 * @return mixed
	*/
	function base_price( $options=array() ) {

		// Return boolean if has flag was set
		if ( $options['supports'] )
			return it_exchange_product_supports_feature( $this->product->ID, 'customer-pricing', array( 'setting' => 'enabled' ) );
			
		// Return boolean if has flag was set
		if ( $options['has'] )
			return it_exchange_product_has_feature( $this->product->ID, 'customer-pricing', array( 'setting' => 'enabled' ) );

		if ( it_exchange_product_supports_feature( $this->product->ID, 'customer-pricing', array( 'setting' => 'enabled' ) )
				&& it_exchange_product_has_feature( $this->product->ID, 'customer-pricing', array( 'setting' => 'enabled' ) ) ) {
			
			$defaults   = array(
				'before' => '<span class="it-exchange-base-price">',
				'after'  => '</span>',
				'format' => 'html',
				'free-label' => __( 'Free', 'LION' ),
				'plus'   => true,
			);
			$options = ITUtility::merge_defaults( $options, $defaults );

			$result     = '';
			$base_price = 0;
			$db_price = 0;
			$price_options = it_exchange_get_product_feature( $this->product->ID, 'customer-pricing', array( 'setting' => 'pricing-options' ) );
			$nyop_enabled = it_exchange_get_product_feature( $this->product->ID, 'customer-pricing', array( 'setting' => 'nyop_enabled' ) );
			
			if ( 'yes' === $nyop_enabled ) {
				$nyop_min_price = it_exchange_convert_from_database_number( it_exchange_get_product_feature( $this->product->ID, 'customer-pricing', array( 'setting' => 'nyop_min' ) ) );
				if ( 0 == $base_price || $nyop_min_price < $base_price )
					$base_price = $nyop_min_price;
			}
			
			if ( !empty( $price_options ) ) {
				foreach( $price_options as $price_option ) {
					$db_price = $price_option['price'];
					$price = it_exchange_convert_from_database_number( $price_option['price'] );
					if ( 0 == $base_price || $price < $base_price )
						$base_price = $price;
					if ( 'checked' === $price_option['default'] ) {
						$base_price = $price;
						break;
					}
				}
			}
			
			$price    = empty( $base_price ) ? '<span class="free-label">' . $options['free-label'] . '</span>' : it_exchange_format_price( $base_price );
			$price    = ( empty( $options['free-label'] ) && empty( $base_price ) ) ? it_exchange_format_price( $base_price ) : $price;

			if ( 'html' == $options['format'] )
				$result .= $options['before'];

			$result .= $price . ( $options['plus'] ? '+' : '' );

			if ( 'html' == $options['format'] )
				$result .= $options['after'];
			$result .= '<input type="hidden" class="it-exchange-customer-pricing-new-base-price" name="it-exchange-customer-pricing-new-base-price" value="' . $db_price . '">';

			return $result;
		}

		return false;
	}

	/**
	 * The product customer pricing
	 *
	 * @since 1.0.0
	 * @return mixed
	*/
	function customer_pricing( $options=array() ) {

		// Return boolean if has flag was set
		if ( $options['supports'] )
			return it_exchange_product_supports_feature( $this->product->ID, 'customer-pricing', array( 'setting' => 'enabled' ) );
			
		// Return boolean if has flag was set
		if ( $options['has'] )
			return it_exchange_product_has_feature( $this->product->ID, 'customer-pricing', array( 'setting' => 'enabled' ) );

		if ( it_exchange_product_supports_feature( $this->product->ID, 'customer-pricing', array( 'setting' => 'enabled' ) )
				&& it_exchange_product_has_feature( $this->product->ID, 'customer-pricing', array( 'setting' => 'enabled' ) ) ) {
			
			$defaults   = array(
				'before' => '',
				'after'  => '',
			);
			$options = ITUtility::merge_defaults( $options, $defaults );

			$hidden = '';
			$result = '';
			
			$price_options = it_exchange_get_product_feature( $this->product->ID, 'customer-pricing', array( 'setting' => 'pricing-options' ) );
			$output_type = it_exchange_get_product_feature( $this->product->ID, 'customer-pricing', array( 'setting' => 'output_type' ) );
			//nyop = Name Your Own Price
			$nyop_enabled = it_exchange_get_product_feature( $this->product->ID, 'customer-pricing', array( 'setting' => 'nyop_enabled' ) );
			$nyop_min = it_exchange_get_product_feature( $this->product->ID, 'customer-pricing', array( 'setting' => 'nyop_min' ) );
			$nyop_max = it_exchange_get_product_feature( $this->product->ID, 'customer-pricing', array( 'setting' => 'nyop_max' ) );
		
			$result .= $options['before'];
				
			if ( !empty( $price_options ) ) {
					
				switch ( $output_type ) {
				
					case 'select':
						$result .= '<select class="it-exchange-customer-pricing-base-price-selector" name="it_exchange_customer_pricing_base_price_selector">';
						foreach( $price_options as $price_option ) {
							$fprice = it_exchange_format_price( it_exchange_convert_from_database_number( $price_option['price'] ) );
							$result .= '<option data-price="' . $fprice . '" value="' . $price_option['price'] . '" ' . selected( 'checked', $price_option['default'], false ) . ' >' . $fprice;
							if ( !empty( $price_option['label'] ) )
								$result .= ' - ' . $price_option['label'];
							
							$result .= '</option>';
						}
						if ( 'yes' === $nyop_enabled ) {
							$result .= '<option value="other">' . __( 'Name your own price', 'LION' ) . '</option>';
							$hidden = 'it-exchange-hidden';
						}
						$result .= '</select>';
						break;

					case 'radio':
					default:
						$result .= '<ul>';
						foreach( $price_options as $price_option ) {
							$fprice = it_exchange_format_price( it_exchange_convert_from_database_number( $price_option['price'] ) );
							$result .= '<li><input class="it-exchange-customer-pricing-base-price-selector" type="radio" name="it_exchange_customer_pricing_base_price_selector" data-price="' . $fprice . '" value="' . $price_option['price'] . '" ' . checked( 'checked', $price_option['default'], false ) . ' />' . $fprice;
							if ( !empty( $price_option['label'] ) )
								$result .= ' - ' . $price_option['label'];
							$result .= '</li>';
						}
						if ( 'yes' === $nyop_enabled ) {
							$result .= '<li class="it-exchange-nyop-option"><input class="it-exchange-customer-pricing-base-price-selector" type="radio" name="it_exchange_customer_pricing_base_price_selector" value="other" />' . __( 'Name your own price', 'LION' ) . '</li>';
							$hidden = 'it-exchange-hidden';
						}
						$result .= '</ul>';
						$result .= '</select>';
						break;
					
				}
			
			}
			
			if ( 'yes' === $nyop_enabled ) {
				$result .= '<div class="it-exchange-customer-nyop-section">';
				$result .= '<input class="it-exchange-customer-pricing-base-price-nyop-input ' . $hidden . '" type="text" name="it_exchange_customer_pricing_base_price_selector" value="" />';
				if ( empty( $price_options ) )
					$result .= ' ' . __( 'Name your own price', 'LION' );
					
				$result .= '<p class="it-exchange-customer-pricing-base-price-nyop-description ' . $hidden . '">';
				if ( !empty( $nyop_min ) && 0 < $nyop_min )
					$result .= '<span class="it-exchange-customer-price-min">' . sprintf( __( 'Min: %s', 'LION' ), it_exchange_format_price( it_exchange_convert_from_database_number( $nyop_min ) ) ) . '</span>';
					
				if ( !empty( $nyop_max ) && 0 < $nyop_max )
					$result .= '<span class="it-exchange-customer-price-max">' . sprintf( __( 'Max: %s', 'LION' ), it_exchange_format_price( it_exchange_convert_from_database_number( $nyop_max ) ) ) . '</span>';
				$result .= '</p>';
				$result .= '</div>';
			}
			
			global $post;
			$result .= '<input type="hidden" class="it-exchange-customer-pricing-product-id" name="it-exchange-customer-pricing-product-id" value="' . $post->ID . '">';

			$result .= $options['after'];

			return $result;
		}

		return false;
	}
}
