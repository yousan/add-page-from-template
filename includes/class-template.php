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
    public $status = NULL;

    public function getTemplateSlug() {
        $pattern = '/.*\/page-(?P<slug>.*)\.php$/';
        if (preg_match($pattern, $this->path, $match)) {
            return $match['slug'];
        } else { // nothing to mach

        }
    }

    public function __construct($path) {
        $this->path = $path;
        $this->filename = basename($path);
        if ( $this->filename ) { // page-hoge.php => hoge
            $pattern = '/^page-(.*)\.php$/';
            $replacement = '${1}';
            $this->slug = preg_replace($pattern, $replacement, $this->filename);
        }
        $this->status = $this->getStatus();
    }

    private function getStatus() {
        return AP_TemplateStatus::ENABLED;
    }
}
