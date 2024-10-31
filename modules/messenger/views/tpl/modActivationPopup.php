<div id="toeModActivationPopupShellFhf" style="display: none;">
	<center>
		<form id="toeModActivationPopupFormFhf">
			<label>
				<?php _e('Enter your activation key here')?>:
				<?php echo htmlFhf::text('activation_key')?>
			</label>
			<?php echo htmlFhf::hidden('page', array('value' => 'options'))?>
			<?php echo htmlFhf::hidden('action', array('value' => 'activatePlugin'))?>
			<?php echo htmlFhf::hidden('reqType', array('value' => 'ajax'))?>
			<?php echo htmlFhf::hidden('plugName')?>
			<?php echo htmlFhf::hidden('goto')?>
			<?php echo htmlFhf::submit('activate', array('value' => __('Activate')))?>
			<br />
			<div id="toeModActivationPopupMsgFhf"></div>
		</form>
	</center>
	<i><?php _e('To get your keys - go to')?>
		<a target="_blank" href="http://readyshoppingcart.com/my-account/my-orders/">http://readyshoppingcart.com/my-account/my-orders/</a>
		<?php _e('and copy & paste key from your ordered extension here.')?>
	</i>
</div>