<div id="fhfAdminOptionsTabs">
    <h1>
        <?php echo FHF_WP_PLUGIN_NAME?>
        <?php //_e('version')?>
        <!--[<?php //echo FHF_VERSION?>]-->
    </h1>
	<ul>
		<?php foreach($this->tabsData as $tId => $tData) { ?>
		<li class="<?php echo $tId?>"><a href="#<?php echo $tId?>"><?php _e($tData['title'])?></a></li>
		<?php }?>
	</ul>
	<?php foreach($this->tabsData as $tId => $tData) { ?>
	<div id="<?php echo $tId?>"><?php echo $tData['content']?></div>
	<?php }?>
</div>
<div id="fhfAdminTemplatesSelection"><?php echo $this->presetTemplatesHtml?></div>
