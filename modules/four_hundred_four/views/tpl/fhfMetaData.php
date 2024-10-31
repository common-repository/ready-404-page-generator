<?php if(!$this->optsMod->isEmpty('page_meta_keywords')) { ?>
	<meta name="keywords" content="<?php echo $this->optsMod->get('page_meta_keywords')?>">
<?php }?>
<?php if(!$this->optsMod->isEmpty('page_meta_description')) { ?>
	<meta name="description" content="<?php echo $this->optsMod->get('page_meta_description')?>">
<?php }?>