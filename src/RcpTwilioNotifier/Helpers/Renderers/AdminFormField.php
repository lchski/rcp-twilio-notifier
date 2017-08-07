<?php
/**
 * RCP: RcpTwilioNotifier\Helpers\Renderers\AdminFormField class
 *
 * @package WordPress
 * @subpackage RcpTwilioNotifier\Helpers\Renderers
 */

namespace RcpTwilioNotifier\Helpers\Renderers;

/**
 * Render routine wrapper markup for admin form fields.
 */
class AdminFormField {

	/**
	 * Render a form field conforming to WordPress markup patterns.
	 *
	 * @param string $id              The field's ID. Used for the field name, too.
	 * @param string $label           Short label for the field.
	 * @param string $description     Longer description of the field.
	 * @param func   $field_callback  A callback to render the field. Receives the field’s ID as a parameter.
	 */
	public static function render( $id, $label, $description, $field_callback ) {
		?>
			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
				</th>
				<td>
					<?php $field_callback( $id ); ?>
					<p class="description"><?php echo esc_html( $description ); ?></p>
				</td>
			</tr>
		<?php
	}

}
