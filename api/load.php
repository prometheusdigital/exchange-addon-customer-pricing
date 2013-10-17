<?php
/**
 * iThemes Exchange Customer Pricing Add-on
 * load theme API functions
 * @package IT_Exchange_Addon_Customer_Pricing
 * @since 1.0.0
*/

if ( is_admin() ) {
	// Admin only
} else {
	// Frontend only
	include( 'theme.php' );
}