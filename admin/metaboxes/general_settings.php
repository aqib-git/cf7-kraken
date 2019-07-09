<?php


class CF7_Kraken_General_Settings_Metabox {

	/**
	 * Metabox Id.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @var string
	 */
	protected $id = 'cf7k_general_settings_metabox';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function __construct()
	{
		add_meta_box(
			$this->id,
			__( 'General Settings', 'cf7_kraken' ),
			[ $this, 'template' ],
			'cf7k_integrations',
			'normal',
			'high'
		);
	}

	/**
	 * Metabox Template.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function template( $post ) {
		$contact_forms = get_posts([
			'post_type'   => 'wpcf7_contact_form',
			'numberposts' => -1,
		]);

		$modules = cf7k_init()->modules;

		$cf7_id         = get_post_meta( $post->ID, 'cf7_id', true );
		$integrations   = get_post_meta( $post->ID, 'integrations', true );

		if ( empty ( $integrations ) ) {
			$integrations = [];
		}

		wp_nonce_field( 'cf7k_cpt_meta_box_nonce', 'cf7k_cpt_meta_box_nonce' );
		?>
		<div class="cf7k-cpt-metabox">
			<table>
				<tbody>
					<tr>
						<th>Select Contact Form</th>
						<td>
							<select name="cf7_id">
								<?php foreach( $contact_forms as $contact_form ) : ?>
									<option value="<?php echo $contact_form->ID; ?>" <?php selected( $cf7_id, $contact_form->ID );?>> <?php echo esc_html( $contact_form->post_title ); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<th>Enable Integrations</th>
						<td>
							<select name="integrations[]" class="js-cf7k-integrations" multiple="multiple">
								<?php foreach( $modules as $module ) : ?>
									<option value="<?php echo esc_html( $module->get_name() ); ?>" <?php selected( in_array( $module->get_name(), $integrations, true ), true ); ?>> <?php echo esc_html( $module->get_title() ); ?></option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php
	}
}

new CF7_Kraken_General_Settings_Metabox();
