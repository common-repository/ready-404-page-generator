<?php
class frameFhf {
    private $_modules = array();
    private $_tables = array();
    private $_allModules = array();
    /**
     * bool Uses to know if we are on one of the plugin pages
     */
    private $_inPlugin = false;
    /**
     * Array to hold all scripts and add them in one time in addScripts method
     */
    private $_scripts = array();
    private $_scriptsInitialized = false;
    private $_styles = array();
	private $_stylesInitialized = false;
    
    private $_scriptsVars = array();
    private $_mod = '';
    private $_action = '';
    /**
     * Object with result of executing non-ajax module request
     */
    private $_res = null;
    
    public function __construct() {
        $this->_res = toeCreateObjFhf('response', array());
        
    }
    static public function getInstance() {
        static $instance;
        if(!$instance) {
            $instance = new frameFhf();
        }
        return $instance;
    }
    static public function _() {
        return self::getInstance();
    }
    public function parseRoute() {
        // Check plugin
        $pl = reqFhf::getVar('pl');
        if($pl == FHF_CODE) {
            $mod = reqFhf::getMode();
            if($mod)
                $this->_mod = $mod;
            $action = reqFhf::getVar('action');
            if($action)
                $this->_action = $action;
        }
    }
    public function setMod($mod) {
        $this->_mod = $mod;
    }
    public function getMod() {
        return $this->_mod;
    }
    public function setAction($action) {
        $this->_action = $action;
    }
    public function getAction() {
        return $this->_action;
    }
    protected function _extractModules() {
        $activeModules = $this->getTable('modules')
                ->innerJoin( $this->getTable('modules_type'), 'type_id' )
                ->get($this->getTable('modules')->alias(). '.*, '. $this->getTable('modules_type')->alias(). '.label as type_name');
        if($activeModules) {
            foreach($activeModules as $m) {
                $code = $m['code'];
                $moduleLocationDir = FHF_MODULES_DIR;
                if(!empty($m['ex_plug_dir'])) {
                    $moduleLocationDir = utilsFhf::getExtModDir( $m['ex_plug_dir'] );
                }

                if(is_dir($moduleLocationDir. $code)) {
                    $this->_allModules[$m['code']] = 1;
                    if((bool)$m['active']) {
                        importClassFhf($code. strFirstUp(FHF_CODE), $moduleLocationDir. $code. DS. 'mod.php');
                        $moduleClass = toeGetClassNameFhf($code);
                        if(class_exists($moduleClass)) {
                            $this->_modules[$code] = new $moduleClass($m);
                            $this->_modules[$code]->setParams((array)json_decode($m['params']));
                            if(is_dir($moduleLocationDir. $code. DS. 'tables')) {
                                $this->_extractTables($moduleLocationDir. $code. DS. 'tables'. DS);
                            }
                        }
                    }
                }
            }
        }
        //$operationTime = microtime(true) - $startTime;
    }
    protected function _initModules() {
        if(!empty($this->_modules)) {
            foreach($this->_modules as $mod) {
                 $mod->init();
            }
        }
    }
    public function init() {
        //$startTime = microtime(true);
		// TODO: Init lang here
        //langFhf::init();
        reqFhf::init();
        $this->_extractTables();
        $this->_extractModules();

        $this->_initModules();

		modInstallerFhf::checkActivationMessages();
		
        $this->_execModules();
        
        add_action('init', array($this, 'addScripts'));
        add_action('init', array($this, 'addStyles'));

        register_activation_hook(  FHF_DIR. DS. FHF_MAIN_FILE, array('utilsFhf', 'activatePlugin')  ); //See classes/install.php file
        register_deactivation_hook(FHF_DIR. DS. FHF_MAIN_FILE, array('utilsFhf', 'deactivatePlugin'));
        
        add_action('admin_notices', array('errorsFhf', 'displayOnAdmin'));

        //$operationTime = microtime(true) - $startTime;
    }
    /**
     * Check permissions for action in controller by $code and made corresponding action
     * @param string $code Code of controller that need to be checked
     * @param string $action Action that need to be checked
     * @return bool true if ok, else - should exit from application
     */
    public function checkPermissions($code, $action) {
        if($this->havePermissions($code, $action))
            return true;
        else {
            exit(_e('You have no permissions to view this page'));
        }
    }
    /**
     * Check permissions for action in controller by $code
     * @param string $code Code of controller that need to be checked
     * @param string $action Action that need to be checked
     * @return bool true if ok, else - false
     */
    public function havePermissions($code, $action) {
        $res = true;
        $mod = $this->getModule($code);
        $action = strtolower($action);
        if($mod) {
            $permissions = $mod->getController()->getPermissions();
            if(!empty($permissions)) {  // Special permissions
                if(isset($permissions[S_METHODS]) 
                    && !empty($permissions[S_METHODS])
                    
                ) {
                    foreach($permissions[S_METHODS] as $method => $permissions) {   // Make case-insensitive
                        $permissions[S_METHODS][strtolower($method)] = $permissions;
                    }
                    if(array_key_exists($action, $permissions[S_METHODS])) {        // Permission for this method exists
                        $currentUserPosition = frameFhf::_()->getModule('user')->getCurrentUserPosition();
                        if((is_array($permissions[ FHF_METHODS ][ $action ]) && !in_array($currentUserPosition, $permissions[ FHF_METHODS ][ $action ]))
                            || (!is_array($permissions[ FHF_METHODS ][ $action ]) && $permissions[S_METHODS][$action] != $currentUserPosition)
                        ) {
                            $res = false;
                        }
                    }
                }
                if(isset($permissions[S_USERLEVELS])
                    && !empty($permissions[S_USERLEVELS])
                ) {
                    $currentUserPosition = frameFhf::_()->getModule('user')->getCurrentUserPosition();
                    foreach($permissions[S_USERLEVELS] as $userlevel => $methods) {
                        if(is_array($methods)) {
                            $lowerMethods = array_map('strtolower', $methods);          // Make case-insensitive
                            if(in_array($action, $lowerMethods)) {                      // Permission for this method exists
                                if($currentUserPosition != $userlevel) 
                                    $res = false;
                                break;
                            }
                        } else {
                            $lowerMethod = strtolower($methods);            // Make case-insensitive
                            if($lowerMethod == $action) {                   // Permission for this method exists
                                if($currentUserPosition != $userlevel) 
                                    $res = false;
                                break;
                            }
                        }
                    }
                }

            }
        }
        return $res;
    }
    public function getRes() {
        return $this->_res;
    }
    protected function _execModules() {
        if($this->_mod) {
            // If module exist and is active
            $mod = $this->getModule($this->_mod);
            if($mod && $this->_action) {
                if($this->checkPermissions($this->_mod, $this->_action)) {
                    switch(reqFhf::getVar('reqType')) {
                        case 'ajax':
							// Make ajax requests - facter, don't need to wait until prev request will finish to run next one as session will be released already
							session_write_close();
                            add_action('wp_ajax_'. $this->_action, array($mod->getController(), $this->_action));
                            add_action('wp_ajax_nopriv_'. $this->_action, array($mod->getController(), $this->_action));
                            break;
                        default:
                            $this->_res = $mod->exec($this->_action);
                            break;
                    }
                }
            }
        }
    }
    protected function _extractTables($tablesDir = FHF_TABLES_DIR) {
        $mDirHandle = opendir($tablesDir);
        while(($file = readdir($mDirHandle)) !== false) {
            if(is_file($tablesDir. $file) && $file != '.' && $file != '..' && strpos($file, '.php')) {
                $this->_extractTable( str_replace('.php', '', $file), $tablesDir );
            }
        }
    }
    protected function _extractTable($tableName, $tablesDir = FHF_TABLES_DIR) {
        importClassFhf('noClassNameHere', $tablesDir. $tableName. '.php');
        $this->_tables[$tableName] = tableFhf::_($tableName);
        //var_dump($tableName, $this->_tables[$tableName]);
    }
    /**
     * public alias for _extractTables method
     * @see _extractTables
     */
    public function extractTables($tablesDir) {
        if(!empty($tablesDir))
            $this->_extractTables($tablesDir);
    }
    public function exec() {
        /**
         * @deprecated
         */
        /*if(!empty($this->_modules)) {
            foreach($this->_modules as $mod) {
                $mod->exec();
            }
        }*/
    }
    public function getTables () {
        return $this->_tables;
    }
    /**
     * Return table by name
     * @param string $tableName table name in database
     * @return object table
     * @example frameFhf::_()->getTable('products')->getAll()
     */
    public function getTable($tableName) {
        if(empty($this->_tables[$tableName])) {
            $this->_extractTable($tableName);
        }
        return $this->_tables[$tableName];
    }
    public function getModules($filter = array()) {
        $res = array();
        if(empty($filter))
            $res = $this->_modules;
        else {
            foreach($this->_modules as $code => $mod) {
                if(isset($filter['type'])) {
                    if(is_numeric($filter['type']) && $filter['type'] == $mod->getTypeID())
                        $res[$code] = $mod;
                    elseif($filter['type'] == $mod->getType())
                        $res[$code] = $mod;
                }
            }
        }
        return $res;
    }
    
    public function getModule($code) {
        return (isset($this->_modules[$code]) ? $this->_modules[$code] : NULL);
    }
    public function inPlugin() {
        return $this->_inPlugin;
    }
    /**
     * Push data to script array to use it all in addScripts method
     * @see wp_enqueue_script definition
     */
    public function addScript($handle, $src = '', $deps = array(), $ver = false, $in_footer = false, $vars = array()) {
        if($this->_scriptsInitialized) {
            wp_enqueue_script($handle, $src, $deps, $ver, $in_footer);
        } else {
            $this->_scripts[] = array(
                'handle' => $handle, 
                'src' => $src, 
                'deps' => $deps, 
                'ver' => $ver, 
                'in_footer' => $in_footer,
                'vars' => $vars
            );
        }
    }
    /**
     * Add all scripts from _scripts array to wordpress
     */
    public function addScripts() {
        if(!empty($this->_scripts)) {
            foreach($this->_scripts as $s) {
                wp_enqueue_script($s['handle'], $s['src'], $s['deps'], $s['ver'], $s['in_footer']);
                
                if($s['vars'] || (isset($this->_scriptsVars[$s['handle']]) && $this->_scriptsVars[$s['handle']])) {
                    $vars = array();
                    if($s['vars'])
                        $vars = $s['vars'];
                    if(isset($this->_scriptsVars[$s['handle']]) && $this->_scriptsVars[$s['handle']])
                        $vars = array_merge($vars, $this->_scriptsVars[$s['handle']]);
                    if($vars) {
                        foreach($vars as $k => $v) {
                            wp_localize_script($s['handle'], $k, $v);
                        }
                    }
                }
            }
        }
        $this->_scriptsInitialized = true;
    }
    public function addJSVar($script, $name, $val) {
        if($this->_scriptsInitialized) {
            wp_localize_script($script, $name, $val);
        } else {
            $this->_scriptsVars[$script][$name] = $val;
        }
    }
    
    public function addStyle($handle, $src = false, $deps = array(), $ver = false, $media = 'all') {
		if($this->_stylesInitialized) {
			wp_enqueue_style($handle, $src, $deps, $ver, $media);
		} else {
			$this->_styles[] = array(
				'handle' => $handle,
				'src' => $src,
				'deps' => $deps,
				'ver' => $ver,
				'media' => $media 
			);
		}
    }
    public function addStyles() {
        if(!empty($this->_styles)) {
            foreach($this->_styles as $s) {
                wp_enqueue_style($s['handle'], $s['src'], $s['deps'], $s['ver'], $s['media']);
            }
        }
		$this->_stylesInitialized = true;
    }
    //Very interesting thing going here.............
    public function loadPlugins() {
        require_once(ABSPATH. 'wp-includes/pluggable.php'); 
        //require_once(ABSPATH.'wp-load.php');
        //load_plugin_textdomain('some value');
    }
    public function loadWPSettings() {
        require_once(ABSPATH. 'wp-settings.php'); 
    }
	public function loadAdminEditor() {
		if ( ! class_exists( '_WP_Editors' ) )
			require( ABSPATH . WPINC . '/class-wp-editor.php' );
	}
    public function moduleActive($code) {
        return isset($this->_modules[$code]);
    }
    public function moduleExists($code) {
        if($this->moduleActive($code))
            return true;
        return isset($this->_allModules[$code]);
    }
	/**
	 * This is custom method for each plugin and should be modified if you create copy from this instance.
	 */
	public function isAdminPlugPage() {
		$page = reqFhf::getVar('page');
		if($page == $this->getModule('adminmenu')->getMainSlug())
			return true;
		return false;
	}
}
