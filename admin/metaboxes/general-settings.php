<?php
/**
 * General Settings Metabox.
 *
 * @package cf7_kraken
 * @since 1.0.0
 */

/**
 * General Settings Metabox class.
 *
 * @since 1.0.0
 */
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
	public function __construct() {
		add_meta_box(
			$this->id,
			__( 'General Settings', 'cf7-kraken' ),
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
	 * @param WP_Post $post Post object.
	 *
	 * @return void
	 */
	public function template( $post ) {
		$contact_forms = get_posts(
			[
				'post_type'   => 'wpcf7_contact_form',
				'numberposts' => -1,
			]
		);

		$modules      = cf7k_init()->modules;
		$cf7_id       = get_post_meta( $post->ID, 'cf7_id', true );
		$integrations = get_post_meta( $post->ID, 'integrations', true );

		if ( empty( $integrations ) ) {
			$integrations = [];
		}

		$sorted_modules = [];
		foreach ( $integrations as $integration ) {
			if ( isset( $modules[ $integration ] ) ) {
				$sorted_modules[ $integration ] = $modules[ $integration ];
			}
		}

		foreach ( $modules as $module_name => $module ) {
			if ( ! isset( $sorted_modules[ $module_name ] ) ) {
				$sorted_modules[ $module_name ] = $module;
			}
		}

		wp_nonce_field( 'cf7k_cpt_meta_box_nonce', 'cf7k_cpt_meta_box_nonce' );
		?>
		<div class="cf7k-cpt-metabox">
			<table>
				<tbody>
					<tr>
						<th>Select Contact Form</th>
						<td>
							<?php if ( count( $contact_forms ) > 0 ): ?>
								<select name="cf7_id">
									<?php foreach ( $contact_forms as $contact_form ) : ?>
										<option value="<?php echo esc_attr( $contact_form->ID ); ?>" <?php echo esc_attr( selected( $cf7_id, $contact_form->ID, false ) ); ?>> <?php echo esc_html( $contact_form->post_title ); ?></option>
									<?php endforeach; ?>
								</select>
							<?php else : ?>
								<a class="create-cf7-link" href="<?php echo esc_url( admin_url( 'admin.php?page=wpcf7-new' ) ); ?>" target="_blank"><?php esc_html_e( 'Create a Contact Form', 'cf7-kraken' ); ?> <i class="dashicons dashicons-external"></i></a>
							<?php endif; ?>
						</td>
					</tr>
					<tr class="integrations-row hidden">
						<th>Enable Integrations</th>
						<td>
							<select name="integrations[]" class="js-cf7k-integrations" multiple="multiple">
								<?php foreach ( $sorted_modules as $module_name => $module ) : ?>
									<option value="<?php echo esc_html( $module_name ); ?>" <?php echo esc_attr( selected( in_array( $module_name, $integrations, true ), true, false ) ); ?>> <?php echo esc_html( $module->get_title() ); ?></option>
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
