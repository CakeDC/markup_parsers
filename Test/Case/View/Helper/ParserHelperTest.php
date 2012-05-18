<?php
/**
 * Copyright 2010-2012, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010-2012, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('View', 'View');
App::uses('AppHelper', 'View/Helper');
App::uses('ParserHelper', 'MarkupParsers.View/Helper');

/**
 * Parser helper test case
 *
 * @package markup_parsers
 * @subpackage markup_parsers.tests.cases.libs
 */
class ParserHelperTest extends CakeTestCase {

/**
 * setUp method
 *
 * @access public
 * @return void
 */
	function setup() {
		Configure::write('Parsers', array(
			'doc_markdown' => array(
				'MarkdownParser',
				'MarkupParsers.Lib/Parser'),
			'bbcode' => array(
				'BbcodeParser',
				'MarkupParsers.Lib/Parser'),
			'html' => array(
				'HtmlParser',
				'MarkupParsers.Lib/Parser')));

		$controller = null;
		$View = new View($controller);
		$this->Parser = new ParserHelper($View);
	}

/**
 * tearDown method
 *
 * @access public
 * @return void
 */
	function tearDown() {
		ClassRegistry::flush();
		unset($this->Parser);
	}

/**
 * testParse
 *
 * @return void
 */
	public function testParse() {
		$string = '# Foobar';
		$result = $this->Parser->parse($string, 'doc_markdown');
		$this->assertEqual($result, array('<h1>Foobar</h1>'));

		$string = '[b]Foobar[/b]';
		$result = $this->Parser->parse($string, 'bbcode');
		$this->assertEqual($result, array('<strong>Foobar</strong>'));
	}

}