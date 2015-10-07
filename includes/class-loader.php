<?php

/**
 * Created by PhpStorm.
 * User: yousan
 * Date: 9/30/15
 * Time: 6:52 PM
 * https://firegoby.jp/archives/5309
 */
class AP_Loader
{
    /** @var AP_Template[]  */
    private $templates = array();

    /** @var AP_Loader */
    private static $instance = null;


    public function __construct($templates){
        $this->templates = $templates;
    }

    /**
     * これ外からインスタンスをもらわずに完結できないかな
     */
    public static function getInstance($templates) {
        $classname = __CLASS__;
        self::$instance = new $classname($templates);
        self::$instance->initialize();

        return self::$instance;
    }

    private function initialize() {
        register_activation_hook(__FILE__, array(self::$instance, 'activation_callback'));
        register_deactivation_hook(__FILE__,  array(self::$instance, 'deactivation_callback'));


        add_action('init',  array(self::$instance, 'init'));
        add_filter('query_vars', array(self::$instance, 'query_vars'));
        add_action('template_redirect', array(self::$instance, 'template_redirect'));
        // 他のプラグインや管理者の操作によってflush_rewrite_rules()が発火した際にこのプラグイン用のrewrite ruleを再登録する
        add_action('delete_option', array(self::$instance, 'delete_option'), 10, 1 );


        $className = __CLASS__;
        //add_action('template_redirect', array($className, 'template_redirect'));

    }

    public function init()
    {
        foreach($this->templates as $template) {
            add_rewrite_endpoint($template->getTemplateSlug(), EP_ROOT);
        }
        //add_rewrite_endpoint('evnets', EP_ROOT);

        // Flushing the rewrite rules is an expensive operation, there are tutorials and examples that suggest executing it on the 'init' hook. This is bad practice.
        // https://codex.wordpress.org/Function_Reference/flush_rewrite_rules

        // todo: option化
        flush_rewrite_rules();
    }


    public function query_vars($vars) {
        //$templates = AP_TemplateSearcher::getTemplates();
        //foreach($templates as $template) {
        foreach($this->templates as $template) {
            $vars[] = $template->getTemplateSlug();
        }
        return $vars;
    }


    public function template_redirect() {
        global $wp_query;
        //$templates = AP_TemplateSearcher::getTemplates();
        //var_dump($wp_query);
        //foreach($templates as $template) {
        foreach($this->templates as $template) {
            if (isset($wp_query->query[$template->getTemplateSlug()])) {
                $template = $template->path;
                apply_filters("page_template", $template);
                if ($template = apply_filters('template_include', $template)) {
                    include($template);
                }
                exit; // WordPressの処理を止める！
            }
        }
    }


// 有効化時の処理
    public function activation_callback() {
        /*
         * プラグインが有効化されていることをオプションに保存する
         * この時点でis_plugin_active()の戻り値はfalse
         */
        update_option( 'my_plugin_activated', true );
        flush_rewrite_rules();
    }

// 無効化時の処理
    public function deactivation_callback() {
        /*
         * プラグインが無効化された！
         * この時点でis_plugin_active()の戻り値はtrue
         */
        delete_option( 'my_plugin_activated' );
        flush_rewrite_rules();
    }

// delete_optionフックのコールバック関数
    public function delete_option($option){
        /*
         * flush_rewrite_rules()が発火&プラグインが有効化されている場合に限りrewrite ruleを再登録
         * register_activation_hook()発火時にはまだis_plugin_active()の戻り値はtrueのままなのでget_option()の値で評価する必要がある。
         */
        if ( 'rewrite_rules' === $option && get_option('my_plugin_activated') ) {
            foreach($this->templates as $template) {
                add_rewrite_endpoint($template->getTemplateSlug(), EP_ROOT);
            }
        }
    }

    public function destroy() {
        // 未実装
    }

}