/**
 * Progressive enhancement for the RCP registration page.
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier
 */

/**
 * RCPTN Registration Controller
 *
 * Manages RCPTN modifications to RCP registration form.
 */
var RCPTN_Registration_Controller = {
	$rcp_subscription_levels_list: jQuery( '#rcp_subscription_levels' ),

	addonInputTemplate: '<div>' +
							'<input type="checkbox" id="rcptn_all_regions_addon">' +
							'<label for="rcptn_all_regions_addon">Enable all regions add-on</label>' +
						'</div>',

	/**
	 * Class setup
	 */
	init: function() {
		if ( null === document.getElementById( 'rcp_registration_form' ) ) {
			return;
		}

		if ( null === document.getElementById( 'rcp_subscription_levels' ) ) {
			return;
		}

		this.insertAddonInput();
		this.toggleSubscriptionInputs( 'basic' );
		this.setListeners();
	},

	insertAddonInput: function() {
		jQuery( '#rcp_subscription_levels' ).after( this.addonInputTemplate );
	},

	toggleSubscriptionInputs: function( enabled_level ) {
		var that = this;

		jQuery.each(
			rcptn_registration_handler_data.associated_subscription_ids, function( index, subscription_pair ) {
				if ( 'basic' === enabled_level ) {
					that.$rcp_subscription_levels_list.find( '.rcp_subscription_level_' + subscription_pair.basic_level ).show();
					that.$rcp_subscription_levels_list.find( '.rcp_subscription_level_' + subscription_pair.addon_level ).hide();
				} else if ( 'addon' === enabled_level ) {
					that.$rcp_subscription_levels_list.find( '.rcp_subscription_level_' + subscription_pair.addon_level ).show();
					that.$rcp_subscription_levels_list.find( '.rcp_subscription_level_' + subscription_pair.basic_level ).hide();
				}
			}
		);
	},

	/**
	 * Listeners to respond to various actions
	 */
	setListeners: function() {
		var that = this;

		jQuery( '#rcptn_all_regions_addon' ).on(
			'click', function(e) {
				if ( jQuery( this )[0].checked ) {
					that.toggleSubscriptionInputs( 'addon' );
				} else {
					that.toggleSubscriptionInputs( 'basic' );
				}
			}
		);
	}
};

// Make the registration class globally available.
window['RCPTN_Registration_Controller'] = RCPTN_Registration_Controller;

jQuery( document ).ready(
	function() {
		RCPTN_Registration_Controller.init();
	}
);
