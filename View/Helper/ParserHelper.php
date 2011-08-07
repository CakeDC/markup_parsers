<?php
/**
 * Copyright 2010, Cake Development Corporation (http://cakedc.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2010, Cake Development Corporation (http://cakedc.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Parser Helper
 *
 * @package markup_parsers
 * @subpackage markup_parsers.views.helpers
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
			App::import('lib', 'MarkupParsers.ParserRegistry');
			$Parser = ParserRegistry::getParser($parser);
			$parsed = $Parser->parse($text);
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
