<?php
class installerFhf {
	static public $update_to_version_method = '';
	static public function init() {
		global $wpdb;
		$wpPrefix = $wpdb->prefix;
		//$start = microtime(true);					// Speed debug info
		//$queriesCountStart = $wpdb->num_queries;	// Speed debug info
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		$current_version = get_option($wpPrefix. FHF_DB_PREF. 'db_version', 0);
		$installed = (int) get_option($wpPrefix. FHF_DB_PREF. 'db_installed', 0);
		$eol = "\n\r";
		if(!$installed) {
			/**
			 * htmltype 
			 */
			if (!dbFhf::exist("@__htmltype")) {
				dbDelta(dbFhf::prepareQuery("CREATE TABLE IF NOT EXISTS `@__htmltype` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `label` varchar(32) NOT NULL,
				  `description` varchar(255) NOT NULL,
				  PRIMARY KEY (`id`),
				  UNIQUE INDEX `label` (`label`)
				) DEFAULT CHARSET=utf8"));
				dbFhf::query("INSERT INTO `@__htmltype` VALUES
					(1, 'text', 'Text'),
					(2, 'password', 'Password'),
					(3, 'hidden', 'Hidden'),
					(4, 'checkbox', 'Checkbox'),
					(5, 'checkboxlist', 'Checkboxes'),
					(6, 'datepicker', 'Date Picker'),
					(7, 'submit', 'Button'),
					(8, 'img', 'Image'),
					(9, 'selectbox', 'Drop Down'),
					(10, 'radiobuttons', 'Radio Buttons'),
					(11, 'countryList', 'Countries List'),
					(12, 'selectlist', 'List'),
					(13, 'countryListMultiple', 'Country List with posibility to select multiple countries'),
					(14, 'block', 'Will show only value as text'),
					(15, 'statesList', 'States List'),
					(16, 'textFieldsDynamicTable', 'Dynamic table - multiple text options set'),
					(17, 'textarea', 'Textarea'),
					(18, 'checkboxHiddenVal', 'Checkbox with Hidden field');");
			}
			/**
			 * modules 
			 */
			if (!dbFhf::exist("@__modules")) {
				dbDelta(dbFhf::prepareQuery("CREATE TABLE IF NOT EXISTS `@__modules` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `code` varchar(64) NOT NULL,
				  `active` tinyint(1) NOT NULL DEFAULT '0',
				  `type_id` smallint(3) NOT NULL DEFAULT '0',
				  `params` text,
				  `has_tab` tinyint(1) NOT NULL DEFAULT '0',
				  `label` varchar(128) DEFAULT NULL,
				  `description` text,
				  `ex_plug_dir` varchar(255) DEFAULT NULL,
				  PRIMARY KEY (`id`),
				  UNIQUE INDEX `code` (`code`)
				) DEFAULT CHARSET=utf8;"));
				dbFhf::query("INSERT INTO `@__modules` (id, code, active, type_id, params, has_tab, label, description) VALUES
					(NULL, 'adminmenu',1,1,'',0,'Admin Menu',''),
					(NULL, 'options',1,1,'',1,'Options',''),
					(NULL, 'user',1,1,'',1,'Users',''),
					(NULL, 'pages',1,1,'". json_encode(array()). "',0,'Pages',''),
					(NULL, 'templates',1,1,'',1,'Templates for Plugin',''),
					(NULL, 'messenger', 1, 1, '', 1, 'Notifications', 'Module provides the ability to create templates for user notifications and for mass mailing.'),
					(NULL, 'log', 1, 1, '', 0, 'Log', 'Internal system module to log some actions on server'),
					(NULL, 'subscribe', 1, 1, '', 0, 'Subscribe', 'Subscribe'),
					(NULL, 'stpl', 1, 1, '', 0, 'Super Template', 'Super Template'),
					(NULL, 'stpl_additions', 1, 1, '', 0, 'Super Template Additions', 'Super Template Additions'),
					(NULL, 'mail', 1, 1, '', 0, 'mail', 'mail'),
					(NULL, 'four_hundred_four', 1, 1, '', 0, 'four_hundred_four', 'four_hundred_four'),
					(NULL, 'promo_ready', 1, 1, '', 0, 'promo_ready', 'promo_ready');");
			}
			/**
			 *  modules_type 
			 */
			if(!dbFhf::exist("@__modules_type")) {
				dbDelta(dbFhf::prepareQuery("CREATE TABLE IF NOT EXISTS `@__modules_type` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `label` varchar(64) NOT NULL,
				  PRIMARY KEY (`id`)
				) AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;"));
				dbFhf::query("INSERT INTO `@__modules_type` VALUES
					(1,'system'),
					(2,'widget'),
					(3,'addons');");
			}
			/**
			 * options 
			 */
			if(!dbFhf::exist("@__options")) {
				dbDelta(dbFhf::prepareQuery("CREATE TABLE IF NOT EXISTS `@__options` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `code` varchar(64) CHARACTER SET latin1 NOT NULL,
				  `value` text NULL,
				  `label` varchar(128) CHARACTER SET latin1 DEFAULT NULL,
				  `description` text CHARACTER SET latin1,
				  `htmltype_id` smallint(2) NOT NULL DEFAULT '1',
				  `params` text NULL,
				  `cat_id` mediumint(3) DEFAULT '0',
				  `sort_order` mediumint(3) DEFAULT '0',
				  `value_type` varchar(16) CHARACTER SET latin1 DEFAULT NULL,
				  PRIMARY KEY (`id`),
				  KEY `id` (`id`),
				  UNIQUE INDEX `code` (`code`)
				) DEFAULT CHARSET=utf8"));
				dbFhf::query("INSERT INTO `@__options` (`id`,`code`,`value`,`label`,`description`,`htmltype_id`,`params`,`cat_id`,`sort_order`) VALUES
					(NULL,'page_title','". __('Page Not Found'). " | ". dbFhf::escape(get_bloginfo('name')). "','Page Title','',1,'',1,0),
					(NULL,'page_meta_keywords','','Page Meta Keywords','',1,'',1,0),
					(NULL,'page_meta_description','','Page Meta Description','',17,'',1,0),
					(NULL,'display_standard_header_footer','1','Display Template standart Header and Footer','',18,'',1,0),

					(NULL,'default_from_name','". get_bloginfo('name'). "','Default From Name','Default From name in emails from your site',1,'',1,0),
					(NULL,'default_from_email','". get_bloginfo('admin_email'). "','Default From Email','Default From email in emails from your site',1,'',1,0),
					(NULL,'default_reply_name','". get_bloginfo('name'). "','Default Reply To Name','Default Reply to name in emails from your site',1,'',1,0),
					(NULL,'default_reply_email','". get_bloginfo('admin_email'). "','Default Reply To Email','Default Reply to email in emails from your site',1,'',1,0),

					(NULL,'subscr_admin_email','". get_bloginfo('admin_email'). "','Email notification about new subscriber','You you don\'t want to get such notifications - just clear this field',1,'',3,0),
					(NULL,'subscr_enter_email_msg','". __('Please enter your email'). "','\"Enter Email\" message','Default \"Enter Email\" message for your subscribe form',1,'',3,0),
					(NULL,'subscr_success_msg','". __('Thank you for subscription!'). "','Subscribe success message','Message that user will see after subscribe',1,'',3,0),
					(NULL,'subscr_activation_required','1','Subscribe activation required','If this checked - after subscription user will get notification message with subscribe activation link',18,'',3,0),
					(NULL,'subscr_form_title','Subscribe','Default subscribe form title','Default subscribe form title',1,'',3,0),

					(NULL,'template','100','Template','Template',1,'',2,0);");
			}
			/* options categories */
			if(!dbFhf::exist("@__options_categories")) {
				dbDelta(dbFhf::prepareQuery("CREATE TABLE IF NOT EXISTS `@__options_categories` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `label` varchar(128) NOT NULL,
				  PRIMARY KEY (`id`),
				  KEY `id` (`id`)
				) DEFAULT CHARSET=utf8"));
				dbFhf::query("INSERT INTO `@__options_categories` VALUES
					(1, 'General'),
					(2, 'Template'),
					(3, 'Subscribe'),
					(4, 'Social');");
			}
			/**
			 * Email Templates
			 */
			if(!dbFhf::exist("@__email_templates")) {
				dbDelta(dbFhf::prepareQuery("CREATE TABLE IF NOT EXISTS `@__email_templates` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `label` varchar(128) NOT NULL,
					  `subject` varchar(255) NOT NULL,
					  `body` text NOT NULL,
					  `variables` text NOT NULL,
					  `active` tinyint(1) NOT NULL,
					  `name` varchar(128) NOT NULL,
					  `module` varchar(128) NOT NULL,
					  PRIMARY KEY (`id`),
					  UNIQUE INDEX `name` (`name`)
					) DEFAULT CHARSET=utf8"));
				$emailTemplates = array(
					'fhf_confirm' => array(
						'body' => 'Hello!'. $eol. 'Thank you for subscribing for :site_name!'. $eol. 'To complete your subscription please follow the link bellow:'. $eol. '<a href=":link">:link</a>'. $eol. 'Regards,'. $eol. ':site_name team.',
						'variables' => array('site_name', 'link'),
					),
					'fhf_admin_notify' => array(
						'body' => 'Hello!'. $eol. 'New user activated subscription on your site :site_name for email :email.',
						'variables' => array('site_name', 'email'),
					),
					'fhf_new_post' => array(
						'body' => 'Hello!'. $eol. 'New entry was published on :site_name.'. $eol. 'Visit it by following next link:'. $eol. '<a href=":post_link">:post_title</a>'. $eol. 'Regards,'. $eol. ':site_name team.',
						'variables' => array('site_name', 'post_link', 'post_title'),
					),
				);
				dbFhf::query("INSERT INTO `@__email_templates` (`id`, `label`, `subject`, `body`, `variables`, `active`, `name`, `module`) VALUES 
					(NULL, 'Subscribe Confirm', 'Subscribe Confirmation', '". $emailTemplates['fhf_confirm']['body']. "', '[\"". implode('","', $emailTemplates['fhf_confirm']['variables'])."\"]', 1, 'fhf_confirm', 'subscribe'),
					(NULL, 'Subscribe Admin Notify', 'New subscriber', '". $emailTemplates['fhf_admin_notify']['body']. "', '[\"". implode('","', $emailTemplates['fhf_admin_notify']['variables'])."\"]', 1, 'fhf_admin_notify', 'subscribe'),
					(NULL, 'Subscribe New Entry', ':site_name - New Entry!', '". $emailTemplates['fhf_new_post']['body']. "', '[\"". implode('","', $emailTemplates['fhf_new_post']['variables'])."\"]', 1, 'fhf_new_post', 'subscribe');");
			}
			/**
			 * Subscribers
			 */
			// Will do this always for now
			$insertWpSubscribers = true;
			if(!dbFhf::exist("@__subscribers")) {
				dbDelta(dbFhf::prepareQuery("CREATE TABLE IF NOT EXISTS `@__subscribers` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `user_id` int(11) NOT NULL DEFAULT '0',
					  `email` varchar(255) NOT NULL,
					  `name` varchar(255) DEFAULT NULL,
					  `created` datetime NOT NULL,
					  `unsubscribe_date` datetime DEFAULT NULL,
					  `active` tinyint(4) NOT NULL DEFAULT '1',
					  `token` varchar(255) DEFAULT NULL,
					  `ip` varchar(64) DEFAULT NULL,
					  PRIMARY KEY (`id`)
					) DEFAULT CHARSET=utf8"));
				$insertWpSubscribers = true;
			}
			/**
			 * Subscribers Lists
			 */
			if(!dbFhf::exist("@__subscribers_lists")) {
				dbDelta(dbFhf::prepareQuery("CREATE TABLE IF NOT EXISTS `@__subscribers_lists` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `label` varchar(255) NOT NULL,
					  `description` text,
					  `protected` tinyint(1) NOT NULL DEFAULT '0',
					  PRIMARY KEY (`id`)
					) DEFAULT CHARSET=utf8"));
				dbFhf::query("INSERT INTO `@__subscribers_lists` (`id`, `label`, `description`, `protected`) VALUES 
				(". FHF_WP_LIST_ID. ", '". __('Wordpress Users'). "', '". __('Wordpress Users list'). "', 1),
				(2, '". __('First Subscribers List'). "', '". sprintf(__('Default list, created automaticaly on first install of %1$s'), FHF_WP_PLUGIN_NAME). "', 0);");

				// Reserve 100 first IDs for future use
				dbFhf::query("ALTER TABLE `@__subscribers_lists` AUTO_INCREMENT = 100;");
			}
			/**
			 * Subscribers Lists to Subscribers Connection
			 */
			if(!dbFhf::exist("@__subscribers_to_lists")) {
				dbDelta(dbFhf::prepareQuery("CREATE TABLE IF NOT EXISTS `@__subscribers_to_lists` (
					  `subscriber_id` int(11) NOT NULL,
					  `subscriber_list_id` int(11) NOT NULL,
					  KEY `subscriber_id` (`subscriber_id`),
					  KEY `subscriber_list_id` (`subscriber_list_id`),
					  UNIQUE KEY `subscriber_to_list` (`subscriber_id`, `subscriber_list_id`)
					) DEFAULT CHARSET=utf8"));
				$insertWpSubscribers = true;
			}
			/**
			 * Copy wp subscribers to oursubscribers list
			 */
			if($insertWpSubscribers) {
				$wpSubscribers = get_users(array('role' => 'subscriber'));
				if(!empty($wpSubscribers)) {
					$dbDateCreated = dbFhf::timeToDate();
					foreach($wpSubscribers as $sub) {
						if(dbFhf::query('INSERT INTO @__subscribers (user_id, email, name, created, active, token) 
							VALUES ('. $sub->data->ID. ', "'. $sub->data->user_email. '", "'. $sub->data->display_name. '", "'. $dbDateCreated. '", 1, "'. md5($sub->data->user_email. AUTH_KEY). '");')
						&& ($newFhfId = dbFhf::lastID())
						) {
							dbFhf::query('INSERT INTO @__subscribers_to_lists (subscriber_id, subscriber_list_id) 
								VALUES ('. $newFhfId. ', '. FHF_WP_LIST_ID. ')');
						}
					}
				}
			}
			/**
			 * Log table - all logs in project
			 */
			if(!dbFhf::exist("@__log")) {
				dbDelta(dbFhf::prepareQuery("CREATE TABLE `@__log` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `type` varchar(64) NOT NULL,
				  `data` text,
				  `date_created` int(11) NOT NULL DEFAULT '0',
				  `uid` int(11) NOT NULL DEFAULT 0,
				  PRIMARY KEY (`id`)
				) DEFAULT CHARSET=utf8"));
			}
			/**
			* Super Templates
			*/
			// We will do this only one first time
			$initStplDatabase = false;
			if(!dbFhf::exist("@__stpl")) {
			   dbDelta(dbFhf::prepareQuery("CREATE TABLE IF NOT EXISTS `@__stpl` (
				 `id` int(11) NOT NULL AUTO_INCREMENT,
				 `protected` tinyint(1) NOT NULL DEFAULT '0',
				 `category_id` tinyint(2) NOT NULL DEFAULT '0',
				 `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
				 `style_params` text,
				 `preview_img` varchar(64),
				 `label` VARCHAR(64) NULL DEFAULT NULL,
				 `parent_id` INT(11) NULL DEFAULT '0',
				 PRIMARY KEY (`id`)
			   ) DEFAULT CHARSET=utf8"));
			   $initStplDatabase = true;
			}
			if(!dbFhf::exist("@__stpl_rows")) {
			   dbDelta(dbFhf::prepareQuery("CREATE TABLE IF NOT EXISTS `@__stpl_rows` (
				 `id` int(11) NOT NULL AUTO_INCREMENT,
				 `stpl_id` int(11) NOT NULL,
				 `height` int(11) NOT NULL DEFAULT '0',
				 `background_color` varchar(24),
				 PRIMARY KEY (`id`)
			   ) DEFAULT CHARSET=utf8"));
			}
			if(!dbFhf::exist("@__stpl_cols")) {
			   dbDelta(dbFhf::prepareQuery("CREATE TABLE IF NOT EXISTS `@__stpl_cols` (
				 `id` int(11) NOT NULL AUTO_INCREMENT,
				 `stpl_row_id` int(11) NOT NULL,
				 `width` int(11) NOT NULL DEFAULT '0',
				 `content` text,
				 `element_class` varchar(64),
				 PRIMARY KEY (`id`)
			   ) DEFAULT CHARSET=utf8"));
			}
			if($initStplDatabase) {
			   self::installDefaultStpls();
			   // Reserve 100 first IDs for future use
				dbFhf::query("ALTER TABLE `@__stpl` AUTO_INCREMENT = ". (FHF_STPL_DEFINED_IDS_MAX + 1). ";");
			}
		}
		/*****/
		installerDbUpdaterFhf::runUpdate();

		update_option($wpPrefix. FHF_DB_PREF. 'db_version', FHF_VERSION);
		add_option($wpPrefix. FHF_DB_PREF. 'db_installed', 1);
		//$time = microtime(true) - $start;	// Speed debug info
	}
	static public function delete() {
		global $wpdb;
		$wpPrefix = $wpdb->prefix; /* add to 0.0.3 Versiom */
		$deleteOptions = reqFhf::getVar('deleteOptions');
		if(frameFhf::_()->getModule('pages')) {
			if(is_null($deleteOptions)) {
				frameFhf::_()->getModule('pages')->getView()->displayDeactivatePage();
				exit();
			}
		}
		if((bool) $deleteOptions) {
			dbFhf::query("DROP TABLE IF EXISTS `@__htmltype`");
			dbFhf::query("DROP TABLE IF EXISTS `@__modules`");
			dbFhf::query("DROP TABLE IF EXISTS `@__modules_type`");
			dbFhf::query("DROP TABLE IF EXISTS `@__options`");
			dbFhf::query("DROP TABLE IF EXISTS `@__options_categories`");
			dbFhf::query("DROP TABLE IF EXISTS `@__email_templates`");
			dbFhf::query("DROP TABLE IF EXISTS `@__subscribers`");
			dbFhf::query("DROP TABLE IF EXISTS `@__subscribers_lists`");
			dbFhf::query("DROP TABLE IF EXISTS `@__subscribers_to_lists`");
			dbFhf::query("DROP TABLE IF EXISTS `@__log`");
			dbFhf::query("DROP TABLE IF EXISTS `@__stpl`");
			dbFhf::query("DROP TABLE IF EXISTS `@__stpl_rows`");
			dbFhf::query("DROP TABLE IF EXISTS `@__stpl_cols`");
		}
		delete_option($wpPrefix. FHF_DB_PREF. 'db_version');
		delete_option($wpPrefix. FHF_DB_PREF. 'db_installed');
	}
	static public function update() {
		global $wpdb;
		$wpPrefix = $wpdb->prefix;
		$currentVersion = get_option($wpPrefix. FHF_DB_PREF. 'db_version', 0);
		if(!$currentVersion || version_compare(FHF_VERSION, $currentVersion, '>')) {
			self::init();
			//update_option($wpPrefix. FHF_DB_PREF. 'db_version', FHF_VERSION);
		}
	}
	// Install default (pre-set) Super Tempates
	static public function installDefaultStpls() {
		/*dbFhf::query("INSERT INTO `@__stpl` (`id`, `protected`, `category_id`, `date_created`, `style_params`, `preview_img`, `label`, `parent_id`) VALUES 
		('1', '1', '0', '2014-03-10 17:52:11', 'a:5:{s:15:\"background_type\";s:4:\"none\";s:16:\"background_color\";s:0:\"\";s:18:\"background_img_pos\";s:4:\"tile\";s:16:\"background_image\";s:0:\"\";s:10:\"font_style\";a:8:{s:4:\"text\";a:4:{s:8:\"selector\";s:1:\"*\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:5:\"links\";a:4:{s:8:\"selector\";s:1:\"a\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#0000ee\";}s:2:\"h1\";a:4:{s:8:\"selector\";s:2:\"h1\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"22px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h2\";a:4:{s:8:\"selector\";s:2:\"h2\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"18px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h3\";a:4:{s:8:\"selector\";s:2:\"h3\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"16px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h4\";a:4:{s:8:\"selector\";s:2:\"h4\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"14px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h5\";a:4:{s:8:\"selector\";s:2:\"h5\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h6\";a:4:{s:8:\"selector\";s:2:\"h6\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"12px\";s:5:\"color\";s:7:\"#000000\";}}}', '1.png', 'Simple 1 Column', '1'),
		('2', '1', '0', '2014-04-16 12:55:50', 'a:5:{s:15:\"background_type\";s:5:\"color\";s:16:\"background_color\";s:7:\"#f0f0f1\";s:18:\"background_img_pos\";s:7:\"stretch\";s:16:\"background_image\";s:0:\"\";s:10:\"font_style\";a:8:{s:4:\"text\";a:4:{s:8:\"selector\";s:1:\"*\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"18px\";s:5:\"color\";s:7:\"#000000\";}s:5:\"links\";a:4:{s:8:\"selector\";s:1:\"a\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#0000ee\";}s:2:\"h1\";a:4:{s:8:\"selector\";s:2:\"h1\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"22px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h2\";a:4:{s:8:\"selector\";s:2:\"h2\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"18px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h3\";a:4:{s:8:\"selector\";s:2:\"h3\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"16px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h4\";a:4:{s:8:\"selector\";s:2:\"h4\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"14px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h5\";a:4:{s:8:\"selector\";s:2:\"h5\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h6\";a:4:{s:8:\"selector\";s:2:\"h6\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"12px\";s:5:\"color\";s:7:\"#000000\";}}}', '2.png', 'Simple 2 Column', '2'),
		('3', '1', '0', '2014-04-16 23:08:40', 'a:5:{s:15:\"background_type\";s:4:\"none\";s:16:\"background_color\";s:0:\"\";s:18:\"background_img_pos\";s:7:\"stretch\";s:16:\"background_image\";s:0:\"\";s:10:\"font_style\";a:8:{s:4:\"text\";a:4:{s:8:\"selector\";s:1:\"*\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:5:\"links\";a:4:{s:8:\"selector\";s:1:\"a\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#0000ee\";}s:2:\"h1\";a:4:{s:8:\"selector\";s:2:\"h1\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"22px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h2\";a:4:{s:8:\"selector\";s:2:\"h2\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"18px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h3\";a:4:{s:8:\"selector\";s:2:\"h3\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"16px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h4\";a:4:{s:8:\"selector\";s:2:\"h4\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"14px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h5\";a:4:{s:8:\"selector\";s:2:\"h5\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h6\";a:4:{s:8:\"selector\";s:2:\"h6\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"12px\";s:5:\"color\";s:7:\"#000000\";}}}', '3.png', 'Simple 3 Column', '3'),
		
		('100', '0', '0', '2014-03-10 17:52:11', 'a:5:{s:15:\"background_type\";s:4:\"none\";s:16:\"background_color\";s:0:\"\";s:18:\"background_img_pos\";s:4:\"tile\";s:16:\"background_image\";s:0:\"\";s:10:\"font_style\";a:8:{s:4:\"text\";a:4:{s:8:\"selector\";s:1:\"*\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:5:\"links\";a:4:{s:8:\"selector\";s:1:\"a\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#0000ee\";}s:2:\"h1\";a:4:{s:8:\"selector\";s:2:\"h1\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"22px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h2\";a:4:{s:8:\"selector\";s:2:\"h2\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"18px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h3\";a:4:{s:8:\"selector\";s:2:\"h3\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"16px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h4\";a:4:{s:8:\"selector\";s:2:\"h4\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"14px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h5\";a:4:{s:8:\"selector\";s:2:\"h5\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h6\";a:4:{s:8:\"selector\";s:2:\"h6\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"12px\";s:5:\"color\";s:7:\"#000000\";}}}', '', '', '1');");
		dbFhf::query("INSERT INTO `@__stpl_rows` (`id`, `stpl_id`, `height`, `background_color`) VALUES 
		('1', '1', '93', ''),
		('2', '1', '99', ''),
		('3', '1', '102', ''),
		('4', '1', '48', ''),
		('5', '1', '117', ''),
		('6', '1', '116', ''),
		('7', '2', '99', ''),
		('8', '2', '132', ''),
		('9', '2', '158', ''),
		('10', '2', '79', ''),
		('11', '3', '69', ''),
		('12', '3', '612', ''),
		('13', '3', '86', ''),
		
		('14', '100', '93', ''),
		('15', '100', '99', ''),
		('16', '100', '102', ''),
		('17', '100', '48', ''),
		('18', '100', '117', ''),
		('19', '100', '116', '');");
		dbFhf::query("INSERT INTO `@__stpl_cols` (`id`, `stpl_row_id`, `width`, `content`, `element_class`) VALUES 
		('1', '1', '616', '<h1 style=\"text-align: center;\">404 Error</h1>', 'stplCanvasElementText'),
		('2', '2', '616', '<h2 style=\"text-align: center;\">Sorry, but page you are looking for was&nbsp;not found</h2>', 'stplCanvasElementText'),
		('3', '3', '610', '[ready_stpl_search_form]', 'stplCanvasElementSearch'),
		('4', '4', '610', '<h3>Try other pages:</h3>', 'stplCanvasElementText'),
		('5', '5', '610', '[ready_stpl_menu menu=\"18\" add_classes=\"site-navigation primary-navigation\" add_styles=\"float: none;\"]', 'stplCanvasElementMenu'),
		('6', '6', '610', '[ready_subscribe_form list=\"1\" subscr_form_title=\"Subscribe:\" subscr_enter_email_msg=\"Enter Email\" subscr_success_msg=\"Thank you for subscribe!\"]', 'stplCanvasSubscribeForm'),
		('7', '7', '564', '<p><a href=\"FHF_STPL_SITE_URL\"><img style=\"display: block; margin-left: auto; margin-right: auto;\" title=\"logo\" alt=\"\" src=\"FHF_STPL_ADDITIONSimg/custom/logo.png\" height=\"65\" width=\"111\"></a></p>', 'stplCanvasElementText'),
		('8', '8', '279', '<h1>404</h1><h2>Page Not Found</h2>', 'stplCanvasElementText'),
		('9', '8', '279', '<address>Something went wrong - page that you requested was not found, sorry.</address>', 'stplCanvasElementText'),
		('10', '9', '564', '[ready_stpl_search_form]', 'stplCanvasElementSearch'),
		('11', '10', '558', '<div class=\"stplCanvasSocSet\" style=\"text-align: center;\"><a href=\"https://www.facebook.com/ReadyECommerce\" target=\"_blank\" title=\"facebook\"><img style=\"padding: 10px 10px 10px 0px; display: inline;\" src=\"FHF_STPL_SITE_URLwp-content/plugins/ready-404-page-generator/modules/stpl/img/soc_icons/facebook-2.png\"></a><a href=\"\" target=\"_blank\" title=\"twitter\"><img style=\"padding: 10px 10px 10px 0px; display: inline;\" src=\"FHF_STPL_SITE_URLwp-content/plugins/ready-404-page-generator/modules/stpl/img/soc_icons/twitter-2.png\"></a><a href=\"\" target=\"_blank\" title=\"google_plus\"><img style=\"padding: 10px 10px 10px 0px; display: inline;\" src=\"FHF_STPL_SITE_URLwp-content/plugins/ready-404-page-generator/modules/stpl/img/soc_icons/google_plus-2.png\"></a><a href=\"\" target=\"_blank\" title=\"youtube\"><img style=\"padding: 10px 10px 10px 0px; display: inline;\" src=\"FHF_STPL_SITE_URLwp-content/plugins/ready-404-page-generator/modules/stpl/img/soc_icons/youtube-2.png\"></a><input class=\"stplCanvasSocSetId\" value=\"2\" type=\"hidden\"></div>', 'stplCanvasElementSocial'),
		('12', '11', '564', '<h1 style=\"text-align: center;\">Ooops, 404</h1>', 'stplCanvasElementText'),
		('13', '12', '144', '[ready_subscribe_form list=\"1\" subscr_form_title=\"Subscribe\" subscr_enter_email_msg=\"Enter Email\" subscr_success_msg=\"Thank you for subscribe!\"]', 'stplCanvasSubscribeForm'),
		('14', '12', '259', '<h4 style=\"text-align: center;\">&nbsp;Sorry, but requested page was not found</h4><p style=\"text-align: center;\"><a href=\"FHF_STPL_SITE_URL\"><img style=\"display: inline;\" class=\"aligncenter size-full wp-image-1359\" alt=\"douh\" src=\"FHF_STPL_ADDITIONSimg/custom/douh.png\" height=\"500\" width=\"210\"></a></p>', 'stplCanvasElementText'),
		('15', '12', '149', '[new_content_ready title_style=\"h1\" title_align=\"left\" show_content=\"excerpt\" posts_num=\"2\" category=\"0\"]', 'stplCanvasElementNewContent'),
		('16', '13', '564', '<div class=\"stplCanvasSocSet\" style=\"text-align: center;\"><a href=\"https://www.facebook.com/ReadyECommerce\" target=\"_blank\" title=\"facebook\"><img style=\"padding: 10px 10px 10px 0px; display: inline;\" src=\"FHF_STPL_SITE_URLwp-content/plugins/ready-404-page-generator/modules/stpl/img/soc_icons/facebook-1.png\"></a><a href=\"\" target=\"_blank\" title=\"twitter\"><img style=\"padding: 10px 10px 10px 0px; display: inline;\" src=\"FHF_STPL_SITE_URLwp-content/plugins/ready-404-page-generator/modules/stpl/img/soc_icons/twitter-1.png\"></a><a href=\"\" target=\"_blank\" title=\"google_plus\"><img style=\"padding: 10px 10px 10px 0px; display: inline;\" src=\"FHF_STPL_SITE_URLwp-content/plugins/ready-404-page-generator/modules/stpl/img/soc_icons/google_plus-1.png\"></a><a href=\"\" target=\"_blank\" title=\"youtube\"><img style=\"padding: 10px 10px 10px 0px; display: inline;\" src=\"FHF_STPL_SITE_URLwp-content/plugins/ready-404-page-generator/modules/stpl/img/soc_icons/youtube-1.png\"></a><input class=\"stplCanvasSocSetId\" value=\"1\" type=\"hidden\"></div>', 'stplCanvasElementSocial'),
		
		('17', '14', '616', '<h1 style=\"text-align: center;\">404 Error</h1>', 'stplCanvasElementText'),
		('18', '15', '616', '<h2 style=\"text-align: center;\">Sorry, but page you are looking for was&nbsp;not found</h2>', 'stplCanvasElementText'),
		('19', '16', '610', '[ready_stpl_search_form]', 'stplCanvasElementSearch'),
		('20', '17', '610', '<h3>Try other pages:</h3>', 'stplCanvasElementText'),
		('21', '18', '610', '[ready_stpl_menu menu=\"18\" add_classes=\"site-navigation primary-navigation\" add_styles=\"float: none;\"]', 'stplCanvasElementMenu'),
		('22', '19', '610', '[ready_subscribe_form list=\"1\" subscr_form_title=\"Subscribe:\" subscr_enter_email_msg=\"Enter Email\" subscr_success_msg=\"Thank you for subscribe!\"]', 'stplCanvasSubscribeForm');");*/
		
		/*dbFhf::query("INSERT INTO `@__stpl` (`id`, `protected`, `category_id`, `date_created`, `style_params`, `preview_img`, `label`, `parent_id`) VALUES 
('1', '1', '0', '2014-03-10 17:52:11', 'a:5:{s:15:\"background_type\";s:5:\"image\";s:16:\"background_color\";s:0:\"\";s:18:\"background_img_pos\";s:4:\"tile\";s:16:\"background_image\";s:54:\"FHF_STPL_MOD_URLimg/common/MissingJigsawPiece/body.png\";s:10:\"font_style\";a:8:{s:4:\"text\";a:4:{s:8:\"selector\";s:1:\"*\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"16px\";s:5:\"color\";s:7:\"#000000\";}s:5:\"links\";a:4:{s:8:\"selector\";s:1:\"a\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"16px\";s:5:\"color\";s:7:\"#0000ee\";}s:2:\"h1\";a:4:{s:8:\"selector\";s:2:\"h1\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"22px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h2\";a:4:{s:8:\"selector\";s:2:\"h2\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"18px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h3\";a:4:{s:8:\"selector\";s:2:\"h3\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"16px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h4\";a:4:{s:8:\"selector\";s:2:\"h4\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"14px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h5\";a:4:{s:8:\"selector\";s:2:\"h5\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h6\";a:4:{s:8:\"selector\";s:2:\"h6\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"12px\";s:5:\"color\";s:7:\"#000000\";}}}', '', '', '0'),
('2', '1', '0', '2014-04-30 22:15:44', 'a:5:{s:15:\"background_type\";s:5:\"image\";s:16:\"background_color\";s:7:\"#f0f0f1\";s:18:\"background_img_pos\";s:4:\"tile\";s:16:\"background_image\";s:38:\"FHF_STPL_MOD_URLimg/common/Ouch/bg.png\";s:10:\"font_style\";a:8:{s:4:\"text\";a:4:{s:8:\"selector\";s:1:\"*\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"18px\";s:5:\"color\";s:7:\"#000000\";}s:5:\"links\";a:4:{s:8:\"selector\";s:1:\"a\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#0000ee\";}s:2:\"h1\";a:4:{s:8:\"selector\";s:2:\"h1\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:5:\"158px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h2\";a:4:{s:8:\"selector\";s:2:\"h2\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"36px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h3\";a:4:{s:8:\"selector\";s:2:\"h3\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"18px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h4\";a:4:{s:8:\"selector\";s:2:\"h4\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"14px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h5\";a:4:{s:8:\"selector\";s:2:\"h5\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h6\";a:4:{s:8:\"selector\";s:2:\"h6\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"12px\";s:5:\"color\";s:7:\"#000000\";}}}', '', 'Simple 2 Column', '0'),
('3', '1', '0', '2014-05-01 15:39:52', 'a:5:{s:15:\"background_type\";s:5:\"image\";s:16:\"background_color\";s:0:\"\";s:18:\"background_img_pos\";s:4:\"tile\";s:16:\"background_image\";s:48:\"FHF_STPL_MOD_URLimg/common/SaveMe/background.png\";s:10:\"font_style\";a:8:{s:4:\"text\";a:4:{s:8:\"selector\";s:1:\"*\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:5:\"links\";a:4:{s:8:\"selector\";s:1:\"a\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#0000ee\";}s:2:\"h1\";a:4:{s:8:\"selector\";s:2:\"h1\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"78px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h2\";a:4:{s:8:\"selector\";s:2:\"h2\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"26px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h3\";a:4:{s:8:\"selector\";s:2:\"h3\";s:11:\"font-family\";s:10:\"Droid Sans\";s:9:\"font-size\";s:4:\"20px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h4\";a:4:{s:8:\"selector\";s:2:\"h4\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"14px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h5\";a:4:{s:8:\"selector\";s:2:\"h5\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h6\";a:4:{s:8:\"selector\";s:2:\"h6\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"12px\";s:5:\"color\";s:7:\"#000000\";}}}', '', 'Simple 3 Column', '0'),
('100', '0', '0', '2014-03-10 17:52:11', 'a:5:{s:15:\"background_type\";s:5:\"image\";s:16:\"background_color\";s:0:\"\";s:18:\"background_img_pos\";s:4:\"tile\";s:16:\"background_image\";s:54:\"FHF_STPL_MOD_URLimg/common/MissingJigsawPiece/body.png\";s:10:\"font_style\";a:8:{s:4:\"text\";a:4:{s:8:\"selector\";s:1:\"*\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"16px\";s:5:\"color\";s:7:\"#000000\";}s:5:\"links\";a:4:{s:8:\"selector\";s:1:\"a\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"16px\";s:5:\"color\";s:7:\"#0000ee\";}s:2:\"h1\";a:4:{s:8:\"selector\";s:2:\"h1\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"22px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h2\";a:4:{s:8:\"selector\";s:2:\"h2\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"18px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h3\";a:4:{s:8:\"selector\";s:2:\"h3\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"16px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h4\";a:4:{s:8:\"selector\";s:2:\"h4\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"14px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h5\";a:4:{s:8:\"selector\";s:2:\"h5\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h6\";a:4:{s:8:\"selector\";s:2:\"h6\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"12px\";s:5:\"color\";s:7:\"#000000\";}}}', '', '', '1');");
dbFhf::query("INSERT INTO `@__stpl_rows` (`id`, `stpl_id`, `height`, `background_color`) VALUES 
('1', '3', '19', ''),
('2', '3', '76', ''),
('3', '3', '110', ''),
('4', '3', '65', ''),
('5', '3', '49', ''),
('6', '3', '246', ''),
('7', '3', '48', ''),
('8', '3', '72', ''),
('9', '3', '80', ''),
('10', '2', '50', ''),
('11', '2', '221', ''),
('12', '2', '112', ''),
('13', '2', '89', ''),
('14', '2', '75', ''),
('15', '2', '148', ''),
('16', '1', '22', ''),
('17', '1', '431', ''),
('18', '1', '51', ''),
('19', '1', '67', ''),
('20', '1', '71', ''),
('21', '100', '22', ''),
('22', '100', '431', ''),
('23', '100', '51', ''),
('24', '100', '67', ''),
('25', '100', '71', '');");
dbFhf::query("INSERT INTO `@__stpl_cols` (`id`, `stpl_row_id`, `width`, `content`, `element_class`) VALUES 
('1', '1', '558', '<style type=\"text/css\">\nh3 {\ntext-shadow:rgba(255,255,255,.16) 0 2px 0;\n}\n#saveMeButt {\nwidth: 513px;\nbackground: url(\"FHF_STPL_MOD_URLimg/common/SaveMe/button.png\") no-repeat scroll 0 0 rgba(0, 0, 0, 0);\nheight: 228px;\ndisplay: block;\nmargin: 13px auto 23px;\n}\n#saveMeButt:hover {\nbackground:url(\"FHF_STPL_MOD_URLimg/common/SaveMe/button_hover.png\");\n}\n.saveMeMenu ul {\ntext-align: center;\nlist-style: none outside none;\n}\n.saveMeMenu ul li {\ndisplay: inline;\n}\n.saveMeMenu ul li a {\ncolor: #9B9FA8 !important;\ntext-decoration: none;\ntext-shadow: 0 1px 0 rgba(0, 0, 0, 0.75);\n}\n.saveMeMenu ul li:after {\ncontent: \"\0000a0\0000a0\0000a0\0000a0|\0000a0\0000a0\0000a0\0000a0\";\ncolor: #9B9FA8 !important;\n}\n.saveMeMenu ul li:last-of-type:after {\ncontent: \"\";\n\n}\n</style>', 'stplCanvasElementText'),
('2', '2', '564', '<a href=\"FHF_STPL_SITE_URL\"><img style=\"display: block; margin-left: auto; margin-right: auto;\" title=\"logo\" alt=\"\" src=\"SUB_STPL_MOD_URLimg/common/SaveMe/logo.png\"></a>', 'stplCanvasElementImage'),
('3', '3', '558', '<h1 style=\"text-align: center;\"><span style=\"color: #ffffff;\">ooops...</span></h1>', 'stplCanvasElementText'),
('4', '4', '558', '<h2 style=\"text-align: center;\"><span style=\"color: #999999;\">It seems the page you were looking for doesnвЂ™t exist</span></h2>', 'stplCanvasElementText'),
('5', '5', '558', '<h3 style=\"text-align: center;\"><span style=\"color: #808080;\">You can either press this button</span></h3>', 'stplCanvasElementText'),
('6', '6', '558', '<div id=\"saveMeButtShell\">\n<a href=\"FHF_STPL_SITE_URL\" id=\"saveMeButt\"></a>\n</div>', 'stplCanvasElementText'),
('7', '7', '558', '<h3 style=\"text-align: center;\"><span style=\"color: #808080;\">Or you can...</span></h3>', 'stplCanvasElementText'),
('8', '8', '558', '[ready_stpl_search_form]', 'stplCanvasElementSearch'),
('9', '9', '558', '[ready_stpl_menu menu=\"FHF_STPL_RAND_MENU_ID\" add_classes=\"saveMeMenu\" add_styles=\"\"]', 'stplCanvasElementMenu'),
('10', '10', '564', '<style type=\"text/css\">\n.ouch_menu ul li {\nlist-style: none outside none;\npadding-right: 10px;\nfloat: left;\n}\n.ouch_menu ul li:after {\ncontent: \"\0000a0\0000a0\0000a0\\/\";\n}\n.ouch_menu ul li:last-of-type:after {\ncontent: \"\";\n}\n.ouch_menu ul {\nmargin: 0;\n}\n.ouch_menu ul li a {\ntext-align: left;\n	text-decoration: none;\n	text-shadow: 0px 0px 10px rgba(255,255,255,0.9);\n	color: #666 !important;;\n	outline: none;\n	-o-transition:all .3s;\n	-moz-transition:all .3s;\n	-webkit-transition:all .3s;\n	-ms-transition:all .3s;\n	transition: all 0.3s ease-in-out;\n}\n.ouch_menu ul li a:hover {\n	text-shadow: 1px 1px 1px rgba(0,0,0,0.1);\n	color: #00d2ff !important;\n}\n</style>', 'stplCanvasElementText'),
('11', '11', '558', '<h1><span style=\"color: #808080;\">OUCH!</span></h1>', 'stplCanvasElementText'),
('12', '12', '564', '<h2><span style=\"color: #808080;\">sorry the page you are looking for does not exist.</span></h2>', 'stplCanvasElementText'),
('13', '13', '558', '<h3><span style=\"color: #808080;\">you can explore our site back to the navigation below.</span></h3>', 'stplCanvasElementText'),
('14', '14', '558', '[ready_stpl_menu menu=\"FHF_STPL_RAND_MENU_ID\" add_classes=\"ouch_menu\" add_styles=\"\"]', 'stplCanvasElementMenu'),
('15', '15', '558', '<div class=\"stplCanvasSocSet\" style=\"text-align: center;\"><div class=\"fhfStplCanvasSocialDesignPresentation\"><a href=\"http://www.facebook.com/ReadyECommerce\" target=\"_blank\" title=\"facebook\" class=\"fhfStplSocLink-facebook-3 fhfStplSocLinks-3\"></a><a href=\"https://twitter.com/ReadyEcommerceW\" target=\"_blank\" title=\"twitter\" class=\"fhfStplSocLink-twitter-3 fhfStplSocLinks-3\"></a><a href=\"https://plus.google.com/u/0/101846604906752638355/about\" target=\"_blank\" title=\"google_plus\" class=\"fhfStplSocLink-google_plus-3 fhfStplSocLinks-3\"></a><a href=\"https://www.youtube.com/channel/UCHfmzraXLZdZVJmCe-59pww\" target=\"_blank\" title=\"youtube\" class=\"fhfStplSocLink-youtube-3 fhfStplSocLinks-3\"></a></div><input class=\"stplCanvasSocSetId\" value=\"3\" type=\"hidden\"></div>', 'stplCanvasElementSocial'),
('16', '16', '616', '<style type=\"text/css\">\n\n.animated{\n	-webkit-animation-fill-mode:both;\n	-moz-animation-fill-mode:both;\n	-ms-animation-fill-mode:both;\n	-o-animation-fill-mode:both;\n	animation-fill-mode:both;\n	-webkit-animation-duration:2s;\n	-moz-animation-duration:2s;\n	-ms-animation-duration:2s;\n	-o-animation-duration:2s;\n	animation-duration:2s;\n	-webkit-animation-delay: 2s;\n	-moz-animation-delay: 2s;\n	-ms-animation-delay: 2s;\n    animation-delay: 2s;\n}\n\n@-webkit-keyframes swing {\n	20%, 40%, 60%, 80%, 100% { -webkit-transform-origin: top center; }	20% { -webkit-transform: rotate(15deg); }	\n	40% { -webkit-transform: rotate(-10deg); }\n	60% { -webkit-transform: rotate(5deg); }	\n	80% { -webkit-transform: rotate(-5deg); }	\n	100% { -webkit-transform: rotate(0deg); }\n}\n\n@-moz-keyframes swing {\n	20% { -moz-transform: rotate(15deg); }	\n	40% { -moz-transform: rotate(-10deg); }\n	60% { -moz-transform: rotate(5deg); }	\n	80% { -moz-transform: rotate(-5deg); }	\n	100% { -moz-transform: rotate(0deg); }\n}\n\n@-o-keyframes swing {\n	20% { -o-transform: rotate(15deg); }	\n	40% { -o-transform: rotate(-10deg); }\n	60% { -o-transform: rotate(5deg); }	\n	80% { -o-transform: rotate(-5deg); }	\n	100% { -o-transform: rotate(0deg); }\n}\n\n@keyframes swing {\n	20% { transform: rotate(15deg); }	\n	40% { transform: rotate(-10deg); }\n	60% { transform: rotate(5deg); }	\n	80% { transform: rotate(-5deg); }	\n	100% { transform: rotate(0deg); }\n}\n\n.swing {\n	-webkit-transform-origin: top center;\n	-moz-transform-origin: top center;\n	-o-transform-origin: top center;\n	transform-origin: top center;\n	-webkit-animation-name: swing;\n	-moz-animation-name: swing;\n	-o-animation-name: swing;\n	animation-name: swing;\n}\n</style>', 'stplCanvasElementText'),
('17', '17', '616', '<div><img class=\"animated swing\" style=\"display: block; margin-left: auto; margin-right: auto;\" title=\"404_dark\" src=\"FHF_STPL_MOD_URLimg/common/MissingJigsawPiece/404_dark.png\" alt=\"\"></div>', 'stplCanvasElementText'),
('18', '18', '610', '<h1 style=\"text-align: center;\"><span style=\"color: #c0c0c0;\">The page you are looking for seems to be missing</span></h1>', 'stplCanvasElementText'),
('19', '19', '610', '<p style=\"text-align: center;\"><span style=\"color: #c0c0c0;\">Go back, or return to&nbsp;<a title=\"FHF_STPL_SITE_URL\" href=\"FHF_STPL_SITE_URL\"><span style=\"color: #c0c0c0;\">FHF_STPL_SITE_URL</span></a> to choose a new page.</span><br><span style=\"color: #c0c0c0;\"> Please report any broken links to <a title=\"FHF_STPL_ADMIN_EMAIL\" href=\"mailto:FHF_STPL_ADMIN_EMAIL\"><span style=\"color: #c0c0c0;\">our team</span></a>.</span></p>', 'stplCanvasElementText'),
('20', '20', '610', '[ready_stpl_search_form]', 'stplCanvasElementSearch'),
('21', '21', '616', '<style type=\"text/css\">\n\n.animated{\n	-webkit-animation-fill-mode:both;\n	-moz-animation-fill-mode:both;\n	-ms-animation-fill-mode:both;\n	-o-animation-fill-mode:both;\n	animation-fill-mode:both;\n	-webkit-animation-duration:2s;\n	-moz-animation-duration:2s;\n	-ms-animation-duration:2s;\n	-o-animation-duration:2s;\n	animation-duration:2s;\n	-webkit-animation-delay: 2s;\n	-moz-animation-delay: 2s;\n	-ms-animation-delay: 2s;\n    animation-delay: 2s;\n}\n\n@-webkit-keyframes swing {\n	20%, 40%, 60%, 80%, 100% { -webkit-transform-origin: top center; }	20% { -webkit-transform: rotate(15deg); }	\n	40% { -webkit-transform: rotate(-10deg); }\n	60% { -webkit-transform: rotate(5deg); }	\n	80% { -webkit-transform: rotate(-5deg); }	\n	100% { -webkit-transform: rotate(0deg); }\n}\n\n@-moz-keyframes swing {\n	20% { -moz-transform: rotate(15deg); }	\n	40% { -moz-transform: rotate(-10deg); }\n	60% { -moz-transform: rotate(5deg); }	\n	80% { -moz-transform: rotate(-5deg); }	\n	100% { -moz-transform: rotate(0deg); }\n}\n\n@-o-keyframes swing {\n	20% { -o-transform: rotate(15deg); }	\n	40% { -o-transform: rotate(-10deg); }\n	60% { -o-transform: rotate(5deg); }	\n	80% { -o-transform: rotate(-5deg); }	\n	100% { -o-transform: rotate(0deg); }\n}\n\n@keyframes swing {\n	20% { transform: rotate(15deg); }	\n	40% { transform: rotate(-10deg); }\n	60% { transform: rotate(5deg); }	\n	80% { transform: rotate(-5deg); }	\n	100% { transform: rotate(0deg); }\n}\n\n.swing {\n	-webkit-transform-origin: top center;\n	-moz-transform-origin: top center;\n	-o-transform-origin: top center;\n	transform-origin: top center;\n	-webkit-animation-name: swing;\n	-moz-animation-name: swing;\n	-o-animation-name: swing;\n	animation-name: swing;\n}\n</style>', 'stplCanvasElementText'),
('22', '22', '616', '<div><img class=\"animated swing\" style=\"display: block; margin-left: auto; margin-right: auto;\" title=\"404_dark\" src=\"FHF_STPL_MOD_URLimg/common/MissingJigsawPiece/404_dark.png\" alt=\"\"></div>', 'stplCanvasElementText'),
('23', '23', '610', '<h1 style=\"text-align: center;\"><span style=\"color: #c0c0c0;\">The page you are looking for seems to be missing</span></h1>', 'stplCanvasElementText'),
('24', '24', '610', '<p style=\"text-align: center;\"><span style=\"color: #c0c0c0;\">Go back, or return to&nbsp;<a title=\"FHF_STPL_SITE_URL\" href=\"FHF_STPL_SITE_URL\"><span style=\"color: #c0c0c0;\">FHF_STPL_SITE_URL</span></a> to choose a new page.</span><br><span style=\"color: #c0c0c0;\"> Please report any broken links to <a title=\"FHF_STPL_ADMIN_EMAIL\" href=\"mailto:FHF_STPL_ADMIN_EMAIL\"><span style=\"color: #c0c0c0;\">our team</span></a>.</span></p>', 'stplCanvasElementText'),
('25', '25', '610', '[ready_stpl_search_form]', 'stplCanvasElementSearch');");*/
		
		dbFhf::query("INSERT INTO `@__stpl` (`id`, `protected`, `category_id`, `date_created`, `style_params`, `preview_img`, `label`, `parent_id`) VALUES 
('1', '1', '0', '2014-03-10 17:52:11', 'a:5:{s:15:\"background_type\";s:5:\"image\";s:16:\"background_color\";s:0:\"\";s:18:\"background_img_pos\";s:4:\"tile\";s:16:\"background_image\";s:54:\"FHF_STPL_MOD_URLimg/common/MissingJigsawPiece/body.png\";s:10:\"font_style\";a:8:{s:4:\"text\";a:4:{s:8:\"selector\";s:1:\"*\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"16px\";s:5:\"color\";s:7:\"#000000\";}s:5:\"links\";a:4:{s:8:\"selector\";s:1:\"a\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"16px\";s:5:\"color\";s:7:\"#0000ee\";}s:2:\"h1\";a:4:{s:8:\"selector\";s:2:\"h1\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"22px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h2\";a:4:{s:8:\"selector\";s:2:\"h2\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"18px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h3\";a:4:{s:8:\"selector\";s:2:\"h3\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"16px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h4\";a:4:{s:8:\"selector\";s:2:\"h4\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"14px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h5\";a:4:{s:8:\"selector\";s:2:\"h5\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h6\";a:4:{s:8:\"selector\";s:2:\"h6\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"12px\";s:5:\"color\";s:7:\"#000000\";}}}', 'MissingJigsawPiece-small.png', 'Missing Jigsaw Piece', '0'),
('2', '1', '0', '2014-04-30 22:15:44', 'a:5:{s:15:\"background_type\";s:5:\"image\";s:16:\"background_color\";s:7:\"#f0f0f1\";s:18:\"background_img_pos\";s:4:\"tile\";s:16:\"background_image\";s:38:\"FHF_STPL_MOD_URLimg/common/Ouch/bg.png\";s:10:\"font_style\";a:8:{s:4:\"text\";a:4:{s:8:\"selector\";s:1:\"*\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"18px\";s:5:\"color\";s:7:\"#000000\";}s:5:\"links\";a:4:{s:8:\"selector\";s:1:\"a\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#0000ee\";}s:2:\"h1\";a:4:{s:8:\"selector\";s:2:\"h1\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:5:\"158px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h2\";a:4:{s:8:\"selector\";s:2:\"h2\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"36px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h3\";a:4:{s:8:\"selector\";s:2:\"h3\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"18px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h4\";a:4:{s:8:\"selector\";s:2:\"h4\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"14px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h5\";a:4:{s:8:\"selector\";s:2:\"h5\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h6\";a:4:{s:8:\"selector\";s:2:\"h6\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"12px\";s:5:\"color\";s:7:\"#000000\";}}}', 'Ouch-small.png', 'Ouch!', '0'),
('3', '1', '0', '2014-05-01 15:39:52', 'a:5:{s:15:\"background_type\";s:5:\"image\";s:16:\"background_color\";s:0:\"\";s:18:\"background_img_pos\";s:4:\"tile\";s:16:\"background_image\";s:48:\"FHF_STPL_MOD_URLimg/common/SaveMe/background.png\";s:10:\"font_style\";a:8:{s:4:\"text\";a:4:{s:8:\"selector\";s:1:\"*\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:5:\"links\";a:4:{s:8:\"selector\";s:1:\"a\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#0000ee\";}s:2:\"h1\";a:4:{s:8:\"selector\";s:2:\"h1\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"78px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h2\";a:4:{s:8:\"selector\";s:2:\"h2\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"26px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h3\";a:4:{s:8:\"selector\";s:2:\"h3\";s:11:\"font-family\";s:10:\"Droid Sans\";s:9:\"font-size\";s:4:\"20px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h4\";a:4:{s:8:\"selector\";s:2:\"h4\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"14px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h5\";a:4:{s:8:\"selector\";s:2:\"h5\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h6\";a:4:{s:8:\"selector\";s:2:\"h6\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"12px\";s:5:\"color\";s:7:\"#000000\";}}}', 'SaveMe-small.png', 'Save Me', '0'),
('100', '0', '0', '2014-03-10 17:52:11', 'a:5:{s:15:\"background_type\";s:5:\"image\";s:16:\"background_color\";s:0:\"\";s:18:\"background_img_pos\";s:4:\"tile\";s:16:\"background_image\";s:54:\"FHF_STPL_MOD_URLimg/common/MissingJigsawPiece/body.png\";s:10:\"font_style\";a:8:{s:4:\"text\";a:4:{s:8:\"selector\";s:1:\"*\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"16px\";s:5:\"color\";s:7:\"#000000\";}s:5:\"links\";a:4:{s:8:\"selector\";s:1:\"a\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"16px\";s:5:\"color\";s:7:\"#0000ee\";}s:2:\"h1\";a:4:{s:8:\"selector\";s:2:\"h1\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"22px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h2\";a:4:{s:8:\"selector\";s:2:\"h2\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"18px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h3\";a:4:{s:8:\"selector\";s:2:\"h3\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"16px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h4\";a:4:{s:8:\"selector\";s:2:\"h4\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"14px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h5\";a:4:{s:8:\"selector\";s:2:\"h5\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"13px\";s:5:\"color\";s:7:\"#000000\";}s:2:\"h6\";a:4:{s:8:\"selector\";s:2:\"h6\";s:11:\"font-family\";s:12:\"Trebuchet MS\";s:9:\"font-size\";s:4:\"12px\";s:5:\"color\";s:7:\"#000000\";}}}', 'MissingJigsawPiece-small.png', 'Missing Jigsaw Piece', '1');");
dbFhf::query("INSERT INTO `@__stpl_rows` (`id`, `stpl_id`, `height`, `background_color`) VALUES 
('1', '3', '19', ''),
('2', '3', '76', ''),
('3', '3', '110', ''),
('4', '3', '65', ''),
('5', '3', '49', ''),
('6', '3', '246', ''),
('7', '3', '48', ''),
('8', '3', '72', ''),
('9', '3', '80', ''),
('10', '2', '50', ''),
('11', '2', '221', ''),
('12', '2', '112', ''),
('13', '2', '89', ''),
('14', '2', '75', ''),
('15', '2', '148', ''),
('16', '1', '22', ''),
('17', '1', '431', ''),
('18', '1', '51', ''),
('19', '1', '67', ''),
('20', '1', '71', ''),
('21', '100', '22', ''),
('22', '100', '431', ''),
('23', '100', '51', ''),
('24', '100', '67', ''),
('25', '100', '71', '');");
dbFhf::query("INSERT INTO `@__stpl_cols` (`id`, `stpl_row_id`, `width`, `content`, `element_class`) VALUES 
('1', '1', '558', '<style type=\"text/css\">\nh3 {\ntext-shadow:rgba(255,255,255,.16) 0 2px 0;\n}\n#saveMeButt {\nwidth: 513px;\nbackground: url(\"FHF_STPL_MOD_URLimg/common/SaveMe/button.png\") no-repeat scroll 0 0 rgba(0, 0, 0, 0);\nheight: 228px;\ndisplay: block;\nmargin: 13px auto 23px;\n}\n#saveMeButt:hover {\nbackground:url(\"FHF_STPL_MOD_URLimg/common/SaveMe/button_hover.png\");\n}\n.saveMeMenu ul {\ntext-align: center;\nlist-style: none outside none;\n}\n.saveMeMenu ul li {\ndisplay: inline;\n}\n.saveMeMenu ul li a {\ncolor: #9B9FA8 !important;\ntext-decoration: none;\ntext-shadow: 0 1px 0 rgba(0, 0, 0, 0.75);\n}\n.saveMeMenu ul li:after {\ncontent: \"0000a00000a00000a00000a0|0000a00000a00000a00000a0\";\ncolor: #9B9FA8 !important;\n}\n.saveMeMenu ul li:last-of-type:after {\ncontent: \"\";\n\n}\n</style>', 'stplCanvasElementText'),
('2', '2', '564', '<a href=\"FHF_STPL_SITE_URL\"><img style=\"display: block; margin-left: auto; margin-right: auto;\" title=\"logo\" alt=\"\" src=\"SUB_STPL_MOD_URLimg/common/SaveMe/logo.png\"></a>', 'stplCanvasElementImage'),
('3', '3', '558', '<h1 style=\"text-align: center;\"><span style=\"color: #ffffff;\">ooops...</span></h1>', 'stplCanvasElementText'),
('4', '4', '558', '<h2 style=\"text-align: center;\"><span style=\"color: #999999;\">It seems the page you were looking for doesnРІР‚в„ўt exist</span></h2>', 'stplCanvasElementText'),
('5', '5', '558', '<h3 style=\"text-align: center;\"><span style=\"color: #808080;\">You can either press this button</span></h3>', 'stplCanvasElementText'),
('6', '6', '558', '<div id=\"saveMeButtShell\">\n<a href=\"FHF_STPL_SITE_URL\" id=\"saveMeButt\"></a>\n</div>', 'stplCanvasElementText'),
('7', '7', '558', '<h3 style=\"text-align: center;\"><span style=\"color: #808080;\">Or you can...</span></h3>', 'stplCanvasElementText'),
('8', '8', '558', '[ready_stpl_search_form]', 'stplCanvasElementSearch'),
('9', '9', '558', '[ready_stpl_menu menu=\"FHF_STPL_RAND_MENU_ID\" add_classes=\"saveMeMenu\" add_styles=\"\"]', 'stplCanvasElementMenu'),
('10', '10', '564', '<style type=\"text/css\">\n.ouch_menu ul li {\nlist-style: none outside none;\npadding-right: 10px;\nfloat: left;\n}\n.ouch_menu ul li:after {\ncontent: \"0000a00000a0/\";\n}\n.ouch_menu ul li:last-of-type:after {\ncontent: \"\";\n}\n.ouch_menu ul {\nmargin: 0;\n}\n.ouch_menu ul li a {\ntext-align: left;\n	text-decoration: none;\n	text-shadow: 0px 0px 10px rgba(255,255,255,0.9);\n	color: #666 !important;;\n	outline: none;\n	-o-transition:all .3s;\n	-moz-transition:all .3s;\n	-webkit-transition:all .3s;\n	-ms-transition:all .3s;\n	transition: all 0.3s ease-in-out;\n}\n.ouch_menu ul li a:hover {\n	text-shadow: 1px 1px 1px rgba(0,0,0,0.1);\n	color: #00d2ff !important;\n}\n</style>', 'stplCanvasElementText'),
('11', '11', '558', '<h1><span style=\"color: #808080;\">OUCH!</span></h1>', 'stplCanvasElementText'),
('12', '12', '564', '<h2><span style=\"color: #808080;\">sorry the page you are looking for does not exist.</span></h2>', 'stplCanvasElementText'),
('13', '13', '558', '<h3><span style=\"color: #808080;\">you can explore our site back to the navigation below.</span></h3>', 'stplCanvasElementText'),
('14', '14', '558', '[ready_stpl_menu menu=\"FHF_STPL_RAND_MENU_ID\" add_classes=\"ouch_menu\" add_styles=\"\"]', 'stplCanvasElementMenu'),
('15', '15', '558', '<div class=\"stplCanvasSocSet\" style=\"text-align: center;\"><div class=\"fhfStplCanvasSocialDesignPresentation\"><a href=\"http://www.facebook.com/ReadyECommerce\" target=\"_blank\" title=\"facebook\" class=\"fhfStplSocLink-facebook-3 fhfStplSocLinks-3\"></a><a href=\"https://twitter.com/ReadyEcommerceW\" target=\"_blank\" title=\"twitter\" class=\"fhfStplSocLink-twitter-3 fhfStplSocLinks-3\"></a><a href=\"https://plus.google.com/u/0/101846604906752638355/about\" target=\"_blank\" title=\"google_plus\" class=\"fhfStplSocLink-google_plus-3 fhfStplSocLinks-3\"></a><a href=\"https://www.youtube.com/channel/UCHfmzraXLZdZVJmCe-59pww\" target=\"_blank\" title=\"youtube\" class=\"fhfStplSocLink-youtube-3 fhfStplSocLinks-3\"></a></div><input class=\"stplCanvasSocSetId\" value=\"3\" type=\"hidden\"></div>', 'stplCanvasElementSocial'),
('16', '16', '616', '<style type=\"text/css\">\n/* Jigsaw Swing Animation */\n.animated{\n	-webkit-animation-fill-mode:both;\n	-moz-animation-fill-mode:both;\n	-ms-animation-fill-mode:both;\n	-o-animation-fill-mode:both;\n	animation-fill-mode:both;\n	-webkit-animation-duration:2s;\n	-moz-animation-duration:2s;\n	-ms-animation-duration:2s;\n	-o-animation-duration:2s;\n	animation-duration:2s;\n	-webkit-animation-delay: 2s;\n	-moz-animation-delay: 2s;\n	-ms-animation-delay: 2s;\n    animation-delay: 2s;\n}\n\n@-webkit-keyframes swing {\n	20%, 40%, 60%, 80%, 100% { -webkit-transform-origin: top center; }	20% { -webkit-transform: rotate(15deg); }	\n	40% { -webkit-transform: rotate(-10deg); }\n	60% { -webkit-transform: rotate(5deg); }	\n	80% { -webkit-transform: rotate(-5deg); }	\n	100% { -webkit-transform: rotate(0deg); }\n}\n\n@-moz-keyframes swing {\n	20% { -moz-transform: rotate(15deg); }	\n	40% { -moz-transform: rotate(-10deg); }\n	60% { -moz-transform: rotate(5deg); }	\n	80% { -moz-transform: rotate(-5deg); }	\n	100% { -moz-transform: rotate(0deg); }\n}\n\n@-o-keyframes swing {\n	20% { -o-transform: rotate(15deg); }	\n	40% { -o-transform: rotate(-10deg); }\n	60% { -o-transform: rotate(5deg); }	\n	80% { -o-transform: rotate(-5deg); }	\n	100% { -o-transform: rotate(0deg); }\n}\n\n@keyframes swing {\n	20% { transform: rotate(15deg); }	\n	40% { transform: rotate(-10deg); }\n	60% { transform: rotate(5deg); }	\n	80% { transform: rotate(-5deg); }	\n	100% { transform: rotate(0deg); }\n}\n\n.swing {\n	-webkit-transform-origin: top center;\n	-moz-transform-origin: top center;\n	-o-transform-origin: top center;\n	transform-origin: top center;\n	-webkit-animation-name: swing;\n	-moz-animation-name: swing;\n	-o-animation-name: swing;\n	animation-name: swing;\n}\n</style>', 'stplCanvasElementText'),
('17', '17', '616', '<div><img class=\"animated swing\" style=\"display: block; margin-left: auto; margin-right: auto;\" title=\"404_dark\" src=\"FHF_STPL_MOD_URLimg/common/MissingJigsawPiece/404_dark.png\" alt=\"\"></div>', 'stplCanvasElementText'),
('18', '18', '610', '<h1 style=\"text-align: center;\"><span style=\"color: #c0c0c0;\">The page you are looking for seems to be missing</span></h1>', 'stplCanvasElementText'),
('19', '19', '610', '<p style=\"text-align: center;\"><span style=\"color: #c0c0c0;\">Go back, or return to&nbsp;<a title=\"FHF_STPL_SITE_URL\" href=\"FHF_STPL_SITE_URL\"><span style=\"color: #c0c0c0;\">FHF_STPL_SITE_URL</span></a> to choose a new page.</span><br><span style=\"color: #c0c0c0;\"> Please report any broken links to <a title=\"FHF_STPL_ADMIN_EMAIL\" href=\"mailto:FHF_STPL_ADMIN_EMAIL\"><span style=\"color: #c0c0c0;\">our team</span></a>.</span></p>', 'stplCanvasElementText'),
('20', '20', '610', '[ready_stpl_search_form]', 'stplCanvasElementSearch'),
('21', '21', '616', '<style type=\"text/css\">\n/* Jigsaw Swing Animation */\n.animated{\n	-webkit-animation-fill-mode:both;\n	-moz-animation-fill-mode:both;\n	-ms-animation-fill-mode:both;\n	-o-animation-fill-mode:both;\n	animation-fill-mode:both;\n	-webkit-animation-duration:2s;\n	-moz-animation-duration:2s;\n	-ms-animation-duration:2s;\n	-o-animation-duration:2s;\n	animation-duration:2s;\n	-webkit-animation-delay: 2s;\n	-moz-animation-delay: 2s;\n	-ms-animation-delay: 2s;\n    animation-delay: 2s;\n}\n\n@-webkit-keyframes swing {\n	20%, 40%, 60%, 80%, 100% { -webkit-transform-origin: top center; }	20% { -webkit-transform: rotate(15deg); }	\n	40% { -webkit-transform: rotate(-10deg); }\n	60% { -webkit-transform: rotate(5deg); }	\n	80% { -webkit-transform: rotate(-5deg); }	\n	100% { -webkit-transform: rotate(0deg); }\n}\n\n@-moz-keyframes swing {\n	20% { -moz-transform: rotate(15deg); }	\n	40% { -moz-transform: rotate(-10deg); }\n	60% { -moz-transform: rotate(5deg); }	\n	80% { -moz-transform: rotate(-5deg); }	\n	100% { -moz-transform: rotate(0deg); }\n}\n\n@-o-keyframes swing {\n	20% { -o-transform: rotate(15deg); }	\n	40% { -o-transform: rotate(-10deg); }\n	60% { -o-transform: rotate(5deg); }	\n	80% { -o-transform: rotate(-5deg); }	\n	100% { -o-transform: rotate(0deg); }\n}\n\n@keyframes swing {\n	20% { transform: rotate(15deg); }	\n	40% { transform: rotate(-10deg); }\n	60% { transform: rotate(5deg); }	\n	80% { transform: rotate(-5deg); }	\n	100% { transform: rotate(0deg); }\n}\n\n.swing {\n	-webkit-transform-origin: top center;\n	-moz-transform-origin: top center;\n	-o-transform-origin: top center;\n	transform-origin: top center;\n	-webkit-animation-name: swing;\n	-moz-animation-name: swing;\n	-o-animation-name: swing;\n	animation-name: swing;\n}\n</style>', 'stplCanvasElementText'),
('22', '22', '616', '<div><img class=\"animated swing\" style=\"display: block; margin-left: auto; margin-right: auto;\" title=\"404_dark\" src=\"FHF_STPL_MOD_URLimg/common/MissingJigsawPiece/404_dark.png\" alt=\"\"></div>', 'stplCanvasElementText'),
('23', '23', '610', '<h1 style=\"text-align: center;\"><span style=\"color: #c0c0c0;\">The page you are looking for seems to be missing</span></h1>', 'stplCanvasElementText'),
('24', '24', '610', '<p style=\"text-align: center;\"><span style=\"color: #c0c0c0;\">Go back, or return to&nbsp;<a title=\"FHF_STPL_SITE_URL\" href=\"FHF_STPL_SITE_URL\"><span style=\"color: #c0c0c0;\">FHF_STPL_SITE_URL</span></a> to choose a new page.</span><br><span style=\"color: #c0c0c0;\"> Please report any broken links to <a title=\"FHF_STPL_ADMIN_EMAIL\" href=\"mailto:FHF_STPL_ADMIN_EMAIL\"><span style=\"color: #c0c0c0;\">our team</span></a>.</span></p>', 'stplCanvasElementText'),
('25', '25', '610', '[ready_stpl_search_form]', 'stplCanvasElementSearch');");



	}
	static public function setUsed() {
		update_option(FHF_DB_PREF. 'plug_was_used', 1);
	}
	static public function isUsed() {
		return (int) get_option(FHF_DB_PREF. 'plug_was_used');
	}
}
