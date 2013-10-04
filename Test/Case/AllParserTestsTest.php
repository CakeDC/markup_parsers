<?php
/**
 * Group test - MarkupParsers
 */
class AllHelperTestsTest extends PHPUnit_Framework_TestSuite {

	/**
	 * Suite method, defines tests for this suite.
	 *
	 * @return void
	 */
	public static function suite() {
		$Suite = new CakeTestSuite('All Parser tests');
		$path = dirname(__FILE__);
		$Suite->addTestDirectory($path . DS . 'Lib' . DS . 'Parser');
		return $Suite;
	}
}
