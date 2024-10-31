<?php
/**
 * Plugin Name: Ready! 404 Page Generator
 * Plugin URI: http://readyshoppingcart.com/product/404-page-generator-wordpress-plugin/
 * Description: Create 404 not found page in a minute. Professional templates and examples.
 * Version: 0.2.1
 * Author: readyshoppingcart.com
 * Author URI: http://readyshoppingcart.com
 **/
    require_once(dirname(__FILE__). DIRECTORY_SEPARATOR. 'config.php');
    require_once(dirname(__FILE__). DIRECTORY_SEPARATOR. 'functions.php');
    importClassFhf('dbFhf');
    importClassFhf('installerFhf');
    importClassFhf('baseObjectFhf');
    importClassFhf('moduleFhf');
    importClassFhf('modelFhf');
    importClassFhf('viewFhf');
    importClassFhf('controllerFhf');
    importClassFhf('dispatcherFhf');
    importClassFhf('fieldFhf');
    importClassFhf('tableFhf');
    importClassFhf('frameFhf');
    importClassFhf('reqFhf');
    importClassFhf('uriFhf');
    importClassFhf('htmlFhf');
    importClassFhf('responseFhf');
    importClassFhf('validatorFhf');
    importClassFhf('errorsFhf');
    importClassFhf('utilsFhf');
    importClassFhf('modInstallerFhf');
    importClassFhf('wpUpdater');
	importClassFhf('toeWordpressWidgetFhf');
	importClassFhf('installerDbUpdaterFhf');
	importClassFhf('dateFhf');

    installerFhf::update();
    errorsFhf::init();
    
    dispatcherFhf::doAction('onBeforeRoute');
    frameFhf::_()->parseRoute();
    dispatcherFhf::doAction('onAfterRoute');

    dispatcherFhf::doAction('onBeforeInit');
    frameFhf::_()->init();
    dispatcherFhf::doAction('onAfterInit');

    dispatcherFhf::doAction('onBeforeExec');
    frameFhf::_()->exec();
    dispatcherFhf::doAction('onAfterExec');
