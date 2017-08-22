/**
 * Progressive enhancement for the RCP registration page.
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier
 */

(function($) {
	var rcp_registration_form   = document.getElementById( 'rcp_registration_form' ),
		rcp_subscription_levels = document.getElementById( 'rcp_subscription_levels' );

	if ( null === rcp_registration_form ) {
		return;
	}

	if ( null === rcp_subscription_levels ) {
		return;
	}

	$( '#rcp_subscription_levels' ).after( rcptn_get_addon_template() );

	rcptn_toggle_subscription_inputs( 'basic' );

	$( '#rcptn_all_regions_addon' ).on(
		'click', function(e) {
			if ( $( this )[0].checked ) {
				rcptn_toggle_subscription_inputs( 'addon' );
			} else {
				rcptn_toggle_subscription_inputs( 'basic' );
			}
		}
	);
})( jQuery );

function rcptn_get_addon_template() {
	return '<div>' +
		'<input type="checkbox" id="rcptn_all_regions_addon">' +
		'<label for="rcptn_all_regions_addon">Enable all regions add-on</label>' +
		'</div>';
}

function rcptn_toggle_subscription_inputs( enabled_level ) {
	var $rcp_subscription_levels_list = jQuery( '#rcp_subscription_levels' );

	jQuery.each(
		rcptn_registration_handler_data.associated_subscription_ids, function( index, subscription_pair ) {
			if ( 'basic' === enabled_level ) {
				$rcp_subscription_levels_list.find( '.rcp_subscription_level_' + subscription_pair.basic_level ).show();
				$rcp_subscription_levels_list.find( '.rcp_subscription_level_' + subscription_pair.addon_level ).hide();
			} else if ( 'addon' === enabled_level ) {
				$rcp_subscription_levels_list.find( '.rcp_subscription_level_' + subscription_pair.addon_level ).show();
				$rcp_subscription_levels_list.find( '.rcp_subscription_level_' + subscription_pair.basic_level ).hide();
			}
		}
	);
}
