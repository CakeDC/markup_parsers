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
	public function parse($text, $parser = 'markdown') {
		$parsed = array($text);
		try {
			App::uses('ParserRegistry', 'MarkupParsers.Lib');
			$parsed = ParserRegistry::getParser($parser)->parse($text);
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
}
