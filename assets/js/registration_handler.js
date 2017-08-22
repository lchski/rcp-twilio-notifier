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

	var $rcp_subscription_levels_list = $( '#rcp_subscription_levels' );

	$rcp_subscription_levels_list.after( rcptn_get_addon_template() );

	$.each(
		rcptn_registration_handler_data.associated_subscription_ids, function( index, subscription_pair ) {
			$rcp_subscription_levels_list.find( '.rcp_subscription_level_' + subscription_pair.addon_level ).hide();
		}
	);
})( jQuery );

function rcptn_get_addon_template() {
	return '<div>' +
		'<input type="checkbox" id="rcptn_all_regions_addon">' +
		'<label for="rcptn_all_regions_addon">Enable all regions add-on</label>' +
		'</div>';
}
