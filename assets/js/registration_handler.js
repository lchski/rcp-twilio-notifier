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

	$subscription_levels_items: {
		basic: [],
		addon: []
	},

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

		this.setSubscriptionLevelVariables();

		this.insertAddonInput();
		this.toggleSubscriptionInputs( 'basic' );

		this.setListeners();
	},

	setSubscriptionLevelVariables: function() {
		var basic_level_selectors = rcptn_registration_handler_data.associated_subscription_ids.map(
			function( subscription_pair ) {
					return '.rcp_subscription_level_' + subscription_pair.basic_level;
			}
		).join( ', ' );

		var addon_level_selectors = rcptn_registration_handler_data.associated_subscription_ids.map(
			function( subscription_pair ) {
					return '.rcp_subscription_level_' + subscription_pair.addon_level;
			}
		).join( ', ' );

		this.$subscription_levels_items.basic = this.$rcp_subscription_levels_list.find( basic_level_selectors );
		this.$subscription_levels_items.addon = this.$rcp_subscription_levels_list.find( addon_level_selectors );
	},

	insertAddonInput: function() {
		jQuery( '#rcp_subscription_levels' ).after( this.addonInputTemplate );
	},

	toggleSubscriptionInputs: function( enabled_level ) {
		if ( 'basic' === enabled_level ) {
			this.$subscription_levels_items.basic.show();
			this.$subscription_levels_items.addon.hide();
		} else if ( 'addon' === enabled_level ) {
			this.$subscription_levels_items.addon.show();
			this.$subscription_levels_items.basic.hide();
		}
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
