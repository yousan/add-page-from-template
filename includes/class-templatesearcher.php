<?php
/**
 * Created by PhpStorm.
 * User: yousan
 * Date: 9/30/15
 * Time: 5:09 PM
 */

if (!class_exists('WP_AddRewriteRules')):

    include_once 'class-template.php';

    class AP_TemplateSearcher
    {

        // フルパスを保存する
        static private $path = '';

        public function __construct() {
            $this->setPath();
        }

        private static function setPath() {
            self::$path = get_stylesheet_directory().'/pages/';
        }

        /**
         * @return AP_Template[]
         */
        public static function getTemplates() {
            self::setPath();
            if (!is_dir(self::$path)) {
                return array();
            }

            $rets = array();
            foreach(glob(self::$path.'page-*.php') as $file) {
                $rets[] = new AP_Template($file);
            }
            return $rets;
        }
    }


endif;