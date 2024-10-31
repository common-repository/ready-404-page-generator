<input type="submit" onclick="openTemplateSelectFhf(); return false;" class="button button-primary button-large" value="<?php _e('Select template')?>">
<div class="wrap">
	<div class="metabox-holder">
		<div class="postbox-container" style="width: 100%;">
			<div class="meta-box-sortables ui-sortable">
				<div id="idMainFhfOpts" class="postbox fhfAdminTemplateOptRow fhfAvoidJqueryUiStyle" style="display: block">
					<div class="handlediv" title="Click to toggle"><br></div>
					<h3 class="hndle"><?php _e( 'Main Settings' )?></h3>
					<div class="inside">
						<form class="fhfNiceStyle" id="fhfAdminOptionsForm">
							<table width="100%">
								<?php foreach($this->allOptions as $opt) { ?>
								<tr class="fhfAdminOptionRow-<?php echo $opt['code']?> fhfTblRow">
									<td><?php _e($opt['label'])?></td>
									<td>
									<?php
										$htmltype = $opt['htmltype'];
										if($opt['code'] != 'template') {	// For template will be unique option editor
											$htmlOptions = array('value' => $opt['value'], 'attrs' => 'class="fhfGeneralOptInput"');
											switch($htmltype) {
												case 'checkbox': case 'checkboxHiddenVal':
													$htmlOptions['checked'] = (bool)$opt['value'];
													break;
											}
											if(!empty($opt['params']) && is_array($opt['params'])) {
												$htmlOptions = array_merge($htmlOptions, $opt['params']);
											}
											echo htmlFhf::$htmltype('opt_values['. $opt['code']. ']', $htmlOptions);
										}
									?>
									</td>
								</tr>
								<?php }?>
								<tr>
									<td>
										<?php echo htmlFhf::hidden('reqType', array('value' => 'ajax'))?>
										<?php echo htmlFhf::hidden('page', array('value' => 'options'))?>
										<?php echo htmlFhf::hidden('action', array('value' => 'saveMainGroup'))?>
										<?php echo htmlFhf::submit('saveAll', array('value' => __('Save All Changes'), 'attrs' => 'class="button button-primary button-large"'))?>
									</td>
									<td id="fhfAdminMainOptsMsg"></td>
								</tr>
							</table>
						</form>
					</div>
				</div>
				<div id="idFhfMainFhfOpts" class="postbox fhfAdminTemplateOptRow fhfAvoidJqueryUiStyle" style="display: block">
					<div class="handlediv" title="Click to toggle"><br></div>
					<h3 class="hndle"><?php _e( 'Subscribe Settings' )?></h3>
					<div class="inside"><?php echo $this->subscribeSettings?></div>
				</div>
			</div>
		</div>
	</div>
</div>
<div style="clear: both;"></div>


