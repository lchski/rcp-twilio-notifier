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
require_once dirname( __FILE__ ) . '/src/RcpTwilioNotifier/Autoloader.php';
RcpTwilioNotifier_Autoloader::register();

$rcptwilionotifier = new RcpTwilioNotifier_Plugin();
