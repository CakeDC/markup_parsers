<?php

//App::import('Vendor', 'MarkupParsers.Markdownify', array('file'=>'markdownify'.DS.'markdownify.php'));
App::import('Vendor', 'MarkupParsers.Markdownify', array('file'=>'markdownify'.DS.'markdownify_extra.php'));

/**
 * Tries to use markdownify lib to undo html and parse it back to markdownify
 * this is usual if you start blogging in markdownify and need to convert previous posts
 * into this new format
 *
 * 2012-02-08 ms
 */
class MarkdownifyLib {

	public $settings = array();

	protected $_defaults = array(
		'engine' => 'extra', # extra or default
		'keepHTML' => MDFY_KEEPHTML,
		'bodyWidth' => MDFY_BODYWIDTH,
		'linksAfterEachParagraph' => MDFY_LINKS_EACH_PARAGRAPH
	);

	public function __construct($settings = array()) {
		//$linksAfterEachParagraph = MDFY_LINKS_EACH_PARAGRAPH, $bodyWidth = MDFY_BODYWIDTH, $keepHTML = MDFY_KEEPHTML
		$this->settings = array_merge($this->_defaults, $settings);
	}

	public function parseString($html, $options = array()) {
		if ($this->settings['engine'] == 'extra') {
			$Parser = new Markdownify_Extra();
		} else {
			$Parser = new Markdownify();
		}
		return $Parser->parseString($html);
	}

}
