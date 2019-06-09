<?php
    $contact_forms = get_posts([
        'post_type'   => 'wpcf7_contact_form',
        'numberposts' => -1,
    ]);
?>
<div class="cf7k-cpt-metabox">
	<table>
		<tbody>
			<tr>
				<th>Select Contact Form</th>
				<td>
					<select name="cf7k_contact_form">
						<?php foreach( $contact_forms as $contact_form ) : ?>
							<option value="<?php echo $contact_form->ID; ?>"> <?php echo esc_html( $contact_form->post_title ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th>Enable Integrations</th>
				<td>
					<select name="cf7k_contact_form">
						<?php foreach( $contact_forms as $contact_form ) : ?>
							<option value="<?php echo $contact_form->ID; ?>"> <?php echo esc_html( $contact_form->post_title ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<?php
