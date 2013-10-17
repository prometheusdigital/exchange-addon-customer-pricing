<?php
/**
 * iThemes Exchange Customer Pricing Add-on
 * @package IT_Exchange_Addon_Customer_Pricing
 * @since 1.0.0
*/

/**
 * Shows the nag when needed.
 *
 * @since 1.0.0
 *
 * @return void
*/
function it_exchange_customer_pricing_addon_show_version_nag() {
	if ( $GLOBALS['it_exchange']['version'] < '1.5.0' ) {
		?>
		<div id="it-exchange-add-on-min-version-nag" class="it-exchange-nag">
			<?php printf( __( 'The Customer Pricing add-on requires iThemes Exchange version 1.5.0 or greater. %sPlease upgrade Exchange%s.', 'LION' ), '<a href="' . admin_url( 'update-core.php' ) . '">', '</a>' ); ?>
		</div>
		<script type="text/javascript">
			jQuery( document ).ready( function() {
				if ( jQuery( '.wrap > h2' ).length == '1' ) {
					jQuery("#it-exchange-add-on-min-version-nag").insertAfter('.wrap > h2').addClass( 'after-h2' );
				}
			});
		</script>
		<?php
	}
}
//add_action( 'admin_notices', 'it_exchange_customer_pricing_addon_show_version_nag' );

/**
 * Enqueues Customer Pricing scripts to WordPress Dashboard
 *
 * @since 1.0.0
 *
 * @param string $hook_suffix WordPress passed variable
 * @return void
*/
function it_exchange_customer_pricing_addon_admin_wp_enqueue_scripts( $hook_suffix ) {
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
	
	if ( isset( $post_type ) && 'it_exchange_prod' === $post_type ) {
		$deps = array( 'post', 'jquery-ui-sortable', 'jquery-ui-droppable', 'jquery-ui-tabs', 'jquery-ui-tooltip', 'jquery-ui-datepicker', 'autosave' );
		wp_enqueue_script( 'it-exchange-membership-addon-add-edit-product', ITUtility::get_url_from_file( dirname( __FILE__ ) ) . '/admin/js/add-edit-product.js', $deps );
	}
}
add_action( 'admin_enqueue_scripts', 'it_exchange_customer_pricing_addon_admin_wp_enqueue_scripts' );

/**
 * Enqueues Customer Pricing styles to WordPress Dashboard
 *
 * @since 1.0.0
 *
 * @return void
*/
function it_exchange_customer_pricing_addon_admin_wp_enqueue_styles() {
	global $post, $hook_suffix;

	if ( isset( $_REQUEST['post_type'] ) ) {
		$post_type = $_REQUEST['post_type'];
	} else {
		if ( isset( $_REQUEST['post'] ) ) {
			$post_id = (int) $_REQUEST['post'];
		} else if ( isset( $_REQUEST['post_ID'] ) ) {
			$post_id = (int) $_REQUEST['post_ID'];
		} else {
			$post_id = 0;
		}


		if ( $post_id )
			$post = get_post( $post_id );

		if ( isset( $post ) && !empty( $post ) )
			$post_type = $post->post_type;
	}

	// Exchange Product pages
	if ( isset( $post_type ) && 'it_exchange_prod' === $post_type ) {
		wp_enqueue_style( 'it-exchange-membership-addon-add-edit-product', ITUtility::get_url_from_file( dirname( __FILE__ ) ) . '/admin/styles/add-edit-product.css' );
	}
}
add_action( 'admin_print_styles', 'it_exchange_customer_pricing_addon_admin_wp_enqueue_styles' );

function it_exchange_customer_pricing_before_print_metabox_base_price( $product ) {
	
	$enabled = it_exchange_get_product_feature( $product->ID, 'customer-pricing', array( 'setting' => 'enabled' ) );
	$hide = ( 'yes' == $enabled ) ? 'hide-if-js' : '';

	$html  = '<div id="base-price-customer-pricing-disabled" class="' . $hide . '">';
	
	echo $html;
}
add_action( 'it_exchange_before_print_metabox_base_price', 'it_exchange_customer_pricing_before_print_metabox_base_price' );

function it_exchange_customer_pricing_after_print_metabox_base_price( $product ) {
	$description = __( 'Price', 'LION' );
	$description = apply_filters( 'it_exchange_base-price_addon_metabox_description', $description );
	
	$enabled = it_exchange_get_product_feature( $product->ID, 'customer-pricing', array( 'setting' => 'enabled' ) );
	$hide = ( 'no' == $enabled ) ? 'hide-if-js' : '';
	
	$html  = '</div>'; //ending starting div from it_exchange_customer_pricing_before_print_metabox_base_price
	$html .= '<div id="base-price-customer-pricing-enabled" class="' . $hide . '">';	
	$html .= '<label for="base-price">' . esc_html( $description ) . '</label>';
	$html .= '<div>' . __( 'Customer Pricing', 'LION' ) . ' <span class="tip" title="' . __( 'To change the pricing for this product, go to Customer Pricing under the Advanced section.', 'LION' ) . '">i</span></div>';
	$html .= '</div>';
	
	echo $html;
}
add_action( 'it_exchange_after_print_metabox_base_price', 'it_exchange_customer_pricing_after_print_metabox_base_price' );

/**
 * Adds Customer Pricing Template Path to iThemes Exchange Template paths
 *
 * @since 1.0.0
 * @param array $possible_template_paths iThemes Exchange existing Template paths array
 * @param array $template_names
 * @return array
*/
function it_exchange_customer_pricing_addon_template_path( $possible_template_paths, $template_names ) {
	$possible_template_paths[] = dirname( __FILE__ ) . '/templates/';
	return $possible_template_paths;
}
add_filter( 'it_exchange_possible_template_paths', 'it_exchange_customer_pricing_addon_template_path', 10, 2 );