<?php
class four_hundred_fourFhf extends moduleFhf {
	public function init() {
		add_action('template_redirect', array($this, 'afterQueryParse'));
		parent::init();
	}
	public function afterQueryParse() {
		if(is_404()) {
			add_filter('wp_title', array($this, 'substituteTitle'));
			add_filter('wp_head', array($this->getView(), 'displayMetaData'));
			$this->getView()->displayFhfPage();
			exit();
		}
	}
	public function substituteTitle($title) {
		$optTitle = frameFhf::_()->getModule('options')->get('page_title');
		return empty($optTitle) ? $title : $optTitle;	
	}
}