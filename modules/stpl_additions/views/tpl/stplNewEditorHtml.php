<div class="fhfStplCanvasMenuShell" style="display: none;">
	<table width="100%">
		<tr>
			<td><?php _e('Menu')?>:</td><td><?php echo htmlFhf::selectbox('menu')?></td>
		</tr>
		<tr>
			<td>
				<?php _e('Additiona classes')?>:<br />
				<i><?php _e('for advanced users only')?></i>
			</td>
			<td><?php echo htmlFhf::text('add_classes')?></td>
		</tr>
		<tr>
			<td>
				<?php _e('Additiona styles')?>:<br />
				<i><?php _e('for advanced users only')?></i>
			</td>
			<td><?php echo htmlFhf::text('add_styles')?></td>
		</tr>
	</table>
</div>
<div class="fhfStplCanvasSubscribeFormShell" style="display: none;">
	<table width="100%">
		<tr>
			<td><?php _e('Subscribe to List')?>:</td><td><?php echo htmlFhf::selectbox('list')?></td>
		</tr>
		<tr>
			<td><?php _e('Form title')?>:</td><td><?php echo htmlFhf::text('subscr_form_title')?></td>
		</tr>
		<tr>
			<td><?php _e('"Enter Email" message')?>:</td><td><?php echo htmlFhf::text('subscr_enter_email_msg')?></td>
		</tr>
		<tr>
			<td><?php _e('Success message')?>:</td><td><?php echo htmlFhf::text('subscr_success_msg')?></td>
		</tr>
	</table>
</div>