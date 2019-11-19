<?php
/**
 * Template for mailchimp integration metabox.
 *
 * @package cf7_kraken
 * @since 1.0.0
 */

$mailchimp = get_post_meta( get_the_ID(), 'mailchimp', true );
$mailchimp = empty( $mailchimp ) ? [] : $mailchimp;
?>

<div class="cf7k-cpt-metabox cf7k-cpt-metabox-mailchimp">
	<table>
		<tbody>
			<tr>
				<th><?php esc_html_e( 'API Key', 'cf7-kraken' ); ?></th>
				<td>
					<input type="text" class="large-text code" name="mailchimp[api_key]" value="<?php echo empty( $mailchimp['api_key'] ) ? '' : esc_attr( $mailchimp['api_key'] ); ?>">
					<div>
						<small>
							<i>
								<?php esc_html_e( 'Get Your Mailchimp Account API Key', 'cf7-kraken' ); ?>
								<a href="https://mailchimp.com/help/about-api-keys/#Find_or_Generate_Your_API_Key" target="blank">
									<?php esc_html_e( 'More Info.', 'cf7-kraken' ); ?>
								</a>
							</i>
						</small>
					</div>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Audience', 'cf7-kraken' ); ?></th>
				<td>
					<div class="cf7k-cpt-metabox-mailchimp-audience">
						<select name="mailchimp[audience]" data-value="<?php echo esc_attr( $mailchimp['audience'] ); ?>">
							<option value="">- None -</option>
						</select>
						<i class="cf7k-spin dashicons dashicons-update-alt"></i>
					</div>
				</td>
			</tr>
			<tr class="cf7k-cpt-metabox-mailchimp-groups-row hidden">
				<th><?php esc_html_e( 'Groups', 'cf7-kraken' ); ?></th>
				<td>
					<div class="cf7k-cpt-metabox-mailchimp-groups">
						<select name="mailchimp[groups][]" multiple="multiple" data-value="<?php echo esc_attr( json_encode( $mailchimp['groups'] ) ); ?>">
						</select>
						<i class="cf7k-spin dashicons dashicons-update-alt"></i>
					</div>
				</td>
			</tr>
			<tr class="cf7k-cpt-metabox-mailchimp-double-optin-row hidden">
				<th><?php esc_html_e( 'Double Opt-In', 'cf7-kraken' ); ?></th>
				<td>
					<input type="checkbox" value="yes" name="mailchimp[double_optin]" <?php checked( ! empty( $mailchimp['double_optin'] ), true )?>>
				</td>
			</tr>
			</tr>
			<tr class="cf7k-cpt-metabox-mailchimp-field-mapping-row hidden">
				<th><?php esc_html_e( 'Field Mapping', 'cf7-kraken' ); ?></th>
				<td>
					<i class="cf7k-spin dashicons dashicons-update-alt"></i>
					<div class="cf7k-cpt-metabox-mailchimp-field-mapping-header hidden">
						<div><strong><?php echo __( 'Form Field'); ?></strong></div>
						<div><strong><?php echo __( 'Mailchimp Merge Fields'); ?></strong></div>
					</div>
					<div class="cf7k-cpt-metabox-mailchimp-field-mapping hidden"></div>
					<div class="cf7k-cpt-metabox-mailchimp-field-mapping-add hidden">
						<button type="button" class="button button-primary button-large">Add</button>
					</div>
					<input type="hidden" name="mailchimp[field_mapping]" value="<?php echo esc_attr( $mailchimp['field_mapping'] ); ?>">
				</td>
			</tr>
		</tbody>
	</table>
</div>
