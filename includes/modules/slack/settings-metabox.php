<?php
/**
 * Template for slack integration metabox.
 *
 * @package cf7_kraken
 * @since 1.0.0
 */

$slack = get_post_meta( get_the_ID(), 'slack', true );
$slack = empty( $slack ) ? [] : $slack;
?>

<div class="cf7k-cpt-metabox">
	<table>
		<tbody>
			<tr>
				<th><?php esc_html_e( 'Webhook URL', 'cf7-kraken' ); ?></th>
				<td>
					<input type="text" class="large-text code" name="slack[webhook_url]" value="<?php echo empty( $slack['webhook_url'] ) ? '' : esc_url( $slack['webhook_url'] ); ?>">
					<div><small><i><?php esc_html_e( 'Enter the Slack webhook URL for Slack notifications', 'cf7-kraken' ); ?> <a href="https://slack.com/apps/A0F7XDUAZ-incoming-webhooks/" target="blank"><?php esc_html_e( 'More Info.', 'cf7-kraken' ); ?></a></i></small></div>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Channel', 'cf7-kraken' ); ?></th>
				<td>
					<input type="text" class="large-text code" name="slack[channel]" value="<?php echo empty( $slack['channel'] ) ? '' : esc_html( $slack['channel'] ); ?>">
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Username', 'cf7-kraken' ); ?></th>
				<td>
					<input type="text" class="large-text code" name="slack[username]" value="<?php echo empty( $slack['username'] ) ? '' : esc_html( $slack['username'] ); ?>">
				</td>
			</tr>
			<tr>
				<th>Pre Text</th>
				<td>
					<input type="text" class="large-text code" name="slack[pre_text]" value="<?php echo empty( $slack['pre_text'] ) ? '' : esc_html( $slack['pre_text'] ); ?>">
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Title', 'cf7-kraken' ); ?></th>
				<td>
					<input type="text" class="large-text code" name="slack[title]" value="<?php echo empty( $slack['title'] ) ? '' : esc_html( $slack['title'] ); ?>">
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Description', 'cf7-kraken' ); ?></th>
				<td>
					<input type="text" class="large-text code" name="slack[text]" value="<?php echo empty( $slack['text'] ) ? '' : esc_html( $slack['text'] ); ?>">
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Fallback Message', 'cf7-kraken' ); ?></th>
				<td>
					<input type="text" class="large-text code" name="slack[fallback_message]" value="<?php echo empty( $slack['fallback_message'] ) ? '' : esc_html( $slack['fallback_message'] ); ?>">
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Send Form Data', 'cf7-kraken' ); ?></th>
				<td>
					<input type="checkbox" value="yes" name="slack[send_form_data]" <?php checked( ! empty( $slack['send_form_data'] ), true ); ?>>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Timestamp', 'cf7-kraken' ); ?></th>
				<td>
					<input type="checkbox" value="yes" name="slack[show_timestamp]" <?php checked( ! empty( $slack['show_timestamp'] ), true ); ?>>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Footer', 'cf7-kraken' ); ?></th>
				<td>
					<input type="checkbox" value="yes" name="slack[show_footer]" <?php checked( ! empty( $slack['show_footer'] ), true ); ?>>
				</td>
			</tr>
			<tr>
				<th><?php esc_html_e( 'Webhook Color', 'cf7-kraken' ); ?></th>
				<td>
					<input type="text" name="slack[webhook_color]" class="cf7k-color-picker-field" value="<?php echo empty( $slack['webhook_color'] ) ? '' : esc_html( $slack['webhook_color'] ); ?>">
				</td>
			</tr>
		</tbody>
	</table>
</div>
