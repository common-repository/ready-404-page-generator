<?php
class stplViewFhf extends viewFhf {
	public function load($d = array()) {
		$this->assign('titleStyles', array(
			'h1' => __('Heading 1'),
			'h2' => __('Heading 2'),
			'h3' => __('Heading 3'),
			'h4' => __('Heading 4'),
			'h5' => __('Heading 5'),
			'h6' => __('Heading 6'),
		));
		$this->assign('aligns', array(
			'left'		=> __('Left'),
			'center'	=> __('Center'),
			'right'		=> __('Right'),
		));
		$this->assign('showContent', array(
			'excerpt'	=> __('Excerpt'),
			'full' => __('Full Post'),
		));
		$this->assign('fonts', array(
			'Arial'				=> 'Arial',
			'Arial Black'		=> 'Arial Black',
			'Comic Sans MS'		=> 'Comic Sans MS',
			'Courier New'		=> 'Courier New',
			'Droid Sans'		=> 'Droid Sans',
			'Georgia'			=> 'Georgia',
			'Impact'			=> 'Impact',
			'Tahoma'			=> 'Tahoma',
			'Times New Roman'	=> 'Times New Roman',
			'Trebuchet MS'		=> 'Trebuchet MS',
			'Verdana'			=> 'Verdana',
		));
		$this->assign('styleElements', array(
			'text'	=> array('label' => __('Text'),		'selector' => '*', 'defaults' => array('font-family' => 'Trebuchet MS', 'font-size' => '13px', 'color' => '#000000')),
			'links' => array('label' => __('Links'),	'selector' => 'a', 'defaults' => array('font-family' => 'Trebuchet MS', 'font-size' => '13px', 'color' => '#0000EE')),
			'h1'	=> array('label' => __('Heading 1'), 'selector' => 'h1', 'defaults' => array('font-family' => 'Trebuchet MS', 'font-size' => '22px', 'color' => '#000000')),
			'h2'	=> array('label' => __('Heading 2'), 'selector' => 'h2', 'defaults' => array('font-family' => 'Trebuchet MS', 'font-size' => '18px', 'color' => '#000000')),
			'h3'	=> array('label' => __('Heading 3'), 'selector' => 'h3', 'defaults' => array('font-family' => 'Trebuchet MS', 'font-size' => '16px', 'color' => '#000000')),
			'h4'	=> array('label' => __('Heading 4'), 'selector' => 'h4', 'defaults' => array('font-family' => 'Trebuchet MS', 'font-size' => '14px', 'color' => '#000000')),
			'h5'	=> array('label' => __('Heading 5'), 'selector' => 'h5', 'defaults' => array('font-family' => 'Trebuchet MS', 'font-size' => '13px', 'color' => '#000000')),
			'h6'	=> array('label' => __('Heading 6'), 'selector' => 'h6', 'defaults' => array('font-family' => 'Trebuchet MS', 'font-size' => '12px', 'color' => '#000000')),
		));
		$postsNum = array();
		for($i = 1; $i <= 10; $i++) {
			$postsNum[ $i ] = $i;
		}
		$this->assign('postsNum', $postsNum);
		$fontSizesList = array(8, 9, 10, 11, 12, 13, 14, 15, 16, 18, 20, 22, 24, 26, 28, 30, 32, 34, 36, 38, 40, 44, 48, 52, 56, 60, 66, 72, 78, 84, 90, 98, 106, 114, 124, 134, 146, 158);
		$fontSizes = array();
		foreach($fontSizesList as $f) {
			$fontSizes[ $f. 'px' ] = $f. 'px';
		}
		$this->assign('fontSizes', $fontSizes);
		$editElements = array(
			'stplCanvasElementText' => array(
				'label' => __('Text and Titles'),
				'icon' => $this->getModule()->getModPath(). 'img/element_icons/text.png',
			),
			'stplCanvasElementImage' => array(
				'label' => __('Images'),
				'icon' => $this->getModule()->getModPath(). 'img/element_icons/images.png',
			),
			'stplCanvasElementSocial' => array(
				'label' => __('Social Icons and Bookmarks'),
				'icon' => $this->getModule()->getModPath(). 'img/element_icons/soc_icons.png',
			),
			'stplCanvasElementDivider' => array(
				'label' => __('Dividers'),
				'icon' => $this->getModule()->getModPath(). 'img/element_icons/dividers.png',
			),
			'stplCanvasElementNewContent' => array(
				'label' => __('Dynamic Wordpress Content'),
				'icon' => $this->getModule()->getModPath(). 'img/element_icons/dynamic.png',
			),
			'stplCanvasElementStaticContent' => array(
				'label' => __('Static Wordpress Content'),
				'icon' => $this->getModule()->getModPath(). 'img/element_icons/static.png',
			),
		);
		$editElements = dispatcherFhf::applyFilters('stplEditElements', $editElements);
		$this->assign('editElements', $editElements);
		$socDesigns = array(
			1 => array('useImg' => true),
			2 => array('useImg' => true),
			3 => array('useImg' => false),
		);
		$this->assign('socDesigns', $socDesigns);
		return parent::getContent('stplEditor');
	}
	public function generateContent($idOrContent, $options = array()) {
		$stpl = array();
		if(is_numeric($idOrContent)) {
			$stpl = $this->getModel()->getById($idOrContent);
		} elseif(is_array($idOrContent)) {
			$stpl = $idOrContent;
		}
		if($stpl) {
			$this->getModule()->connectFrontendAssets();
			$widthMod = $this->getModule()->getWidthMod();
			$fullPage = isset($options['fullPage']) ? $options['fullPage'] : false;
			$this->assign('widthMod', $widthMod);
			$this->assign('fullPage', $fullPage);
			$this->assign('options', $options);
			$this->assign('stpl', $stpl);
			$stplContent = parent::getContent('stplContent');
			if(isset($stpl['style_params']) && isset($stpl['style_params']['font_style'])) {
				$this->getModule()->loadLib('simple_html_dom');
				$stplContentObj = str_get_html( $stplContent );
				$fontStyleKeys = array('font-family', 'font-size', 'color');
				foreach($stpl['style_params']['font_style'] as $key => $style) {
					if($key === 'text') continue;
					$elements = $stplContentObj->find($style['selector']);
					if(!empty($elements)) {
						foreach($elements as $element) {
							if($element->hasClass('fa')) continue;
							$stylesArray = $element->getStyleArray();
							foreach($fontStyleKeys as $styleKey) {
								if(isset($stylesArray[ $styleKey ]) && $key === 'text') continue;
								$stylesArray[ $styleKey ] = $style[ $styleKey ];
							}
							$element->setStyleFromArray( $stylesArray );
						}
					}
				}
				$stplContent = $stplContentObj;
			}
			return $stplContent;
		} else
			$this->pushError(__('Can not find template for to generate'));
		return false;
	}
	public function showTextEditor() {
		parent::display('stplTextEditor');
	}
}
