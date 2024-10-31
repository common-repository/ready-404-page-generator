<?php
class templatesFhf extends moduleFhf {
    /**
     * Returns the available tabs
     * 
     * @return array of tab 
     */
    protected $_styles = array();
	
    public function init() {
		dispatcherFhf::addFilter('adminOptionsTabs', array($this, 'addOptionsTab'));
		
		dispatcherFhf::addFilter('beforeOptionUpdate', array($this, 'templateOptSave'));
		dispatcherFhf::addFilter('optionUpdateRes', array($this, 'templateOptUpdateAddResData'), 10, 2);
		
		$this->initAssets();
        parent::init();
    }
	public function templateOptSave($d) {
		if($d['code'] == 'template') {
			$selectedId = $d['opt_values'][ $d['code'] ];
			if($selectedId < FHF_STPL_DEFINED_IDS_MAX) {
				$childId = frameFhf::_()->getModule('stpl')->getModel()->getIdByParent( $selectedId );
				if(!$childId) {
					$childId = frameFhf::_()->getModule('stpl')->getModel()->duplicateTpl( $selectedId );
				}
			}
			$d['opt_values'][ $d['code'] ] = $childId;
		}
		return $d;
	}
	public function templateOptUpdateAddResData($res, $d) {
		if($d['code'] == 'template') {
			$res->addData('real_stpl_id', frameFhf::_()->getModule('options')->get('template'));
		}
		return $res;
	}
	public function addOptionsTab($tabs) {
		frameFhf::_()->addScript('adminTemplateOptions', $this->getModPath(). 'js/admin.template.options.js');
		$tabs['fhfSelectTpl'] = array(
			'title' => 'Select Template', 'content' => $this->getView()->getAdminTemplateSelect(),
		);
		$tabs['fhfEditTpl'] = array(
			'title' => 'Edit Template', 'content' => $this->getView()->getAdminTemplateEdit(),
		);
		return $tabs;
	}
	public function initAssets() {
		$this->_styles = array(
            'styleFhf'				=> array('path' => FHF_CSS_PATH. 'style.css'), 
			'adminStylesFhf'		=> array('path' => FHF_CSS_PATH. 'adminStyles.css'), 
			'jquery-tabsFhf'			=> array('path' => FHF_CSS_PATH. 'jquery-tabs.css'),
			'jquery-buttonsFhf'		=> array('path' => FHF_CSS_PATH. 'jquery-buttons.css'),
			'wp-jquery-ui-dialogFhf'	=> array(),
			'farbtastic'			=> array(),
			// Our corrections for ui dialog
			'jquery-dialog'			=> array('path' => FHF_CSS_PATH. 'jquery-dialog.css'),
			'jquery-timepicker'		=> array('path' => FHF_CSS_PATH. 'jquery-timepicker.css'),
			'jquery.slideInput'		=> array('path' => FHF_CSS_PATH. 'jquery.slideInput.css'),
			'jquery-dataTables'		=> array('path' => FHF_CSS_PATH. 'jquery.dataTables.css'),
			'jquery-ui-datepicker'	=> array('path' => FHF_CSS_PATH. 'jquery-datepicker.css'),
			'jquery-ui-resizable'	=> array('path' => FHF_CSS_PATH. 'jquery-resizable.css'),
        );
		$ajaxurl = admin_url('admin-ajax.php');
        $jsData = array(
            'siteUrl'					=> FHF_SITE_URL,
            'imgPath'					=> FHF_IMG_PATH,
			'cssPath'					=> FHF_CSS_PATH,
            'loader'					=> FHF_LOADER_IMG, 
            'close'						=> FHF_IMG_PATH. 'cross.gif', 
            'ajaxurl'					=> $ajaxurl,
            'animationSpeed'			=> frameFhf::_()->getModule('options')->get('js_animation_speed'),
			'FHF_CODE'					=> FHF_CODE,
			'ball_loader'				=> FHF_IMG_PATH. 'ajax-loader-ball.gif',
			'ok_icon'					=> FHF_IMG_PATH. 'ok-icon.png',
			'options'					=> frameFhf::_()->getModule('options')->getAllowedPublicOptions(),
			'FHF_ANY'					=> FHF_ANY,
			'FHF_TIME_IMMEDIATELY'		=> FHF_TIME_IMMEDIATELY,
			
			'FHF_TYPE_NOW'				=> FHF_TYPE_NOW,
			'FHF_TYPE_NEW_CONTENT'		=> FHF_TYPE_NEW_CONTENT,
			'FHF_TYPE_SCHEDULE'			=> FHF_TYPE_SCHEDULE,
        );
        
		frameFhf::_()->addScript('jquery');

		frameFhf::_()->addScript('commonFhf', FHF_JS_PATH. 'common.js');
		frameFhf::_()->addScript('coreFhf', FHF_JS_PATH. 'core.js');
		$loadStyles = false;
        if (is_admin()) {
			if(reqFhf::getVar('reqType') != 'ajax' && frameFhf::_()->isAdminPlugPage()) {
				frameFhf::_()->addScript('jquery-ui-tabs', '', array('jquery'));
				frameFhf::_()->addScript('jquery-ui-dialog', '', array('jquery'));
				frameFhf::_()->addScript('jquery-ui-button', '', array('jquery'));
				frameFhf::_()->addScript('jquery-ui-resizable', '', array('jquery'));
				frameFhf::_()->addScript('jquery-ui-sortable', '', array('jquery'));
				frameFhf::_()->addScript('jquery-ui-accordion', '', array('jquery'));
				frameFhf::_()->addScript('jquery-ui-datepicker', '', array('jquery'));
				frameFhf::_()->addScript('jquery-ui-timepicker-sub', FHF_JS_PATH. 'jquery.timepicker.min.js', array('jquery'));
				frameFhf::_()->addScript('jquery-slideInput-sub', FHF_JS_PATH. 'jquery.slideInput.js', array('jquery'));
				frameFhf::_()->addScript('jquery-dataTables-sub', FHF_JS_PATH. 'jquery.dataTables.js', array('jquery'));
				frameFhf::_()->addScript('jquery-dataTables-columnFilter-sub', FHF_JS_PATH. 'jquery.dataTables.columnFilter.js', array('jquery'));
				
				frameFhf::_()->addScript('farbtastic');
				frameFhf::_()->addScript('adminOptionsFhf', FHF_JS_PATH. 'admin.options.js');
				frameFhf::_()->addScript('ajaxupload', FHF_JS_PATH. 'ajaxupload.js');
				frameFhf::_()->addScript('postbox', get_bloginfo('wpurl'). '/wp-admin/js/postbox.js');
				
				add_thickbox();
				$jsData['allCheckRegPlugs']	= modInstallerFhf::getCheckRegPlugs();
				
				frameFhf::_()->addScript('wp-color-picker');
				frameFhf::_()->addStyle('wp-color-picker');
				$loadStyles = true;
			}
		}
		$jsData = dispatcherFhf::applyFilters('jsInitVariables', $jsData);
        frameFhf::_()->addJSVar('coreFhf', 'FHF_DATA', $jsData);
        if($loadStyles) {
			foreach($this->_styles as $s => $sInfo) {
				if(isset($sInfo['for'])) {
					if(($sInfo['for'] == 'frontend' && is_admin()) || ($sInfo['for'] == 'admin' && !is_admin()))
						continue;
				}
				$canBeSubstituted = true;
				if(isset($sInfo['substituteFor'])) {
					switch($sInfo['substituteFor']) {
						case 'frontend':
							$canBeSubstituted = !is_admin();
							break;
						case 'admin':
							$canBeSubstituted = is_admin();
							break;
					}
				}
				if(!empty($sInfo['path'])) {
					frameFhf::_()->addStyle($s, $sInfo['path']);
				} else {
					frameFhf::_()->addStyle($s);
				}
			}
		}
	}
}
