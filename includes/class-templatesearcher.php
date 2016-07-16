<?php
/**
 * TemlateSearchar
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
            self::$path = (get_stylesheet_directory() . DIRECTORY_SEPARATOR .
                AP_Option::get_('base_dir'));
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
            $basedir_path = preg_replace('#/+$#', '', self::$path); // remove last slash
            $files = self::filesToArray($basedir_path);
            foreach ($files as $file) {
                try {
                    $template = new AP_Template($file);
                    $rets[] = $template;
                } catch (Exception $e){
                    // do nothing. just invalid filename.
                }
            }
            return $rets;
        }

        /**
         * recursive search.
         * Return only file.
         *
         * @param $dir
         * @return string[]
         */
        private static function filesToArray($dir)
        {
            $result = array();
            $cdir = scandir($dir);
            foreach ( $cdir as $key => $value ){
                if (!in_array($value, array('.', '..'))) {
                    if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                        $result = array_merge($result,
                            static::filesToArray($dir . DIRECTORY_SEPARATOR . $value));
                    } else {
                        $result[] = $dir . DIRECTORY_SEPARATOR .$value;
                    }
                }
            }
            return $result;
        }
    }


endif;