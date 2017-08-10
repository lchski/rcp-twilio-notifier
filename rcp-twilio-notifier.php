<?php
/**
 * Plugin Name: RCP Twilio Notifier
 * Version: 1.0
 * Plugin URI: https://lucascherkewski.com/
 * Description: Provides Twilio integration for RCP.
 * Author: Lucas Cherkewski
 * Author URI: https://lucascherkewski.com/
 * Requires at least: 4.0
 * Tested up to: 4.8
 *
 * Text Domain: rt
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Lucas Cherkewski
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Setup class autoloader.
require_once dirname( __FILE__ ) . '/vendor/autoload.php';

// Conditionally load environment variables.
if ( file_exists( __DIR__ . '/.env' ) ) {
	(new \Dotenv\Dotenv( __DIR__ ))->load();
}

$rcptwilionotifier = new RcpTwilioNotifier\Plugin();
add_action( 'plugins_loaded', array( $rcptwilionotifier, 'load' ) );
