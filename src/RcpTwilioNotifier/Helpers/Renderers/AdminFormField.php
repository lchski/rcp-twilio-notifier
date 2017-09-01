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
	 * @param string       $id              The field's ID. Used for the field name, too.
	 * @param string       $label           Short label for the field.
	 * @param string|array $description     Longer description of the field. If an array, renders a paragraph for each item.
	 * @param callable     $field_callback  A callback to render the field. Receives the field’s ID as a parameter.
	 * @param array        $args {
	 *          Optional. Finer control over various settings.
	 *
	 *    @type bool  $required     Whether the field is required.
	 *    @type mixed $field_value  A value to pass to the field callback, representing the field’s value.
	 * }
	 */
	public static function render( $id, $label, $description, $field_callback, $args = array() ) {
		$defaults = array(
			'required'    => true,
			'field_value' => null,
		);

		$merged_args = wp_parse_args( $args, $defaults );

		?>
			<tr class="form-field">
				<th scope="row" valign="top">
					<label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?><?php self::render_required( $merged_args['required'] ); ?></label>
				</th>
				<td>
					<?php
					if ( null !== $merged_args['field_value'] ) {
						$field_callback( $id, $merged_args['field_value'] );
					} else {
						$field_callback( $id );
					}
					?>
					<?php self::render_description( $description ); ?>
				</td>
			</tr>
		<?php
	}

	/**
	 * Outputs a notice if the field is required.
	 *
	 * @param bool $is_required  Whether the field is required.
	 */
	private static function render_required( $is_required ) {
		if ( $is_required ) {
			echo ' ' . esc_html__( '(required)', 'rcptn' );
		}
	}

	/**
	 * Renders a single description paragraph or a series thereof, depending on the input.
	 *
	 * @param array|string $description  Longer description for the field.
	 */
	private static function render_description( $description ) {
		if ( is_string( $description ) && '' === $description ) {
			return;
		}

		$renderer = function( $text ) {
			echo '<p class="description">' . esc_html( $text ) . '</p>';
		};

		if ( is_string( $description ) ) {
			$renderer( $description );
			return;
		}

		if ( is_array( $description ) ) {
			array_map( $renderer, $description );
			return;
		}
	}

}
