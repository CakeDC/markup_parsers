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

/**
 * Parser Registry. A factory to return parser instances from available parsers listed using the Configure class
 * 
 * To setup a new parser use
 * 
 *		Configure::write('Parsers.my_parser' => array(
 *			'name' => 'MyParser',
 *			'className' => 'MyPlugin.MyParser'
 *		))
 *
 * @package markup_parsers
 * @subpackage markup_parsers.libs
 */
class ParserRegistry {

/**
 * Available payment processors
 *
 * @var array
 */
	protected static $_parsers = null;

/**
 * Available parsers
 *
 * @var array
 */
	private static $__availableParsers = array();

/**
 * Initializes the registry by loading all the existing payment processors
 *
 * @return void
 */
	protected static function _init() {
		if (empty(self::$__availableParsers)) {
			self::$__availableParsers = Configure::read('Parsers');
		}
	}

/**
 * Returns the payment processors that implements the passed interfaces
 *
 * @return array
 */
	public static function getParsers() {
		self::_init();

		$result = array();
		$parsers = Configure::read('Parsers');
		foreach (self::$__availableParsers as $key => $parser) {
			$result[$key] = $parser['name'];
		}

		return $result;
	}

/**
 * Returns a parser instance
 *
 * @param string parser key
 * @return object Parser instance
 * 
 */
	public static function getParser($parser = '') {
		self::_init();

		if (empty(self::$_parsers[$parser])) {
			list($class, $location) = self::$__availableParsers[$parser];
			App::uses($class, $location);

			if (!class_exists($class)) {
				throw new Exception(__d('markup_parsers', 'Invalid Parser', true));
			}
			self::$_parsers[$parser] = new $class();
		}

		return self::$_parsers[$parser];
	}

}
