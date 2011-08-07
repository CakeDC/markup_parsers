# Markup Parsers Plugin for CakePHP #

This plugin offers a solution for working with different type of markup for you application. It offers both some markup parser implementations and a parser factory responsible for registering available parser classes and returning instances of them.

It provides three different markup implementations:

## BBcode Parser ##

Supports the [b], [p], [u], [s], [sub], [sup]. [indent], [img], [color], [code], [color], [size], [font]. [align], [quote], [h1] ... [h6], [table] and it's associated [tr], [th] and [td], [list], [ulist] and [li]. It can also link emails and urls using the [url] tag

This parser will also split the passed string in pages, using the [Page separator] marker as separator.

## Markdown Parser ##

Supports regular markdown syntax with a few exceptions and additions:

    * reference style links are not supported, only inline links work.
    * Setext style headers are not supported, only ATX style headers work.
    * Block quotes are not implemented at this time.

As this class was original intended to parse functions doc blocks, it has some additional syntax items:

    * Class::method() links. These are links to other class + methods in your code base.
    * Class::$property links. These are links to other class properties in your code base.
    * Code blocks - Code blocks can be indicated with either {{{ code }}} or @@@ code @@@ or indented.

## Html ##

The simplest of all parsers, it just adds some sugar to split a HTML string into multiple pages, and strip some dangerous content.

This parser splits the input string in multiple pages using the `<!--Page Separator-->` marker as separator

## The ParserRegistry Class##

Every parser needs to be configured so they can be instantiated by the helper using the ParserRegistry class. To configure the parsers your are going to use in tour app put use the Configure class to list them.

        Configure::write('Parsers.markdown' => array(
			'name' => 'Markdown',
			'class' => array('MarkdownParser', 'MarkupParsers.Parser'), 
		));

If you have your own parser implementation you can list it to into the array this way:

        Configure::write('Parsers.my_parser' => array(
		    'name' => 'MyParser',
		    'class' => array('MyParser', 'MyPlugin.Parser'),
	    ));

## Writing a parser ##

You can develop your own parser or extend the shipped ones for your specific needs.
A Parser class must be a Library implementing the `ParserInterface` shipped as a plugin lib.

Here is a simple Parser example:

	App::uses('ParserInterface', 'MarkupParsers.Lib');
	class FoobarParser implements ParserInterface {
		public function parse($string, $options = array()) {
			return str_replace('foo', 'bar', $string);
		}
	}

Then you can access it from the ParserRegistry after configuring it as detailed previously.

## Using the helper ##

To use any parser in your views just include the Parser helper into the $helpers array in your controller:

        public $helpers = array('MarkupParsers.Parser');

And in your views

        echo $this->Parser->parse($string, 'my_parser'); // the second parameter can be left blank ad will use 'markdown' as default


## Requirements ##

* PHP version: PHP 5.2+
* CakePHP version: Cakephp 1.3 Stable

## Support ##

For support and feature request, please visit the [Markup Parsers Plugin Support Site](http://cakedc.lighthouseapp.com/projects/61291-markup-parsers).

For more information about our Professional CakePHP Services please visit the [Cake Development Corporation website](http://cakedc.com).

## License ##

Copyright 2009-2011, [Cake Development Corporation](http://cakedc.com)

Licensed under [The MIT License](http://www.opensource.org/licenses/mit-license.php)<br/>
Redistributions of files must retain the above copyright notice.

## Copyright ###

Copyright 2009-2011<br/>
[Cake Development Corporation](http://cakedc.com)<br/>
1785 E. Sahara Avenue, Suite 490-423<br/>
Las Vegas, Nevada 89104<br/>
http://cakedc.com<br/>