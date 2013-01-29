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

App::uses('TextileParser', 'MarkupParsers.Parser');

/**
 * Textile test case
 *
 * @package markup_parsers
 * @subpackage markup_parsers.tests.cases.libs
 */
class TextileParserTest extends CakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setup() {
		$this->Parser = new TextileParser();
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		ClassRegistry::flush();
		unset($this->Parser);
	}

/**
 * testParse
 *
 * @return void
 * @access public
 */
	public function testParse() {
		$testText = <<<TEXT
h1. test

* One
* Two
* Three
TEXT;
		$expected = <<<HTML
<h1>test</h1>

	<ul>
		<li>One</li>
		<li>Two</li>
		<li>Three</li>
	</ul>
HTML;
		$result = $this->Parser->parse($testText);
        //debug($result); die();
		$this->assertTextEquals($expected, $result);
	}


}

