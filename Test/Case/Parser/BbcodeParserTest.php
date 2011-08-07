<?php
/**
 * Copyright 2010-2011, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010-2011, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::import('Lib', array('MarkupParsers.BbcodeParser'));

/**
 * HtmlParser test case
 *
 * @package markup_parsers
 * @subpackage markup_parsers.tests.cases.libs
 */
class BbcodeParserTest extends CakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setup() {
		$this->Parser = new BbcodeParser();
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
[h1]Test[/h1]
This [b]is[/b] some [i]nice[/i] Text.
With an URL to [url url=http://www.cakedc.com]cakedc.com[/url].
[s]Strike through text[/s]
TEXT;
		$expected = <<<HTML
<h1>Test</h1>
This <strong>is</strong> some <span style="font-style:italic;">nice</span> Text.<br />
With an URL to <a href="http://www.cakedc.com">cakedc.com</a>.<br />
<del>Strike through text</del>
HTML;
		$result = $this->Parser->parse($testText);
		$this->assertEqual($result, $expected);
	}

/**
 * test Parse BBCode with a not closed tag 
 *
 * @return void
 * @access public
 */
	public function testParseNotClosedTags() {
		$testText = <<<TEXT
[h1]Test[/h1]
This is an [i]incorrect[/i BBCode markup.
TEXT;
		$expected = <<<HTML
<h1>Test</h1>
This is an [i]incorrect[/i BBCode markup.
HTML;
		$result = $this->Parser->parse($testText);
		$this->assertEqual($result, $expected);
		
		$testText = <<<TEXT
[h1]Test[/h1]
A first [i]italic text[/i]
This is an [i]incorrect[/i BBCode [i]markup[/i].
TEXT;
		$expected = <<<HTML
<h1>Test</h1>
A first <span style="font-style:italic;">italic text</span>
This is an <span style="font-style:italic;">incorrect[/i BBCode [i]markup</span>.
HTML;
		$result = $this->Parser->parse($testText);
		$this->assertEqual($result, $expected);
	}

}
?>