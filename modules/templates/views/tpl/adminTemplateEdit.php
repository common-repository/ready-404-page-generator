<script type="text/javascript">
// <!--
var fhfTplId = <?php echo $this->tplId?>;
// -->
</script>
<form id="fhfAdminTplSaveForm">
	<div id="fhfAdminTplShell"></div>
	<div>
		<?php echo htmlFhf::hidden('id')?>
		<?php echo htmlFhf::hidden('page', array('value' => 'stpl'))?>
		<?php echo htmlFhf::hidden('action', array('value' => 'save'))?>
		<?php echo htmlFhf::hidden('reqType', array('value' => 'ajax'))?>
		<?php echo htmlFhf::submit('save', array('value' => __('Save'), 'attrs' => 'class="button button-primary"'))?>
		<div id="fhfAdminTplSaveMsg"></div>
	</div>
</form>