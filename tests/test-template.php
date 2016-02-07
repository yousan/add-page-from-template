<?php

/**
 * Created by PhpStorm.
 * User: yousan
 * Date: 2/7/16
 * Time: 11:08 PM
 */
class AP_Template_Test extends WP_UnitTestCase {

	public function testRetrieveTitle() {
		$template = new AP_Template(__DIR__ . 'pages/page-hoge.php');
		$content = <<< EOF
/**
 * Title: hogeee
 */
EOF;
		$title = $template->retrieveTitle($content);
		$this->assertEquals('hogeee', $title);
	}
}