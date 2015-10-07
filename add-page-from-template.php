<?php
/**
 * Plugin Name: Add Page From Template
 * Plugin URI: https://github.com/yousan/add-page-from-template
 * Description: PHPのテンプレートファイルから固定ページを生成します。
 * Description(en): Add pages from template files.
 * Version: 0.0.0.1
 * Author: Yousan_O
 * Author URI: http://www.l2tp.org
 * License: GPL2
 */



main();
function main() {
    include_once 'includes/class-templatesearcher.php';
    include_once 'includes/class-loader.php';

    $templates = AP_TemplateSearcher::getTemplates();
    $loader = AP_Loader::getInstance($templates);
}
