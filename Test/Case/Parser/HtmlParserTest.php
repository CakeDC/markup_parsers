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
App::import('Lib', array('MarkupParsers.HtmlParser'));

/**
 * HtmlParser test case
 *
 * @package markup_parsers
 * @subpackage markup_parsers.tests.cases.libs
 */
class HtmlParserTest extends CakeTestCase {

/**
 * setUp method
 *
 */
	public function setup() {
		$this->Parser = new HtmlParser();
	}

/**
 * tearDown method
 *
 */
	public function tearDown() {
		ClassRegistry::flush();
		unset($this->Parser);
	}

/**
 * testParse
 *
 * @return void
 */
	public function testParse() {
		$string = 'This is some <strong>Html text</strong>';
		$result = $this->Parser->parse($string);
		$this->assertEqual($result, array($string));

		$string = <<<HTML
<h1>This is a first page</h1>
<p>
	With text and <script language="javascript">alert('scripts');</script>
</p>
<!-- Page separator -->
<h1>This is a second page</h1>
<p>
	With other things
</p>
HTML;
		$result = Sanitize::stripWhiteSpace($this->Parser->parse($string));
		$expected = array(
			"<h1>This is a first page</h1><p>With text and </p>",
			"<h1>This is a second page</h1><p>With other things</p>");
		$this->assertEqual($result, $expected);
	}

/**
 * testParse text containing some code
 *
 * @return void
 */
	public function testParseCode() {
		$string = '<h1>This is a code page</h1><code><?php echo "Hello world!"; ?></code><code><?php echo "Hello world again!"; ?></code><p>And other things</p>';
		$result = $this->Parser->parse($string);
		$expected = array(
			'<h1>This is a code page</h1>'
			. highlight_string('<?php echo "Hello world!"; ?>', true)
			. highlight_string('<?php echo "Hello world again!"; ?>', true)
			. '<p>And other things</p>');
		$this->assertEqual($result, $expected);

		$result = $this->Parser->parse($string, array('highlight_code' => false));
		
		$expected = array(
			'<h1>This is a code page</h1>'.
			'<code>&lt;?php&nbsp;echo&nbsp;&quot;Hello&nbsp;world!&quot;;&nbsp;?&gt;</code>'.
			'<code>&lt;?php&nbsp;echo&nbsp;&quot;Hello&nbsp;world&nbsp;again!&quot;;&nbsp;?&gt;</code>'.
			'<p>And other things</p>'
		);
		$this->assertEqual($result, $expected);
	}

}