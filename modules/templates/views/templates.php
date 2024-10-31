<?php
class templatesViewFhf extends viewFhf {
	public function getAdminTemplateSelect() {
		$templates = frameFhf::_()->getModule('stpl')->getModel()->getList(array('protected' => 1));
		$this->assign('templates', $templates);
		return parent::getContent('adminTemplateSelect');
	}
	public function getAdminTemplateEdit() {
		$tplId = frameFhf::_()->getModule('options')->get('template');
		if($tplId >= FHF_STPL_DEFINED_IDS_MAX) {
			$tplId = frameFhf::_()->getModule('stpl')->getModel()->getParentById($tplId);
		}
		$this->assign('tplId', $tplId);
		return parent::getContent('adminTemplateEdit');
	}
}