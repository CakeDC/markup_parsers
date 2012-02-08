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

App::uses('AppHelper', 'View/Helper');

/**
 * Parser Helper
 *
 * @package MarkupParser
 * @subpackage MarkupParser.View.Helper
 */
class ParserHelper extends AppHelper {

/**
 * Parse text from some plain text format
 *
 * @param string $text Text for parsing
 * @param string $format Format type
 * @return array Parsed pages of text
 */
	public function parse($text, $parser = 'markdown', $options = array()) {
		$parsed = array($text);
		try {
			App::uses('ParserRegistry', 'MarkupParsers.Lib');
			$parsed = ParserRegistry::getParser($parser)->parse($text, $options);
			if (!is_array($parsed)) {
				$parsed = array($parsed);
			}
		} catch (Exception $e) {
			if (Configure::read('debug') > 0) {
				throw $e;
			} else {
				$this->log($e->getMessage());
			}
		}
		return $parsed;
	}

/**
 * Parse text and return a string with parsed content.
 * Multi-page content will be returned as one string with pages joined with
 * the separator passed in 3rd param
 *
 * @param string $text Text for parsing
 * @param string $format Format type
 * @param array $options
 * - see parse() 
 * - pageGlue Separator to use to join pages together [default: none]
 * @return array Parsed text
 */
	public function parseAsString($text, $parser = 'markdown', $options = array()) {
		$pageGlue = '';
		if (isset($options['pageGlue'])) {
			$pageGlue = $options['pageGlue'];
			unset($options['pageGlue']);
		}
		return implode($pageGlue, $this->parse($text, $parser, $options));
	}
	
}
