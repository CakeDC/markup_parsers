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
  * Defaul Textile symbol replacement scheme.
  * Use these keys to override these symbols in $options['symbols']
  * Values in this array are not being actually used in the parser but are present for reference.
  * 
  * Example:
  *  $options = array(
  *     'symbols' => array(
  *         'quote_single_open' => "'",
  *         'quote_single_close' => "'",
  *         'trademark' => '<span title="registered trademark">&#8482;</span>'
  *         ...
  *     )
  *  )
  * 
  * Symbols can also be overriden using the Configure array:
  * 
  * @var array
  */
    protected $_symbols = array(
            'quote_single_open'  => '&#8216;',
            'quote_single_close' => '&#8217;',
            'quote_double_open'  => '&#8220;',
            'quote_double_close' => '&#8221;',
            'apostrophe'         => '&#8217;',
            'prime'              => '&#8242;',
            'prime_double'       => '&#8243;',
            'ellipsis'           => '&#8230;',
            'emdash'             => '&#8212;',
            'endash'             => '&#8211;',
            'dimension'          => '&#215;',
            'trademark'          => '&#8482;',
            'registered'         => '&#174;',
            'copyright'          => '&#169;',
            'half'               => '&#189;',
            'quarter'            => '&#188;',
            'threequarters'      => '&#190;',
            'degrees'            => '&#176;',
            'plusminus'          => '&#177;',
            'fn_ref_pattern'     => '<sup{atts}>{marker}</sup>',
            'fn_foot_pattern'    => '<sup{atts}>{marker}</sup>',
            'nl_ref_pattern'     => '<sup{atts}>{marker}</sup>',
        );

/**
 * Parses $text containing Textile markup text and generates the correct HTML
 *
 * ### Options:
 * - doctype    = xhtml ['xhtml' | 'html5']
 * - stripHtml  = false bool - remove HTML tags before parsing
 * - restricted = true bool - sanitazes untrusted input against malicious exploits
 * - lite       = '' - ['' | 1] - switch to lite mode
 * - encode     = '' - deprecated
 * - noimage    = '' - disables images
 * - strict     = '' - FALSE to strip whitespace before parsing
 * - rel        = '' - Relationship attribute applied to generated links
 * - relative_image_prefix  = '' - applies a base url for all relative images
 * - dimensionless_images   = false bool if true disables width and height check for use in IMG attributes
 *
 * @param string $text Text to be converted
 * @param array $options Array of options for converting
 * @return string Parsed HTML
 * @todo adapt dimensionless_images to CakePHP's image url=>path conversion
 */
	public function parse($text, $options = array()) {
        $defaults = array(
            'doctype' => 'xhtml',
            'stripHtml' => false,
            'restricted' => false,
            'lite' => '',
            'encode' => '',
            'noimage' => '',
            'strict' => '',
            'rel' => '',
            'dimensionless_images' => false,
            'relative_image_prefix' => '',
            'symbols' => array()
        );
        if (!empty($options['restricted'])) {
            $defaults['lite'] = 1;
            $defaults['noimage'] = 1;
            $defaults['rel'] = 'nofollow';
        }
		$options = array_merge($defaults, $options);

		if (!empty($options['stripHtml'])) {
			$text = strip_tags($text);
		}
        
        // New Textile instance
        App::import('Vendor', 'MarkupParsers.textile/textile');
        $Textile = new Textile_Parser($options['doctype']);
        
        // Set additional options
        
        if (!empty($options['symbols']) && is_array($options['symbols'])){
            foreach($options['symbols'] as $symbol => $replacement){
                if (array_key_exists($symbol, $this->_symbols)){
                    $Textile->setSymbol($symbol, $replacement);
                }
            }
        }
        if ($options['dimensionless_images']){
            $Textile->setDimensionlessImages();
        }
        if (!empty($options['relative_image_prefix'])){
            $Textile->setRelativeImagePrefix($options['relative_image_prefix']);
        }
        
        // parse
        if (!$options['restricted']){
            return trim($Textile->TextileThis($text, $options['lite'], $options['encode'], $options['noimage'], $options['strict'], $options['rel']));
        }
        else {
            return trim($Textile->TextileRestricted($text, $options['lite'], $options['noimage'], $options['rel']));
        }
		
		return null;
	}


}