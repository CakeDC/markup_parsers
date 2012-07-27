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
 * Bbcode Parser
 *
 * @package markup_parsers
 * @subpackage markup_parsers.libs
 */
class BbcodeParser implements ParserInterface {

/**
 * Filters
 * 
 * @var array
 */
	public $filters = array('cake', 'code', 'email', 'image', 'extended', 'link', 'list', 'table');

/**
 * Quote
 * 
 * @var string
 */
	public $quote = 'all';

/**
 * Quote Style
 * 
 * @var string
 */
	public $quoteStyle = 'double';

/**
 * Strict
 * 
 * @var boolean
 */
	public $strict = true;

/**
 * Page separator pattern
 * 
 * @var string
 */
	public static $pageSeparator = '[Page separator]';

/**
 * Whether or not the code must be highlighted in the __highlightCode method
 * 
 * @var boolean
 */
	protected $_codeHighlightingEnabled = true;

	protected $__charset = 'UTF-8';

	protected $__text = null;

	protected $__parsed = null;

	protected $__preparsed = null;

	protected $__tags = array();

	protected $__builtTags = array();

	protected $__basicTags = array(
		'b' => array(
			'open' => 'strong',
			'close' => 'strong',
			'allowed' => 'all',
			'attributes' => array()
		),
		'i' => array(
			'open' => 'span style="font-style:italic;"',
			'close' => 'span',
			'allowed' => 'all',
			'attributes' => array()
		),
		'u' => array(
			'open' => 'span style="text-decoration:underline;"',
			'close' => 'span',
			'allowed' => 'all',
			'attributes' => array()
		),
		's' => array(
			'open' => 'del',
			'close' => 'del',
			'allowed' => 'all',
			'attributes' => array()
		),
		'sub' => array(
			'open' => 'sub',
			'close' => 'sub',
			'allowed' => 'all',
			'attributes' => array()
		),
		'sup' => array(
			'open' => 'sup',
			'close' => 'sup',
			'allowed' => 'all',
			'attributes' => array()
		),
		'indent' => array(
			'open' => 'blockquote',
			'close' => 'blockquote',
			'allowed' => 'all',
			'attributes' => array()
		),
		'p' => array(
			'open' => 'p',
			'close' => 'p',
			'allowed' => 'all',
			'attributes' => array()
		),
	);
	protected $__emailTags = array(
		'email' => array(
			'open' => 'a',
			'close' => 'a',
			'allowed' => 'none^img',
			'attributes' => array('email' => 'href=%2$smailto:%1$s%2$s')
		)
	);
	protected $__imageTags = array(
		'img' => array(
			'open' => 'img',
			'close' => '',
			'allowed' => 'none',
			'attributes' => array(
				'img' => 'src=%2$s%1$s%2$s',
				'w' => 'width=%2$s%1$d%2$s',
				'h' => 'height=%2$s%1$d%2$s'
			)
		)
	);
	protected $__extendedTags = array(
		'color' => array(
			'open' => 'span',
			'close' => 'span',
			'allowed' => 'all',
			'attributes' => array('color' => 'style=%2$scolor:%1$s%2$s')
		),
		'code' => array(
			'open' => 'code',
			'close' => 'code',
			'allowed' => 'all',
			'attributes' => array()
		),
		'size' => array(
			'open' => 'span',
			'close' => 'span',
			'allowed' => 'all',
			'attributes' => array('size' => 'style=%2$sfont-size:%1$spt%2$s')
		),
		'font' => array(
			'open' => 'span',
			'close' => 'span',
			'allowed' => 'all',
			'attributes' => array('font' => 'style=%2$sfont-family:%1$s%2$s')
		),
		'align' => array(
			'open' => 'div',
			'close' => 'div',
			'allowed' => 'all',
			'attributes' => array('align' => 'style=%2$stext-align:%1$s%2$s')
		),
		'quote' => array(
			'open' => 'q',
			'close' => 'q',
			'allowed' => 'all',
			'attributes' => array('quote' => 'cite=%2$s%1$s%2$s')
		),
		'h1' => array(
			'open' => 'h1',
			'close' => 'h1',
			'allowed' => 'all',
			'attributes' => array()
		),
		'h2' => array(
			'open' => 'h2',
			'close' => 'h2',
			'allowed' => 'all',
			'attributes' => array()
		),
		'h3' => array(
			'open' => 'h3',
			'close' => 'h3',
			'allowed' => 'all',
			'attributes' => array()
		),
		'h4' => array(
			'open' => 'h4',
			'close' => 'h4',
			'allowed' => 'all',
			'attributes' => array()
		),
		'h5' => array(
			'open' => 'h5',
			'close' => 'h5',
			'allowed' => 'all',
			'attributes' => array()
		),
		'h6' => array(
			'open' => 'h6',
			'close' => 'h6',
			'allowed' => 'all',
			'attributes' => array()
		)
	);
	protected $__linkTags = array(
		'url' => array(
			'open' => 'a',
			'close' => 'a',
			'allowed' => 'none^img',
			'attributes' => array('url' => 'href=%2$s%1$s%2$s')
		)
	);
	protected $__tableTags = array(
		'table' => array(
			'open' => 'table',
			'close' => 'table',
			'allowed' => 'all',
			'child' => 'none^tr',
			'attributes' => array('table' => 'border=%2$s%1$d%2$s')
		),
		'tr' => array(
			'open' => 'tr',
			'close' => 'tr',
			'allowed' => 'all',
			'child' => 'none^td,th',
			'attributes' => array()
		),
		'td' => array(
			'open' => 'td',
			'close' => 'td',
			'allowed' => 'all',
			'attributes' => array()
		),
		'th' => array(
			'open' => 'th',
			'close' => 'th',
			'allowed' => 'all',
			'attributes' => array()
		)
	);
	protected $__listTags = array(
		'list' => array(
			'open' => 'ol',
			'close' => 'ol',
			'allowed' => 'all',
			'child' => 'none^li',
			'attributes' => array('list' => 'style=%2$slist-style-type:%1$s;%2$s')
		),
		'ulist' => array(
			'open' => 'ul',
			'close' => 'ul',
			'allowed' => 'all',
			'child' => 'none^li',
			'attributes' => array('ulist' => 'style=%2$slist-style-type:%1$s;%2$s')
		),
		'li' => array(
			'open' => 'li',
			'close' => 'li',
			'allowed' => 'all',
			'parent' => 'none^ulist,list',
			'attributes' => array()
		)
	);

	function __addParaTag($string) {
		$str = explode("\n", $string);
		$count = count($str);
		$newString = null;
		for ($i = 0; $i < $count; $i++) {
			if (preg_match('/[\\w]/', $str[$i])) {
				$newString[] = '[p]' . htmlentities($str[$i], ENT_COMPAT, $this->__charset) . '[/p]';
			}
		}
		$string = implode(null, $newString);
		return $string;
	}

	function __allowed($out, $in) {
		if (!$out || ($this->__tags[$out]['allowed'] == 'all')) {
			return true;
		}
		if ($this->__tags[$out]['allowed'] == 'none') {
			return false;
		}
		$ar = explode('^', $this->__tags[$out]['allowed']);
		$tags = explode(',', $ar[1]);
		if ($ar[0] == 'none' && in_array($in, $tags)) {
			return true;
		}
		if ($ar[0] == 'all' && in_array($in, $tags)) {
			return false;
		}
		return false;
	}

	function __basic() {
		preg_match_all('/(\\[indent])([\\s\\S]*?)(\\[\/indent])/i', $this->__text, $result, PREG_PATTERN_ORDER);
		if (!empty($result[0])) {
			$count = count($result[0]);
			for ($i = 0; $i < $count; $i++) {
				$this->__text = str_replace($result[0][$i], $this->__encode(str_replace("\r\n", " ", '[indent]' . $this->__addParaTag($result[2][$i]) . '[/indent]')), $this->__text);
			}
		}
		$this->__preparsed = $this->__text;
	}

	function __buildOutput() {
		$this->__parsed = '';
		foreach ($this->__builtTags as $tag) {
			switch ($tag['type']) {
				case 0:
					$this->__parsed .= $tag['text'];
					break;
				case 1:
					$this->__parsed .= '<' . $this->__tags[$tag['tag']]['open'];
					if ($this->quoteStyle === 'single') {
						$quote = "'";
					} else {
						$quote = '"';
					}
					foreach ($tag['attributes'] as $key => $value) {
						$value = preg_replace('#(activex|applet|about|chrome|meta|xml|blink|link|style|script|embed|object|iframe|frame|frameset|ilayer|layer|bgsound|title|base):#is', "\\1&#058;", $value);
						if (($this->quote == 'nothing') || (($this->quote == 'strings') && is_numeric($v))) {
							$this->__parsed .= ' ' . sprintf($this->__tags[$tag['tag']]['attributes'][$key], $value, '');
						} else {
							$this->__parsed .= ' ' . sprintf($this->__tags[$tag['tag']]['attributes'][$key], $value, $quote);
						}
					}
					if ($this->__tags[$tag['tag']]['close'] == '' && $this->strict === true) {
						$this->__parsed .= ' /';
					}
					$this->__parsed .= '>';
					break;
				case 2:
					if ($this->__tags[$tag['tag']]['close'] != '') {
						$this->__parsed .= '</' . $this->__tags[$tag['tag']]['close'] . '>';
					}
					break;
			}
		}
	}

	function __buildTag($string) {
		$tag = array('text' => $string, 'attributes' => array());
		if (substr($string, 1, 1) == '/') {
			$tag['tag'] = strtolower(substr($string, 2, strlen($string) - 3));
			if (!in_array($tag['tag'], array_keys($this->__tags))) {
				return false;
			} else {
				$tag['type'] = 2;
				return $tag;
			}
		} else {
			$tag['type'] = 1;
			if (strpos($string, ' ') && (strpos($string, '=') === false)) {
				return false;
			}
			$tags = array();
			if (preg_match('/\\[([a-z0-9]+)[^\\]]*\\]/i', $string, $tags) == 0) {
				return false;
			}
			$tag['tag'] = strtolower($tags[1]);
			if (!in_array($tag['tag'], array_keys($this->__tags))) {
				return false;
			}
			$attributes = array();
			preg_match_all('/[\\s\\[]([a-z0-9]+)=([^\\s\\]]+)(?=[\\s\\]])/i', $string, $attributes, PREG_SET_ORDER);
			foreach ($attributes as $attribute) {
				$name = strtolower($attribute[1]);
				if (in_array($name, array_keys($this->__tags[$tag['tag']]['attributes']))) {
					$tag['attributes'][$name] = $attribute[2];
				}
			}
			return $tag;
		}
	}

	function __buildTags() {
		$string = $this->__preparsed;
		$position = 0;
		$length = strlen($string);
		$openedTags = array();

		while (($position < $length)) {
			$tag = array();
			$open = strpos($string, '[', $position);
			if ($open === false) {
				$open = $length;
				$nextPosition = $length;
			}
			if ($open + 1 > $length) {
				$nextPosition = $length;
			} else {
				$nextPosition = strpos($string, '[', $open + 1);
				if ($nextPosition === false) {
					$nextPosition = $length;
				}
			}
			$close = strpos($string, ']', $position);
			if ($close === false) {
				$close = $length + 1;
			}
			if ($open == $position) {
				if (($nextPosition < $close)) {
					$newPosition = $nextPosition;
					$tag['text'] = substr($string, $position, $nextPosition - $position);
					$tag['type'] = 0;
				} else {
					$newPosition = $close + 1;
					$newTag = $this->__buildTag(substr($string, $position, $close - $position + 1));
					if (($newTag !== false)) {
						$tag = $newTag;
						// Remove an opening tag if there is a no matching closing tag in the string
						if ($newTag['type'] == 1 && !empty($this->__tags[$newTag['tag']]['close'])) {
							$closingTag = '[/' . $newTag['tag'] . ']';
							$expectedOccurences = array_key_exists($newTag['tag'], $openedTags) ? $openedTags[$newTag['tag']] : 0;
							if (substr_count($string, $closingTag, $position) < $expectedOccurences + 1) {
								$tag = array();
							}
						}
					}
					if ($tag == array()) {
						$tag['text'] = substr($string, $position, $close - $position + 1);
						$tag['type'] = 0;
					} elseif ($tag['type'] == 1) {
						if (array_key_exists($tag['tag'], $openedTags)) {
							$openedTags[$tag['tag']]++;
						} else {
							$openedTags[$tag['tag']] = 1;
						}
					} elseif ($tag['type'] == 2 && array_key_exists($tag['tag'], $openedTags)) {
						$openedTags[$tag['tag']]--;
					}
				}
			} else {
				$newPosition = $open;
				$tag['text'] = substr($string, $position, $open - $position);
				$tag['type'] = 0;
			}
			if ($tag['type'] === 0 && isset($prev) && $prev['type'] === 0) {
				$tag['text'] = $prev['text'] . $tag['text'];
				array_pop($this->__builtTags);
			}
			$this->__builtTags[] = $tag;
			$prev = $tag;
			$position = $newPosition;
		}
	}

	function __cake() {
		preg_match_all('/(\\[model])([\\s\\S]*?)(\\[\/model])/i', $this->__text, $result, PREG_PATTERN_ORDER);
		if (!empty($result[0])) {
			$count = count($result[0]);
			for ($i = 0; $i < $count; $i++) {
				$position = strpos('<?php', $result[2][$i]);
				if ($position === false) {
					$this->__text = str_replace($result[0][$i], $this->__encode('<h4>[b]Model Class:[/b]</h4>' . $this->__highlightCode('<?php ' . $result[2][$i] . '?>', true)), $this->__text);
				} else {
					$this->__text = str_replace($result[0][$i], $this->__encode('<h4>[b]Model Class:[/b]</h4>' . $this->__highlightCode($result[2][$i])), $this->__text);
				}
			}
		}
		preg_match_all('/(\\[view])([\\s\\S]*?)(\\[\/view])/i', $this->__text, $result, PREG_PATTERN_ORDER);
		if (!empty($result[0])) {
			$count = count($result[0]);
			for ($i = 0; $i < $count; $i++) {
				$this->__text = str_replace($result[0][$i], $this->__encode('<h4>[b]View Template:[/b]</h4>' . $this->__highlightCode($result[2][$i])), $this->__text);
			}
		}
		preg_match_all('/(\\[controller])([\\s\\S]*?)(\\[\/controller])/i', $this->__text, $result, PREG_PATTERN_ORDER);
		if (!empty($result[0])) {
			$count = count($result[0]);
			for ($i = 0; $i < $count; $i++) {
				$position = strpos('<?php', $result[2][$i]);
				if ($position === false) {
					$this->__text = str_replace($result[0][$i], $this->__encode('<h4>[b]Controller Class:[/b]</h4>' . $this->__highlightCode('<?php ' . $result[2][$i] . '?>', true)), $this->__text);
				} else {
					$this->__text = str_replace($result[0][$i], $this->__encode('<h4>[b]Controller Class:[/b]</h4>' . $this->__highlightCode($result[2][$i])), $this->__text);
				}
			}
		}
		preg_match_all('/(\\[helper])([\\s\\S]*?)(\\[\/helper])/i', $this->__text, $result, PREG_PATTERN_ORDER);
		if (!empty($result[0])) {
			$count = count($result[0]);
			for ($i = 0; $i < $count; $i++) {
				$position = strpos('<?php', $result[2][$i]);
				if ($position === false) {
					$this->__text = str_replace($result[0][$i], $this->__encode('<h4>[b]Helper Class:[/b]</h4>' . $this->__highlightCode('<?php ' . $result[2][$i] . '?>', true)), $this->__text);
				} else {
					$this->__text = str_replace($result[0][$i], $this->__encode('<h4>[b]Helper Class:[/b]</h4>' . $this->__highlightCode($result[2][$i])), $this->__text);
				}
			}
		}
		preg_match_all('/(\\[component])([\\s\\S]*?)(\\[\/component])/i', $this->__text, $result, PREG_PATTERN_ORDER);
		if (!empty($result[0])) {
			$count = count($result[0]);
			for ($i = 0; $i < $count; $i++) {
				$position = strpos('<?php', $result[2][$i]);
				if ($position === false) {
					$this->__text = str_replace($result[0][$i], $this->__encode('<h4>[b]Component Class:[/b]</h4>' . $this->__highlightCode('<?php ' . $result[2][$i] . '?>', true)), $this->__text);
				} else {
					$this->__text = str_replace($result[0][$i], $this->__encode('<h4>[b]Component Class:[/b]</h4>' . $this->__highlightCode($result[2][$i])), $this->__text);
				}
			}
		}
		preg_match_all('/(\\[datasource])([\\s\\S]*?)(\\[\/datasource])/i', $this->__text, $result, PREG_PATTERN_ORDER);
		if (!empty($result[0])) {
			$count = count($result[0]);
			for ($i = 0; $i < $count; $i++) {
				$position = strpos('<?php', $result[2][$i]);
				if ($position === false) {
					$this->__text = str_replace($result[0][$i], $this->__encode('<h4>[b]DataSource Class:[/b]</h4>' . $this->__highlightCode('<?php ' . $result[2][$i] . '?>', true)), $this->__text);
				} else {
					$this->__text = str_replace($result[0][$i], $this->__encode('<h4>[b]DataSource Class:[/b]</h4>' . $this->__highlightCode($result[2][$i])), $this->__text);
				}
			}
		}
		preg_match_all('/(\\[behavior])([\\s\\S]*?)(\\[\/behavior])/i', $this->__text, $result, PREG_PATTERN_ORDER);
		if (!empty($result[0])) {
			$count = count($result[0]);
			for ($i = 0; $i < $count; $i++) {
				$position = strpos('<?php', $result[2][$i]);
				if ($position === false) {
					$this->__text = str_replace($result[0][$i], $this->__encode('<h4>[b]Behavior Class:[/b]</h4>' . $this->__highlightCode('<?php ' . $result[2][$i] . '?>', true)), $this->__text);
				} else {
					$this->__text = str_replace($result[0][$i], $this->__encode('<h4>[b]Behavior Class:[/b]</h4>' . $this->__highlightCode($result[2][$i])), $this->__text);
				}
			}
		}
		$this->__preparsed = $this->__text;
	}

	function __child($out, $in) {
		if (!isset($this->__tags[$out]['child']) || ($this->__tags[$out]['child'] == 'all')) {
			return false;
		}
		$ar = explode('^', $this->__tags[$out]['child']);
		$tags = explode(',', $ar[1]);
		if ($ar[0] == 'none') {
			if ($in && in_array($in, $tags)) {
				return false;
			}
			return $this->__buildTag('[' . $tags[0] . ']');
		}
		if ($ar[0] == 'all' && $in && !in_array($in, $tags)) {
			return false;
		}
		return true;
	}

	function __code() {
		preg_match_all('/(\\[code])([\\s\\S]*?)(\\[\/code])/i', $this->__text, $result, PREG_PATTERN_ORDER);
		if (!empty($result[0])) {
			$count = count($result[0]);
			for ($i = 0; $i < $count; $i++) {
				$this->__text = str_replace($result[0][$i], $this->__encode($this->__highlightCode($result[2][$i])), $this->__text);
			}
		}
		preg_match_all('/(\\[php])([\\s\\S]*?)(\\[\/php])/i', $this->__text, $result, PREG_PATTERN_ORDER);
		if (!empty($result[0])) {
			$count = count($result[0]);
			for ($i = 0; $i < $count; $i++) {
				$position = strpos('<?php', $result[2][$i]);
				if ($position === false) {
					$this->__text = str_replace($result[0][$i], $this->__encode('<h4>[b]PHP Snippet:[/b]</h4>' . $this->__highlightCode('<?php ' . $result[2][$i] . '?>', true)), $this->__text);
				} else {
					$this->__text = str_replace($result[0][$i], $this->__encode('<h4>[b]PHP Snippet:[/b]</h4>' . $this->__highlightCode($result[2][$i])), $this->__text);
				}
			}
		}
		preg_match_all('/(\\[sql])([\\s\\S]*?)(\\[\/sql])/i', $this->__text, $result, PREG_PATTERN_ORDER);
		if (!empty($result[0])) {
			$count = count($result[0]);
			for ($i = 0; $i < $count; $i++) {
				$this->__text = str_replace($result[0][$i], $this->__encode('<h4>[b]SQL:[/b]</h4>' . $this->__highlightCode($result[2][$i])), $this->__text);
			}
		}
		preg_match_all('/(\\[html])([\\s\\S]*?)(\\[\/html])/i', $this->__text, $result, PREG_PATTERN_ORDER);
		if (!empty($result[0])) {
			$count = count($result[0]);
			for ($i = 0; $i < $count; $i++) {
				$this->__text = str_replace($result[0][$i], $this->__encode('<h4>[b]HTML:[/b]</h4>' . $this->__highlightCode($result[2][$i])), $this->__text);
			}
		}
		preg_match_all('/(\\[xml])([\\s\\S]*?)(\\[\/xml])/i', $this->__text, $result, PREG_PATTERN_ORDER);
		if (!empty($result[0])) {
			$count = count($result[0]);
			for ($i = 0; $i < $count; $i++) {
				$this->__text = str_replace($result[0][$i], $this->__encode('<h4>[b]XML:[/b]</h4>' . $this->__highlightCode($result[2][$i])), $this->__text);
			}
		}
		preg_match_all('/(\\[css])([\\s\\S]*?)(\\[\/css])/i', $this->__text, $result, PREG_PATTERN_ORDER);
		if (!empty($result[0])) {
			$count = count($result[0]);
			for ($i = 0; $i < $count; $i++) {
				$this->__text = str_replace($result[0][$i], $this->__encode('<h4>[b]CSS:[/b]</h4>' . $this->__highlightCode($result[2][$i])), $this->__text);
			}
		}
		$this->__preparsed = $this->__text;
	}

	function __construct() {
		foreach ($this->filters as $filter) {
			$type = '__' . $filter . 'Tags';
			if (isset($this->$type)) {
				$this->__tags = array_merge($this->__tags, $this->$type);
			}
		}
		$this->__tags = array_merge($this->__tags, $this->__basicTags);
	}

	function __decode($value) {
		preg_match_all('/(\\[encoded])(.*)(\\[\/encoded])/', $value, $result, PREG_PATTERN_ORDER);
		if (!empty($result[0])) {
			$count = count($result[0]);
			for ($i = 0; $i < $count; $i++) {
				$value = str_replace($result[0][$i], base64_decode($result[2][$i]), $value);
			}
		}
		$this->__preparsed = $value;
	}

	function __encode($value) {
		return '[encoded]' . base64_encode($value) . '[/encoded]';
	}

	function __email() {
		$pattern = array('/(^|\\s)([-a-z0-9_.]+@[-a-z0-9.]+\\.[a-z]{2,4})/i', '/\\[email(\\]|.*\\])(.*)\\[\/email\\]/i');
		$replace = array('\\1[email=\\2]\\2[/email]', '[email=\\2\\1\\2[/email]');
		$this->__preparsed = preg_replace($pattern, $replace, $this->__text);
	}

	function __extended() {
		return true;
	}

	function __highlightCode($result) {
		if (!$this->_codeHighlightingEnabled) {
			$formated = '<code>' . $result . '</code>';
		} else {
			$formated = highlight_string($result, true);
		}
		return $formated;
	}

	function __image() {
		$this->__preparsed = preg_replace('/\\[img(\\s?.*)\\](.*)\\[\/img\\]/', "[img=\$2\$1][/img]", $this->__text);
	}

	function __link() {
		$pattern = array("/(?<![\"'=\]\/])(\[[^\]]*\])?(((http|https|ftp):\/\/|www)[@-a-z0-9.]+\.[a-z]{2,4}[^\s()\[\]]*)/i", "!\[url(\]|\s.*\])(.*)\[/url\]!iU", "!\[url=((([a-z]*:(//)?)|www)[@-a-z0-9.]+)([^\s\[\]]*)\](.*)\[/url\]!i");
		$pp = preg_replace_callback($pattern[0], array($this, '__linkExpand'), $this->__text);
		$pp = preg_replace($pattern[1], "[url=\$2\$1\$2[/url]", $pp);
		$this->__preparsed = preg_replace_callback($pattern[2], array($this, '__linkFinish'), $pp);
	}

	function __linkExpand($matches) {
		$pass = strpos($matches[1], '[url');
		if ($pass !== false) {
			return $matches[0];
		}
		$punctuation = '.,;:';
		$trailing = '';
		$last = substr($matches[2], -1);
		while (strpos($punctuation, $last) !== false) {
			$trailing = $last . $trailing;
			$matches[2] = substr($matches[2], 0, -1);
			$last = substr($matches[2], -1);
		}
		$off = strpos($matches[2], ':');
		if ($off === false) {
			return $matches[1] . '[url=http://' . $matches[2] . ']' . $matches[2] . '[' . '/url' . ']' . $trailing;
		}
		$scheme = substr($matches[2], 0, $off);
		if (in_array($scheme, array('http', 'https', 'ftp'))) {
			return $matches[1] . '[url]' . $matches[2] . '[/url]' . $trailing;
		} else {
			return $matches[0];
		}
	}

	function __linkFinish($matches) {
		$urlServ = $matches[1];
		$path = $matches[5];
		$off = strpos($urlServ, ':');
		if ($off === false) {
			$urlServ = 'http://' . $urlServ;
			$off = strpos($urlServ, ':');
		}
		if (!$path) {
			$path = '/';
		}
		$protocol = substr($urlServ, 0, $off);
		if (in_array($protocol, array('http', 'https', 'ftp'))) {
			return '[url=' . $urlServ . $path . ']' . $matches[6] . '[' . '/url' . ']';
		} else {
			return $matches[6];
		}
	}

	function __list() {
		$pattern = array('/\\[\\*\\]/i', '/\\[(u?)list=(?-i:A)(\\s*[^\\]]*)\\]/i', '/\\[(u?)list=(?-i:a)(\\s*[^\\]]*)\\]/i', '/\\[(u?)list=(?-i:I)(\\s*[^\\]]*)\\]/i', '/\\[(u?)list=(?-i:i)(\\s*[^\\]]*)\\]/i', '/\\[(u?)list=(?-i:1)(\\s*[^\\]]*)\\]/i', '/\\[(u?)list([^\\]]*)\\]/i');
		$replace = array('[li]', '[$1list=upper-alpha$2]', '[$1list=lower-alpha$2]', '[$1list=upper-roman$2]', '[$1list=lower-roman$2]', '[$1list=decimal$2]', '[$1list$2]');
		$text = preg_replace($pattern, $replace, $this->__text);
		preg_match_all('/(\\[nested]([\\s\\S]*)\\[\/nested])/', $text, $result, PREG_PATTERN_ORDER);
		if (!empty($result[0])) {
			$count = count($result[0]);
			for ($i = 0; $i < $count; $i++) {
				$text = str_replace($result[0][$i], str_replace("\r\n", " ", $result[2][$i]), $text);
			}
		}
		preg_match_all('/(\\[u?list=?[\\s\\S]*?]([\\s\\S])*?\\[\/u?list])/', $text, $result, PREG_PATTERN_ORDER);
		if (!empty($result[0])) {
			$count = count($result[0]);
			for ($i = 0; $i < $count; $i++) {
				$text = str_replace($result[0][$i], str_replace("\r\n", " ", $result[1][$i]), $text);
			}
		}
		$this->__preparsed = $text;
	}

	function __parent($out, $in) {
		if (!isset($this->__tags[$in]['parent']) || ($this->__tags[$in]['parent'] == 'all')) {
			return false;
		}
		$ar = explode('^', $this->__tags[$in]['parent']);
		$tags = explode(',', $ar[1]);
		if ($ar[0] == 'none') {
			if ($out && in_array($out, $tags)) {
				return false;
			}
			return $this->__buildTag('[' . $tags[0] . ']');
		}
		if ($ar[0] == 'all' && $out && !in_array($out, $tags)) {

			return false;
		}
		return true;
	}

	function __parse() {
		$this->__preparsed = $this->__text;
		$this->__basic();
		foreach ($this->filters as $filter) {
			$this->__text = $this->__preparsed;
			$method = '__' . $filter;
			$this->$method();
		}
		$this->__stripTags();
		$this->__decode($this->__preparsed);
		$this->__buildTags();
		$this->__validateTags();
		$this->__buildOutput();
	}

	function __reset() {
		$this->__text = null;
		$this->__parsed = null;
		$this->__preparsed = null;
		$this->__builtTags = array();
	}

	function __stripTags() {
		if (get_magic_quotes_gpc()) {
			$this->__preparsed = stripslashes($this->__preparsed);
		}
		$this->__preparsed = nl2br($this->__preparsed);
		$this->__preparsed = str_replace(array("&amp;", "&lt;", "&gt;", "><br />", "]<br />"), array("&amp;amp;", "&amp;lt;", "&amp;gt;", ">", "]"), $this->__preparsed);
		$this->__preparsed = preg_replace('#(&\#*\w+)[\x00-\x20]+;#u', "$1;", $this->__preparsed);
		$this->__preparsed = preg_replace('#(&\#x*)([0-9A-F]+);*#iu', "$1$2;", $this->__preparsed);
		$this->__preparsed = preg_replace('#(<[^>]+[\x00-\x20\"\'])(on|xmlns)[^>]*>#iUu', "$1>", $this->__preparsed);
		$this->__preparsed = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*)[\\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iUu', '$1=$2nojavascript...', $this->__preparsed);
		$this->__preparsed = preg_replace('#([a-z]*)[\x00-\x20]*=([\'\"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iUu', '$1=$2novbscript...', $this->__preparsed);
		$this->__preparsed = preg_replace('#(<[^>]+)style[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*).*expression[\x00-\x20]*\([^>]*>#iU', "$1>", $this->__preparsed);
		$this->__preparsed = preg_replace('#(<[^>]+)style[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*).*behaviour[\x00-\x20]*\([^>]*>#iU', "$1>", $this->__preparsed);
		$this->__preparsed = preg_replace('#(<[^>]+)style[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*).*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*>#iUu', "$1>", $this->__preparsed);
		$this->__preparsed = preg_replace('#</*\w+:\w[^>]*>#i', "", $this->__preparsed);
		do {
			$oldstring = $this->__preparsed;
			$this->__preparsed = preg_replace('#</*(applet|meta|xml|blink|link|style|script|embed|object|iframe|frame|frameset|ilayer|layer|bgsound|title|base)[^>]*>#i', "", $this->__preparsed);
		} while ($oldstring != $this->__preparsed);
	}

	function __table() {
		$this->__preparsed = preg_replace('/\\[table(\\s?.*)\\](.*)\\[\/table\\]/', "[table=\$2\$1][/table]", $this->__text);
	}

	function __validateTags() {
		$newTags = array();
		$openTags = array();
		foreach ($this->__builtTags as $tag) {
			$previous = end($newTags);
			if (trim($tag['text']) !== '') {
				switch ($tag['type']) {
					case 0:
						if (($child = $this->__child(end($openTags), 'text')) && $child !== false && $child !== true) {
							$newTags[] = $child;
							$openTags[] = $child['tag'];
						}
						if ($previous['type'] === 0) {
							$tag['text'] = $previous['text'] . $tag['text'];
							array_pop($newTags);
						}
						$newTags[] = $tag;
						break;
					case 1:
						if (!$this->__allowed(end($openTags), $tag['tag']) || ($parent = $this->__parent(end($openTags), $tag['tag'])) === true || ($child = $this->__child(end($openTags), $tag['tag'])) === true) {
							$tag['type'] = 0;
							if ($previous['type'] === 0) {
								$tag['text'] = $previous['text'] . $tag['text'];
								array_pop($newTags);
							}
						} else {
							if ($parent) {
								if ($tag['tag'] == end($openTags)) {
									$newTags[] = $this->__buildTag('[/' . $tag['tag'] . ']');
									array_pop($openTags);
								} else {
									$newTags[] = $parent;
									$openTags[] = $parent['tag'];
								}
							}
							if ($child) {
								$newTags[] = $child;
								$openTags[] = $child['tag'];
							}
							$openTags[] = $tag['tag'];
						}
						$newTags[] = $tag;
						break;
					case 2:
						if (($tag['tag'] == end($openTags) || $this->__allowed(end($openTags), $tag['tag']))) {
							if (in_array($tag['tag'], $openTags)) {
								$tmpOpenTags = array();
								while (end($openTags) != $tag['tag']) {
									$newTags[] = $this->__buildTag('[/' . end($openTags) . ']');
									$tmpOpenTags[] = end($openTags);
									array_pop($openTags);
								}
								$newTags[] = $tag;
								array_pop($openTags);
							}
						} else {
							$tag['type'] = 0;
							if ($previous['type'] === 0) {
								$tag['text'] = $previous['text'] . $tag['text'];
								array_pop($newTags);
							}
							$newTags[] = $tag;
						}
						break;
				}
			} else {
				$newTags[] = $tag;
				unset($tag);
			}
		}
		while (end($openTags)) {
			$newTags[] = $this->__buildTag('[/' . end($openTags) . ']');
			array_pop($openTags);
		}
		$this->__builtTags = $newTags;
	}

/**
 * Parse method used for parsing a BBCode text and generating Html
 * 
 * @param string $string text to convert
 * @param array $options Valid keys are:
 * 	- charset
 * 	- highlight_code: whether or not the highlight_code() PHP function must be used for the code
 * 		It generates a messy markup adn can be disabled for users that want "classic" html <code> tags
 * @return string Parsed string
 */
	function parse($string, $options = array()) {
		$defaults = array(
			'charset' => 'UTF-8',
			'highlight_code' => true);
		extract(array_merge($defaults, $options));

		$this->_codeHighlightingEnabled = $highlight_code;
		
		$this->__charset = $charset;
		$this->__reset();
		$this->__text = $string;
		$this->__parse();
		
		$data = explode(self::$pageSeparator, $this->__parsed);
		if (count($data) == 1) {
			// For compatibility reasons
			$data = $data[0];
		}
		return $data;
	}

/**
 * Strip
 *
 * @param string
 * @param string
 */
	function strip($string, $charset = 'UTF-8') {
		$this->__charset = $charset;
		$this->__reset();
		$this->__text = $string;
		preg_match_all('/(\\[.*])([\\s\\S]*?)(\\[\/.*])/i', $this->__text, $result, PREG_PATTERN_ORDER);
		if (!empty($result[0])) {
			$count = count($result[0]);
			for ($i = 0; $i < $count; $i++) {
				$this->__text = str_replace($result[0][$i], str_replace("\r\n", " ", $result[2][$i]), $this->__text);
			}
		}
		$this->__preparsed = $this->__text;
		$this->__stripTags();
		return $this->__preparsed;
	}
}
