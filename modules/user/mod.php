<?php
class userFhf extends moduleFhf {
    public function loadUserData() {
        return $this->getCurrent();
    }
    public function isAdmin() {
		if(!function_exists('wp_get_current_user')) {
			frameFhf::_()->loadPlugins();
		}
        return current_user_can('administrator');
    }
	public function getCurrentUserPosition() {
		if($this->isAdmin())
			return FHF_ADMIN;
		else if($this->getCurrentID())
			return FHF_LOGGED;
		else 
			return FHF_GUEST;
	}
    public function getCurrentID() {
        return $this->getController()->getModel()->getCurrentID();
    }
}

