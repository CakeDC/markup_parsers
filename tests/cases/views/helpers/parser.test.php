<?php
/**
 * Copyright 2010, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::import('Core', array('Helper', 'AppHelper'));
App::import('Helper', array('MarkupParsers.Parser'));

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
				'name' => 'Markdown',
				'className' => 'MarkupParsers.DocMarkdown'),
			'bbcode' => array(
				'name' => 'BBCode',
				'className' => 'MarkupParsers.Bbcode'),
			'html' => array(
				'name' => 'Html',
				'className' => 'MarkupParsers.Html')));

		$this->Parser = new ParserHelper();
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

		$string = 'Foobar page 1' . BbcodeParser::$pageSeparator . 'Foobar page 2';
		$result = $this->Parser->parse($string, 'bbcode');
		$this->assertEqual($result, array('Foobar page 1', 'Foobar page 2'));
	}

/**
 * Test parseAsString method
 *
 * @return void
 */
	public function testParseAsString() {
		$string = '[b]Foobar[/b]';
		$result = $this->Parser->parseAsString($string, 'bbcode');
		$this->assertEqual($result, '<strong>Foobar</strong>');

		$string = 'Foobar page 1' . BbcodeParser::$pageSeparator . 'Foobar page 2';
		$result = $this->Parser->parseAsString($string, 'bbcode');
		$this->assertEqual($result, 'Foobar page 1Foobar page 2');

		$result = $this->Parser->parseAsString($string, 'bbcode', ' / ');
		$this->assertEqual($result, 'Foobar page 1 / Foobar page 2');
	}
}