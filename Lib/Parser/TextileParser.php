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

App::uses('ParserInterface', 'MarkupParsers.Lib');

/**
 * This Textile parser just offers the standard Textile parser functions.
 *
 * @package markup_parsers
 * @subpackage markup_parsers.libs
 */
class TextileParser implements ParserInterface {

/**
 * The text being parsed.
 *
 * @var string
 */
	protected $_text = null;

/**
 * Parses $text containing Textile markup text and generates the correct HTML
 *
 * ### Options:
 *
 * - stripHtml: remove any HTML before parsing.
 * - restricted: [true]
 * - lite: 
 * - encode:
 * - noimage:
 * - strict:
 * - rel:
 *
 * @param string $text Text to be converted
 * @param array $options Array of options for converting
 * @return string Parsed HTML
 */
	public function parse($text, $options = array()) {
        
        if (empty($options['restricted'])) {
            $defaults = array(
                'stripHtml' => false,
                'restricted' => false,
                'lite' => '',
                'encode' => '',
                'noimage' => '',
                'strict' => '',
                'rel' => ''
            );
        }
        else {
            $defaults = array(
                'stripHtml' => false,
                'restricted' => false,
                'lite' => 1,
                'encode' => '',
                'noimage' => 1,
                'strict' => '',
                'rel' => 'nofollow'
            );
        }
		$options = array_merge($defaults, $options);

		if (!empty($options['stripHtml'])) {
			$text = strip_tags($text);
		}

		
        App::import('Vendor', 'MarkupParsers.textile/textile');
        $Textile = new Textile_Parser;
        if (!$options['restricted']){
            $text = trim($Textile->TextileThis($text, $options['lite'], $options['encode'], $options['noimage'], $options['strict'], $options['rel']));
            return $text;
            
        }
        else {
            
            return trim($Textile->TextileRestricted($text, $options['lite'], $options['noimage'], $options['rel']));
        }
		
		return null;
	}


}