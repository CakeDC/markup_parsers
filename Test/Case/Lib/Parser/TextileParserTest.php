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

    
    /**
     * Test standard Textile symbol replacement
     * 
     * @return void
     * 
     */
    public function testSymbols(){
        
        $testText = <<<TEXT
"Textile(c)" is a registered(r) 'trademark' of Textpattern(tm) -- or TXP(That's textpattern!) -- at least it was - back in '88 when 2x4 was (+/-)5(o)C ... QED!

p{font-size: 200%;}. 2(1/4) 3(1/2) 4(3/4)

TEXT;
         $expected = <<<HTML
<p>&#8220;Textile&#169;&#8221; is a registered&#174; &#8216;trademark&#8217; of Textpattern&#8482; &#8212; or <acronym title="That&#8217;s textpattern!"><span class="caps">TXP</span></acronym> &#8212; at least it was &#8211; back in &#8217;88 when 2&#215;4 was &#177;5&#176;C &#8230; <span class="caps">QED</span>!</p>

<p style="font-size: 200%;">2&#188; 3&#189; 4&#190;</p>
HTML;
        $result = $this->Parser->parse($testText);
        //debug($result); die();
		$this->assertTextEquals($expected, $result);
    }
    
    
    
    /**
     * Test custom symbol replacement
     * 
     * @see TextileParser::$_symbols
     * @return void
     */
    public function testSymbolReplacement(){
        $customSymbols = array(
            'registered' => '(REG)',
            'copyright' => '(C)',
            'emdash' => '---'
        );
        $options = array(
            'symbols' => $customSymbols
        );
        
        $testText = <<<TEXT
"Textile(c)" is a registered(r) 'trademark' of Textpattern(tm) -- or
TEXT;
         $expected = <<<HTML
<p>&#8220;Textile(C)&#8221; is a registered(REG) &#8216;trademark&#8217; of Textpattern&#8482; --- or</p>
HTML;
        
        $result = $this->Parser->parse($testText, $options);
        //debug($result); die();
		$this->assertTextEquals($expected, $result);
    }
    
    
    
    
    /**
     * Test unsafe input for pass-through AND sanitized output
     * - restricted option true/false
     * 
     * @return void
     */
    public function testRestricted(){
        $unsafeInput = <<<HACK
"Free stuff around the corner":file://some.nice-domain.com/very_safe.exe
HACK;
        $trustedSource = <<<HTML
<p><a href="file://some.nice-domain.com/very_safe.exe">Free stuff around the corner</a></p>
HTML;
        $options = array(
            'restricted' => false
        );
        $result = $this->Parser->parse($unsafeInput, $options);
        $this->assertTextEquals($trustedSource, $result);
        
        
        $untrustedSource = <<<HTML
<p>&#8220;Free stuff around the corner&#8221;:file://some.nice-domain.com/very_safe.exe</p>
HTML;
        $options = array(
            'restricted' => true
        );
        $result = $this->Parser->parse($unsafeInput, $options);
        $this->assertTextEquals($untrustedSource, $result);
    }
    
    
    
    /**
     * Test HTML stripping. 
     * Although not a Textile feature, borrowed it from Markdown implementation
     * - stripHtml option true/false
     * 
     * 
     */
    public function testStripHtml(){
        $mixedInput = <<<TEXT
<div class="
    amazing-css-skills
    ">
    <a href="/reference.html" style="
    display: block;
    height: 200px;
">This link is accidental</a>
"But this one is on purpose":/reference.html
TEXT;
        $expected = <<<HTML
This link is accidental
<a href="/reference.html">But this one is on purpose</a>
HTML;
        $options = array(
            'stripHtml' => true
        );
        $result = $this->Parser->parse($mixedInput, $options);
        $this->assertTextEquals($expected, $result);
    }
    
    
    
    /**
     * Images without absolute path can be prefixed.
     * 
     * @return void
     */
    public function testImagePrefix(){
        $options = array(
            'relative_image_prefix' => '/img/'
        );
        $input = '!image.jpg!';
        $expected = '<p><img alt="" src="/img/image.jpg" /></p>';
        $result = $this->Parser->parse($input, $options);
        $this->assertTextEquals($expected, $result);
    }
    
    
    
    /**
     * Textile features automatic width and height parameters by guessing
     * image absolute path by concatenating image URL and DOCUMENT_ROOT
     * 
     * This feature is not yet fully supported in the CakePHP Parser because of CakePHP's 
     * url rewrite rule 
     * (/img/test.png != DOCUMENT_ROOT/img/test.png, but DOCUMENT_ROOT/app/webroot/img/test.png)
     * 
     * Test skips if the /webroot/img/cake.power.gif image is not available.
     * 
     */
    public function testImageDimensions(){
        $this->skipUnless( $this->_getTestImage() );
        
        $image = $this->_getTestImage();
        $inputString = '!' . $image['url'] . '!';
        
        $result = $this->Parser->parse($inputString);
        $size = getimagesize($image['path']);
        $expectWidth = 'width="'.$size[0].'"';
        $expectHeight = 'height="'.$size[1].'"';
        
        $this->assertTextContains($expectWidth, $result);
        $this->assertTextContains($expectHeight, $result);
    }
    
    
    /**
     * Disable automatic WxH reading.
     * 
     * @see TextileParserTest::testImageDimensions()
     */
    public function testImageDimensionless(){
        $this->skipUnless( $this->_getTestImage() );
        
        $image = $this->_getTestImage();
        $inputString = '!' . $image['url'] . '!';
        
        $options = array(
            'dimensionless_images' => true
        );
        $result = $this->Parser->parse($inputString, $options);
        $this->assertTextNotContains('width="', $result, '', true);
        $this->assertTextNotContains('height="', $result, '', true);
    }
    
    public function testMarkupFormat(){
        $this->skipUnless(function_exists('json_decode'));
        $testsJsonString = @file_get_contents(dirname(__FILE__) . DS . 'TextileFormatTests.json');
        $this->skipUnless($testsJsonString);
        $this->tests = json_decode($testsJsonString, true);
        $this->skipUnless(is_array($this->tests));
        
        
        foreach($this->tests as $message => $test){
            
            if (is_string($test)) { debug($test); die(); }
            $result = $this->Parser->parse($test['input']);
            $expect = $test['expect'];
            
            //mangle whitespace to match tests
            $result = preg_replace('/^[^\S\n]+/m', '', $result);
            $expect = preg_replace('/^[^\S\n]+/m', '', $expect);
            
            //trim ending newline in expect
            $expect = rtrim($expect, "\n\r");
            
            if (!empty($test['special'])){
                switch ($test['special']){
                    case 'strip_rand':
                        $regex = '/
                            (?<= noteref|noteid|note|fnrev|fn)
                            \d[^"]+
                            (?=")
                            /mx';
                        $result = preg_replace($regex, '', $result);
                        break;
                    case 'skip':
                        continue 2;
                        break;
                }
            }
            
            $this->assertTextEquals($expect, $result, $message);
        }
        
    }
    
    
    /**
     * Helper method for image testing
     * Returns the url and path to the "cake.power.gif" image, which is available on fresh 
     * CakePHP installs.
     * 
     * @return array
     */
    protected function _getTestImage(){
        if (!is_file(IMAGES. 'cake.power.gif')) return false;
        $root = preg_split( '|[\\\/]|', trim(env('DOCUMENT_ROOT'), '/\\'));
        $images = preg_split( '|[\\\/]|', trim(IMAGES, '/\\'));
        return array( 
            'url' => '/'. implode('/', array_diff($images, $root)) . '/cake.power.gif',
            'path' => IMAGES.'cake.power.gif' 
            );
    }
    
    

}

