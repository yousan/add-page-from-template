<?php

/**
 * Created by PhpStorm.
 * User: yousan
 * Date: 2/7/16
 * Time: 11:08 PM
 */
class AP_Template_Test extends WP_UnitTestCase {

	public function testRetrieveTitle() {
		$template = new AP_Template(__DIR__ . '/pages/page-hoge.php');
		$this->assertEquals('hogeee', $template->title);

		$template = new AP_Template(__DIR__ . '/pages/page-fuga.php');
		$this->assertEquals('FuGa!', $template->title);
	}
}