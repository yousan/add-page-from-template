<?php

/**
 * Created by PhpStorm.
 * User: yousan
 * Date: 9/30/15
 * Time: 6:52 PM
 * Thankkkkkks! https://firegoby.jp/archives/5309
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
     *
     */
    public static function getInstance($templates) {
        $classname = __CLASS__;
        self::$instance = new $classname($templates);
        self::$instance->initialize();

        return self::$instance;
    }

    /**
     *
     */
    private function initialize() {
        register_activation_hook(__FILE__, array(self::$instance, 'activation_callback'));
        register_deactivation_hook(__FILE__,  array(self::$instance, 'deactivation_callback'));

        $this->register_update_rewrite_rules();
        // to prevent multiple template searcher runs, make this class singleton.
        //add_action('init',  array(self::$instance, 'init'));
        add_filter('query_vars', array(self::$instance, 'query_vars'));
        add_action('template_redirect', array(self::$instance, 'template_redirect'));
        add_action('delete_option', array(self::$instance, 'delete_option'), 10, 1 );
    }

    /**
     * rewrite_ruleを更新するタイミングを決定する
     * オプションで「積極的な更新」を選択したときにはregistered_post_type
     *
     * thanks! the master of rewrite_rules!
     * https://github.com/yousan/add-page-from-template/issues/1#event-456557115
     */
    private function register_update_rewrite_rules() {
        $is_aggressive = AP_Option::get_('aggressive');
        if ($is_aggressive) {
            add_action('registered_post_type', array(self::$instance, 'update_rewrite_rules'));
        }
    }

    public function update_rewrite_rules()
    {
        foreach($this->templates as $template) {
            add_rewrite_endpoint($template->getTemplateSlug(), EP_ROOT);
        }
        flush_rewrite_rules();
    }


    public function query_vars($vars) {
        foreach($this->templates as $template) {
            $vars[] = $template->getTemplateSlug();
        }
        return $vars;
    }


    public function template_redirect() {
        global $wp_query;
        foreach($this->templates as $template) {
            if (isset($wp_query->query[$template->getTemplateSlug()])) {
                $template = $template->path;
                apply_filters("page_template", $template);
                if ($template = apply_filters('template_include', $template)) {
                    include($template);
                }
                exit; // Stop!
            }
        }
    }


    public function activation_callback() {
        /*
         * プラグインが有効化されていることをオプションに保存する
         * この時点でis_plugin_active()の戻り値はfalse
         */
        update_option( 'apft_plugin_activated', true );
        flush_rewrite_rules();
    }

// 無効化時の処理
    public function deactivation_callback() {
        /*
         * プラグインが無効化された！
         * この時点でis_plugin_active()の戻り値はtrue
         */
        delete_option( 'apft_plugin_activated' );
        flush_rewrite_rules();
    }

// delete_optionフックのコールバック関数
    public function delete_option($option){
        /*
         * flush_rewrite_rules()が発火&プラグインが有効化されている場合に限りrewrite ruleを再登録
         * register_activation_hook()発火時にはまだis_plugin_active()の戻り値はtrueのままなのでget_option()の値で評価する必要がある。
         */
        if ( 'rewrite_rules' === $option && get_option('apft_plugin_activated') ) {
            foreach($this->templates as $template) {
                add_rewrite_endpoint($template->getTemplateSlug(), EP_ROOT);
            }
        }
    }

    public function destroy() {
        // 未実装
    }

}