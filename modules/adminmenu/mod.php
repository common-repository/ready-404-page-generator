<?php
class adminmenuFhf extends moduleFhf {
	protected $_mainSlug = 'ready-404-page-generator';
	public function getMainSlug() {
		return $this->_mainSlug;
	}
    public function init() {
        parent::init();
		add_action('admin_menu', array($this, 'initMenu'), 9);
		$plugName = plugin_basename(FHF_DIR. FHF_MAIN_FILE);
		add_filter('plugin_action_links_'. $plugName, array($this, 'addSettingsLinkForPlug') );
    }
	public function addSettingsLinkForPlug($links) {
		array_unshift($links, '<a href="'. uriFhf::_(array('baseUrl' => admin_url('admin.php'), 'page' => plugin_basename($this->getMainSlug()))). '">'. __('Settings'). '</a>');
		return $links;
	}
	public function initMenu() {
		$mainSlug = dispatcherFhf::applyFilters('adminMenuMainSlug', $this->_mainSlug);
		$mainMenuPageOptions = array(
			'page_title' => FHF_WP_PLUGIN_NAME, 
			'menu_title' => FHF_WP_PLUGIN_NAME, 
			'capability' => 'manage_options',
			'menu_slug' => $mainSlug,
			'function' => array(frameFhf::_()->getModule('options'), 'getAdminPage'));
		$mainMenuPageOptions = dispatcherFhf::applyFilters('adminMenuMainOption', $mainMenuPageOptions);
        add_menu_page($mainMenuPageOptions['page_title'], $mainMenuPageOptions['menu_title'], $mainMenuPageOptions['capability'], $mainMenuPageOptions['menu_slug'], $mainMenuPageOptions['function']);
	}
}

