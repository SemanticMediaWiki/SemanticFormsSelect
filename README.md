# SemanticFormsSelect MediaWiki Extension

[![Packagist download count](https://poser.pugx.org/mediawiki/semantic-forms-select/d/total.png)](https://packagist.org/packages/mediawiki/semantic-forms-select)

Original code from https://code.google.com/p/semanticformsselect/downloads/list

## Requirements

- PHP 5.3.2 or later
- MediaWiki 1.23 or later
- [Semantic MediaWiki][smw] 2.0 or later
- [Semantic Forms][sf] 2.7 or later

## Installation

The recommended way to install Semantic Forms Select is by using [Composer][composer] with an entry in MediaWiki's `composer.json`.

```json
{
	"require": {
		"mediawiki/semantic-forms-select": "~1.2"
	}
}
```
1. From your MediaWiki installation directory, execute
   `composer require mediawiki/semantic-forms-select:~1.2`
2. Navigate to _Special:Version_ on your wiki and verify that the package
   have been successfully installed.

## Usage

See http://www.mediawiki.org/wiki/Extension:SemanticFormsSelect for more information and examples.

## Contribution and support

If you want to contribute work to the project please subscribe to the developers mailing list and
have a look at the contribution guideline.

* [File an issue](https://github.com/SemanticMediaWiki/SemanticFormsSelect/issues)
* [Submit a pull request](https://github.com/SemanticMediaWiki/SemanticFormsSelect/pulls)
* Ask a question on [the mailing list](https://semantic-mediawiki.org/wiki/Mailing_list)
* Ask a question on the #semantic-mediawiki IRC channel on Freenode.

## Tests

This extension provides unit and integration tests that are run by a [continues integration platform][travis]
but can also be executed using `composer phpunit` from the extension base directory.

## License

[GNU General Public License, version 2 or later][gpl-licence].

[gpl-licence]: https://www.gnu.org/copyleft/gpl.html
[smw]: https://github.com/SemanticMediaWiki/SemanticMediaWiki
[travis]: https://travis-ci.org/SemanticMediaWiki/SemanticFormsSelect
[sf]: https://www.mediawiki.org/wiki/Extension:Semantic_Forms
[composer]: https://getcomposer.org/
