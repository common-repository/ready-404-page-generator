<?php
class stpl_additionsControllerFhf extends controllerFhf {
	public function getMenusListForSelect() {
		$res = new responseFhf();
		if(($menus = $this->getModel()->getMenusList(reqFhf::get('post')))) {
			$res->addData('menus', $menus);
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function getPermissions() {
		return array(
			FHF_USERLEVELS => array(
				FHF_ADMIN => array('getMenusListForSelect')
			),
		);
	}
}

