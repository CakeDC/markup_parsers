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

/**
 * Parser Interface needed to be implemented by any Parser
 *
 * @package markup_parsers
 * @subpackage markup_parsers.libs
 */
interface ParserInterface {

/**
 * Parse a string
 *
 * @param string $text Text to parse
 * @param array $options Options specific to the parser
 * @return mixed Either a string or an array of parsed pages (in case of multiple pages)
 */
	public function parse($text, $options = array());

}