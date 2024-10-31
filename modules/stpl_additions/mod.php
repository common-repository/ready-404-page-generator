<?php
/**
 * Super Template Additions module
 */
class stpl_additionsFhf extends moduleFhf {
	public function __construct($d, $params = array()) {
		parent::__construct($d, $params);
		dispatcherFhf::addFilter('stplAjaxTypes', array($this, 'addAjaxTypes'));
	}
	public function init() {
		dispatcherFhf::addFilter('stplEditElements', array($this, 'addNewEditElements'));
		dispatcherFhf::addFilter('adminOptionsTabs', array($this, 'addOptionsTab'));
		dispatcherFhf::addAction('stplEditorEnd', array($this, 'addNewEditorHtml'));
		
		add_shortcode('ready_stpl_search_form', array($this, 'searchFormShortcode'));
		add_shortcode('ready_stpl_menu', array($this, 'menuShortcode'));

		parent::init();
	}
	public function addOptionsTab($tabs) {
		frameFhf::_()->addScript('adminStplAdditions', $this->getModPath(). 'js/admin.stpl_additions.js', array('adminStplElements'));
		return $tabs;
	}
	public function addNewEditElements($editElements) {
		$editElements['stplCanvasElementSearch'] = array(
			'icon' => $this->getModPath(). 'img/element_icons/search.png',
			'label' => __('Search Form'),
		);
		$editElements['stplCanvasElementMenu'] = array(
			'icon' => $this->getModPath(). 'img/element_icons/menu.png',
			'label' => __('Menu'),
		);
		$editElements['stplCanvasSubscribeForm'] = array(
			'icon' => $this->getModPath(). 'img/element_icons/subscribe.png',
			'label' => __('Subscribe Form'),
		);
		return $editElements;
	}
	public function addNewEditorHtml() {
		$this->getView()->displayNewEditorHtml();
	}
	public function searchFormShortcode() {
		return get_search_form(false);
	}
	public function menuShortcode($params) {
		$addClass = isset($params['add_classes']) && !empty($params['add_classes']) ? 'class="'. $params['add_classes']. '"' : '';
		$addStyle = isset($params['add_styles']) && !empty($params['add_styles']) ? 'style="'. $params['add_styles']. '"' : '';
		return '<nav '. $addClass. ' '. $addStyle. '>'. wp_nav_menu(array_merge(array(
			'echo' => false,
		), $params)). '</nav>';
	}
	public function addAjaxTypes($ajaxTypes) {
		$ajaxTypes[] = 'stplCanvasElementSearch';
		$ajaxTypes[] = 'stplCanvasElementMenu';
		$ajaxTypes[] = 'stplCanvasSubscribeForm';
		return $ajaxTypes;
	}
}

