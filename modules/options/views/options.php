<?php
class optionsViewFhf extends viewFhf {
    public function getAdminPage() {
		$tabsData = array(
			'fhfMainOptions'		=> array('title' => 'Main',		'content' => $this->getMainOptionsTab()),
		);
		$tabsData = dispatcherFhf::applyFilters('adminOptionsTabs', $tabsData);
		$this->assign('tabsData', $tabsData);
        parent::display('optionsAdminPage');
    }
	public function getMainOptionsTab() {
		$generalOptions = $this->getModel()->getByCategories('General');
		if(!isset($this->optModel))
			$this->assign('optModel', $this->getModel());
		$this->assign('allOptions', $generalOptions['opts']);
		$this->assign('subscribeSettings', frameFhf::_()->getModule('subscribe')->getView()->getAdminOptions());
		return parent::getContent('mainOptionsTab');
	}
}
