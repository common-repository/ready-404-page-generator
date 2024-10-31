<?php
class optionsControllerFhf extends controllerFhf {
    public function save() {
		$res = new responseFhf();
		$saveData = reqFhf::get('post');
		if($this->getModel()->save($saveData)) {
			$res->addMessage(__('Done'));
			$res = dispatcherFhf::applyFilters('optionUpdateRes', $res, $saveData);
		} else
			$res->pushError ($this->getModel('options')->getErrors());
		return $res->ajaxExec();
    }
	public function saveGroup() {
		$res = new responseFhf();
		if($this->getModel()->saveGroup(reqFhf::get('post'))) {
			$res->addMessage(__('Done'));
		} else
			$res->pushError ($this->getModel('options')->getErrors());
		return $res->ajaxExec();
	}
	public function saveBgImg() {
		$res = new responseFhf();
		if($this->getModel()->saveBgImg(reqFhf::get('files'))) {
			$res->addData(array('imgPath' => frameFhf::_()->getModule('options')->getBgImgFullPath()));
			$res->addMessage(__('Done'));
		} else
			$res->pushError ($this->getModel('options')->getErrors());
		return $res->ajaxExec();
	}
	public function saveLogoImg() {
		$res = new responseFhf();
		if($this->getModel()->saveLogoImg(reqFhf::get('files'))) {
			$res->addData(array('imgPath' => frameFhf::_()->getModule('options')->getLogoImgFullPath()));
			$res->addMessage(__('Done'));
		} else
			$res->pushError ($this->getModel('options')->getErrors());
		return $res->ajaxExec();
	}
	/**
	 * Will save main options and detect - whether or not sub mode enabled/disabled to trigger appropriate actions
	 */
	public function saveMainGroup() {
		$res = new responseFhf();
		$oldMode = $this->getModel()->get('mode');
		if($this->getModel()->saveGroup(reqFhf::get('post'))) {
			$res->addMessage(__('Done'));
			$newMode = $this->getModel()->get('mode');
		} else
			$res->pushError ($this->getModel('options')->getErrors());
		return $res->ajaxExec();
	}
	/**
	 * Will save subscriptions options as usual options + try to re-saive email templates from this part
	 */
	public function saveSubscriptionGroup() {
		$res = new responseFhf();
		if($this->getModel()->saveGroup(reqFhf::get('post'))) {
			$res->addMessage(__('Done'));
			$emailTplData = reqFhf::getVar('email_tpl');
			if(!empty($emailTplData) && is_array($emailTplData)) {
				foreach($emailTplData as $id => $tData) {
					frameFhf::_()->getModule('messenger')->getController()->getModel('email_templates')->save(array(
						'id'		=> $id, 
						'subject'	=> $tData['subject'],
						'body'		=> $tData['body'],
					));
				}
			}
		} else
			$res->pushError ($this->getModel('options')->getErrors());
		return $res->ajaxExec();
	}
	public function setTplDefaultList() {
		
	}
	public function setTplDefault() {
		$res = new responseFhf();
		$newOptValue = $this->getModel()->setTplAnyDefault(reqFhf::get('post'));
		if($newOptValue !== false) {
			$res->addMessage(__('Done'));
			$res->addData('newOptValue', $newOptValue);
		} else
			$res->pushError ($this->getModel('options')->getErrors());
		return $res->ajaxExec();
	}
	public function removeBgImg() {
		$res = new responseFhf();
		if($this->getModel()->removeBgImg(reqFhf::get('post'))) {
			$res->addMessage(__('Done'));
		} else
			$res->pushError ($this->getModel('options')->getErrors());
		return $res->ajaxExec();
	}
	public function removeLogoImg() {
		$res = new responseFhf();
		if($this->getModel()->removeLogoImg(reqFhf::get('post'))) {
			$res->addMessage(__('Done'));
		} else
			$res->pushError ($this->getModel('options')->getErrors());
		return $res->ajaxExec();
	}
	public function activatePlugin() {
		$res = new responseFhf();
		if($this->getModel('modules')->activatePlugin(reqFhf::get('post'))) {
			$res->addMessage(lang::_('Plugin was activated'));
		} else {
			$res->pushError($this->getModel('modules')->getErrors());
		}
		return $res->ajaxExec();
	}
	public function activateUpdate() {
		$res = new responseFhf();
		if($this->getModel('modules')->activateUpdate(reqFhf::get('post'))) {
			$res->addMessage(lang::_('Very good! Now plugin will be updated.'));
		} else {
			$res->pushError($this->getModel('modules')->getErrors());
		}
		return $res->ajaxExec();
	}
	public function getPermissions() {
		return array(
			FHF_USERLEVELS => array(
				FHF_ADMIN => array('save', 'saveGroup', 'saveBgImg', 'saveLogoImg', 
					'saveMainGroup', 'saveSubscriptionGroup', 'setTplDefault', 
					'removeBgImg', 'removeLogoImg',
					'activatePlugin', 'activateUpdate')
			),
		);
	}
}

