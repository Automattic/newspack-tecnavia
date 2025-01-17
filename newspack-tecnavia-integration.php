<?php
/**
 * Plugin Name: Newspack Tecnavia Integration
 * Description: Provides integration with Tecnavia for Newspack.
 * Version: 1.0.0
 * Author: Automattic
 * Author URI: https://newspack.com/
 * License: GPL2
 * Text Domain: newspack-tecnavia-integration
 *
 * @package Newspack_Tecnavia_Integration
 */

defined( 'ABSPATH' ) || exit;

define( 'NEWSPACK_TECNAVIA_INTEGRATION_VERSION', '1.0.0' );

// Define NEWSPACK_TECNAVIA_INTEGRATION_PLUGIN_FILE.
if ( ! defined( 'NEWSPACK_TECNAVIA_INTEGRATION_PLUGIN_FILE' ) ) {
	define( 'NEWSPACK_TECNAVIA_INTEGRATION_PLUGIN_FILE', __FILE__ );
}

// Define NEWSPACK_TECNAVIA_INTEGRATION_PLUGIN_DIR.
if ( ! defined( 'NEWSPACK_TECNAVIA_INTEGRATION_PLUGIN_DIR' ) ) {
	define( 'NEWSPACK_TECNAVIA_INTEGRATION_PLUGIN_DIR', dirname( plugin_basename( NEWSPACK_TECNAVIA_INTEGRATION_PLUGIN_FILE ) ) );
}

require_once 'vendor/autoload.php';
