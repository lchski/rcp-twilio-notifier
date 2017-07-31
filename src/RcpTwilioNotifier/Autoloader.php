<?php
/**
 * Autoloader: RcpTwilioNotifier_Autoloader class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier
 */

/**
 * Autoloads MyPlugin classes using PSR-0 standard.
 *
 * Props to Carl Alexander (https://carlalexander.ca/organizing-files-object-oriented-wordpress-plugin/), as per usual.
 */
class RcpTwilioNotifier_Autoloader {

	/**
	 * Registers RcpTwilioNotifier_Autoloader as an SPL autoloader.
	 *
	 * @param boolean $prepend  Whether to prepend or append this autoloader to the autoloader stack.
	 */
	public static function register( $prepend = false ) {
		if ( version_compare( phpversion(), '5.3.0', '>=' ) ) {
			spl_autoload_register( array( new self(), 'autoload' ), true, $prepend );
		} else {
			spl_autoload_register( array( new self(), 'autoload' ) );
		}
	}

	/**
	 * Handles autoloading of RcpTwilioNotifier classes.
	 *
	 * @param string $class  The class to load.
	 */
	public static function autoload( $class ) {
		if ( 0 !== strpos( $class, 'RcpTwilioNotifier' ) ) {
			return;
		}

		$file = dirname( __FILE__ ) . '/../' . str_replace( array( '_', "\0" ), array( '/', '' ), $class ) . '.php';

		if ( is_file( $file ) ) {
			require_once $file;
		}
	}
}
