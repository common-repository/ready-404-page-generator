<?php
class promo_readyControllerFhf extends controllerFhf {
    public function welcomePageSaveInfo() {
		$res = new responseFhf();
		if($this->getModel()->welcomePageSaveInfo(reqFhf::get('post'))) {
			$res->addMessage(__('Information was saved. Thank you!'));
			$originalPage = reqFhf::getVar('original_page');
			$returnArr = explode('|', $originalPage);
			$return = $this->getModule()->decodeSlug(str_replace('return=', '', $returnArr[1]));
			$return = admin_url( strpos($return, '?') ? $return : 'admin.php?page='. $return);
			$res->addData('redirect', $return);
			installerFhf::setUsed();
		} else {
			$res->pushError($this->getModel()->getErrors());
		}
		return $res->ajaxExec();
	}
	/**
	 * @see controller::getPermissions();
	 */
	public function getPermissions() {
		return array(
			FHF_USERLEVELS => array(
				FHF_ADMIN => array('welcomePageSaveInfo')
			),
		);
	}
}