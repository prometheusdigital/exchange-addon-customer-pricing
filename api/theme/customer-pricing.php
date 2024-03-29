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
	 * Current product in ExchangeWP Global
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
	function __construct() {
		// Set the current global product as a property
		$this->product = empty( $GLOBALS['it_exchange']['product'] ) ? false : $GLOBALS['it_exchange']['product'];
	}

	/**
	 * Deprecated Constructor
	 *
	 * @since 1.0.0
	 *
	 * @return void
	*/
	function IT_Theme_API_Customer_Pricing() {
		self::__construct();
	}

	/**
	 * Returns the context. Also helps to confirm we are an ExchangeWP theme API class
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

			$addon_settings = it_exchange_get_option( 'addon_customer_pricing' );

			$defaults   = array(
				'before'        => '<span class="it-exchange-base-price">',
				'after'         => '</span>',
				'format'        => 'html',
				'free-label'    => __( 'Free', 'LION' ),
				'show-or-more'  => true,
				'or-more-label' => $addon_settings['customer-pricing-or-more-label']
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

			$price    = empty( $base_price ) ? '<span class="free-label">' . $options['free-label'] . '</span>' : apply_filters( 'it_exchange_customer_pricing_product_price', it_exchange_format_price( $base_price ), $this->product->ID );
			$price    = ( empty( $options['free-label'] ) && empty( $base_price ) ) ? it_exchange_format_price( $base_price ) : $price;

			if ( 'html' == $options['format'] )
				$result .= $options['before'];

			$result .= $price . ( $options['show-or-more'] ? ' <span class="customer-pricing-or-more-label">' . $options['or-more-label'] . '</span>' : '' );

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

			$addon_settings = it_exchange_get_option( 'addon_customer_pricing' );

			$defaults   = array(
				'before'      => '',
				'after'       => '',
				'nyop-label'  => $addon_settings['customer-pricing-nyop-label'],
				'output-type' => $addon_settings['customer-pricing-output-type'],
			);
			$options = ITUtility::merge_defaults( $options, $defaults );

			$hidden = '';
			$result = '';

			$price_options = it_exchange_get_product_feature( $this->product->ID, 'customer-pricing', array( 'setting' => 'pricing-options' ) );
			//nyop = Name Your Own Price
			$nyop_enabled = it_exchange_get_product_feature( $this->product->ID, 'customer-pricing', array( 'setting' => 'nyop_enabled' ) );
			$nyop_min = it_exchange_get_product_feature( $this->product->ID, 'customer-pricing', array( 'setting' => 'nyop_min' ) );
			$nyop_max = it_exchange_get_product_feature( $this->product->ID, 'customer-pricing', array( 'setting' => 'nyop_max' ) );

			$result .= $options['before'];

			$result .= '<div class="it-exchange-customer-pricing-options">';

			if ( !empty( $price_options ) ) {

				if ( 'no' === $nyop_enabled && 1 === count( $price_options ) ) {

					//Don't display anything, just pretend like the normal base-price is the setting
					//even though they selected customer-pricing with only one option!

				} else {

					switch ( $options['output-type'] ) {

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
								$result .= '<option value="other">' . $options['nyop-label'] . '</option>';
								$hidden = 'it-exchange-hidden';
							}
							$result .= '</select>';
							break;

						case 'radio':
						default:
							$result .= '<ul>';
							foreach( $price_options as $price_option ) {
								$fprice = it_exchange_format_price( it_exchange_convert_from_database_number( $price_option['price'] ) );
								$result .= '<li><input id="it-exchange-customer-pricing-' . $fprice . '" class="it-exchange-customer-pricing-base-price-selector" type="radio" name="it_exchange_customer_pricing_base_price_selector" data-price="' . $fprice . '" value="' . $price_option['price'] . '" ' . checked( 'checked', $price_option['default'], false ) . ' /><label for="it-exchange-customer-pricing-' . $fprice . '">' . $fprice;
								if ( !empty( $price_option['label'] ) )
									$result .= ' - ' . $price_option['label'];
								$result .= '</label>';
								$result .= '</li>';
							}
							if ( 'yes' === $nyop_enabled ) {
								$result .= '<li class="it-exchange-nyop-option"><input id="it-exchange-customer-pricing-nyop" class="it-exchange-customer-pricing-base-price-selector" type="radio" name="it_exchange_customer_pricing_base_price_selector" value="other" /><label for="it-exchange-customer-pricing-nyop">' . $options['nyop-label'] . '</label></li>';
								$hidden = 'it-exchange-hidden';
							}
							$result .= '</ul>';
							break;

					}

				}

			}

			if ( 'yes' === $nyop_enabled ) {

				$settings = it_exchange_get_option( 'settings_general' );
				$currency = it_exchange_get_currency_symbol( $settings['default-currency'] );
				$position = $settings['currency-symbol-position'];
				$thousands = $settings['currency-thousands-separator'];
				$decimals = $settings['currency-decimals-separator'];

				$result .= '<div class="it-exchange-customer-nyop-section">';
				$result .= "<input class=\"it-exchange-customer-pricing-base-price-nyop-input $hidden\" type=\"text\" name=\"it_exchange_customer_pricing_base_price_selector\"
							data-position='$position' data-symbol='$currency' data-thousands='$thousands' data-decimals='$decimals' />";
				if ( empty( $price_options ) )
					$result .= ' ' . $options['nyop-label'];

				$result .= '<p class="it-exchange-customer-pricing-base-price-nyop-description ' . $hidden . '">';
				if ( !empty( $nyop_min ) && 0 < $nyop_min )
					$result .= '<span class="it-exchange-customer-price-min"><small>' . sprintf( __( 'Min: %s', 'LION' ), it_exchange_format_price( it_exchange_convert_from_database_number( $nyop_min ) ) ) . '</small></span>';

				if ( !empty( $nyop_max ) && 0 < $nyop_max )
					$result .= '<span class="it-exchange-customer-price-max"><small>' . sprintf( __( 'Max: %s', 'LION' ), it_exchange_format_price( it_exchange_convert_from_database_number( $nyop_max ) ) ) . '</small></span>';
				$result .= '</p>';
				$result .= '</div>';
			}

			global $post;
			$result .= '<input type="hidden" class="it-exchange-customer-pricing-product-id" name="it-exchange-customer-pricing-product-id" value="' . $post->ID . '">';

			$result .= '</div>';
			$result .= $options['after'];

			return $result;
		}

		return false;
	}
}
