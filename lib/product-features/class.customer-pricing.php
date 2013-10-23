<?php
/**
 * This will control customer pricing options on the frontend products
 *
 * @since 1.0.0
 * @package IT_Exchange_Addon_Customer_Pricing
*/


class IT_Exchange_Addon_Customer_Pricing_Product_Feature_Customer_Pricing {

	/**
	 * Constructor. Registers hooks
	 *
	 * @since 1.0.0
	 * @return void
	*/
	function IT_Exchange_Addon_Customer_Pricing_Product_Feature_Customer_Pricing() {
		if ( is_admin() ) {
			add_action( 'load-post-new.php', array( $this, 'init_feature_metaboxes' ) );
			add_action( 'load-post.php', array( $this, 'init_feature_metaboxes' ) );
			add_action( 'it_exchange_save_product', array( $this, 'save_feature_on_product_save' ) );
		}
		add_action( 'it_exchange_enabled_addons_loaded', array( $this, 'add_feature_support_to_product_types' ) );
		add_action( 'it_exchange_update_product_feature_customer-pricing', array( $this, 'save_feature' ), 9, 3 );
		add_filter( 'it_exchange_get_product_feature_customer-pricing', array( $this, 'get_feature' ), 9, 3 );
		add_filter( 'it_exchange_product_has_feature_customer-pricing', array( $this, 'product_has_feature') , 9, 2 );
		add_filter( 'it_exchange_product_supports_feature_customer-pricing', array( $this, 'product_supports_feature') , 9, 2 );
	}

	/**
	 * Register the product feature and add it to enabled product-type addons
	 *
	 * @since 1.0.0
	*/
	function add_feature_support_to_product_types() {
		// Register the product feature
		$slug        = 'customer-pricing';
		$description = __( "This displays a custom pricing options for all Exchange product types", 'LION' );
		it_exchange_register_product_feature( $slug, $description );

		// Add it to all enabled product-type addons
		$products = it_exchange_get_enabled_addons( array( 'category' => 'product-type' ) );
		foreach( $products as $key => $params ) {
			it_exchange_add_feature_support_to_product_type( 'customer-pricing', $params['slug'] );
		}
	}

	/**
	 * Register's the metabox for any product type that supports the feature
	 *
	 * @since 1.0.0
	 * @return void
	*/
	function init_feature_metaboxes() {
		
		global $post;
		
		if ( isset( $_REQUEST['post_type'] ) ) {
			$post_type = $_REQUEST['post_type'];
		} else {
			if ( isset( $_REQUEST['post'] ) )
				$post_id = (int) $_REQUEST['post'];
			elseif ( isset( $_REQUEST['post_ID'] ) )
				$post_id = (int) $_REQUEST['post_ID'];
			else
				$post_id = 0;

			if ( $post_id )
				$post = get_post( $post_id );

			if ( isset( $post ) && !empty( $post ) )
				$post_type = $post->post_type;
		}
			
		if ( !empty( $_REQUEST['it-exchange-product-type'] ) )
			$product_type = $_REQUEST['it-exchange-product-type'];
		else
			$product_type = it_exchange_get_product_type( $post );
		
		if ( !empty( $post_type ) && 'it_exchange_prod' === $post_type ) {
			if ( !empty( $product_type ) &&  it_exchange_product_type_supports_feature( $product_type, 'customer-pricing' ) )
				add_action( 'it_exchange_product_metabox_callback_' . $product_type, array( $this, 'register_metabox' ) );
		}
		
	}

	/**
	 * Registers the feature metabox for a specific product type
	 *
	 * Hooked to it_exchange_product_metabox_callback_[product-type] where product type supports the feature 
	 *
	 * @since 1.0.0
	 * @return void
	*/
	function register_metabox() {
		add_meta_box( 'it-exchange-product-customer-pricing', __( 'Customer Pricing', 'LION' ), array( $this, 'print_metabox' ), 'it_exchange_prod', 'it_exchange_advanced' );
	}

	/**
	 * This echos the feature metabox.
	 *
	 * @since 1.0.0
	 * @return void
	*/
	function print_metabox( $post ) {
		// Grab the iThemes Exchange Product object from the WP $post object
		$product = it_exchange_get_product( $post );

		// Set the value of the feature for this product
		$enabled = it_exchange_get_product_feature( $product->ID, 'customer-pricing', array( 'setting' => 'enabled' ) );
		$price_options = it_exchange_get_product_feature( $product->ID, 'customer-pricing', array( 'setting' => 'pricing-options' ) );
		//nyop = Name Your Own Price
		$nyop_enabled = it_exchange_get_product_feature( $product->ID, 'customer-pricing', array( 'setting' => 'nyop_enabled' ) );
		$nyop_min = it_exchange_format_price( it_exchange_convert_from_database_number( it_exchange_get_product_feature( $product->ID, 'customer-pricing', array( 'setting' => 'nyop_min' ) ) ) );
		$nyop_max = it_exchange_get_product_feature( $product->ID, 'customer-pricing', array( 'setting' => 'nyop_max' ) );
		if ( 0 == $nyop_max ) {
			$nyop_max = __( 'No Limit', 'LION' );
		} else {
			$nyop_max = it_exchange_format_price( it_exchange_convert_from_database_number( $nyop_max ) );	
		}
		
		?>
        
		<p class="it-exchange-customer-pricing-checkbox">
		<input type="checkbox" id="it-exchange-customer-pricing-enable"  class="it-exchange-checkbox-enable" name="it-exchange-customer-pricing-enable" value="yes" <?php checked( 'yes', $enabled ); ?> />&nbsp;<label for="it-exchange-customer-pricing-enable"><?php _e( 'Enable customer price options for this product', 'LION' ); ?> <span class="tip" title="<?php _e( 'This option allows you to accept different payment amounts for the product as selected by the customer.', 'LION' ); ?>">i</span></label>
		</p>
        
        <div class="it-exchange-customer-pricing-enable<?php echo ( 'no' == $enabled ) ? ' hide-if-js' : '' ?>">
        
        <label for="it-exchange-customer-pricing-options"><?php _e( 'Pricing Options', 'LION' ); ?> <span class="tip" title="<?php _e( 'Create the price options available to your customer for this product.', 'LION' ); ?>">i</span></label>
        <div class="it-exchange-customer-pricing-list-wrapper">
			<?php
            if ( !empty( $price_options ) )
                $hidden_class = '';
            else
                $hidden_class = 'hidden';
            ?>
        	<div class="it-exchange-customer-pricing-list <?php echo $hidden_class; ?>">
                <div class="it-exchange-customer-pricing-list-titles">
                    <div class="it-exchange-customer-pricing-item columns-wrapper">
                        <div class="it-exchange-customer-pricing-price column">
                            <span><?php _e( 'Price', 'LION' ); ?></span>
                        </div>
                        <div class="it-exchange-customer-pricing-title column">
                            <span><?php _e( 'Title (optional)', 'LION' ); ?></span>
                        </div>
                    </div>
                </div>
                <?php $count = 0; ?>
                <div class="it-exchange-customer-pricing-addon-pricing">
                <?php
				if ( !empty( $price_options ) ) {
					foreach( $price_options as $price_option ) {
						echo it_exchange_customer_pricing_addon_build_price_option( $price_option, $count++ );
					}
				}
                ?>
				<script type="text/javascript" charset="utf-8">
                    var it_exchange_customer_pricing_addon_price_options_interation = <?php echo $count; ?>;
                </script>
                </div>
            </div>
            <?php
			if ( !empty( $hidden_class ) ) {
				?>
                <div class="it-exchange-customer-pricing-no-prices"><?php _e( 'No price options have been added to this product yet.', 'LION' ); ?></div>
                <?php	
			}
			?>
        </div>
		<div class="it-exchange-customer-pricing-footer">
	        <div class="it-exchange-customer-pricing-add-new-price left">
	            <a href class="button"><?php _e( 'Add New Price', 'LION' ); ?></a>
	        </div>
		</div>
        
		<p class="it-exchange-customer-pricing-nyop-checkbox">
		<input type="checkbox" id="it-exchange-customer-pricing-enable-nyop"  class="it-exchange-checkbox-enable" name="it-exchange-customer-pricing-enable-nyop" value="yes" <?php checked( 'yes', $nyop_enabled ); ?> />&nbsp;<label for="it-exchange-customer-pricing-enable-nyop"><?php _e( 'Let customers name their own price?', 'LION' ); ?> <span class="tip" title="<?php _e( 'This allows your customers to set their own price.', 'LION' ); ?>">i</span></label>
		</p>
        
        <div class="it-exchange-customer-pricing-enable-nyop columns-wrapper<?php echo ( 'no' == $nyop_enabled ) ? ' hide-if-js' : '' ?>">
            <div class="it-exchange-customer-pricing-nyop-min column">
                <label for="it-exchange-customer-pricing-nyop-min"><?php _e( 'Minimum price', 'LION' ); ?></label>
                <input type="text" id="it-exchange-customer-pricing-nyop-min" class="it-exchange-customer-pricing-nyop-min-max" name="it-exchange-customer-pricing-nyop-min" value="<?php esc_attr_e( $nyop_min ); ?>" />
			</div>
            <div class="it-exchange-customer-pricing-nyop-max column">
                <label for="it-exchange-customer-pricing-nyop-max"><?php _e( 'Maximum price', 'LION' ); ?></label>
                <input type="text" id="it-exchange-customer-pricing-nyop-max" class="it-exchange-customer-pricing-nyop-min-max" name="it-exchange-customer-pricing-nyop-max" value="<?php esc_attr_e( $nyop_max ); ?>" />
			</div>
        </div>
        
        </div>
		<?php
	}

	/**
	 * This saves the value
	 *
	 * @since 1.0.0
	 *
	 * @param object $post wp post object
	 * @return void
	*/
	function save_feature_on_product_save() {
		// Abort if we can't determine a product type
		if ( ! $product_type = it_exchange_get_product_type() )
			return;

		// Abort if we don't have a product ID
		$product_id = empty( $_POST['ID'] ) ? false : $_POST['ID'];
		if ( ! $product_id )
			return;

		// Abort if this product type doesn't support this feature 
		if ( ! it_exchange_product_type_supports_feature( $product_type, 'customer-pricing' ) )
			return;
		
		// Set enabled
		$enabled = empty( $_POST['it-exchange-customer-pricing-enable'] ) ? 'no' : 'yes';
		it_exchange_update_product_feature( $product_id, 'customer-pricing', $enabled, array( 'setting' => 'enabled' ) );
		
		if ( ! empty( $_POST['it-exchange-customer-pricing-options'] ) ) {
			$price_options = array();
			
			foreach( $_POST['it-exchange-customer-pricing-options'] as $key => $option ) {
				if ( '' != trim( $option['price'] ) ) {
					$price_options[] = array(
						'price' => it_exchange_convert_to_database_number( $option['price'] ),
						'label' => $option['label'],
						'default' => $option['default'],
					);
				}
			}
			
			it_exchange_update_product_feature( $product_id, 'customer-pricing', $price_options, array( 'setting' => 'pricing-options' ) );
			$pricing_options = true;
		} else {
			it_exchange_update_product_feature( $product_id, 'customer-pricing', array(), array( 'setting' => 'pricing-options' ) );
			$pricing_options = false;
		}
		
		//nyop = Name Your Own Price
		$nyop_enabled = empty( $_POST['it-exchange-customer-pricing-enable-nyop'] ) ? 'no' : 'yes';
		it_exchange_update_product_feature( $product_id, 'customer-pricing', $nyop_enabled, array( 'setting' => 'nyop_enabled' ) );
		
		$nyop_min =  empty( $_POST['it-exchange-customer-pricing-nyop-min'] ) ? 0 : it_exchange_convert_to_database_number( $_POST['it-exchange-customer-pricing-nyop-min'] );
		
		$nyop_max =  empty( $_POST['it-exchange-customer-pricing-nyop-max'] ) ? 0 : it_exchange_convert_to_database_number( $_POST['it-exchange-customer-pricing-nyop-max'] );
		
		if ( 0 != $nyop_max && $nyop_min > $nyop_max) {
			$nyop_temp = $nyop_min;
			$nyop_min = $nyop_max;
			$nyop_max = $nyop_temp;	
		}
		
		it_exchange_update_product_feature( $product_id, 'customer-pricing', $nyop_min, array( 'setting' => 'nyop_min' ) );
		it_exchange_update_product_feature( $product_id, 'customer-pricing', $nyop_max, array( 'setting' => 'nyop_max' ) );
		
		// If there weren't any pricing options set and name your own price isn't set, disable
		// customer pricing for this product
		if ( !$pricing_options && 'no' === $nyop_enabled )
			it_exchange_update_product_feature( $product_id, 'customer-pricing', 'no', array( 'setting' => 'enabled' ) );
				
	}

	/**
	 * Return the product's features
	 *
	 * @since 1.0.0
	 * @param mixed $existing the values passed in by the WP Filter API. Ignored here.
	 * @param integer product_id the WordPress post ID
	 * @return string product feature
	*/
	function save_feature( $product_id, $new_value, $options=array() ) {
		$defaults['setting'] = 'enabled';
		$options = ITUtility::merge_defaults( $options, $defaults );
		
		switch ( $options['setting'] ) {
			
			case 'enabled':
				update_post_meta( $product_id, '_it-exchange-customer-pricing-enabled', $new_value );
				break;
			case 'pricing-options':
				update_post_meta( $product_id, '_it-exchange-customer-pricing-options', $new_value );
				break;
			case 'nyop_enabled':
				update_post_meta( $product_id, '_it-exchange-customer-pricing-nyop-enabled', $new_value );
				break;
			case 'nyop_min':
				update_post_meta( $product_id, '_it-exchange-customer-pricing-nyop-min', $new_value );
				break;
			case 'nyop_max':
				update_post_meta( $product_id, '_it-exchange-customer-pricing-nyop-max', $new_value );
				break;
			
		}
		return true;
	}

	/**
	 * Return the product's features
	 *
	 * @since 0.4.0
	 *
	 * @param mixed $existing the values passed in by the WP Filter API. Ignored here.
	 * @param integer product_id the WordPress post ID
	 * @return string product feature
	*/
	function get_feature( $existing, $product_id, $options=array() ) {
		$defaults['setting'] = 'enabled';
		$options = ITUtility::merge_defaults( $options, $defaults );
		
		switch ( $options['setting'] ) {
			
			case 'enabled':
				$enabled = get_post_meta( $product_id, '_it-exchange-customer-pricing-enabled', true );
				return empty( $enabled ) ? 'no' : $enabled;
				break;
			case 'pricing-options':
				return get_post_meta( $product_id, '_it-exchange-customer-pricing-options', true );
				break;
			case 'nyop_enabled':
				$nyop_enabled = get_post_meta( $product_id, '_it-exchange-customer-pricing-nyop-enabled', true );
				return empty( $nyop_enabled ) ? 'no' : $nyop_enabled;
				break;
			case 'nyop_min':
				return get_post_meta( $product_id, '_it-exchange-customer-pricing-nyop-min', true );
				break;
			case 'nyop_max':
				return get_post_meta( $product_id, '_it-exchange-customer-pricing-nyop-max', true );
				break;
			
		}
		
		return false;
	}

	/**
	 * Does the product have the feature?
	 *
	 * @since 1.0.0
	 * @param mixed $result Not used by core
	 * @param integer $product_id
	 * @return boolean
	*/
	function product_has_feature( $result, $product_id, $options=array() ) {
		$defaults['setting'] = 'enabled';
		$options = ITUtility::merge_defaults( $options, $defaults );

		// Does this product type support this feature?
		if ( false === $this->product_supports_feature( false, $product_id, $options ) )
			return false;

		// If it does support, does it have it?
		return (boolean) $this->get_feature( false, $product_id, $options );
	}

	/**
	 * Does the product support this feature?
	 *
	 * This is different than if it has the feature, a product can 
	 * support a feature but might not have the feature set.
	 *
	 * @since 1.0.0
	 * @param mixed $result Not used by core
	 * @param integer $product_id
	 * @return boolean
	*/
	function product_supports_feature( $result, $product_id ) {
		// Does this product type support this feature?
		$product_type = it_exchange_get_product_type( $product_id );
		if ( ! it_exchange_product_type_supports_feature( $product_type, 'customer-pricing' ) )
			return false;
			
		// Determine if this product has turned on product availability
		if ( 'no' == it_exchange_get_product_feature( $product_id, 'customer-pricing', array( 'setting' => 'enabled' ) ) ) 
			return false;

		return true;
	}
}
$IT_Exchange_Addon_Customer_Pricing_Product_Feature_Customer_Pricing = new IT_Exchange_Addon_Customer_Pricing_Product_Feature_Customer_Pricing();