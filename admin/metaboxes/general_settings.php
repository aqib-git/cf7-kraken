<?php
    $contact_forms = get_posts([
        'post_type'   => 'wpcf7_contact_form',
        'numberposts' => -1,
	]);

	$modules = cf7_kraken()->modules;
?>
<div class="cf7k-cpt-metabox">
	<table>
		<tbody>
			<tr>
				<th>Select Contact Form</th>
				<td>
					<select>
						<?php foreach( $contact_forms as $contact_form ) : ?>
							<option value="<?php echo $contact_form->ID; ?>"> <?php echo esc_html( $contact_form->post_title ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th>Enable Integrations</th>
				<td>
					<select class="js-cf7k-integrations" multiple="multiple">
						<?php foreach( $modules as $module ) : ?>
							<option value="<?php echo esc_html( $module->get_name() ); ?>"> <?php echo esc_html( $module->get_title() ); ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<?php
