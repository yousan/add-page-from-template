<?php

/**
 * Created by PhpStorm.
 * User: yousan
 * Date: 9/30/15
 * Time: 5:57 PM
 */

class AP_Template {

    public $path;

    public $filename;

    public $slug = '';
    public $title = '';
    public $status = NULL;
    public $pagename = '';

    public function __construct($path) {
        $this->path = $path;
        $this->filename = basename($path);
        $pattern = '#^(?<path>.*)page-(?<unislug>[^\.]+)\.php$#';

        // abspath => relpath
        $fullBaseDir = untrailingslashit(get_stylesheet_directory().DIRECTORY_SEPARATOR.
            AP_Option::get_('base_dir').DIRECTORY_SEPARATOR);
        $fullBaseDir = preg_replace('#/+#','/', $fullBaseDir); // remove duplicate slash

        $relpath = str_replace($fullBaseDir, '', $this->path);
        if ( !empty($this->filename) &&
             !empty($relpath) &&
            preg_match($pattern, $relpath, $match)) { // page-hoge.php => hoge
            $slug = stripslashes($match['path'] . $match['unislug']);
            $this->slug = preg_replace('#^/#', '', $slug);
            $this->pagename = $match['unislug'];
        } else {
            throw new Exception('Invalid Template Path'); // no one shows, just return
        }
        $this->status = $this->getStatus();
    }

    /**
     * Returns a template slug name.
     * pages/page-hoge.php => hoge
     * pages/hoge/page-fuga.php => fuga
     *
     * @return string
     */
    public function getTemplateSlug() {
        return $this->slug;
    }

    private function getStatus() {
        /** @var WP_Rewrite */
        //$wp_rewrite;
        // incomplete
        return AP_TemplateStatus::ENABLED;
    }

    /**
     * Retrieve title from template file.
     */
    public function getTitle() {
        var_dump($this);
    }

    public function retrieveTitle($content) {
        return '';
    }

}
