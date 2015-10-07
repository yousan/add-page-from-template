<?php

/**
 * Created by PhpStorm.
 * User: yousan
 * Date: 9/30/15
 * Time: 5:57 PM
 */

class AP_Template {
    public $path;

    public function getTemplateSlug() {
        $pattern = '/.*\/page-(?P<slug>.*)\.php$/';
        if (preg_match($pattern, $this->path, $match)) {
            return $match['slug'];
        } else { // nothing to mach

        }
    }

    public function __construct($path) {
        $this->path = $path;
    }
}
