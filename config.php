<?php
    global $wpdb;
    if (WPLANG == '') {
        define('FHF_WPLANG', 'en_GB');
    } else {
        define('FHF_WPLANG', WPLANG);
    }
    if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

    define('FHF_PLUG_NAME', basename(dirname(__FILE__)));
    define('FHF_DIR', WP_PLUGIN_DIR. DS. FHF_PLUG_NAME. DS);
    define('FHF_TPL_DIR', FHF_DIR. 'tpl'. DS);
    define('FHF_CLASSES_DIR', FHF_DIR. 'classes'. DS);
    define('FHF_TABLES_DIR', FHF_CLASSES_DIR. 'tables'. DS);
	define('FHF_HELPERS_DIR', FHF_CLASSES_DIR. 'helpers'. DS);
    define('FHF_LANG_DIR', FHF_DIR. 'lang'. DS);
    define('FHF_IMG_DIR', FHF_DIR. 'img'. DS);
    define('FHF_TEMPLATES_DIR', FHF_DIR. 'templates'. DS);
    define('FHF_MODULES_DIR', FHF_DIR. 'modules'. DS);
    define('FHF_FILES_DIR', FHF_DIR. 'files'. DS);
    define('FHF_ADMIN_DIR', ABSPATH. 'wp-admin'. DS);

    define('FHF_SITE_URL', get_bloginfo('wpurl'). '/');
    define('FHF_JS_PATH', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/js/');
    define('FHF_CSS_PATH', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/css/');
    define('FHF_IMG_PATH', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/img/');
    define('FHF_MODULES_PATH', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/modules/');
    define('FHF_TEMPLATES_PATH', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/templates/');
    define('FHF_IMG_POSTS_PATH', FHF_IMG_PATH. 'posts/');
    define('FHF_JS_DIR', FHF_DIR. 'js/');

    define('FHF_URL', FHF_SITE_URL);

    define('FHF_LOADER_IMG', FHF_IMG_PATH. 'loading-cube.gif');
	define('FHF_TIME_FORMAT', 'H:i:s');
    define('FHF_DATE_DL', '/');
    define('FHF_DATE_FORMAT', 'm/d/Y');
    define('FHF_DATE_FORMAT_HIS', 'm/d/Y ('. FHF_TIME_FORMAT. ')');
    define('FHF_DATE_FORMAT_JS', 'mm/dd/yy');
    define('FHF_DATE_FORMAT_CONVERT', '%m/%d/%Y');
    define('FHF_WPDB_PREF', $wpdb->prefix);
    define('FHF_DB_PREF', 'fhf_');    /*TheOneEcommerce*/
    define('FHF_MAIN_FILE', 'fhf.php');

    define('FHF_DEFAULT', 'default');
    define('FHF_CURRENT', 'current');
    
    
    define('FHF_PLUGIN_INSTALLED', true);
    define('FHF_VERSION', '0.2.1');
    define('FHF_USER', 'user');
    
    
    define('FHF_CLASS_PREFIX', 'fhfc');        
    define('FHF_FREE_VERSION', false);
    
    define('FHF_API_UPDATE_URL', 'http://somereadyapiupdatedomain.com');
    
    define('FHF_SUCCESS', 'Success');
    define('FHF_FAILED', 'Failed');
	define('FHF_ERRORS', 'fhfErrors');
	
	define('FHF_THEME_MODULES', 'theme_modules');
	
	
	define('FHF_ADMIN',	'admin');
	define('FHF_LOGGED','logged');
	define('FHF_GUEST',	'guest');
	
	define('FHF_ALL',		'all');
	
	define('FHF_METHODS',		'methods');
	define('FHF_USERLEVELS',	'userlevels');
	/**
	 * Framework instance code, unused for now
	 */
	define('FHF_CODE', 'fhf');
	/**
	 * Plugin name
	 */
	define('FHF_WP_PLUGIN_NAME', 'Ready! 404 Page Editor');
	/**
	 * Build-in Subscribers lists IDs
	 */
	define('FHF_WP_LIST_ID', 1);
	/**
	 * Newsletters send types
	 */
	define('FHF_TYPE_NOW', 'now');
	define('FHF_TYPE_NEW_CONTENT', 'new_content');
	define('FHF_TYPE_SCHEDULE', 'schedule');
	define('FHF_ANY', 'any');
	/**
	 * Newsletters send time types
	 */
	define('FHF_TIME_IMMEDIATELY', 'immediately');
	define('FHF_TIME_APPOINTED', 'appointed');
	/**
	 * Newsletters schedules new types
	 */
	define('FHF_S_MIN', 'one_min');
	/**
	 * Newsletters cron main filter name
	 */
	define('FHF_SCHEDULE_FILTER', FHF_CODE. '_schedule_send');
	/**
	 * Test mode - create logs in files, etc.
	 */
	define('FHF_TEST_MODE', true);
	/**
	 * Max super template predefined tempalte ID
	 */
	define('FHF_STPL_DEFINED_IDS_MAX', 100);
