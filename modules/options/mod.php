<?php
class optionsFhf extends moduleFhf {
	protected $_uploadDir = 'sub';
	protected $_bgImgFhfDir = 'bg_img';
	protected $_bgLogoImgFhfDir = 'logo_img';
    /**
     * This method provides fast access to options model method get
     * @see optionsModel::get($d)
     */
    public function get($d = array()) {
        return $this->getController()->getModel()->get($d);
    }
	/**
     * This method provides fast access to options model method get
     * @see optionsModel::get($d)
     */
	public function isEmpty($d = array()) {
		return $this->getController()->getModel()->isEmpty($d);
	}
	
	public function getUploadDir() {
		return $this->_uploadDir;
	}
	public function getBgImgDir() {
		return $this->_uploadDir. DS. $this->_bgImgFhfDir;
	}
	public function getBgImgFullDir() {
		return utilsFhf::getUploadsDir(). DS. $this->getBgImgDir(). DS. $this->get('bg_image');
	}
	public function getBgImgFullPath() {
		return utilsFhf::getUploadsPath(). '/'. $this->_uploadDir. '/'. $this->_bgImgFhfDir. '/'. $this->get('bg_image');
	}
	public function getLogoImgDir() {
		return $this->_uploadDir. DS. $this->_bgLogoImgFhfDir;
	}
	public function getLogoImgFullDir() {
		return utilsFhf::getUploadsDir(). DS. $this->getLogoImgDir(). DS. $this->get('logo_image');
	}
	public function getLogoImgFullPath() {
		return utilsFhf::getUploadsPath(). '/'. $this->_uploadDir. '/'. $this->_bgLogoImgFhfDir. '/'. $this->get('logo_image');
	}
	public function getAllowedPublicOptions() {
		$res = array();
		if(is_admin()) {
			$alowedForPublic = array('default_from_name', 'default_from_email', 'default_reply_name', 'default_reply_email', 'template');
		} else {
			$alowedForPublic = array();
		}
		if(!empty($alowedForPublic)) {
			$allOptions = $this->getModel()->getByCode();
			foreach($alowedForPublic as $code) {
				if(isset($allOptions[ $code ]))
					$res[ $code ] = $allOptions[ $code ];
			}
		}
		return $res;
	}
	public function getAdminPage() {
		return $this->getView()->getAdminPage();
	}
}

