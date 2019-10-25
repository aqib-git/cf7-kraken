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
					<input type="text" class="large-text code" name="mailchimp[api_key]" value="<?php echo empty( $mailchimp['api_key'] ) ? '' : esc_url( $mailchimp['api_key'] ); ?>">
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
					<select name="mailchimp[audience]">
						<option value="">- None -</option>
					</select>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Double Opt-In', 'cf7-kraken' ); ?></th>
				<td>
					<input type="checkbox" value="yes" name="mailchimp[double_optin]" <?php checked( ! empty( $mailchimp['double_optin'] ), true )?>>
				</td>
			</tr>
		</tbody>
	</table>
</div>
