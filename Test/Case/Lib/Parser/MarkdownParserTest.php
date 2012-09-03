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

App::uses('MarkdownParser', 'MarkupParsers.Parser');

/**
 * HtmlParser test case
 *
 * @package markup_parsers
 * @subpackage markup_parsers.tests.cases.libs
 */
class MarkdownParserTest extends CakeTestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setup() {
		$this->Parser = new MarkdownParser();
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
# test #

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

<p></p>
HTML;
		$result = $this->Parser->parse($testText);
		$this->assertTextEquals($expected, $result);
	}

/**
 * test emphasis and bold elements.
 *
 * @return void
 */
	public function testEmphasisAndBold() {
		$text = 'Normal text *emphasis text* normal *emphasis* normal.';
		$result = $this->Parser->parse($text);
		$expected = '<p>Normal text <em>emphasis text</em> normal <em>emphasis</em> normal.</p>';
		$this->assertTextEquals($expected, $result);

		$text = 'Normal text **bold** normal *emphasis* normal.';
		$result = $this->Parser->parse($text);
		$expected = '<p>Normal text <strong>bold</strong> normal <em>emphasis</em> normal.</p>';
		$this->assertTextEquals($expected, $result);

		$text = 'Normal text ***bold*** normal *emphasis* normal.';
		$result = $this->Parser->parse($text);
		$expected = '<p>Normal text <strong><em>bold</em></strong> normal <em>emphasis</em> normal.</p>';
		$this->assertTextEquals($expected, $result);

		$text = 'Normal text _emphasis text_ normal _emphasis_ normal.';
		$result = $this->Parser->parse($text);
		$expected = '<p>Normal text <em>emphasis text</em> normal <em>emphasis</em> normal.</p>';
		$this->assertTextEquals($expected, $result);

		$text = 'Normal text __bold__ normal _emphasis_ normal.';
		$result = $this->Parser->parse($text);
		$expected = '<p>Normal text <strong>bold</strong> normal <em>emphasis</em> normal.</p>';
		$this->assertTextEquals($expected, $result);

		$text = 'Normal text ___bold___ normal _emphasis_ normal.';
		$result = $this->Parser->parse($text);
		$expected = '<p>Normal text <strong><em>bold</em></strong> normal <em>emphasis</em> normal.</p>';
		$this->assertTextEquals($expected, $result);
	}

/**
 * test inline code elements.
 *
 * @return void
 */
	public function testInlineCode() {
		$text = 'Normal text `code text` normal `code` normal.';
		$result = $this->Parser->parse($text);
		$expected = '<p>Normal text <code>code text</code> normal <code>code</code> normal.</p>';
		$this->assertTextEquals($expected, $result);

		$text = 'Normal text ``code text` normal `code`` normal.';
		$result = $this->Parser->parse($text);
		$expected = '<p>Normal text <code>code text` normal `code</code> normal.</p>';
		$this->assertTextEquals($expected, $result);

		$text = 'Normal text ``code text` < > & normal `code`` normal.';
		$result = $this->Parser->parse($text);
		$expected = '<p>Normal text <code>code text` &lt; > &amp; normal `code</code> normal.</p>';
		$this->assertTextEquals($expected, $result);

		$text = 'Normal text ``code text some_variable_here_code text`` normal.';
		$result = $this->Parser->parse($text);
		$expected = '<p>Normal text <code>code text some_variable_here_code text</code> normal.</p>';
		$this->assertTextEquals($expected, $result);
	}

/**
 * test inline code elements.
 *
 * @return void
 */
	public function testAutoLink() {
		$text = 'Normal text www.foo.com normal code normal.';
		$result = $this->Parser->parse($text);
		$expected = '<p>Normal text <a href="http://www.foo.com">www.foo.com</a> normal code normal.</p>';
		$this->assertTextEquals($expected, $result);

		$text = 'Normal text www.foo.com/page/foo:bar normal code normal.';
		$result = $this->Parser->parse($text);
		$expected = '<p>Normal text <a href="http://www.foo.com/page/foo:bar">www.foo.com/page/foo:bar</a> normal code normal.</p>';
		$this->assertTextEquals($expected, $result);

		$text = 'Normal text http://www.foo.com normal code normal.';
		$result = $this->Parser->parse($text);
		$expected = '<p>Normal text <a href="http://www.foo.com">http://www.foo.com</a> normal code normal.</p>';
		$this->assertTextEquals($expected, $result);
	}

/**
 * testAutoLinksInLists
 *
 * @return void
 */
	public function testAutoLinksInLists() {
		$text = <<<TEXT
* [1] http://cakephp.lighthouseapp.com/projects/42648/changelog-1-3-5
* [2] http://github.com/cakephp/cakephp/downloads
* [3] http://cakephp.lighthouseapp.com/projects/42648
* [4] http://cakedc.com
TEXT;
		$result = $this->Parser->parse($text);
		preg_match_all('/<a href=/', $result, $matches);
		$this->assertIdentical(count($matches[0]), 4);
	}

/**
 * test inline links
 *
 * @return void
 */
	public function testInlineLinks() {
		$text = 'Normal text [test link](http://www.foo.com) normal code normal.';
		$result = $this->Parser->parse($text);
		$expected = '<p>Normal text <a href="http://www.foo.com">test link</a> normal code normal.</p>';
		$this->assertTextEquals($expected, $result);

		$text = 'Normal text [test link](http://www.foo.com "some title") normal code normal.';
		$result = $this->Parser->parse($text);
		$expected = '<p>Normal text <a href="http://www.foo.com" title="some title">test link</a> normal code normal.</p>';
		$this->assertTextEquals($expected, $result);

		$text = "Normal text www.foo.com\nNormal text on new line";
		$result = $this->Parser->parse($text);
		$expected = "<p>Normal text <a href=\"http://www.foo.com\">www.foo.com</a>\nNormal text on new line</p>";
		$this->assertTextEquals($expected, $result);
	}

/**
 * test entity conversion
 *
 * @return void
 */
	public function testEntityConversion() {
		$text = 'Normal < text [test link](http://www.foo.com) normal & code normal.';
		$result = $this->Parser->parse($text);
		$expected = '<p>Normal &lt; text <a href="http://www.foo.com">test link</a> normal &amp; code normal.</p>';
		$this->assertTextEquals($expected, $result);
	}

/**
 * Test Headings
 *
 * @return void
 */
	public function testHeadings() {
		$text = <<<TEXT
# H1
## H2 ##
### heading 3
#### heading 4
##### Imbalanced ##
######## There is no heading 8
TEXT;
		$result = $this->Parser->parse($text);
		$expected = <<<HTML
<h1>H1</h1>
<h2>H2</h2>
<h3>heading 3</h3>
<h4>heading 4</h4>
<h5>Imbalanced</h5>
<h6>## There is no heading 8</h6>
HTML;
		$this->assertTextEquals($expected, $result);
	}

/**
 * test horizontal rules.
 *
 * @return void
 */
	public function testHorizontalRule() {
		$expected = <<<HTML
<p>this is some</p>

<hr />

<p>text</p>
HTML;

		foreach (array('-', '*', '_') as $char) {
			$text = <<<TEXT
this is some
{$char}{$char}{$char}
text
TEXT;
			$result = $this->Parser->parse($text);
			$this->assertTextEquals($expected, $result);

			$text = <<<TEXT
this is some
{$char}  {$char}  {$char}
text
TEXT;
			$result = $this->Parser->parse($text);
			$this->assertTextEquals($expected, $result);

			$text = <<<TEXT
this is some
{$char}{$char}{$char}{$char}{$char}{$char}
text
TEXT;
			$result = $this->Parser->parse($text);
			$this->assertTextEquals($expected, $result);
		}
	}

/**
 * test multiline code blocks
 *
 * @return void
 */
	public function testCodeBlockWithDelimiters() {
		$text = <<<TEXT
this is some
@@@
function test() {
	echo '<test>';
}
@@@
more text
TEXT;
		$expected = <<<HTML
<p>this is some</p>

<pre><code>function test() {
    echo '&lt;test&gt;';
}</code></pre>

<p>more text</p>
HTML;
		$result = $this->Parser->parse($text);
		$this->assertTextEquals($expected, $result);

		$text = <<<TEXT
this is some
{{{
function test() {
	echo '<test>';
}
}}}
more text
TEXT;
		$expected = <<<HTML
<p>this is some</p>

<pre><code>function test() {
    echo '&lt;test&gt;';
}</code></pre>

<p>more text</p>
HTML;
		$result = $this->Parser->parse($text);
		$this->assertTextEquals($expected, $result);
	}

/**
 * test two code blocks with delimiters.
 *
 * @return void
 */
	public function testMultipleCodeBlocksWithDelimiters() {
		$text = <<<TEXT
this is some
{{{
function test() {
	echo '<test>';
}
}}}

more text goes here.

{{{
function test() {
	echo '<test>';
}
}}}

Additional text
TEXT;
		$expected = <<<HTML
<p>this is some</p>

<pre><code>function test() {
    echo '&lt;test&gt;';
}</code></pre>

<p>more text goes here.</p>

<pre><code>function test() {
    echo '&lt;test&gt;';
}</code></pre>

<p>Additional text</p>
HTML;
		$result = $this->Parser->parse($text);
		$this->assertTextEquals($expected, $result);
	}

/**
 * test that code blocks work with no newlines
 *
 * @return void
 */
	public function testCodeBlockNoNewLines() {
		$text = <<<TEXT
this is some

{{{ Router::connectNamed(false, array('default' => true)); }}}

more text
TEXT;
		$expected = <<<HTML
<p>this is some</p>

<pre><code>Router::connectNamed(false, array('default' =&gt; true));</code></pre>

<p>more text</p>
HTML;
		$result = $this->Parser->parse($text);
		$this->assertTextEquals($expected, $result);
	}

/**
 * test indented code blocks
 *
 * @return void
 */
	public function testCodeBlockWithIndents() {
		$text = <<<TEXT
this is some

	function test() {
		echo '<test>';
	}

more text
TEXT;
		$expected = <<<HTML
<p>this is some</p>

<pre><code>function test() {
    echo '&lt;test&gt;';
}</code></pre>

<p>more text</p>
HTML;
		$result = $this->Parser->parse($text);
		$this->assertTextEquals($expected, $result);

		$text = <<<TEXT
this is some

	function test() {
		echo '<test>';
	}

more text
TEXT;
		$expected = <<<HTML
<p>this is some</p>

<pre><code>function test() {
    echo '&lt;test&gt;';
}</code></pre>

<p>more text</p>
HTML;
		$result = $this->Parser->parse($text);
		$this->assertTextEquals($expected, $result);

		$text = <<<TEXT
this is some

	function test() {
		echo '<test>';
	}

	\$foo->bar();

more text
TEXT;
		$expected = <<<HTML
<p>this is some</p>

<pre><code>function test() {
    echo '&lt;test&gt;';
}

\$foo-&gt;bar();</code></pre>

<p>more text</p>
HTML;
		$result = $this->Parser->parse($text);
		$this->assertTextEquals($expected, $result);
	}

/**
 * Test simple ordered list parsing
 *
 * @return void
 */
	public function testSimpleOrderedList() {
		$text = <<<TEXT
Some text here.

 - Line 1
 - Line 2
 - Line 3

more text
TEXT;

		$expected = <<<HTML
<p>Some text here.</p>

<ul>
<li>Line 1</li>
<li>Line 2</li>
<li>Line 3</li>
</ul>

<p>more text</p>
HTML;
		$result = $this->Parser->parse($text);
		$this->assertTextEquals($expected, $result);

		$text = <<<TEXT
Some text here.

 - Line `with code`
 + Line 2
 * Line **bold**

more text
TEXT;

		$expected = <<<HTML
<p>Some text here.</p>

<ul>
<li>Line <code>with code</code></li>
<li>Line 2</li>
<li>Line <strong>bold</strong></li>
</ul>

<p>more text</p>
HTML;
		$result = $this->Parser->parse($text);
		$this->assertTextEquals($expected, $result);
	}

/**
 * test that lists ending on the last line of the text are handled properly
 *
 * @return void
 */
	public function testUnorderedListAtEndOfText() {
		$text = <<<TEXT
### Attributes:

 - `empty` - If true, the empty select option is shown.  If a string,
	that string is displayed as the empty element.
 - this is another line
TEXT;

		$expected = <<<HTML
<h3>Attributes:</h3>

<ul>
<li><code>empty</code> - If true, the empty select option is shown.  If a string,
that string is displayed as the empty element.</li>
<li>this is another line</li>
</ul>

<p></p>
HTML;
		$result = $this->Parser->parse($text);
		$this->assertTextEquals($expected, $result);
	}

/**
 * Test simple ordered list parsing
 *
 * @return void
 */
	public function testSimpleUnorderedList() {
		$text = <<<TEXT
Some text here.

 1. Line 1
 2. Line 2
 3. Line 3

more text
TEXT;

		$expected = <<<HTML
<p>Some text here.</p>

<ol>
<li>Line 1</li>
<li>Line 2</li>
<li>Line 3</li>
</ol>

<p>more text</p>
HTML;
		$result = $this->Parser->parse($text);
		$this->assertTextEquals($expected, $result);

		$text = <<<TEXT
Some text here.

 8. Line `with code`
 100. Line 2
 5. Line **bold**

more text
TEXT;

		$expected = <<<HTML
<p>Some text here.</p>

<ol>
<li>Line <code>with code</code></li>
<li>Line 2</li>
<li>Line <strong>bold</strong></li>
</ol>

<p>more text</p>
HTML;
		$result = $this->Parser->parse($text);
		$this->assertTextEquals($expected, $result);
	}

/**
 * test nested one line lists
 *
 * @return void
 */
	public function testNestedLists() {
		$text = <<<TEXT
Some text here.

 - Line 1
	- Indented 1
	- Indented 2
	- Indented 3
 - Line 2
 - Line 3

more text
TEXT;

		$expected = <<<HTML
<p>Some text here.</p>

<ul>
<li>Line 1
<ul>
<li>Indented 1</li>
<li>Indented 2</li>
<li>Indented 3</li>
</ul></li>
<li>Line 2</li>
<li>Line 3</li>
</ul>

<p>more text</p>
HTML;
		$result = $this->Parser->parse($text);
		$this->assertTextEquals($expected, $result);

		$text = <<<TEXT
Some text here.

 - Line 1
	- Indented 1
	- Indented 2
		- Indented 3
 - Line 2
 - Line 3

more text
TEXT;

		$expected = <<<HTML
<p>Some text here.</p>

<ul>
<li>Line 1
<ul>
<li>Indented 1</li>
<li>Indented 2
<ul>
<li>Indented 3</li>
</ul></li>
</ul></li>
<li>Line 2</li>
<li>Line 3</li>
</ul>

<p>more text</p>
HTML;
		$result = $this->Parser->parse($text);
		$this->assertTextEquals($expected, $result);
	}

/**
 * test mixed lists.
 *
 * @return void
 */
	public function testMixedList() {
		$text = <<<TEXT
Some text here.

 - Line 1
	1. Indented 1
	2. Indented 2
 - Line 2
 - Line 3

more text
TEXT;

		$expected = <<<HTML
<p>Some text here.</p>

<ul>
<li>Line 1
<ol>
<li>Indented 1</li>
<li>Indented 2</li>
</ol></li>
<li>Line 2</li>
<li>Line 3</li>
</ul>

<p>more text</p>
HTML;
		$result = $this->Parser->parse($text);
		$this->assertTextEquals($expected, $result);
	}

/**
 * Test headings embedded in code blocks
 *
 * @return void
 */
	public function testHeadingsInCodeBlocks() {
		$text = <<<TEXT
this is some

	##
	## This should not render as a heading
	function test() {
		echo '<test>';
	}

more text
TEXT;
		$expected = <<<HTML
<p>this is some</p>

<pre><code>##
## This should not render as a heading
function test() {
    echo '&lt;test&gt;';
}</code></pre>

<p>more text</p>
HTML;

		$result = $this->Parser->parse($text);
		$this->assertTextEquals($expected, $result);

		$text = <<<TEXT
Text

	Code 1

Some [linked](http://cakephp.org) text

	Code 2

Last Text
TEXT;
		$expected = <<<HTML
<p>Text</p>

<pre><code>Code 1</code></pre>

<p>Some <a href="http://cakephp.org">linked</a> text</p>

<pre><code>Code 2</code></pre>

<p>Last Text</p>
HTML;

		$result = $this->Parser->parse($text);
		$this->assertTextEquals($expected, $result);
	}

	public function testFunky() {
		$text = <<<TEXT
Text

	Code 1

Text

[Link](http://google.com).

Text

* Bullet 1
* Bullet 2
* Bullet 3

Text
TEXT;
		$expected = <<<HTML
<p>Text</p>

<pre><code>Code 1</code></pre>

<p>Text</p>

<p><a href="http://google.com">Link</a>.</p>

<p>Text</p>

<ul>
<li>Bullet 1</li>
<li>Bullet 2</li>
<li>Bullet 3</li>
</ul>

<p>Text</p>
HTML;

		$result = $this->Parser->parse($text);
		$this->assertTextEquals($expected, $result);
	}

	public function testParseWithOtherEngines() {
		$testText = <<<TEXT
# test #

	Code

 * One
 * Two
 * Three
TEXT;
		$expected = <<<HTML
<h1>test</h1>

<pre><code>Code
</code></pre>

<ul>
<li>One</li>
<li>Two</li>
<li>Three</li>
</ul>
HTML;
		$result = $this->Parser->parse($testText, array('engine'=>'markdown'));
		$this->assertTextEquals($expected, $result);

		$result = $this->Parser->parse($testText, array('engine'=>'markdown_extra'));
		$this->assertTextEquals($expected, $result);
	}


}

