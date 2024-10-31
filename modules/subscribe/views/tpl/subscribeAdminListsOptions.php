<div class="wrap">
	<div class="metabox-holder">
		<div class="postbox-container" style="width: 100%;">
			<div class="meta-box-sortables ui-sortable">
				<div id="idFhfSubscribersLists" class="postbox fhfAdminTemplateOptRow" style="display: block">
					<div class="handlediv" title="Click to toggle"><br></div>
					<h3 class="hndle"><?php _e( 'Subscribers Lists' )?></h3>
					<div class="inside">
						<?php echo htmlFhf::button(array('value' => __('Add New'), 'attrs' => 'id="fhfSubscribersListsAddButt" class="button"'))?>
						<table id="fhfAdminSubersListsTable" width="100%">
							<thead>
								<tr class="fhfTblHeader">
									<td><?php _e('Label')?></td>
									<td><?php _e('Remove')?></td>
								</tr>
							</thead>
							<tbody>
								<tr class="fhfExample fhfTblRow" style="display: none;">
									<td class="label" onclick="fhfSubscrbShowEditListForm(this); return false;"></td>
									<td>
										<a href="#" onclick="fhfSubscrbListRemove(this); return false;"><?php echo htmlFhf::img('cross.gif')?></a>
										<?php echo htmlFhf::hidden('id', array('attrs' => 'class="id" valueTo="value"'))?>
									</td>
								</tr>
							</tbody>
						</table>
						<div id="fhfAdminSubersListsPaging"></div>
						<div id="fhfAdminSubersListsMsg"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<form id="fhfAdminSubersListsForm" style="display: none;">
	<table>
		<tr>
			<td>
				<label for="fhfAdminSubersListsFormLabel"><?php _e('Label')?></label>
			</td>
			<td>
				<?php echo htmlFhf::text('label', array('attrs' => 'id="fhfAdminSubersListsFormLabel"'))?>
			</td>
		</tr>
		<tr>
			<td>
				<label for="fhfAdminSubersListsFormDescription"><?php _e('Description')?></label>
			</td>
			<td>
				<?php echo htmlFhf::textarea('description', array('attrs' => 'id="fhfAdminSubersListsFormDescription"'))?>
			</td>
		</tr>
	</table>
	<?php echo htmlFhf::hidden('id')?>
	<?php echo htmlFhf::hidden('page', array('value' => 'subscribe'))?>
	<?php echo htmlFhf::hidden('action', array('value' => 'saveList'))?>
	<?php echo htmlFhf::hidden('reqType', array('value' => 'ajax'))?>
	<?php echo htmlFhf::submit('save', array('value' => __('Save'), 'attrs' => 'class="button button-primary"'))?>
	<?php echo htmlFhf::button(array('value' => __('Cancel'), 'attrs' => 'class="button" onclick="fhfSubscrbCloseAddForm(this); return false;"'))?>
	<div class="fhfAdminSubersListsFormMsg"></div>
</form>
<div style="clear: both;"></div>