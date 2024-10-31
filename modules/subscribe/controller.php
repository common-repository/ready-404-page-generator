<?php
class subscribeControllerFhf extends controllerFhf {
	public function create() {
		$res = new responseFhf();
		$data = reqFhf::get('post');
		$data['withoutConfirm'] = false;	// DO THIS FROM OPTIONS !!!!!!!!
		
		if($this->getModel()->create($data)) {
			$res->addMessage(__(frameFhf::_()->getModule('options')->get('subscr_success_msg')));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function saveAdmin() {
		$res = new responseFhf();
		$data = reqFhf::get('post');
		$data['withoutConfirm'] = true;
		 if(($id = $this->getModel()->save($data))) {
			 $subscriber = $this->getModel()->getById($id);
			 $res->addData('subscriber', $subscriber);
			 $res->addMessage(__('Done'));
		 } else
			 $res->pushError ($this->getModel()->getErrors());
		 return $res->ajaxExec();
	}
	public function confirm() {
		$res = new responseFhf();
		if($this->getModel()->confirm(reqFhf::get('get'))) {
			$res->addMessage(__('Your subscription was activated!'));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res;
	}
	public function confirmLead() {
		if(($fhfId = $this->getModel()->confirm(reqFhf::get('get')))) {
			$this->getView()->displaySubscribeSuccess(array(
				'fhfId' => $fhfId,
			));
		} else {
			$this->getView()->displaySubscribeErrors(array(
				'errors' => $this->getModel()->getErrors()
			));
		}
		exit();
	}
	public function unsubscribeLead() {
		if(($fhfId = $this->getModel()->unsubscribe(reqFhf::get('get')))) {
			$this->getView()->displayUnsubscribeSuccess(array(
				'fhfId' => $fhfId,
			));
		} else {
			$this->getView()->displayUnsubscribeErrors(array(
				'errors' => $this->getModel()->getErrors()
			));
		}
		exit();
	}
	public function getList() {
		$res = new responseFhf();
		if($count = $this->getModel()->getCount(reqFhf::get('post'))) {
			$list = $this->getModel()->getList(reqFhf::get('post'));
			$res->addData('list', $list);
			$res->addData('count', $count);
			$res->addMessage(__('Done'));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	/**
	 * Get list of subscribers lists
	 */
	public function getListLists() {
		$res = new responseFhf();
		if($count = $this->getModel()->getCountLists()) {
			$list = $this->getModel()->getListLists(reqFhf::get('post'));
			$res->addData('list', $list);
			$res->addData('count', $count);
			$res->addMessage(__('Done'));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function saveList() {
		$res = new responseFhf();
		 if(($id = $this->getModel()->saveList(reqFhf::get('post')))) {
			 $list = $this->getModel()->getListById( $id );
			 $res->addData('list', $list);
			 $res->addMessage(__('Saved'));
		 } else
			 $res->pushError ($this->getModel()->getErrors());
		 return $res->ajaxExec();
	}
	public function removeList() {
		$res = new responseFhf();
		 if($this->getModel()->removeList(reqFhf::get('post'))) {
			 $res->addMessage(__('Done'));
		 } else
			 $res->pushError ($this->getModel()->getErrors());
		 return $res->ajaxExec();
	}
	public function changeStatus() {
		$res = new responseFhf();
		if($this->getModel()->changeStatus(reqFhf::get('post'))) {
			$res->addMessage(__('Done'));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function remove() {
		$res = new responseFhf();
		if($this->getModel()->remove(reqFhf::get('post'))) {
			$res->addMessage(__('Done'));
		} else
			$res->pushError ($this->getModel()->getErrors());
		return $res->ajaxExec();
	}
	public function getPermissions() {
		return array(
			FHF_USERLEVELS => array(
				FHF_ADMIN => array('getList', 'changeStatus', 'remove', 'saveList', 'getListLists', 'removeList', 'saveAdmin')
			),
		);
	}
}

