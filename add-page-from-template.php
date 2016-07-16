<?php
/**
 * Plugin Name: Add Page From Template
 * Plugin URI: https://github.com/yousan/add-page-from-template
 * Description(en): Add pages from template files.
 * Description: Creates virtural page from template file.
 * Version: 0.4.4.1
 * Author: Yousan_O
 * Author URI: http://www.l2tp.org
 * License: GPL2
 */
if ( ! class_exists( 'AddPageFromTemplate' ) ) {

    //Start Plugin
    if ( function_exists( 'add_filter' ) ) {
        add_action( 'plugins_loaded', array( 'AddPageFromTemplate', 'getInstance' ), 11 );
    }
    register_activation_hook(__FILE__,   array('AddPageFromTemplate', 'activation_callback'));
    register_deactivation_hook(__FILE__, array('AddPageFromTemplate', 'deactivation_callback'));

    function activation_callback() {
        /*
         * プラグインが有効化されていることをオプションに保存する
         * この時点でis_plugin_active()の戻り値はfalse
         */
        //AP_Loader::getInstance();
        //update_option( 'apft_plugin_activated', true );
        //flush_rewrite_rules();
    }


    final class AddPageFromTemplate
    {

        private static $instance = NULL;

        /** @var AP_Loader */
        private $loader = NULL;


        private function __construct()
        {
            //auto loader
            spl_autoload_register(array($this, 'autoloader'));
            $templates = AP_TemplateSearcher::getTemplates();
            $this->loader = AP_Loader::getInstance($templates);


            // for admin panel
            if (is_admin()) {
                $apOption = new AP_Option();
            }
        }

        /**
         * プラグイン有効化時に一度だけリライトルールを作り直す
         */
        public static function activation_callback() {
            self::getInstance(); // We need instance
            if ( self::$instance instanceof AP_Loader ) {
                self::$instance->loader->update_rewrite_rules();
                update_option( 'apft_plugin_activated', true );
                flush_rewrite_rules();
            }
        }

        /**
         *
         */
        public static function deactivation_callback() {
            /*
             * プラグインが無効化された
             * この時点でis_plugin_active()の戻り値はtrue
             */
            delete_option( 'apft_plugin_activated' );
            flush_rewrite_rules();
        }



        private function registerActions()
        {
            //spl_autoload_register('apft_autoloader');
        }

        public static function getInstance()
        {
            if (NULL === self::$instance) {
                self::$instance = new self;
            }
            return self::$instance;
        }

        public static function loadTextDomain()
        {
            $domain = 'apft';

            if (is_textdomain_loaded($domain)) {
                return;
            }


            $locale = apply_filters('plugin_locale', get_locale(), $domain);
            $mofile = $domain . '-' . $locale . '.mo';

            // load translation from WordPress plugins language folder
            if (load_textdomain($domain, WP_LANG_DIR . '/plugins/' . $mofile)) {
                return;
            }

            // load translation from plugin folder
            load_textdomain($domain, dirname(__FILE__) . '/languages/' . $mofile);
        }

        private function autoloader($classname)
        {
            if (0 !== (strpos($classname, 'AP_'))) {
                return;
            }
            // to lower, remove AP_ prefix. ex) AP_Opiton => option
            $classname = strtolower(str_replace('AP_', '', $classname));
            $dirpath = dirname(__FILE__) . '/includes/';
            $filepath = $dirpath . 'class-' . $classname . '.php';
            if (file_exists($filepath)) {
                include $filepath;
            } else {
            }
        }
    }
}


