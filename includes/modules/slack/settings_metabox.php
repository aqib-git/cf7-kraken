<?php
$slack = get_post_meta( get_the_ID(), 'slack', true );
$slack = empty( $slack ) ? [] : $slack;
?>

<div class="cf7k-cpt-metabox">
	<table>
		<tbody>
			<tr>
				<th>Webhook URL</th>
				<td>
					<input type="text" class="large-text code" name="slack[webhook_url]" value="<?php echo empty( $slack['webhook_url'] ) ? '' : esc_url( $slack['webhook_url'] ); ?>">
					<div><small><i>Enter the Slack webhook URL for Slack notifications <a href="https://slack.com/apps/A0F7XDUAZ-incoming-webhooks/" target="blank">More Info.</a></i></small></div>
				</td>
			</tr>
			<tr>
				<th>Channel</th>
				<td>
					<input type="text" class="large-text code" name="slack[channel]" value="<?php echo empty( $slack['channel'] ) ? '' : esc_html( $slack['channel'] ); ?>">
				</td>
			</tr>
			<tr>
				<th>Username</th>
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
				<th>Title</th>
				<td>
					<input type="text" class="large-text code" name="slack[title]" value="<?php echo empty( $slack['title'] ) ? '' : esc_html( $slack['title'] ); ?>">
				</td>
			</tr>
			<tr>
				<th>Description</th>
				<td>
					<input type="text" class="large-text code" name="slack[description]" value="<?php echo empty( $slack['description'] ) ? '' : esc_html( $slack['description'] ); ?>">
				</td>
			</tr>
			<tr>
				<th>Fallback Message</th>
				<td>
					<input type="text" class="large-text code" name="slack[fallback_message]" value="<?php echo empty( $slack['fallback_message'] ) ? '' : esc_html( $slack['fallback_message'] ); ?>">
				</td>
			</tr>
			<tr>
				<th>Send Form Data</th>
				<td>
					<input type="checkbox" value="yes" name="slack[send_form_data]" <?php checked( ! empty( $slack['send_form_data'] ), true )?>>
				</td>
			</tr>
			<tr>
				<th>Timestamp</th>
				<td>
					<input type="checkbox" value="yes" name="slack[show_timestamp]" <?php checked( ! empty( $slack['show_timestamp'] ), true )?>>
				</td>
			</tr>
			<tr>
				<th>Footer</th>
				<td>
					<input type="checkbox" value="yes" name="slack[show_footer]" <?php checked( ! empty( $slack['show_footer'] ), true )?>>
				</td>
			</tr>
			<tr>
				<th>Webhook Color</th>
				<td>
					<input type="text" name="slack[webhook_color]" class="cf7k-color-picker-field" value="<?php echo empty( $slack['webhook_color'] ) ? '' : esc_html( $slack['webhook_color'] ); ?>">
				</td>
			</tr>
		</tbody>
	</table>
</div>
