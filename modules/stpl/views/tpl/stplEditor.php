<?php dispatcherFhf::doAction('stplEditorStart')?>
<div class="fhfStplCanvasShell">
	<div class="fhfStplCanvasOptions">
		<div style="float: right;">
			<label style="">
				<?php echo htmlFhf::checkbox('show_grid', array('attrs' => 'onchange="stplCanvasSwitchGridButtClick(this);"', 'checked' => 1))?>
				<?php _e('Show Grid')?>
			</label>
			<label style="">
				<?php echo htmlFhf::checkbox('show_images', array('attrs' => 'onchange="stplCanvasSwitchImagesButtClick(this);"', 'checked' => 1))?>
				<?php _e('Show Images')?>
			</label>
		</div>
		<?php echo htmlFhf::button(array('value' => __('Add Row'), 'attrs' => 'onclick="stplCanvasAddRowFhf(this); return false;" class="button"'))?>
	</div>
	<div class="fhfStplCanvas"></div>
	<div class="fhfStplCanvasPreviewShell">
		<a href="#" onclick="stplCanvasPreviewInBrowserLinkClick(this); return false;"><?php _e('Preview in Browser')?></a>
	</div>
	<div class="fhfStplCanvasRowSettings fhfExample">
		<div class="fhfStplCanvasRowIconMove fhfStplCanvasRowSetting" title="<?php _e('Move')?>"></div>
		<?php ?><?php echo htmlFhf::text('background_color', array('attrs' => 'class="fhfStplCanvasRowIconBgColor"'))?><?php ?>
		<?php /*?><div class="fhfStplCanvasRowIconBgColor fhfStplCanvasRowSetting" title="<?php _e('Background Color')?>"></div><?php */?>
		<div class="fhfStplCanvasRowIconColumns fhfStplCanvasRowSetting" title="<?php _e('Columns')?>"></div>
		<div class="fhfStplCanvasRowIconRemove fhfStplCanvasRowSetting" title="<?php _e('Remove')?>"></div>
	</div>
	<div class="fhfStplCanvasRowColumnsNumShell fhfExample">
		<div class="fhfStplCanvasRowColumnsNumItem">
			<?php echo htmlFhf::text('columns_num', array('value' => 1/*By default*/, 'attrs' => 'class="fhfStplCanvasRowColumnsNumText"'))?>
		</div>
		<div class="fhfStplCanvasRowColumnsNumItem">
			<?php echo htmlFhf::button(array('value' => __('Ok'), 'attrs' => 'class="fhfStplCanvasRowColumnsNumButt button"'))?>
		</div>
	</div>
	<div class="fhfStplCanvasCellSettings fhfExample">
		<div class="fhfStplCanvasCellIconRemove fhfStplCanvasCellSetting" title="<?php _e('Remove')?>"></div>
		<div class="fhfStplCanvasCellIconEdit fhfStplCanvasCellSetting" title="<?php _e('Edit')?>"></div>
		<div class="fhfStplCanvasCellIconMove fhfStplCanvasCellSetting" title="<?php _e('Move')?>"></div>
	</div>
</div>
<?php /*This block will be hidden for now*/?>
<div class="fhfStplCanvasSettings" id="fhfStplCanvasSettings">
	<ul>
		<li><span class="left-corner"></span><a href="#fhfStplCanvasSettingsContentTab"><?php _e('Content')?></a><span class="right-corner"></span></li>
		<li><span class="left-corner"></span><a href="#fhfStplCanvasSettingsStylesTab"><?php _e('Styles')?></a><span class="right-corner"></li>
	</ul>
	<div id="fhfStplCanvasSettingsContentTab">
		<?php $i = 0; ?>
		<?php foreach($this->editElements as $elName => $elData) { ?>
			<div class="fhfStplCanvasContentElementOriginal" data-element="<?php echo $elName?>">
				<img src="<?php echo $elData['icon']?>" />
				<div class="fhfStplCanvasContentElementOriginalLabel"><?php echo $elData['label']?></div>
			</div>
			<?php if($i%2 === 1) { ?>
				<div style="clear: both;"></div>
			<?php }?>
			<?php $i++;?>
		<?php } ?>
	</div>
	<div id="fhfStplCanvasSettingsStylesTab">
		<fieldset class="fhfStplCanvasSettingFieldSet">
			<legend><?php _e('Fonts')?></legend>
			<div class="fhfStplCanvasSettingStylesShell">
				<table width="100%">
				<?php foreach($this->styleElements as $elKey => $elData) { ?>
					<tr class="fhfStplCanvasSettingFontStyleRow">
						<td>
							<?php echo htmlFhf::hidden('font_style['. $elKey. '][selector]', array('value' => $elData['selector']));?>
							<?php echo $elData['label']?>:
						</td>
						<td><?php echo htmlFhf::selectbox('font_style['. $elKey. '][font-family]', array('options' => $this->fonts, 'value' => $elData['defaults']['font-family'], 'attrs' => 'onchange="stplCanvasOnFontStyleChange(this);"'))?></td>
						<td><?php echo htmlFhf::selectbox('font_style['. $elKey. '][font-size]', array('options' => $this->fontSizes, 'value' => $elData['defaults']['font-size'], 'attrs' => 'onchange="stplCanvasOnFontStyleChange(this);"'))?></td>
						<td><?php echo htmlFhf::colorpicker('font_style['. $elKey. '][color]', array('value' => $elData['defaults']['color'], 'change' => 'stplCanvasOnFontStyleChange'))?></td>
					</tr>
				<?php }?>
				</table>
			</div>
		</fieldset>
		<fieldset class="fhfStplCanvasSettingFieldSet">
			<legend><?php _e('Background')?></legend>
			<div class="fhfStplCanvasSettingBgTypeShell">
				<div class="fhfStplCanvasSettingBgTypeRadio">
					<label for="fhfStplCanvasSettingBgTypeNone"><?php _e('None')?></label><?php echo htmlFhf::radiobutton('background_type', array('value' => 'none', 'attrs' => 'id="fhfStplCanvasSettingBgTypeNone"'))?>
					<label for="fhfStplCanvasSettingBgTypeColor"><?php _e('Color')?></label><?php echo htmlFhf::radiobutton('background_type', array('value' => 'color', 'attrs' => 'id="fhfStplCanvasSettingBgTypeColor"'))?>
					<label for="fhfStplCanvasSettingBgTypeImage"><?php _e('Image')?></label><?php echo htmlFhf::radiobutton('background_type', array('value' => 'image', 'attrs' => 'id="fhfStplCanvasSettingBgTypeImage"'))?>
				</div>
				<div id="fhfStplCanvasSettingBgTypeColorContainer" class="fhfStplCanvasSettingBgTypeContainer">
					<?php echo htmlFhf::colorpicker('background_color', array('change' => 'stplCanvasSetBgColorChange'))?>
				</div>
				<div id="fhfStplCanvasSettingBgTypeImageContainer" class="fhfStplCanvasSettingBgTypeContainer">
					<div class="fhfStplCanvasSettingBgImgPosRadio">
						<label for="fhfStplCanvasSettingBgImgStretch"><?php _e('Stretch')?></label><?php echo htmlFhf::radiobutton('background_img_pos', array('value' => 'stretch', 'attrs' => 'id="fhfStplCanvasSettingBgImgStretch"'))?>
						<label for="fhfStplCanvasSettingBgImgTile"><?php _e('Tile')?></label><?php echo htmlFhf::radiobutton('background_img_pos', array('value' => 'tile', 'attrs' => 'id="fhfStplCanvasSettingBgImgTile"'))?>
						<label for="fhfStplCanvasSettingBgImgCenter"><?php _e('Center')?></label><?php echo htmlFhf::radiobutton('background_img_pos', array('value' => 'center', 'attrs' => 'id="fhfStplCanvasSettingBgImgCenter"'))?>
					</div>
					<div class="fhfStplCanvasSettingImageUploaderContainer">
						<?php echo htmlFhf::hidden('background_image', array('attrs' => 'class="fhfStplCanvasSettingImageUploaderValue"'))?>
						<?php echo htmlFhf::button(array('value' => __('Select Image'), 'attrs' => 'class="button"'))?>
						<?php echo htmlFhf::img('', 0, array('attrs' => 'class="fhfStplCanvasSettingImageUploaderExample" style="max-width: 190px; display: none;"'))?>
					</div>
				</div>
			</div>
		</fieldset>
	</div>
</div>
<div class="fhfStplCanvasNewContentShell" style="display: none;">
	<table width="100%">
		<tr>
			<td><?php _e('Title Style')?>:</td>
			<td><?php echo htmlFhf::selectbox('title_style', array('options' => $this->titleStyles));?></td>
		</tr>
		<tr>
			<td><?php _e('Title Align')?>:</td>
			<td><?php echo htmlFhf::selectbox('title_align', array('options' => $this->aligns))?></td>
		</tr>
		<?php /*?>
		<tr>
			<td><?php _e('Image Align')?></td>
			<td><?php echo htmlFhf::selectbox('image_align', array('options' => array_merge($this->aligns, array('no_image' => __('No Image')))))?></td>
		</tr>
		<?php */?>
		<tr>
			<td><?php _e('Show Content')?>:</td>
			<td><?php echo htmlFhf::selectbox('show_content', array('options' => $this->showContent))?></td>
		</tr>
		<tr>
			<td><?php _e('Posts Number')?>:</td>
			<td><?php echo htmlFhf::selectbox('posts_num', array('options' => $this->postsNum))?></td>
		</tr>
		<tr>
			<td><?php _e('From category')?>:</td>
			<td><?php echo htmlFhf::selectbox('category')?></td>
		</tr>
	</table>
</div>
<div class="fhfStplCanvasDividersShell" style="display: none;">
	<?php for($i = 1; $i <= 14; $i++) { ?>
	<div class="fhfStplCanvasDividerOriginal">
		<img class="fhfStplCanvasDividerImg" style="width: 100%; height: 100%;" src="<?php echo $this->getModule()->getModPath()?>img/dividers/<?php echo $i?>.png" />
	</div>
	<?php }?>
</div>
<div class="fhfStplCanvasSocialShell" style="display: none;">
	<div class="fhfStplCanvasSocialDesignsShell">
		<div class="fhfStplCanvasSocialDesignButtShell">
		<?php foreach($this->socDesigns as $i => $socDesOpts) { ?>
			<label for="fhfStplCanvasSocialDesignButt<?php echo $i?>"><?php echo sprintf(__('Design %s'), $i)?></label>
			<?php echo htmlFhf::radiobutton('social_design', array('value' => $i, 'attrs' => 'id="fhfStplCanvasSocialDesignButt'. $i. '" use_img="'. ((int)$socDesOpts['useImg']). '"'))?>
		<?php }?>
		</div>
		<div class="fhfStplCanvasSocialDesignPresentation"></div>
	</div>
	<div style="clear: both;"></div>
	<div class="fhfStplCanvasSocialLinksShell">
		<table width="100%">
			<tr>
				<td><?php _e('Facebook')?>:</td><td><?php echo htmlFhf::text('link_facebook', array('attrs' => 'placeholder="https://www.facebook.com/ReadyECommerce"'))?></td>
			</tr>
			<tr>
				<td><?php _e('Twitter')?>:</td><td><?php echo htmlFhf::text('link_twitter')?></td>
			</tr>
			<tr>
				<td><?php _e('Google+')?>:</td><td><?php echo htmlFhf::text('link_google_plus')?></td>
			</tr>
			<tr>
				<td><?php _e('Youtube')?>:</td><td><?php echo htmlFhf::text('link_youtube')?></td>
			</tr>
		</table>
	</div>
	<div style="clear: both;"></div>
</div>
<div class="fhfStplCanvasStaticContentShell" style="display: none;">
	<table width="100%">
		<tr><td colspan="2"><?php _e('Select Any')?></td></tr>
		<tr>
			<td><?php _e('Post')?>:</td><td><?php echo htmlFhf::selectbox('static_content_post')?></td>
		</tr>
		<tr><td colspan="2"><?php _e('Or')?></td></tr>
		<tr>
			<td><?php _e('Page')?>:</td><td><?php echo htmlFhf::selectbox('static_content_page')?></td>
		</tr>
	</table>
	<table width="100%" style="border-top: 1px solid #D8D8D8;">
		<tr>
			<td><?php _e('Title Style')?>:</td>
			<td><?php echo htmlFhf::selectbox('static_title_style', array('options' => $this->titleStyles));?></td>
		</tr>
		<tr>
			<td><?php _e('Title Align')?>:</td>
			<td><?php echo htmlFhf::selectbox('static_title_align', array('options' => $this->aligns))?></td>
		</tr>
		<tr>
			<td><?php _e('Show Content')?>:</td>
			<td><?php echo htmlFhf::selectbox('static_show_content', array('options' => $this->showContent))?></td>
		</tr>
	</table>
</div>
<?php dispatcherFhf::doAction('stplEditorEnd')?>
