<?php
/**
 * Plugin Name: ExtraChill AI Client
 * Plugin URI: https://extrachill.com
 * Description: Network-activated AI provider library for ExtraChill Platform. Provides unified AI integration across OpenAI, Anthropic, Google Gemini, Grok, and OpenRouter.
 * Version: 1.0.0
 * Author: Chris Huber
 * Author URI: https://chubes.net
 * Network: true
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Text Domain: extrachill-ai-client
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'EXTRACHILL_AI_CLIENT_VERSION', '1.0.0' );
define( 'EXTRACHILL_AI_CLIENT_PLUGIN_FILE', __FILE__ );
define( 'EXTRACHILL_AI_CLIENT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'EXTRACHILL_AI_CLIENT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Load Composer autoloader
if ( file_exists( EXTRACHILL_AI_CLIENT_PLUGIN_DIR . 'vendor/autoload.php' ) ) {
	require_once EXTRACHILL_AI_CLIENT_PLUGIN_DIR . 'vendor/autoload.php';
}

// Activation check: require multisite installation
register_activation_hook( __FILE__, 'extrachill_ai_client_activate' );

function extrachill_ai_client_activate() {
	if ( ! is_multisite() ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		wp_die( 'ExtraChill AI Client plugin requires a WordPress multisite installation.' );
	}
}

// Initialize plugin
add_action( 'plugins_loaded', 'extrachill_ai_client_init' );

function extrachill_ai_client_init() {
	// Load network admin settings page
	if ( is_admin() && is_network_admin() ) {
		require_once EXTRACHILL_AI_CLIENT_PLUGIN_DIR . 'inc/admin-settings.php';
	}
}