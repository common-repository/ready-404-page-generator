<?php
class optionsModelFhf extends modelFhf {
    protected $_allOptions = array();
    public function get($d = array()) {
        $this->_loadOptions();
        $code = false;
        if(is_string($d))
            $code = $d;
        elseif(is_array($d) && isset($d['code']))
            $code = $d['code'];
        if($code) {
            $opt = $this->_getByCode($code);
            if(isset($d['what']) && isset($opt[$d['what']]))
                return $opt[$d['what']];
            else
                return $opt['value'];
        } else {
            return $this->_allOptions;
        }
    }
	public function isEmpty($d = array()) {
		$value = $this->get($d);
		return empty($value);
	}
	public function getByCategories($category = '') {
		$this->_loadOptions();
		$categories = array();
		$returnForCat = !empty($category);	// If this is not empty - will be returned anly for one category
		foreach($this->_allOptions as $opt) {
			if(empty($category)
				|| (is_numeric($category) && $category == $opt['cat_id'])
				|| ($category == $opt['cat_label'])
			) {
				if(empty($categories[ (int)$opt['cat_id'] ]))
					$categories[ (int)$opt['cat_id'] ] = array('cat_id' => $opt['cat_id'], 'cat_label' => $opt['cat_label'], 'opts' => array());
				$categories[ (int)$opt['cat_id'] ]['opts'][] = $opt;
				if($returnForCat)	// Save category ID for returning
					$returnForCat = (int)$opt['cat_id'];
			}
		}
		if($returnForCat)
			return $categories[ $returnForCat ];
		ksort($categories);
		return $categories;
	}
	public function getByCode($d = array()) {
		$res = array();
		$codeData = $this->get($d);
		if(empty($d)) {
			// Sort by code
			foreach($codeData as $opt) {
				$res[ $opt['code'] ] = $opt;
			}
		} else
			$res = $codeData;
		return $res;
	}
    /**
     * Load all options data into protected array
     */
    protected function _loadOptions() {
        if(empty($this->_allOptions)) {
            $options = frameFhf::_()->getTable('options');
            $htmltype = frameFhf::_()->getTable('htmltype');
			$optionsCategories = frameFhf::_()->getTable('options_categories');
            $this->_allOptions = $options->innerJoin($htmltype, 'htmltype_id')
					->leftJoin($optionsCategories, 'cat_id')
					->orderBy(array('cat_id', 'sort_order'))
                    ->getAll($options->alias(). '.*, '. $htmltype->alias(). '.label AS htmltype, '. $optionsCategories->alias(). '.label AS cat_label');
            foreach($this->_allOptions as $i => $opt) {
                if(!empty($this->_allOptions[$i]['params'])) {
                    $this->_allOptions[$i]['params'] = utilsFhf::unserialize($this->_allOptions[$i]['params']);
                }
				if($this->_allOptions[$i]['value_type'] == 'array') {
					$this->_allOptions[$i]['value'] = utilsFhf::unserialize($this->_allOptions[$i]['value']);
					if(!is_array($this->_allOptions[$i]['value']))
						$this->_allOptions[$i]['value'] = array();
				}
				if(empty($this->_allOptions[$i]['cat_id'])) {	// Move all options that have no category - to Other
					$this->_allOptions[$i]['cat_id'] = 6;
					$this->_allOptions[$i]['cat_label'] = 'Other';
				}
            }
        }
    }
    /**
     * Returns option data by it's code
     * @param string $code option's code
     * @return array option's data
     */
    protected function _getByCode($code) {
        $this->_loadOptions();
        if(!empty($this->_allOptions)) {
            foreach($this->_allOptions as $opt) {
                if($opt['code'] == $code)
                    return $opt;
            }
        }
        return false;
    }
	
	/**
     * Set option value by code, do no changes in database
     * @param string $code option's code
	 * @param string $value option's new value
     */
	protected function _setByCode($code, $value) {
        $this->_loadOptions();
        if(!empty($this->_allOptions)) {
            foreach($this->_allOptions as $id => $opt) {
                if($opt['code'] == $code) {
					$this->_allOptions[ $id ]['value'] = $value;
                    break;
				}
            }
        }
    } 
    public function save($d = array()) {
        $id = 0;
		if(isset($d['opt_values']) && is_array($d['opt_values']) && !empty($d['opt_values'])) {
			if(isset($d['code']) && !empty($d['code'])) {
				$d['what'] = 'id';
				$id = $this->get($d);
				$id = intval($id);
			}
			if($id) {
				$d = dispatcherFhf::applyFilters('beforeOptionUpdate', $d);
				$updateData = array('value' => $d['opt_values'][ $d['code'] ]);
				if($this->get(array('code' => $d['code'], 'what' => 'value_type')) == 'array') {
					$updateData['value'] = utilsFhf::serialize( $updateData['value'] );
				}
				
				if(frameFhf::_()->getTable('options')->update($updateData, array('id' => $id))) {
					// Let's update data in current options params to avoid reload it from database
					if(isset($d['code']))
						$this->_setByCode($d['code'], $d['opt_values'][ $d['code'] ]);
					dispatcherFhf::doAction('optionUpdated', $d);
					if(in_array($d['code'], array('bg_color', 'bg_image')) && !isset($d['opt_values'])) {
						// Let's save this here to avoid push user save it by hands
						$this->save(array('opt_values' => array('bg_type' => ($d['code'] == 'bg_image' ? 'image' : 'color')), 'code' => 'bg_type'));
						// Disable Bg slider
						if($this->get('slider_enabled')) {
							$this->save(array('opt_values' => array('slider_enabled' => 0), 'code' => 'slider_enabled'));
						}
					}
					return true;
				} else
					$this->pushError(__('Option '. $d['code']. ' update Failed'));
			} else {
				$this->pushError(__('Invalid option ID or Code'));
			}
		} else
			$this->pushError(__('Empty data to save option'));
        return false;
    }
	public function saveGroup($d = array()) {
		if(isset($d['opt_values']) && is_array($d['opt_values']) && !empty($d['opt_values'])) {
			foreach($d['opt_values'] as $code => $value) {
				$d['code'] = $code;
				$this->save($d);
			}
			return !$this->haveErrors();
		} else
			$this->pushError(__('Empty data to setup'));
	}
	public function saveBgImg($d = array()) {
		if(!empty($d) && isset($d['bg_image']) && !empty($d['bg_image'])) {
			$uploader = toeCreateObjFhf('fileuploader', array());
			if($uploader->validate('bg_image', frameFhf::_()->getModule('options')->getBgImgDir()) && $uploader->upload()) {
				// Remove prev. image
				utilsFhf::deleteFile( frameFhf::_()->getModule('options')->getBgImgFullDir() );
				$fileInfo = $uploader->getFileInfo();
				// Save info for this option
				$this->save(array('code' => 'bg_image', 'opt_values' => array('bg_image' => $fileInfo['path'])));
				return true;
			} else
				 $this->pushError( $uploader->getError() );
		} else
			$this->pushError(__('Empty data to setup'));
		return false;
	}
	public function saveLogoImg($d = array()) {
		if(!empty($d) && isset($d['logo_image']) && !empty($d['logo_image'])) {
			$uploader = toeCreateObjFhf('fileuploader', array());
			if($uploader->validate('logo_image', frameFhf::_()->getModule('options')->getLogoImgDir()) && $uploader->upload()) {
				// Remove prev. image
				utilsFhf::deleteFile( frameFhf::_()->getModule('options')->getLogoImgFullDir() );
				$fileInfo = $uploader->getFileInfo();
				// Save info for this option
				$this->save(array('code' => 'logo_image', 'opt_values' => array('logo_image' => $fileInfo['path'])));
				return true;
			} else
				 $this->pushError( $uploader->getError() );
		} else
			$this->pushError(__('Empty data to setup'));
		return false;
	}
	public function setTplDefault($d = array()) {
		$code = isset($d['code']) ? $d['code'] : '';
		if(!empty($code)) {
			$plTemplate = $this->get('template');		// Current plugin template
			if($plTemplate && frameFhf::_()->getModule($plTemplate)) {
				$newValue = frameFhf::_()->getModule($plTemplate)->getDefOptions($code);
				if($newValue !== NULL) {
					if($this->save(array('opt_values' => array($code => $newValue), 'code' => $code))) {
						return $newValue;
					}
				} else
					$this->pushError(__('There is no default for this option and current template'));
			} else
				$this->pushError(__('There is no default for this option and current template'));
		} else
			$this->pushError(__('Empty option code'));
		return false;
	}
	public function setTplAnyDefault($d = array()) {
		$code = isset($d['code']) ? $d['code'] : '';
		// For multiple options set
		if(is_array($code)) {
			$res = true;
			foreach($code as $c) {
				$res = $this->setTplAnyDefault(array('code' => $c));
			}
			return $res;
		}
		switch($code) {
			case 'bg_image':
				$newOptValue = $this->setBgImgDefault($d);
				$this->setTplDefault(array('code' => 'bg_img_show_type'));
				break;
			case 'logo_image':
				$newOptValue = $this->setLogoDefault($d);
				break;
			case 'msg_title_params':
				$newOptValue = $this->setTitleParamsDefault($d);
				break;
			case 'msg_text_params':
				$newOptValue = $this->setTextParamsDefault($d);
				break;
			case 'slider_images':
				$newOptValue = false;
				if(frameFhf::_()->getModule('bg_slider')) {
					if(frameFhf::_()->getModule('bg_slider')->getController()->getModel()->saveTplDefaultSlides()) {
						$newOptValue = array(
							'slides' => frameFhf::_()->getModule('bg_slider')->getSlidesFullPath(),
							'slidesNames' => frameFhf::_()->getModule('options')->get('slider_images'),
						);
					} else
						$this->pushError( frameFhf::_()->getModule('bg_slider')->getController()->getModel()->getErrors() );
				} else {
					$this->pushError(__('There are no Bg slider module installed'));
				}
				break;
			default:
				$newOptValue = $this->setTplDefault($d);
				break;
		}
		return $newOptValue;
	}
	public function setBgImgDefault($d = array()) {
		$code = isset($d['code']) ? $d['code'] : '';
		if(!empty($code)) {
			$plTemplate = $this->get('template');		// Current plugin template
			if($plTemplate && frameFhf::_()->getModule($plTemplate)) {
				$newValue = frameFhf::_()->getModule($plTemplate)->getDefOptions($code);
				if($newValue !== NULL && file_exists(frameFhf::_()->getModule($plTemplate)->getModDir(). $newValue)) {
					// Remove prev. image
					if(utilsFhf::fileExists( frameFhf::_()->getModule('options')->getBgImgFullDir() ))
						utilsFhf::deleteFile( frameFhf::_()->getModule('options')->getBgImgFullDir() );
					// Copy new image from tpl module directory to uploads dirctory
					copy( frameFhf::_()->getModule($plTemplate)->getModDir(). $newValue, utilsFhf::getUploadsDir(). DS. $this->getModule()->getBgImgDir(). DS. $newValue);
					if($this->save(array('opt_values' => array($code => $newValue), 'code' => $code))) {
						return $this->getModule()->getBgImgFullPath();
					}
				} else
					$this->pushError(__('There is no default for this option and current template'));
			} else
				$this->pushError(__('There is no default for this option and current template'));
		} else
			$this->pushError(__('Empty option code'));
		return false;
	}
	public  function removeBgImg($d = array()) {
		$bgImgDirPath = frameFhf::_()->getModule('options')->getBgImgFullDir();
		if($this->save(array('opt_values' => array('bg_image' => ''), 'code' => 'bg_image'))
			&& utilsFhf::deleteFile( $bgImgDirPath )
		) {
			return true;
		} else
			$this->pushError(__('Unable to remove image'));
	}
	public function setLogoDefault($d = array()) {
		$code = isset($d['code']) ? $d['code'] : '';
		if(!empty($code)) {
			$plTemplate = $this->get('template');		// Current plugin template
			if($plTemplate && frameFhf::_()->getModule($plTemplate)) {
				$newValue = frameFhf::_()->getModule($plTemplate)->getDefOptions($code);
				
				if($newValue !== NULL && file_exists(frameFhf::_()->getModule($plTemplate)->getModDir(). $newValue)) {
					// Remove prev. image
					if(utilsFhf::fileExists(frameFhf::_()->getModule('options')->getLogoImgFullDir()))
						utilsFhf::deleteFile( frameFhf::_()->getModule('options')->getLogoImgFullDir() );
					// Copy new image from tpl module directory to uploads dirctory
					copy( frameFhf::_()->getModule($plTemplate)->getModDir(). $newValue, utilsFhf::getUploadsDir(). DS. $this->getModule()->getLogoImgDir(). DS. $newValue);
					if($this->save(array('opt_values' => array($code => $newValue), 'code' => $code))) {
						return $this->getModule()->getLogoImgFullPath();
					}
				} else
					$this->pushError(__('There is no default for this option and current template'));
			} else
				$this->pushError(__('There is no default for this option and current template'));
		} else
			$this->pushError(__('Empty option code'));
		return false;
	}
	public function removeLogoImg($d = array()) {
		$logoImgDirPath = frameFhf::_()->getModule('options')->getLogoImgFullDir();
		if($this->save(array('opt_values' => array('logo_image' => ''), 'code' => 'logo_image'))
			&& utilsFhf::deleteFile( $logoImgDirPath )
		) {
			return true;
		} else
			$this->pushError(__('Unable to remove image'));
	}
	public function setTitleParamsDefault($d = array()) {
		$res = true;
		$plTemplate = $this->get('template');		// Current plugin template
		if($plTemplate && frameFhf::_()->getModule($plTemplate)) {
			$msgTitleColor = frameFhf::_()->getModule($plTemplate)->getDefOptions('msg_title_color');
			if($msgTitleColor !== NULL) {
				$this->save(array('opt_values' => array('msg_title_color' => $msgTitleColor), 'code' => 'msg_title_color'));
			}
			$msgTitleFont = frameFhf::_()->getModule($plTemplate)->getDefOptions('msg_title_font');
			if($msgTitleFont !== NULL) {
				$this->save(array('opt_values' => array('msg_title_font' => $msgTitleFont), 'code' => 'msg_title_font'));
			}
			if($msgTitleColor !== NULL || $msgTitleFont !== NULL) {
				$res = array('msg_title_color' => $msgTitleColor, 'msg_title_font' => $msgTitleFont);
			}
		}
		// good in any case
		return $res;
	}
	public function setTextParamsDefault($d = array()) {
		$res = true;
		$plTemplate = $this->get('template');		// Current plugin template
		if($plTemplate && frameFhf::_()->getModule($plTemplate)) {
			$msgTextColor = frameFhf::_()->getModule($plTemplate)->getDefOptions('msg_text_color');
			if($msgTextColor !== NULL) {
				$this->save(array('opt_values' => array('msg_text_color' => $msgTextColor), 'code' => 'msg_text_color'));
			}
			$msgTextFont = frameFhf::_()->getModule($plTemplate)->getDefOptions('msg_text_font');
			if($msgTextFont !== NULL) {
				$this->save(array('opt_values' => array('msg_text_font' => $msgTextFont), 'code' => 'msg_text_font'));
			}
			if($msgTextColor !== NULL || $msgTextFont !== NULL) {
				$res = array('msg_text_color' => $msgTextColor, 'msg_text_font' => $msgTextFont);
			}
		}
		// good in any case
		return $res;
	}
}
