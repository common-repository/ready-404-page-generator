<?php
/**
 * Super Template module
 */
class stplFhf extends moduleFhf {
	private $_widthMod = 'full';
	public function __construct($d, $params = array()) {
		parent::__construct($d, $params);
		dispatcherFhf::addFilter('jsInitVariables', array($this, 'addjsInitVars'));
	}
	public function getWidthMod() {
		return $this->_widthMod;
	}
	public function init() {
		dispatcherFhf::addFilter('adminOptionsTabs', array($this, 'addOptionsTab'));
		if(is_admin() && frameFhf::_()->isAdminPlugPage()) {
			add_action('in_admin_footer', array($this, 'showTextEditor'));
			frameFhf::_()->addStyle('adminStpl', $this->getModPath(). 'css/admin.stpl.css');
			$this->connectFrontendAssets();
		}
		$this->_libs = array(
			'simple_html_dom' => array('file' => 'simple_html_dom.php', 'testFunc' => 'checkSimpleHtmlDomExists'),
		);
	}
	public function connectFrontendAssets() {
		frameFhf::_()->addStyle('fontAwesome', $this->getModPath(). 'css/font-awesome.min.css');
		frameFhf::_()->addStyle('stplSocial', $this->getModPath(). 'css/social.css');
	}
	public function checkSimpleHtmlDomExists() {
		return !function_exists('file_get_html');
	}
	public function addOptionsTab($tabs) {
		// Just add javascripts to adin tab
		if(function_exists( 'wp_enqueue_media' )){
			wp_enqueue_media();
		} else {
			wp_enqueue_style('thickbox');
			wp_enqueue_script('media-upload');
			wp_enqueue_script('thickbox');
		}
		frameFhf::_()->addScript('adminStplMath', $this->getModPath(). 'js/admin.stpl.math.js');
		frameFhf::_()->addScript('adminStplDragAnDrop', $this->getModPath(). 'js/admin.stpl.drag-an-drop.js');
		frameFhf::_()->addScript('adminStplElements', $this->getModPath(). 'js/admin.stpl.elements.js');
		frameFhf::_()->addScript('adminStplOptions', $this->getModPath(). 'js/admin.stpl.options.js');
		
		frameFhf::_()->addScript('jquery-effects-core');
		return $tabs;
	}
	/**
	 * Call model shell - for more comfortable access
	 */
	public function save($d = array()) {
		return $this->getModel()->save($d);
	}
	public function showTextEditor() {
		$this->getView()->showTextEditor();
	}
	public function addjsInitVars($jsData) {
		$jsData['stplModPath'] = $this->getModPath();
		$jsData['stplAjaxTypes'] = dispatcherFhf::applyFilters('stplAjaxTypes', array('stplCanvasElementStaticContent', 'stplCanvasElementNewContent'));
		return $jsData;
	}
}

