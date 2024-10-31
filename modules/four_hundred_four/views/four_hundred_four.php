<?php
class four_hundred_fourViewFhf extends viewFhf {
	public function displayFhfPage() {
		$tplId = frameFhf::_()->getModule('options')->get('template');
		$displayStandardHeaderFooter = frameFhf::_()->getModule('options')->get('display_standard_header_footer');
		$stplOpts = array('fullPage' => !$displayStandardHeaderFooter);
		$htmlContent = frameFhf::_()->getModule('stpl')->getView()->generateContent($tplId, $stplOpts);
		if(!$displayStandardHeaderFooter) {
			$this->assign('metaData', $this->getMetaData());
		}
		$this->assign('htmlContent', $htmlContent);
		$this->assign('displayStandardHeaderFooter', $displayStandardHeaderFooter);
		parent::display('fhfPage');
	}
	public function getMetaData() {
		$this->assign('optsMod', frameFhf::_()->getModule('options'));
		return parent::getContent('fhfMetaData');
	}
	public function displayMetaData() {
		echo $this->getMetaData();
	}
}
