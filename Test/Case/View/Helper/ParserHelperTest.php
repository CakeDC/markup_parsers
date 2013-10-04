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

App::uses('Controller', 'Controller');
App::uses('View', 'View');
App::uses('ParserHelper', 'MarkupParsers.View/Helper');

/**
 * Parser helper test case
 *
 */
class ParserHelperTest extends CakeTestCase {

/**
 * SetUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		Configure::write('Parsers', array(
			'markdown' => array(
				'name' => 'Markdown',
				'className' => 'MarkupParsers.Markdown'),
			'bbcode' => array(
				'name' => 'BBCode',
				'className' => 'MarkupParsers.Bbcode'),
			'html' => array(
				'name' => 'Html',
				'className' => 'MarkupParsers.Html'),
			'textile' => array(
				'name' => 'Textile',
				'className' => 'MarkupParsers.Textile')));

		$View = new View(new Controller());
		$this->Parser = new ParserHelper($View);
	}

/**
 * testParse
 *
 * @return void
 */
	public function testParse() {
		$string = '# Foobar';
		$result = $this->Parser->parse($string, 'markdown');
		$this->assertEquals(array('<h1>Foobar</h1>'), $result);

		$string = 'h1. Foobar';
		$result = $this->Parser->parse($string, 'textile');
		$this->assertEquals(array('<h1>Foobar</h1>'), $result);

		$string = '[b]Foobar[/b]';
		$result = $this->Parser->parse($string, 'bbcode');
		$this->assertEquals(array('<strong>Foobar</strong>'), $result);

		$string = '<b>Foobar</b><!-- Page separator --><b>Barfoo</b>';
		$result = $this->Parser->parse($string, 'html');

		$this->assertEquals(array('<b>Foobar</b>', '<b>Barfoo</b>'), $result);
	}

}
