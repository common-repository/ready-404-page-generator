<form actiom="" method="post" id="<?php echo $this->uniqueId?>">
	<div class="fhfSubscribeFormTitle"><?php _e($this->instance['subscr_form_title'])?></div>
	<label>
		<?php _e($this->instance['subscr_enter_email_msg'])?>: 
		<?php echo htmlFhf::text('email')?>
	</label>
	<?php if(isset($this->instance['list']) && $this->instance['list']) {
		foreach($this->instance['list'] as $listId) {
			echo htmlFhf::hidden('list[]', array('value' => $listId));
		}
	}?>
	<?php echo htmlFhf::hidden('mod', array('value' => 'subscribe'))?>
	<?php echo htmlFhf::hidden('action', array('value' => 'create'))?>
	<?php echo htmlFhf::hidden('reqType', array('value' => 'ajax'))?>
	<?php echo htmlFhf::submit('subscribe', array('value' => __('Subscribe')))?>
	
	<div class="fhfSubscribeFormMsg"></div>
	<div class="fhfSubscribeFormSuccess" style="display: none;"><?php _e($this->instance['subscr_success_msg'])?></div>
</form>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#<?php echo $this->uniqueId?>').submit(function(){
		fhfSubscribeFormSend(this);
		return false;
	});
});
</script>

