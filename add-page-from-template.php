<?php
/**
 * Plugin Name: Add Page From Template
 * Plugin URI: https://github.com/yousan/add-page-from-template
 * Description(en): Add pages from template files.
 * Description: PHPのテンプレートファイルから固定ページを生成します。
 * Version: 0.0.0.1
 * Author: Yousan_O
 * Author URI: http://www.l2tp.org
 * License: GPL2
 */


define('APFT_I18N_DOMAIN', 'add-post-from-template');

function apft_autoloader($classname) {
    if (0 !== (strpos($classname, 'AP_'))) {
        return;
    }
    // to lower, remove AP_ prefix. ex) AP_Opiton => option
    $classname = strtolower(str_replace('AP_', '', $classname));
    $dirpath = dirname( __FILE__ ).'/includes/';
    $filepath = $dirpath . 'class-'.$classname.'.php';
    if (file_exists($filepath)) {
        include $filepath;
    }
}
spl_autoload_register('apft_autoloader');


add_page_from_template();

function add_page_from_template() {
    include_once 'includes/class-templatesearcher.php';
    include_once 'includes/class-loader.php';

    $templates = AP_TemplateSearcher::getTemplates();
    $loader = AP_Loader::getInstance($templates);
}

function admin_init()
{
    if(is_admin()) {
        $apOption = new AP_Option();
    }
}

add_action('init', 'admin_init');

