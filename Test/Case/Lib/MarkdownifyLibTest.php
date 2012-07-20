<?php
App::uses('MarkdownifyLib', 'MarkupParsers.Lib');

/**
 * 2012-02-08 ms
 */
class MarkdownifyLibTest extends CakeTestCase {

	public $Markdownify;
	
	
	public function setUp() {
		parent::setUp();
		
		$this->Markdownify = new MarkdownifyLib();
	}

	public function testObject() {
		$this->assertTrue(is_a($this->Markdownify, 'MarkdownifyLib'));
	}

	public function testBasicParsing() {
		$html = <<<HTML
<h1>header one</h1>
<p>
Some Text
</p>

<h2>header two</h2>

<ul>
<li>One</li>
<li>Two</li>
<li>Three</li>
</ul>

<p></p>
HTML;

		$expected = <<<TEXT
# header one

Some Text 

## header two

*   One
*   Two
*   Three
TEXT;
		
		$res = $this->Markdownify->parseString($html);
		//debug($res);
		//ob_flush();
		$this->assertTextEquals($expected, trim($res));
	}

	// not quite the expected result, though...
	public function testComplexParsing() {
		$html = <<<HTML
<h1>header one</h1>

Some Text

<h2>header two</h2>
<h3>header three</h3>
<ol>
<li>One</li>
<li>Two<ul><li>Two-a</li><li>Two-b</li></ul></li>
<li>Three</li>
</ol>

<p>Some <code>inline code</code> and <b>bold text</b> as well as <i>italic text</i> and of course <b><i>both</i></b>!</p>

<p>And some <a href="http://www.google.com">google-link</a>.</p>

HTML;

		// not quite the expected result, though (text without p is problematic)...
		$expected = <<<TEXT
# header one Some Text 

## header two

### header three

1.  One
2.  Two
    *   Two-a
    *   Two-b
3.  Three

Some `inline code` and **bold text** as well as *italic text* and of course ***both***!

And some [google-link][1].

 [1]: http://www.google.com
TEXT;
		
		$res = $this->Markdownify->parseString($html);
		ob_flush();
		$this->assertEquals($expected, trim($res));
	}

	public function assertTextEquals($string, $expected) {
		return $this->assertEquals($string, $expected);
	}
	
}
