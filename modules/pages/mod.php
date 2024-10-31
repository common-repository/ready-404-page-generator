<?php
class pagesFhf extends moduleFhf {
    /**
     * Check if current page is Login page
     */
    public function isLogin() {
		return basename($_SERVER['SCRIPT_NAME']) == 'wp-login.php';
    }
	/**
	 * Show messages at the top of the page. This is kinda weird way to do this, 
	 * but WP gives me no other way to do this - no action or hook right after body tag opening or somwere around this place
	 */
	public function checkSysMessages() {
		$messages = array();
		if(is_404() && frameFhf::_()->getModule('user')->isAdmin()) {
			$messages['adminAlerts'][] = __(array(
				'If you are trying to view your product and see this message - maybe you have some troubles with permalinks settings.',
				'Try to go to <a href="'. get_admin_url(). 'options-permalink.php'. '">Admin panel -> Settings -> Permalinks</a> and re-saive this settings (just click on "Save Changes").<br />',
				'<a href="http://readyshoppingcart.com/faq/ecommerce-plugin-alerts">',
				'Please check FAQ.',
				'</a>',
				
			));
		}
		if(!empty($messages)) {
			$this->getView()->assign('forAdminOnly', __('This message will be visible for admin only.'));
			$this->getView()->assign('messages', $messages);
			$this->getView()->display('pagesSystemMessages');
		}
	}
	public function overwriteProtocol($link, $id, $sample) {
		static $pagesCache;
		$makeHttpsReplace = false;
		if(frameFhf::_()->getModule('options')->get('ssl_on_checkout') || frameFhf::_()->getModule('options')->get('ssl_on_account')) {
			if(!isset($pagesCache[ $id ])) {
				$pageParams = $this->getByID($id);
				if($pageParams == NULL)
					$pagesCache[ $id ] = false;
				else 
					$pagesCache[ $id ] = $pageParams;
			}
			if($pagesCache[ $id ] && is_object($pagesCache[ $id ])) {
				if(
					(frameFhf::_()->getModule('options')->get('ssl_on_checkout')
					&& (($pagesCache[ $id ]->mod == 'checkout') 
						|| ($pagesCache[ $id ]->mod == 'user' && in_array($pagesCache[ $id ]->action, array('getShoppingCart')))))
					|| (frameFhf::_()->getModule('options')->get('ssl_on_account')
					&& (($pagesCache[ $id ]->mod == 'user' && in_array($pagesCache[ $id ]->action, array('getLoginForm', 'getRegisterForm', 'getAccountSummaryHtml', 'getProfileHtml', 'getOrdersList')))
						|| ($pagesCache[ $id ]->mod == 'digital_product' && in_array($pagesCache[ $id ]->action, array('getDownloadsList')))))
				) {
					$makeHttpsReplace = true;
				}
			}
		}
		if($makeHttpsReplace) {
			$link = uriFhf::makeHttps($link);
		}
		return $link;
	}
}

