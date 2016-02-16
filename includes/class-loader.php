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

    /** @var AP_Template */
    private $template = null;


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
        $this->register_update_rewrite_rules();
        // to prevent multiple template searcher runs, make this class singleton.
        //add_action('init',  array(self::$instance, 'init'));
        add_filter( 'query_vars', array(self::$instance, 'query_vars') );
        add_action( 'template_redirect', array(self::$instance, 'template_redirect')) ;
        add_action( 'delete_option', array(self::$instance, 'delete_option'), 10, 1 );
        add_action( 'parse_query', array(self::$instance, 'setPseudoPage') );
    }

    /**
     * queryvarsのpagenameをセット
     */
    public function setPagename($pagename) {
        /** @var WP_Query */
        global $wp_query;
        $wp_query->set('pagename', $pagename);
    }

    public function setPseudoPage( $arg ) {
        /** @var WP_Query */
        global $wp_query;

        if ( $wp_query->is_page ) { // void infinite loop
            return;
        }
        $this->setTemplate();
        if ( !is_null($this->template) ) {
            $this->setGlobalPost();
            $this->setGlobalQuery();
        }

    }

    public function setGlobalQuery() {
        /**
         * @var WP_Query $wp_query
         * @var WP_Post $post
         * */
        global $wp_query, $post;

        $args = array(
            'p' => 0,
            'post_parent' => '',
            'name' => '', // must be null. The post will be seen 'post' when this value is set.
            'pagename' => sanitize_title($this->template->title),
            'author' => 0,
            'title' => $this->template->title,
        );
        $wp_query->queried_object = $post;
        $wp_query->is_home = false;
        $wp_query->is_page = true;
        // 他にも必要そうなものがあればココでセットしていく
        // $wp_query->set('is_home', false)は動かない
    }

    /**
     * Sets global $post variable.
     *
     * @see get_default_post_to_edit()
     */
    private function setGlobalPost() {
        /** @var WP_Post */
        global $post;

        /** @var stdClass */
        $postObj = new stdClass;
        $postObj->ID = 1;
        $postObj->post_author = '';
        $postObj->post_date = '';
        $postObj->post_date_gmt = '';
        $postObj->post_password = '';
        $postObj->post_title = $this->template->title;
        $postObj->post_name = sanitize_title($this->template->title);
        $postObj->post_type = 'page';
        $postObj->post_status = 'publish';
        $postObj->to_ping = '';
        $postObj->pinged = '';
        $postObj->comment_status = $this->getDefaultCommentStatus('page');
        $postObj->ping_status = $this->getDefaultCommentStatus('page', 'pingback');
        $postObj->post_pingback = get_option( 'default_pingback_flag' );
        $postObj->post_category = get_option( 'default_category' );
        $postObj->page_template = 'default';
        $postObj->post_parent = 0;
        $postObj->menu_order = 0;
        $post = new WP_Post( $postObj );

        // set filter
        // @see get_post()
        // void '$_post = $_post->filter( $filter );' returns null
        $post = sanitize_post( $post, 'raw' );
    }

    /**
     * Absorb shocks of version difference.
     * @see https://core.trac.wordpress.org/browser/tags/4.1/src/wp-admin/includes/post.php#L533
     */
    private function getDefaultCommentStatus($arg1=null, $arg2=null) {
        if (function_exists('get_default_comment_status')) {
            return get_default_comment_status( $arg1, $arg2 );
        } else {
            return get_option('default_comment_status');
        }
    }

    /**
     * rewrite_ruleを更新するタイミングを決定する
     * オプションで「積極的な更新」を選択したときにはregistered_post_type
     *
     * thanks! the master of rewrite_rules!
     * @link https://github.com/yousan/add-page-from-template/issues/1#event-456557115
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

    /**
     * Find and set Template.
     * We should found template before parse_query()
     */
    private function setTemplate() {
        global $wp_query;

        foreach($this->templates as $template) {
            if ( isset( $wp_query->query[ $template->getTemplateSlug() ] ) ) {
                $this->template = $template;
            }
        }
    }

    /**
     * 実際に表示させているのはココ！
     */
    public function template_redirect() {
        global $wp_query;
        foreach($this->templates as $template) {
            if (isset($wp_query->query[$template->getTemplateSlug()])) {
                $templatePath = apply_filters("page_template", $template->path);
                $this->setPagename($template->pagename);
                $this->setGlobalPost($template);
                add_action( 'parse_query', array(self::$instance, 'setGlobalQuery') );
                if ($templatePath = apply_filters('template_include', $templatePath)) {
                    include($templatePath);
                }
                exit; // Stop!
            }
        }
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