<?php
/**
 * Group test - MarkupParsers
 */
class AllMarkupParsersTestsTest extends PHPUnit_Framework_TestSuite {

	/**
	 * Suite method, defines tests for this suite.
	 *
	 * @return void
	 */
	public static function suite() {
		$Suite = new CakeTestSuite('All MarkupParsers tests');
		$path = dirname(__FILE__);
		$Suite->addTestDirectory($path . DS . 'View' . DS . 'Helper');
		$Suite->addTestDirectory($path . DS . 'Lib' . DS . 'Parser');
		return $Suite;
	}

}
