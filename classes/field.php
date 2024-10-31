<?php
class fieldFhf {
	public $name = '';
	public $html = '';
	public $type = '';
	public $default = '';
	public $value = '';
	public $label = '';
	public $maxlen = 0;
	public $id = 0;
	public $htmlParams = array();
	public $validate = array();
	public $description = '';
	
	static public $countries = array();
    static public $states = array();
	/**
	 * Wheter or not add error html element right after input field
	 * if bool - will be added standard element
	 * if string - it will be add this string
	 */
	public $errorEl = false;
	/**
	 * Name of method in table object to prepare data before insert / update operations
	 */
	public $adapt = array('htmlFhf' => '', 'dbFrom' => '', 'dbTo' => '');
	/**
	 * Init database field representation
	 * @param string $html html type of field (text, textarea, etc. @see html class)
	 * @param string $type database type (int, varcahr, etc.)
	 * @param mixed $default default value for this field
	 */
	public function __construct($name, $html = 'text', $type = 'other', $default = '', $label = '', $maxlen = 0, $adaption = array(), $validate = '', $description = '') {
		$this->name = $name;
		$this->html = $html;
		$this->type = $type;
		$this->default = $default;
		$this->value = $default;    //Init field value same as default
		$this->label = $label;
		$this->maxlen = $maxlen;
		$this->description = $description;
		if($adaption)
			$this->adapt = $adaption;
		if($validate) {
			$this->setValidation($validate);
		}
		if($type == 'varchar' && !empty($maxlen) && !in_array('validLen', $this->validate)) {
			$this->addValidation('validLen');
		}
	}
	/**
	 * @param mixed $errorEl - if bool and "true" - than we will use standard error element, if string - we will use this string as error element
	 */
	public function setErrorEl($errorEl) {
		$this->errorEl = $errorEl;
	}
	public function getErrorEl() {
		return $this->errorEl;
	}
	public function setValidation($validate) {
		if(is_array($validate))
			$this->validate = $validate;
		else {
			if(strpos($validate, ','))
				$this->validate = array_map('trim', explode(',', $validate));
			else
				$this->validate = array(trim($validate));
		}
	}
	public function addValidation($validate) {
		$this->validate[] = $validate;
	}
	/**
	 * Set $value property. 
	 * Sure - it is public and can be set directly, but it can be more 
	 * comfortable to use this method in future
	 * @param mixed $value value to be set
	 */
	public function setValue($value, $fromDB = false) {
		$this->value = $value;
	}
	public function setLabel($label) {
		$this->label = $label;
	}
	public function setHtml($html) {
		$this->html = $html;
	}
	public function getHtml() {
		return $this->html;
	}
	public function setName($name) {
		$this->name = $name;
	}
	public function getName() {
		return $this->name;
	}
	public function getValue() {
		return $this->value;
	}
	public function getLabel() {
		return $this->label;
	}
	public function setID($id) {
		$this->id = $id;
	}
	public function getID() {
		return $this->id;
	}
	public function setAdapt($adapt) {
		$this->adapt = $adapt;
	}
	public function drawHtml($tag, $id) {
		if(method_exists('htmlFhf', $this->html)) {
			$method = $this->html;
			//echo $this->name. ': '. $this->value. '<br />';
			if(!empty($this->value))
				$this->htmlParams['value'] = $this->value;
			if ($method == 'checkbox') {
				if ($this->value == 1) {
					$this->htmlParams['checked'] = 1;
				}
			}
			$params = $this->processParams($tag, $id);
			if ($params != '')
				return $params;
			if ($this->name == 'default_value') {
				$optionsFromDb = frameFhf::_()->getModule('optionsFhf')->getHelper()->getOptions($id);
				if (!empty($optionsFromDb)) {
					$options = array(0 => __('Select'));
					foreach($optionsFromDb as $k => $v)
						$options[$k] = $v;
					$method = 'selectbox';
					$this->htmlParams['optionsFhf'] = $options;
				}
			}
			$htmlContent = htmlFhf::$method($this->name, $this->htmlParams);
			if(!empty($this->errorEl)) {
				if(is_bool($this->errorEl))
					$errorEl = '<div class="toeErrorForField toe_'. htmlFhf::nameToClassId($this->name). '"></div>';
				else    //if it is string
					$errorEl = $this->errorEl;
				$htmlContent .= $errorEl;
			}
			return $htmlContent;
		}
		return false;
	}
	static public function displayCountry($cid, $key = 'name') {
        if($key == 'name') {
            $countries = self::getCountries();
            return $countries[$cid];
        } else {
            if(empty(self::$countries))
                self::$countries = self::getCachedCountries();
            foreach(self::$countries as $c) {
                if($c['id'] == $cid)
                    return $c[ $key ];
            }
        }
        return false;
    }
    static public function displayState($sid, $key = 'name') {
        $states = self::getStates();
        return empty($states[$sid]) ? $sid : $states[$sid][$key];
    }
    static public function getCountries($notSelected = false) {
        static $options = array();
        if(empty($options[ $notSelected ])) {
			$options[ $notSelected ] = array();
            if(empty(self::$countries))
                self::$countries = self::getCachedCountries();
            if($notSelected) {
				$options[ $notSelected ][0] = is_bool($notSelected) ? __('Not selected') : __($notSelected);
			}
            foreach(self::$countries as $c) $options[ $notSelected ][$c['id']] = $c['name'];
        }
        return $options[ $notSelected ];
    }
    static public function getStates($notSelected = false) {
        static $options = array();
        if(empty($options[ $notSelected ])) {
			$options[ $notSelected ] = array();
            if(empty(self::$states))
                self::$states = self::getCachedStates();
            if($notSelected) {
				$notSelectedLabel = is_bool($notSelected) ? 'Not selected' : $notSelected;
				$options[ $notSelected ][0] = array('name' => __( $notSelectedLabel ), 'country_id' => NULL);
			}
            foreach(self::$states as $s) $options[ $notSelected ][$s['id']] = $s;
        }
        return $options[ $notSelected ];
    }
	public function displayValue() {
		$value = '';
		switch($this->html) {
			case 'countryList':
				$value = self::displayCountry($this->value);
				break;
			case 'statesList':
				$value = self::displayState($this->value);
				break;
			case 'checkboxlist':
				$options = $this->getHtmlParam('optionsFhf');
				$value = array();
				if(!empty($options) && is_array($options)) {
					foreach($options as $opt) {
						if(isset($opt['checked']) && $opt['checked']) {
							$value[] = $opt['text'];
						}
					}
				}
				if(empty($value))
					$value = __('N/A');
				else
					$value = implode('<br />', $value);
				break;
			case 'selectbox': case 'radiobuttons':
				$options = $this->getHtmlParam('optionsFhf');
				if(!empty($options) && !empty($options[ $this->value ])) {
					$value = $options[ $this->value ];
				} else {
					$value = __('N/A');
				}
				break;
			default:
				if ($this->value == '') {
					$value = __('N/A');
				} else {
					if(is_array($this->value)) {
						$options = $this->getHtmlParam('optionsFhf');
						if(!empty($options) && is_array($options)) {
							$valArr = array();
							foreach($this->value as $v) {
								$valArr[] = $options[$v];
							}
							$value = recImplode('<br />', $valArr);
						} else {
							$value = recImplode('<br />', $this->value);
						}
					} else
						$value = $this->value;
				}
				break;
		}
		if($echo)
			echo $value;
		else
			return $value;
	}
	public function showValue() {
		echo $this->displayValue();
	}
	public function display($tag = 1, $id = 0) {
		echo $this->drawHtml($tag, $id);
	}
	public function addHtmlParam($name, $value) {
		$this->htmlParams[$name] = $value;
	}
	/**
	 * Alias for addHtmlParam();
	 */
	public function setHtmlParam($name, $value) {
		$this->addHtmlParam($name, $value);
	}
	public function setHtmlParams($params) {
		$this->htmlParams = $params;
	}
	public function getHtmlParam($name) {
		return isset($this->htmlParams[$name]) ? $this->htmlParams[$name] : false;
	}
	/**
	 * Function to display userfields in front-end
	 * 
	 * @param string $name
	 * @param mixed $fieldValue
	 * @return string 
	 */
	public function viewField($name, $fieldValue = '') {
		$method = $this->html;
		$options = frameFhf::_()->getModule('optionsFhf')->getHelper()->getOptions($this->id);
		$attrs = '';
		if (is_object($this->htmlParams['attr']) && count($this->htmlParams['attr']) > 0) {
			foreach ($this->htmlParams['attr'] as $attribute=>$value) {
				if ($value != '') {
					$attrs .= $attribute.'="'.$value.'" ';
				}
			}
		}
		if ($fieldValue == $this->default_value) {
			$checked = 1;
		} else {
			$checked = 0;
		}
		if ($fieldValue == '') {
			$fieldValue = $this->default_value;
		}
		$params = array('optionsFhf'=>$options, 'attrs' => $attrs, 'value' => $fieldValue, 'checked' => $checked);
		$output = '';
		if(method_exists('htmlFhf', $method)) {
			$output .= htmlFhf::$method($name, $params);
			$output .= htmlFhf::hidden('extra_field['.$this->name.']',array('value'=>$this->id));
		}
		return $output;
	}

	/**
	 * Function to process field params
	 */
	public function processParams($tag, $id){
		return '';
		if ($this->name == "params") {
			if(is_array($this->value) || is_object($this->value)) {
				$params = $this->value;
			} else {
				$params = json_decode($this->value);
			}
			$add_option = '';
			switch ($tag) {
				case 5: 
					$add_option = __('Add Checkbox');
					$options_tag = '';
					$image_tag = ' style="display:none"';
				break;
				case 9: 
					$add_option = __('Add Item');
					$options_tag = '';
					$image_tag = ' style="display:none"';
				break;
				case 12:
					$add_option = __('Add Item');
					$options_tag = '';
					$image_tag = ' style="display:none"';
				break;
				case 10:
					$options_tag = '';
					$add_option = __('Add Radio Button');
					$image_tag = ' style="display:none"';
				break;
				case 8:
					$image_tag = '';
					$options_tag = ' style="display:none"';
				break;
				default:
					$options_tag = ' style="display:none"';
					$image_tag = ' style="display:none"';
					break;
			}
			/*if ($tag > 0 || $id == 0) {
				$output .= '<div class="options options_tag"'.$options_tag.'>';
					$output .= '<span class="add_option">'.$add_option.'</span>';
					$output .= fieldAdapterFhf::_($id,'getExtraFieldOptions',fieldAdapterFhf::STR);
				$output .= '</div>';

				$output .= '<div class="options image_tag"'.$image_tag.'>'.__('Dimensions').':<br />';
					$params->width?$width = $params->width:'';
					$params->height?$height = $params->height:'';
					$output .= __('width').':<br />';
					$output .= htmlFhf::text('params[width]',array('value'=>$width)).'<br />';
					$output .= __('height').':<br />';
					$output .= htmlFhf::text('params[height]',array('value'=>$height)).'<br />';
				$output .= '</div>';
			}
			if($this->adapt['htmlParams']) {
				$output .= fieldAdapterFhf::_($this, $this->adapt['htmlParams'], fieldAdapterFhf::STR);
			} else {
				$output .= '<a href="javascript:void(0);" class="set_properties">'.__('Click to set field "id" and "class"').'</a>';
				$output .= '<div class="attributes" style="display:none;">'.__('Attributes').':<br />';
				$output .= fieldAdapterFhf::_($params,'getFieldAttributes',  fieldAdapterFhf::STR);
				$output .= '</div>';
			}*/
			return $output;
		}
	}

	/**
	 * Check if the element exists in array
	 * @param array $param 
	 */
	function checkVarFromParam($param, $element) {
		return utilsFhf::xmlAttrToStr($param, $element);
		/*if (isset($param[$element])) {
			// convert object element to string
			return (string)$param[$element];
		} else {
			return '';
		}*/
	}

	/**
	 * Prepares configuration options
	 * 
	 * @param file $xml
	 * @return array $config_params 
	 */
	public function prepareConfigOptions($xml) {
	  // load xml structure of parameters
	   $config = simplexml_load_file($xml);           
	   $config_params = array();
	   foreach ($config->params->param as $param) {
		 // read the variables
		  $name = $this->checkVarFromParam($param,'name');
		  $type = $this->checkVarFromParam($param,'type');
		  $label = $this->checkVarFromParam($param,'label');
		  $helper = $this->checkVarFromParam($param,'helper');
		  $module = $this->checkVarFromParam($param,'module');
		  $values = $this->checkVarFromParam($param,'values');
		  $default = $this->checkVarFromParam($param,'default');
		  $description = $this->checkVarFromParam($param,'description');
		  if ($name == '') continue;
		// fill in the variables to configuration array
		  $config_params[$name] = array('type'=>$type,
										'label'=>$label,
										'helperFhf'=>$helper,
										'moduleFhf'=>$module,
										'values'=>$values,
										'default'=>$default,
										'description'=>$description,
										);
	   }
	   return $config_params;
	}
	public function setDescription($desc) {
		$this->description = $desc;
	}
	public function getDescription() {
		return $this->description;
	}
	 /**
	 * Displays the config options for given module
	 * 
	 * @param string $module 
	 * @param array $addDefaultOptions - if you want to add some additionsl options - specify it here
	 */
	public function drawConfig($module, $additionalOptions = array()) {
		if(!frameFhf::_()->getModule($module)) 
			return false; 
		// check for xml file with params structure  
	   if(frameFhf::_()->getModule($module)->isExternal())
		   $config_xml = frameFhf::_()->getModule($module)->getModDir(). 'mod.xml';
	   else
		   $config_xml = FHF_MODULES_DIR.$module.DS.'mod.xml';

	   if (!file_exists($config_xml)) {
		   // if there is no configuration file for this $module
		   return __('There are no configuration options for this module');
	   }
	   $output = '';
	   // reading params structure
	   $configOptions = $this->prepareConfigOptions($config_xml);
	   // reading params from database
	   //bugodel2nia..............
	   if(is_string($this->value))
			$params = Utils::jsonDecode($this->value);
	   elseif(is_object($this->value) || is_array($this->value))
			$params = toeObjectToArray($this->value);
	   //if (!empty($params)) {
	   if (!empty($configOptions)){
		   $i = 0;
		   if (empty($params)) {
			   $params = array('0'=>array());
		   }
		   if(is_array($additionalOptions) && !empty($additionalOptions)) {
			   $configOptions = array_merge($configOptions, $additionalOptions);
		   }
		   foreach ($params as $param) {
			   $output .= '<div class="module_options">';
			   foreach ($configOptions as $key=>$value){
				  $fieldValue = '';
				  $output .= '<div class="module_option">';
				  $method = $configOptions[$key]['type'];
				  $name = 'params['.$i.']['.$key.']';
				  $options = array();
				  // if the values attribute is set
				  if ($configOptions[$key]['values'] != ''){
					  $extract_options = explode(',', $configOptions[$key]['values']);
					  if (count($extract_options) > 1) {
						  foreach ($extract_options as $item=>$string) {
							  if(strpos($string, '=>')) {
								  $keyVal = array_map('trim', explode('=>', $string));
								  $options[ $keyVal[0] ] = $keyVal[1];
							  } else {
									$options[$string] = $string;    
							  }
						  }
					  } else {
						  $fieldValue = $configOptions[$key]['default'];
					  }
				  // if helper is needed to render the object
				  } elseif ($configOptions[$key]['helper'] != '') {
					  $helper_name = $configOptions[$key]['helper'];
					  // is helper from current module or other?
					  if ($configOptions[$key]['module'] != '') {
						  $hmodule = $configOptions[$key]['module'];
					  } else {
						  $hmodule = $module;
					  }
					  // calling the helper class
					  $helper = frameFhf::_()->getModule($hmodule)->getHelper();
					  if ($helper) {
						  // calling the helper method for current option
						  if (method_exists($helper, $helper_name))
							$options = $helper->$helper_name();
					  }
				  } 
					if (isset($param[$key])) {
						$fieldValue = $param[$key];
					} else {
						if ($fieldValue == '')
							$fieldValue = $configOptions[$key]['default']; 
					}
				  // filling the parameters to build html element
					 $htmlParams = array('value'=>$fieldValue,'optionsFhf'=>$options);
					 if($method == 'checkbox') {
						 $htmlParams['value'] = 1;
						 $htmlParams['checked'] = (bool)$fieldValue;
					 }
					 if(!empty($configOptions[$key]['htmlParams']) && is_array($configOptions[$key]['htmlParams'])) {
						 $htmlParams = array_merge($htmlParams, $configOptions[$key]['htmlParams']);
					 }
				  // output label and html element
					 $output .= '<label>'.__($configOptions[$key]['label']);
					 if ($configOptions[$key]['description'] != '') {
						 $output .= '<a class="toeOptTip" tip="'.__($configOptions[$key]['description']).'"></a>';
					 }
					 $output .= '</label><br />';
					 $output .= htmlFhf::$method($name,$htmlParams).'<br />';
					 $output .= '</div>';
			   }
			   $i++;
			 $output .= '</div>';
		   }
	   }
	   return $output;
	}

	public function displayConfig($module) {
	   echo $this->drawConfig($module);
	}
	/**
	 * This method will prepare internal value to it's type
	 * @see $this->type
	 * @return mixed - prepared value on the basis of $this->type
	 */
	public function valToType() {
		switch($this->type) {
			case 'int':
			case 'mediumint':
			case 'smallint':
				$this->value = (int) $this->value;
				break;
			case 'float':
				$this->value = (float) $this->value;
				break;
			case 'double':
			case 'decimal':
				$this->value = (double) $this->value;
				break;
		}
		return $this->type;
	}
	static public function getFontsList() {
		return array("Abel", "Abril Fatface", "Aclonica", "Acme", "Actor", "Adamina", "Advent Pro",
			"Aguafina Script", "Aladin", "Aldrich", "Alegreya", "Alegreya SC", "Alex Brush", "Alfa Slab One", "Alice",
			"Alike", "Alike Angular", "Allan", "Allerta", "Allerta Stencil", "Allura", "Almendra", "Almendra SC", "Amaranth",
			"Amatic SC", "Amethysta", "Andada", "Andika", "Angkor", "Annie Use Your Telescope", "Anonymous Pro", "Antic",
			"Antic Didone", "Antic Slab", "Anton", "Arapey", "Arbutus", "Architects Daughter", "Arimo", "Arizonia", "Armata",
			"Artifika", "Arvo", "Asap", "Asset", "Astloch", "Asul", "Atomic Age", "Aubrey", "Audiowide", "Average",
			"Averia Gruesa Libre", "Averia Libre", "Averia Sans Libre", "Averia Serif Libre", "Bad Script", "Balthazar",
			"Bangers", "Basic", "Battambang", "Baumans", "Bayon", "Belgrano", "Belleza", "Bentham", "Berkshire Swash",
			"Bevan", "Bigshot One", "Bilbo", "Bilbo Swash Caps", "Bitter", "Black Ops One", "Bokor", "Bonbon", "Boogaloo",
			"Bowlby One", "Bowlby One SC", "Brawler", "Bree Serif", "Bubblegum Sans", "Buda", "Buenard", "Butcherman",
			"Butterfly Kids", "Cabin", "Cabin Condensed", "Cabin Sketch", "Caesar Dressing", "Cagliostro", "Calligraffitti",
			"Cambo", "Candal", "Cantarell", "Cantata One", "Cardo", "Carme", "Carter One", "Caudex", "Cedarville Cursive",
			"Ceviche One", "Changa One", "Chango", "Chau Philomene One", "Chelsea Market", "Chenla", "Cherry Cream Soda",
			"Chewy", "Chicle", "Chivo", "Coda", "Coda Caption", "Codystar", "Comfortaa", "Coming Soon", "Concert One",
			"Condiment", "Content", "Contrail One", "Convergence", "Cookie", "Copse", "Corben", "Cousine", "Coustard",
			"Covered By Your Grace", "Crafty Girls", "Creepster", "Crete Round", "Crimson Text", "Crushed", "Cuprum", "Cutive",
			"Damion", "Dancing Script", "Dangrek", "Dawning of a New Day", "Days One", "Delius", "Delius Swash Caps", 
			"Delius Unicase", "Della Respira", "Devonshire", "Didact Gothic", "Diplomata", "Diplomata SC", "Doppio One", 
			"Dorsa", "Dosis", "Dr Sugiyama", "Droid Sans", "Droid Sans Mono", "Droid Serif", "Duru Sans", "Dynalight",
			"EB Garamond", "Eater", "Economica", "Electrolize", "Emblema One", "Emilys Candy", "Engagement", "Enriqueta",
			"Erica One", "Esteban", "Euphoria Script", "Ewert", "Exo", "Expletus Sans", "Fanwood Text", "Fascinate", "Fascinate Inline",
			"Federant", "Federo", "Felipa", "Fjord One", "Flamenco", "Flavors", "Fondamento", "Fontdiner Swanky", "Forum",
			"Francois One", "Fredericka the Great", "Fredoka One", "Freehand", "Fresca", "Frijole", "Fugaz One", "GFS Didot",
			"GFS Neohellenic", "Galdeano", "Gentium Basic", "Gentium Book Basic", "Geo", "Geostar", "Geostar Fill", "Germania One",
			"Give You Glory", "Glass Antiqua", "Glegoo", "Gloria Hallelujah", "Goblin One", "Gochi Hand", "Gorditas",
			"Goudy Bookletter 1911", "Graduate", "Gravitas One", "Great Vibes", "Gruppo", "Gudea", "Habibi", "Hammersmith One",
			"Handlee", "Hanuman", "Happy Monkey", "Henny Penny", "Herr Von Muellerhoff", "Holtwood One SC", "Homemade Apple",
			"Homenaje", "IM Fell DW Pica", "IM Fell DW Pica SC", "IM Fell Double Pica", "IM Fell Double Pica SC",
			"IM Fell English", "IM Fell English SC", "IM Fell French Canon", "IM Fell French Canon SC", "IM Fell Great Primer",
			"IM Fell Great Primer SC", "Iceberg", "Iceland", "Imprima", "Inconsolata", "Inder", "Indie Flower", "Inika",
			"Irish Grover", "Istok Web", "Italiana", "Italianno", "Jim Nightshade", "Jockey One", "Jolly Lodger", "Josefin Sans",
			"Josefin Slab", "Judson", "Julee", "Junge", "Jura", "Just Another Hand", "Just Me Again Down Here", "Kameron",
			"Karla", "Kaushan Script", "Kelly Slab", "Kenia", "Khmer", "Knewave", "Kotta One", "Koulen", "Kranky", "Kreon",
			"Kristi", "Krona One", "La Belle Aurore", "Lancelot", "Lato", "League Script", "Leckerli One", "Ledger", "Lekton",
			"Lemon", "Lilita One", "Limelight", "Linden Hill", "Lobster", "Lobster Two", "Londrina Outline", "Londrina Shadow",
			"Londrina Sketch", "Londrina Solid", "Lora", "Love Ya Like A Sister", "Loved by the King", "Lovers Quarrel",
			"Luckiest Guy", "Lusitana", "Lustria", "Macondo", "Macondo Swash Caps", "Magra", "Maiden Orange", "Mako", "Marck Script",
			"Marko One", "Marmelad", "Marvel", "Mate", "Mate SC", "Maven Pro", "Meddon", "MedievalSharp", "Medula One", "Merriweather",
			"Metal", "Metamorphous", "Michroma", "Miltonian", "Miltonian Tattoo", "Miniver", "Miss Fajardose", "Modern Antiqua",
			"Molengo", "Monofett", "Monoton", "Monsieur La Doulaise", "Montaga", "Montez", "Montserrat", "Moul", "Moulpali",
			"Mountains of Christmas", "Mr Bedfort", "Mr Dafoe", "Mr De Haviland", "Mrs Saint Delafield", "Mrs Sheppards",
			"Muli", "Mystery Quest", "Neucha", "Neuton", "News Cycle", "Niconne", "Nixie One", "Nobile", "Nokora", "Norican",
			"Nosifer", "Nothing You Could Do", "Noticia Text", "Nova Cut", "Nova Flat", "Nova Mono", "Nova Oval", "Nova Round",
			"Nova Script", "Nova Slim", "Nova Square", "Numans", "Nunito", "Odor Mean Chey", "Old Standard TT", "Oldenburg",
			"Oleo Script", "Open Sans", "Open Sans Condensed", "Orbitron", "Original Surfer", "Oswald", "Over the Rainbow",
			"Overlock", "Overlock SC", "Ovo", "Oxygen", "PT Mono", "PT Sans", "PT Sans Caption", "PT Sans Narrow", "PT Serif",
			"PT Serif Caption", "Pacifico", "Parisienne", "Passero One", "Passion One", "Patrick Hand", "Patua One", "Paytone One",
			"Permanent Marker", "Petrona", "Philosopher", "Piedra", "Pinyon Script", "Plaster", "Play", "Playball", "Playfair Display",
			"Podkova", "Poiret One", "Poller One", "Poly", "Pompiere", "Pontano Sans", "Port Lligat Sans", "Port Lligat Slab",
			"Prata", "Preahvihear", "Press Start 2P", "Princess Sofia", "Prociono", "Prosto One", "Puritan", "Quantico",
			"Quattrocento", "Quattrocento Sans", "Questrial", "Quicksand", "Qwigley", "Radley", "Raleway", "Rammetto One",
			"Rancho", "Rationale", "Redressed", "Reenie Beanie", "Revalia", "Ribeye", "Ribeye Marrow", "Righteous", "Rochester",
			"Rock Salt", "Rokkitt", "Ropa Sans", "Rosario", "Rosarivo", "Rouge Script", "Ruda", "Ruge Boogie", "Ruluko",
			"Ruslan Display", "Russo One", "Ruthie", "Sail", "Salsa", "Sancreek", "Sansita One", "Sarina", "Satisfy", "Schoolbell",
			"Seaweed Script", "Sevillana", "Shadows Into Light", "Shadows Into Light Two", "Shanti", "Share", "Shojumaru",
			"Short Stack", "Siemreap", "Sigmar One", "Signika", "Signika Negative", "Simonetta", "Sirin Stencil", "Six Caps",
			"Slackey", "Smokum", "Smythe", "Sniglet", "Snippet", "Sofia", "Sonsie One", "Sorts Mill Goudy", "Special Elite",
			"Spicy Rice", "Spinnaker", "Spirax", "Squada One", "Stardos Stencil", "Stint Ultra Condensed", "Stint Ultra Expanded",
			"Stoke", "Sue Ellen Francisco", "Sunshiney", "Supermercado One", "Suwannaphum", "Swanky and Moo Moo", "Syncopate",
			"Tangerine", "Taprom", "Telex", "Tenor Sans", "The Girl Next Door", "Tienne", "Tinos", "Titan One", "Trade Winds",
			"Trocchi", "Trochut", "Trykker", "Tulpen One", "Ubuntu", "Ubuntu Condensed", "Ubuntu Mono", "Ultra", "Uncial Antiqua",
			"UnifrakturCook", "UnifrakturMaguntia", "Unkempt", "Unlock", "Unna", "VT323", "Varela", "Varela Round", "Vast Shadow",
			"Vibur", "Vidaloka", "Viga", "Voces", "Volkhov", "Vollkorn", "Voltaire", "Waiting for the Sunrise", "Wallpoet",
			"Walter Turncoat", "Wellfleet", "Wire One", "Yanone Kaffeesatz", "Yellowtail", "Yeseva One", "Yesteryear", "Zeyada"
		);
	}
}
