<?php

/**
 * Created by PhpStorm.
 * User: yousan
 * Date: 9/30/15
 * Time: 5:57 PM
 */

class AP_Template {

	/**
	 * Fullpath
	 * ex.) /path/to/theme/pages/page-hoge.php
	 *
	 * @var string
	 */
    public $path;

	/**
	 * Filename
	 * ex.) page-hoge.php
	 *
	 * @var string
	 */
    public $filename;

    public $slug = '';
    public $title = '';
    public $status = NULL;
    public $pagename = '';

	/**
	 * Headers for template files.
	 *
	 * @static
	 * @access private
	 * @var array
	 * @see WP_Theme
	 */
	private static $file_headers = array(
		'Title' => 'Title',
	);


	/**
	 * Header data from the theme's template file.
	 *
	 * @access private
	 * @var array
	 */
	private $headers = array();



	public function __construct($path) {
        $this->path = $path;
        $this->filename = basename($path);
		$this->retrieveHeaders();
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

	private function retrieveHeaders() {
		$this->headers = get_file_data( $this->path, self::$file_headers );
		if ( isset( $this->headers['Title'] ) ) {
			$this->title = $this->headers['Title'];
		}
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
	    return AP_TemplateStatus::ENABLED;
    }

    /**
     * Retrieve title from template file.
     */
    public function getTitle() {
	    return $this->title;
    }

	/**
	 * Retrieve title from template file.
	 * Returns the first title.
	 * Title: OK!
	 *
	 * @param $content
	 *
	 * @return string
	 * @see get_file_data()
	 * @see WP_Theme::file_headers
	 */
    public function retrieveTitle($filepath) {
	    //
    }

}
