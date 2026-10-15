<?php
/**
 * Solaris theme bootstrap. Keeps functions.php as a thin loader — all real
 * logic lives in inc/ so each concern (setup, enqueue, customizer, WooCommerce,
 * hooks, security, CPTs, contact form) stays independently readable.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SOLARIS_VERSION', '1.0.0' );
define( 'SOLARIS_DIR', get_template_directory() );
define( 'SOLARIS_URI', get_template_directory_uri() );

$solaris_includes = array(
	'inc/theme-setup.php',
	'inc/enqueue.php',
	'inc/customizer-controls.php',
	'inc/customizer.php',
	'inc/template-functions.php',
	'inc/template-hooks.php',
	'inc/woocommerce.php',
	'inc/security.php',
	'inc/content-types.php',
	'inc/contact-form.php',
	'inc/ajax-search.php',
	'inc/ajax-quick-view.php',
	'inc/ajax-shop-loading.php',
);

foreach ( $solaris_includes as $solaris_file ) {
	require_once SOLARIS_DIR . '/' . $solaris_file;
}
unset( $solaris_includes, $solaris_file );
