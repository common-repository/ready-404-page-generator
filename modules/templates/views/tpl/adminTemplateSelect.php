<ul class="fhfTemplatesList">
	<?php foreach($this->templates as $tpl) { ?>
	<li class="fhfTemplatePrevShell fhfTemplatePrevShell-existing fhfTemplatePrevShell-<?php echo $tpl['id']?>" onclick="return setTemplateOptionFhf('<?php echo $tpl['id']?>');">
		<h2 style="text-align: center; color: #454545"><?php echo $tpl['label']?></h2><hr />
		<?php echo htmlFhf::img( $tpl['full_preview_img'], false, array('attrs' => 'class="fhfAdminTemplateImgPrev"'));?><hr />
		<input type="submit" onclick="return false;/*it will trigger click on parent element - and it will trigger select template, no need to make this twice*/" class="button button-primary button-large fhfTemplateSelectButt" value="<?php _e('Apply')?>">
	</li>
	<?php } ?>
</ul>