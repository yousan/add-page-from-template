<?php
/**
 * Plugin Name: Add Page From Template
 * Plugin URI: (プラグインの説明と更新を示すページの URI)
 * Description: (プラグインの短い説明)
 * Version: (プラグインのバージョン番号。例: 1.0)
 * Author: (プラグイン作者の名前)
 * Author URI: (プラグイン作者の URI)
 * License: (ライセンス名の「スラッグ」 例: GPL2)
 *
 * Plugin Name: Add Page From Template
 */



main();
function main() {
    include_once 'includes/class-templatesearcher.php';
    include_once 'includes/class-loader.php';

    $templates = AP_TemplateSearcher::getTemplates();
    $loader = AP_Loader::getInstance($templates);
}
