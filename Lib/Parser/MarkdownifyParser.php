<?php

App::import('Vendor', 'MarkupParsers.Markdownify', array('file'=>'markdownify' . DS . 'markdownify_extra.php'));
App::uses('ParserInterface', 'MarkupParsers.Lib');

/**
 * Tries to use markdownify lib to undo html and parse it back to markdownify.
 * This is usual if you start blogging in markdownify and need to convert previous posts
 * into this new format.
 */
class MarkdownifyParser implements ParserInterface {

	public $settings = array();

	protected $_defaults = array(
		'engine' => 'extra', # extra or default
		'keepHTML' => MDFY_KEEPHTML,
		'bodyWidth' => MDFY_BODYWIDTH,
		'linksAfterEachParagraph' => MDFY_LINKS_EACH_PARAGRAPH
	);

/**
 * MarkdownifyParser::parse()
 *
 * @param string $html
 * @param array $options
 * @return string
 */
	public function parse($html, $options = array()) {
		$this->_settings($options);

		if ($this->settings['engine'] === 'extra') {
			$Parser = new Markdownify_Extra();
		} else {
			$Parser = new Markdownify();
		}
		return $Parser->parseString($html);
	}

	/**
	 * MarkdownifyParser::_settings()
	 *
	 * @param array $settings
	 * @return void
	 */
	protected function _settings($settings = array()) {
		//$linksAfterEachParagraph = MDFY_LINKS_EACH_PARAGRAPH, $bodyWidth = MDFY_BODYWIDTH, $keepHTML = MDFY_KEEPHTML
		$this->settings = array_merge($this->_defaults, $settings);
	}

}
