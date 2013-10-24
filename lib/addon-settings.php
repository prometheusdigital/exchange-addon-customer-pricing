<?php
/**
 * iThemes Exchange Customer_Pricing Add-on
 * @package IT_Exchange_Addon_Customer_Pricing
 * @since 1.0.0
*/

/**
 * Call back for settings page
 *
 * This is set in options array when registering the add-on and called from it_exchange_enable_addon()
 *
 * @since 1.0.0
 * @return void
*/
function it_exchange_customer_pricing_settings_callback() {
	$IT_Exchange_Customer_Pricing_Add_On = new IT_Exchange_Customer_Pricing_Add_On();
	$IT_Exchange_Customer_Pricing_Add_On->print_settings_page();
}

/**
 * Sets the default options for customer pricing settings
 *
 * @since 1.0.0
 * @return array settings
*/
function it_exchange_customer_pricing_default_settings( $defaults ) {
	$defaults['customer-pricing-or-more-label'] = __( 'or more', 'LION' );
	$defaults['customer-pricing-nyop-label']    = __( 'Name your price!', 'LION' );
	$defaults['customer-pricing-output-type']   = 'radio';
	return $defaults;
}
add_filter( 'it_storage_get_defaults_exchange_addon_customer_pricing', 'it_exchange_customer_pricing_default_settings' );

class IT_Exchange_Customer_Pricing_Add_On {

	/**
	 * @var boolean $_is_admin true or false
	 * @since 1.0.0
	*/
	var $_is_admin;

	/**
	 * @var string $_current_page Current $_GET['page'] value
	 * @since 1.0.0
	*/
	var $_current_page;

	/**
	 * @var string $_current_add_on Current $_GET['add-on-settings'] value
	 * @since 1.0.0
	*/
	var $_current_add_on;

	/**
	 * @var string $status_message will be displayed if not empty
	 * @since 1.0.0
	*/
	var $status_message;

	/**
	 * @var string $error_message will be displayed if not empty
	 * @since 1.0.0
	*/
	var $error_message;

	/**
 	 * Class constructor
	 *
	 * Sets up the class.
	 * @since 1.0.0
	 * @return void
	*/
	function IT_Exchange_Customer_Pricing_Add_On() {
		$this->_is_admin       = is_admin();
		$this->_current_page   = empty( $_GET['page'] ) ? false : $_GET['page'];
		$this->_current_add_on = empty( $_GET['add-on-settings'] ) ? false : $_GET['add-on-settings'];

		if ( ! empty( $_POST ) && $this->_is_admin && 'it-exchange-addons' == $this->_current_page && 'customer-pricing' == $this->_current_add_on ) {
			add_action( 'it_exchange_save_add_on_settings_customer_pricing', array( $this, 'save_settings' ) );
			do_action( 'it_exchange_save_add_on_settings_customer_pricing' );
		}
	}

	function print_settings_page() {
		$settings = it_exchange_get_option( 'addon_customer_pricing', true );
	
		$form_values  = empty( $this->error_message ) ? $settings : ITForm::get_post_data();
		$form_options = array(
			'id'      => apply_filters( 'it_exchange_add_on_customer_pricing', 'it-exchange-add-on-customer-pricing-settings' ),
			'enctype' => apply_filters( 'it_exchange_add_on_customer_pricing_settings_form_enctype', false ),
			'action'  => 'admin.php?page=it-exchange-addons&add-on-settings=customer-pricing',
		);
		$form         = new ITForm( $form_values, array( 'prefix' => 'it-exchange-add-on-customer-pricing' ) );

		if ( ! empty ( $this->status_message ) )
			ITUtility::show_status_message( $this->status_message );
		if ( ! empty( $this->error_message ) )
			ITUtility::show_error_message( $this->error_message );

		?>
		<div class="wrap">
			<?php screen_icon( 'it-exchange' ); ?>
			<h2><?php _e( 'Customer Pricing Settings', 'LION' ); ?></h2>

			<?php do_action( 'it_exchange_customer_pricing_settings_page_top' ); ?>
			<?php do_action( 'it_exchange_addon_settings_page_top' ); ?>

			<?php $form->start_form( $form_options, 'it-exchange-customer-pricing-settings' ); ?>
				<?php do_action( 'it_exchange_customer_pricing_settings_form_top' ); ?>
				<?php $this->get_customer_pricing_form_table( $form, $form_values ); ?>
				<?php do_action( 'it_exchange_customer_pricing_settings_form_bottom' ); ?>
				<p class="submit">
					<?php $form->add_submit( 'submit', array( 'value' => __( 'Save Changes', 'LION' ), 'class' => 'button button-primary button-large' ) ); ?>
				</p>
			<?php $form->end_form(); ?>
			<?php do_action( 'it_exchange_customer_pricing_settings_page_bottom' ); ?>
			<?php do_action( 'it_exchange_addon_settings_page_bottom' ); ?>
		</div>
		<?php
	}

	function get_customer_pricing_form_table( $form, $settings = array() ) {
		if ( !empty( $settings ) )
			foreach ( $settings as $key => $var )
				$form->set_option( $key, $var );
		?>
        
        <div class="it-exchange-addon-settings it-exchange-customer-pricing-addon-settings">
            <p><?php _e( 'Customer Pricing lets customers choose their price from a list of price options you create or let them enter their own price.', 'LION' ); ?></p>
			<h4><label for="customer-pricing-or-more-label"><?php _e( '"or more" Label', 'LION' ) ?> <span class="tip" title="<?php _e( 'The text that appears next to a price to signify that it has more pricing options.', 'LION' ); ?>">i</span></label></h4>
			<p> <?php $form->add_text_box( 'customer-pricing-or-more-label' ); ?> </p>
			<h4><label for="customer-pricing-nyop-label"><?php _e( '"Name your price" Label', 'LION' ) ?> <span class="tip" title="<?php _e( "The label that appears next to the 'name your price' label.", 'LION' ); ?>">i</span></label></h4>
			<p> <?php $form->add_text_box( 'customer-pricing-nyop-label' ); ?> </p>
            
            <h4><label for="it-exchange-customer-pricing-output-type"><?php _e( 'Price selection type', 'LION' ); ?> <span class="tip" title="<?php _e( 'How would you like the price options to display?', 'LION' ); ?>">i</span></label></h4>
			<p> <?php 
			$output_types = apply_filters( 'it-exchange-customer-pricing-output-types', array(
				'radio' => __( 'Radio', 'LION' ),
				'select' => __( 'Drop Down', 'LION' ),
			) );
			$form->add_drop_down( 'customer-pricing-output-type', $output_types ); ?> </p>
		</div>
		<?php
	}

	/**
	 * Save settings
	 *
	 * @since 1.0.0
	 * @return void
	*/
	function save_settings() {
		$defaults = it_exchange_get_option( 'addon_customer_pricing' );
		$new_values = wp_parse_args( ITForm::get_post_data(), $defaults );

		// Check nonce
		if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'it-exchange-customer-pricing-settings' ) ) {
			$this->error_message = __( 'Error. Please try again', 'LION' );
			return;
		}

		$errors = apply_filters( 'it_exchange_add_on_manual_transaction_validate_settings', $this->get_form_errors( $new_values ), $new_values );
		if ( ! $errors && it_exchange_save_option( 'addon_customer_pricing', $new_values ) ) {
			ITUtility::show_status_message( __( 'Settings saved.', 'LION' ) );
		} else if ( $errors ) {
			$errors = implode( '<br />', $errors );
			$this->error_message = $errors;
		} else {
			$this->status_message = __( 'Settings not saved.', 'LION' );
		}
	}

	/**
	 * Validates for values
	 *
	 * Returns string of errors if anything is invalid
	 *
	 * @since 1.0.0
	 * @return void
	*/
	function get_form_errors( $values ) {
		
		$default_wizard_customer_pricing_settings = apply_filters( 'default_customer_pricing_settings', array( 'customercustomer-pricing-or-more-label', 'customer-pricing-nyop-label', 'customer-pricing-output-type' ) );
		$errors = array();
		return $errors;
	}
}