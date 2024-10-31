<div class="wrap">
	<div class="metabox-holder">
		<div class="postbox-container" style="width: 100%;">
			<div class="meta-box-sortables ui-sortable">
				<div id="idFhfSubscribers" class="postbox fhfAdminTemplateOptRow" style="display: block">
					<div class="handlediv" title="Click to toggle"><br></div>
					<h3 class="hndle"><?php _e( 'Subscribers' )?></h3>
					<div class="inside">
						<?php echo htmlFhf::button(array('value' => __('Add New'), 'attrs' => 'id="fhfSubscribersAddButt" class="button"'))?>
						<?php echo htmlFhf::selectbox('fhf_select_list_in_table', array('attrs' => 'id="fhfSubscribersFilterByListSel" style="width: auto;"'))?>
						<table id="fhfAdminSubersTable" width="100%">
							<thead>
								<tr class="fhfTblHeader">
									<td><?php _e('Email')?></td>
									<td><?php _e('Status')?></td>
									<td><?php _e('Remove')?></td>
								</tr>
							</thead>
							<tbody>
								<tr class="fhfExample fhfTblRow" style="display: none;">
									<td class="email" onclick="fhfSubscrbShowEditForm(this); return false;"></td>
									<td>
										<a href="#" onclick="fhfSubscrbChangeStatus(this); return false;" class="status fhfStatusIndicator" valueTo="class"></a>
									</td>
									<td>
										<a href="#" onclick="fhfSubscrbRemove(this); return false;"><?php echo htmlFhf::img('cross.gif')?></a>
										<?php echo htmlFhf::hidden('id', array('attrs' => 'class="id" valueTo="value"'))?>
									</td>
								</tr>
							</tbody>
						</table>
						<div id="fhfAdminSubersPaging"></div>
						<div id="fhfAdminSubersMsg"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<form id="fhfAdminSubersForm" style="display: none;">
	<table>
		<tr>
			<td valign="top">
				<label for="fhfAdminSubersFormEmail"><?php _e('Email')?></label>
			</td>
			<td valign="top">
				<?php echo htmlFhf::text('email', array('attrs' => 'id="fhfAdminSubersFormEmail"'))?>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<label for="fhfAdminSubersFormLists"><?php _e('Lists')?></label>
			</td>
			<td class="fhfAdminSubersFormListsShell" valign="top"></td>
		</tr>
	</table>
	<?php echo htmlFhf::hidden('id')?>
	<?php echo htmlFhf::hidden('page', array('value' => 'subscribe'))?>
	<?php echo htmlFhf::hidden('action', array('value' => 'saveAdmin'))?>
	<?php echo htmlFhf::hidden('reqType', array('value' => 'ajax'))?>
	<?php echo htmlFhf::submit('save', array('value' => __('Save'), 'attrs' => 'class="button button-primary"'))?>
	<?php echo htmlFhf::button(array('value' => __('Cancel'), 'attrs' => 'class="button" onclick="fhfSubscrbCloseAddForm(this); return false;"'))?>
	<div class="fhfAdminSubersFormMsg"></div>
</form>
<script type="text/javascript">
// <!--
jQuery(document).ready(function(){
	fhfSubersAllLists = <?php echo utilsFhf::jsonEncode($this->allLists)?>;
	fhfSubersTotalSubscribers = <?php echo $this->totalSubscribers?>;
});
// -->
</script>
<div style="clear: both;"></div>
