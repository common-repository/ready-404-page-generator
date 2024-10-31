<?php
class subscribeViewFhf extends viewFhf {
	public function getAdminOptions() {
		$fhfOptions = frameFhf::_()->getModule('options')->getModel()->getByCategories('Subscribe');
		$emailEditTpls = array();
		$emailTpls = frameFhf::_()->getModule('messenger')->getController()->getModel('email_templates')->get(array('module' => 'subscribe'));
		if(!empty($emailTpls)) {
			foreach($emailTpls as $tpl) {
				$emailEditTpls[] = array(
					'label' => $tpl['label'], 
					'content' => frameFhf::_()->getModule('messenger')->getController()->getView()->getOneEmailTplEditor(array('tplData' => $tpl)),
				);
			}
		}
		$this->assign('fhfOptions', $fhfOptions['opts']);
		$this->assign('optModel',	frameFhf::_()->getModule('options')->getModel());
		$this->assign('emailEditTpls', $emailEditTpls);
		return parent::getContent('subscribeAdminOptions');
	}
	public function getAdminSubscribersOptions() {
		$this->assign('allLists', $this->getModel()->getListLists());
		$this->assign('totalSubscribers', $this->getModel()->getCount());
		return parent::getContent('subscribeAdminSubscribersOptions');
	}
	public function getAdminListsOptions() {
		return parent::getContent('subscribeAdminListsOptions');
	}
	public function displaySubscribeWidget($instance) {
		$instance = $this->preFillWidgetFormOptions($instance);
		$this->assign('instance', $instance);
		$this->assign('uniqueId', 'fhfSubscribeForm_'. mt_rand(1, 999999));
		parent::display('subscribeWidget');
	}
	public function displaySubscribeSetupForm($data, $widget) {
		$data = $this->preFillWidgetFormOptions($data);
		$this->assign('data', $data);
		$this->assign('widget', $widget);
		$this->assign('allLists', $this->getModel()->getListLists());
		parent::display('subscribeSetupForm');
	}
	public function preFillWidgetFormOptions($data) {
		$preFillKeys = array('subscr_form_title', 'subscr_enter_email_msg', 'subscr_success_msg');
		foreach($preFillKeys as $key) {
			$data[ $key ] = isset($data[ $key ]) 
				? $data[ $key ] 
				: frameFhf::_()->getModule('options')->get( $key );
		}
		return $data;
	}
	public function displaySubscribeSuccess($d = array()) {
		parent::display('subscribeSuccess');
	}
	public function displaySubscribeErrors($d = array()) {
		$this->assign('errors', $d['errors']);
		parent::display('subscribeErrors');
	}
	public function displayUnsubscribeSuccess($d = array()) {
		parent::display('unsubscribeSuccess');
	}
	public function displayUnsubscribeErrors($d = array()) {
		$this->assign('errors', $d['errors']);
		parent::display('unsubscribeErrors');
	}
	public function getSubscribeFormFromWidget($widget, $params) {
		$this->assign('widget', $widget);
		$this->assign('params', $params);
		return parent::getContent('subscribeFormFromWidget');
	}
}

