<?php
/**
 * Created by PhpStorm.
 * User: yousan
 * Date: 10/5/15
 * Time: 4:41 PM
 */

// インスタンス化するとwp_queryが違うのでとりあえず…

add_action('init',  function()
{
    $templates = AP_TemplateSearcher::getTemplates();
    foreach($templates as $template) {
        add_rewrite_endpoint($template->getTemplateSlug(), EP_ROOT);
    }
    //add_rewrite_endpoint('hoge',  EP_ROOT);
    //add_rewrite_endpoint('evnets', EP_ROOT);
    flush_rewrite_rules(); // 毎度呼び出すとやばそう
});


add_filter('query_vars', function ($vars) {
    $templates = AP_TemplateSearcher::getTemplates();
    //foreach($this->templates as $template) {
    foreach($templates as $template) {
        //var_dump($template->getTemplateSlug());
        //$vars[] = $template->getTemplateSlug();
    }
    $vars[] = 'hoge';
    //$vars[] = 'events';
    return $vars;
});


add_action('template_redirect', function() {
    global $wp_query;
    $templates = AP_TemplateSearcher::getTemplates();
    foreach($templates as $template) {
        if (isset($wp_query->query[$template->getTemplateSlug()])) {
            $template = $template->path;
            apply_filters("page_template", $template);
            if ($template = apply_filters('template_include', $template)) {
                include($template);
            }
            exit; // WordPressの処理を止める！
        }
    }
});


// 有効化時の処理
register_activation_hook(__FILE__,function() {
    /*
     * プラグインが有効化されていることをオプションに保存する
     * この時点でis_plugin_active()の戻り値はfalse
     */
    update_option( 'my_plugin_activated', true );
    flush_rewrite_rules();
});

// 無効化時の処理
register_deactivation_hook(__FILE__, function() {
    /*
     * プラグインが無効化された！
     * この時点でis_plugin_active()の戻り値はtrue
     */
    delete_option( 'my_plugin_activated' );
    flush_rewrite_rules();
});

add_action('delete_option', function($option){
    /*
     * flush_rewrite_rules()が発火&プラグインが有効化されている場合に限りrewrite ruleを再登録
     * register_activation_hook()発火時にはまだis_plugin_active()の戻り値はtrueのままなのでget_option()の値で評価する必要がある。
     */
    if ( 'rewrite_rules' === $option && get_option('my_plugin_activated') ) {
        add_rewrite_endpoint( 'hoge', EP_ROOT );
        add_rewrite_endpoint( 'events', EP_ROOT );
    }
});
