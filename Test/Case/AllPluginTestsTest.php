<?php
/**
 * group test - MarkupParsers
 */
class AllPluginTestsTest extends PHPUnit_Framework_TestSuite {

	/**
	 * suite method, defines tests for this suite.
	 *
	 * @return void
	 */
	public static function suite() {
		$Suite = new CakeTestSuite('All Plugin tests');
		$path = dirname(__FILE__);
		$Suite->addTestDirectory($path . DS . 'View' . DS . 'Helper');
		$Suite->addTestDirectory($path . DS . 'Parser');
		return $Suite;
	}
}
