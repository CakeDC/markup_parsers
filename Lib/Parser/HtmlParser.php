<?php
/**
 * Copyright 2011, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2011, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('ParserInterface', 'MarkupParsers.Lib');
App::uses('Sanitize', 'Utility');

/**
 * Html Parser
 *
 * @package markup_parsers
 * @subpackage markup_parsers.libs
 */
class HtmlParser implements ParserInterface {

/**
 * Page separator pattern
 * 
 * @var string
 */
	public static $pageSeparator = '<!-- Page separator -->';

/**
 * Whether or not the highlight_string() PHP function must be used for the code
 * Used temporarily to persist the value during callback calls
 * 
 * @var boolean
 */
	private $__phpHighlightEnabled = true;

/**
 * Parse method
 * Split the data across multiple pages
 * 
 * @param string $string String to parse
 * @param array $options Valid keys are:
 * 	- highlight_code: whether or not the highlight_string() PHP function must be used for the code
 * 		It generates a messy markup adn can be disabled for users that want "classic" html <code> tags
 */
	public function parse($string, $options = array()) {
		$_defaults = array('highlight_code' => true);
		$options = array_merge($_defaults, $options);
		
		$this->__phpHighlightEnabled = $options['highlight_code'];
		$data = explode(self::$pageSeparator, $string);
		foreach ($data as &$text) {
			$text = Sanitize::stripImages(Sanitize::stripScripts($text));
			$text = preg_replace_callback('/<code>(.*?)<\/code>/s', array($this, '_highlightCode'), $text);
		}
		return $data;
	}

/**
 * Code highlighting method - must be called from a preg_replace_callback
 * 
 * @param array $text Matched code to highlight
 * @return string Formated text
 */
	public function _highlightCode($text) {
		$text = $text[1];
		if ($this->__phpHighlightEnabled) {
			$text = highlight_string($text, true);
		} else {
			$text = htmlspecialchars($text);
			$spaces = array('/[ ]/i', '/[\\t]/i');
			$spaceReplace = array('&nbsp;', '&nbsp;&nbsp;&nbsp;&nbsp;');
			$text = preg_replace($spaces, $spaceReplace, $text);
			$text = '<code>' . $text . '</code>';
		}
		return $text;
	}
}
