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
							'<label for="rcptn_all_regions_addon">' + rcptn_registration_handler_data.addon_input_label + '</label>' +
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

	/**
	 * Find the groups of elements containing the basic and addon levels.
	 */
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

	/**
	 * Insert the input representing the addon.
	 */
	insertAddonInput: function() {
		jQuery( '#rcp_subscription_levels' ).after( this.addonInputTemplate );
	},

	/**
	 * Toggle which set of inputs is visible.
	 *
	 * @param string enabled_level  The level to enable ('basic' or 'addon').
	 */
	toggleSubscriptionInputs: function( enabled_level ) {
		if ( 'basic' === enabled_level ) {
			this.$subscription_levels_items.basic.show();
			this.$subscription_levels_items.addon.hide();
		} else if ( 'addon' === enabled_level ) {
			this.$subscription_levels_items.addon.show();
			this.$subscription_levels_items.basic.hide();
		}

		this.switchSelectedInputLevel( enabled_level );
	},

	/**
	 * Switch the currently selected input according to the new level.
	 *
	 * @param string enabled_level  The level to enable ('basic' or 'addon').
	 */
	switchSelectedInputLevel: function( enabled_level ) {
		// Find the current input and the level.
		var currentlySelectedInput   = this.$rcp_subscription_levels_list.find( 'input[type=radio][name=rcp_level]:checked' ),
			currentlySelectedLevelId = parseInt( currentlySelectedInput.val() );

		// Find the pair of subscriptions that contains the currently selected level.
		var selectedSubscriptionPair = rcptn_registration_handler_data.associated_subscription_ids.filter(
			function( subscription_pair ) {
				return ( subscription_pair.addon_level === currentlySelectedLevelId ) || ( subscription_pair.basic_level === currentlySelectedLevelId );
			}
		)[0];

		// Find the input that corresponds to the currently selected one (the input for the other level in the pair).
		var pairedInput = this.$rcp_subscription_levels_list.find( 'input[type=radio][name=rcp_level]' ).filter(
			function() {
					return parseInt( jQuery( this ).val() ) === selectedSubscriptionPair[ enabled_level + '_level' ];
			}
		);

		// Select the paired input and fire the change event.
		pairedInput.prop( 'checked', true );
		pairedInput.change();
	},

	/**
	 * Set listeners to respond to various actions.
	 */
	setListeners: function() {
		var that = this;

		jQuery( '#rcptn_all_regions_addon' ).on(
			'click', function() {
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
