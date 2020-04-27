<?php
/**
 * Template for Webhook integration metabox.
 *
 * @package cf7_kraken
 * @since 1.1.0
 */

$webhook = get_post_meta( get_the_ID(), 'webhook', true );
$webhook = empty( $webhook ) ? [] : $webhook;
?>

<div class="cf7k-cpt-metabox cf7k-cpt-metabox-webhook">
	<table>
		<tbody>
			<tr>
				<th>
					<?php esc_html_e( 'Webhook URL', 'cf7-kraken' ); ?>
					<span class="cf7k-red-text">(<?php esc_html_e( 'Required', 'cf7-kraken' ); ?>)</span>
				</th>
				<td>
					<input type="text" class="large-text code" name="webhook[webhook_url]" value="<?php echo empty( $webhook['webhook_url'] ) ? '' : esc_url( $webhook['webhook_url'] ); ?>">
					<div><small><i><?php esc_html_e( 'Enter the Zapier webhook URL for receiveing notifications.', 'cf7-kraken' ); ?> <a href="https://zapier.com/apps/webhook/integrations" target="blank"><?php esc_html_e( 'More Info.', 'cf7-kraken' ); ?></a></i></small></div>
				</td>
			</tr>
			<tr class="cf7k-cpt-metabox-webhook-field-mapping-row hidden">
				<th>
					<?php esc_html_e( 'Field Mapping', 'cf7-kraken' ); ?>
				</th>
				<td>
					<i class="cf7k-spin dashicons dashicons-update-alt"></i>
					<div class="cf7k-cpt-metabox-webhook-field-mapping-header hidden">
						<div><strong><?php esc_html_e( 'Form Field', 'cf7-kraken' ); ?></strong></div>
						<div><strong><?php esc_html_e( 'New Field Name', 'cf7-kraken' ); ?></strong></div>
					</div>
					<div class="cf7k-cpt-metabox-webhook-field-mapping hidden"></div>
					<div class="cf7k-cpt-metabox-webhook-field-mapping-add hidden">
						<button type="button" class="button button-primary button-large">Add</button>
					</div>
					<input type="hidden" name="webhook[field_mapping]" value="<?php echo empty( $webhook['field_mapping'] ) ? '' : esc_attr( $webhook['field_mapping'] ); ?>">
				</td>
			</tr>
		</tbody>
	</table>
</div>
