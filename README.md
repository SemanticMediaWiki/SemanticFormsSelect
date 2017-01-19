# Semantic Forms Select

[![Build Status](https://secure.travis-ci.org/SemanticMediaWiki/SemanticFormsSelect.svg?branch=master)](http://travis-ci.org/SemanticMediaWiki/SemanticFormsSelect)
[![Code Coverage](https://scrutinizer-ci.com/g/SemanticMediaWiki/SemanticFormsSelect/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/SemanticMediaWiki/SemanticFormsSelect/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/SemanticMediaWiki/SemanticFormsSelect/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/SemanticMediaWiki/SemanticFormsSelect/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/mediawiki/semantic-forms-select/version.png)](https://packagist.org/packages/mediawiki/semantic-forms-select)
[![Packagist download count](https://poser.pugx.org/mediawiki/semantic-forms-select/d/total.png)](https://packagist.org/packages/mediawiki/semantic-forms-select)
[![Dependency Status](https://www.versioneye.com/php/mediawiki:semantic-forms-select/badge.png)](https://www.versioneye.com/php/mediawiki:semantic-forms-select)

Semantic Forms Select (a.k.a. SFS) can generate a select form element with values retrieved from a `#ask` query or a parser function.

## Requirements

- PHP 5.5 or later
- MediaWiki 1.27 or later
- [Semantic MediaWiki][smw] 2.0 or later
- [Page Forms][pf] 4.0.2 or later

## Installation

The recommended way to install Semantic Forms Select is by using [Composer][composer] with an entry in MediaWiki's `composer.json` or alternatively `composer.local.json`.

```json
{
	"require": {
		"mediawiki/semantic-forms-select": "~2.1"
	}
}
```
1. From your MediaWiki installation directory, execute  
   `composer require mediawiki/semantic-forms-select:~2.1`
2. Add the following line to your "LocalSettings.php" file _after the inclusion of Semantic MediaWiki and Page Forms_:  
   `wfLoadExtension( 'SemanticFormsSelect' );`
3. Navigate to _Special:Version_ on your wiki and verify that the extension
   has been successfully installed.
   
Note that the required extensions Semantic MediaWiki and Page Forms must be installed first according to the installation
instructions provided with them.

## Usage

Please consult the [help](https://www.mediawiki.org/wiki/Extension:SemanticFormsSelect) page for more information and examples.

## Contribution and support

If you want to contribute work to the project please subscribe to the developers mailing list and
have a look at the contribution guideline.

* [File an issue](https://github.com/SemanticMediaWiki/SemanticFormsSelect/issues)
* [Submit a pull request](https://github.com/SemanticMediaWiki/SemanticFormsSelect/pulls)
* Ask a question on [the mailing list](https://www.semantic-mediawiki.org/wiki/Mailing_list)
* Ask a question on the #semantic-mediawiki IRC channel on Freenode.

Original code from https://code.google.com/p/semanticformsselect/downloads/list

## Tests

This extension provides unit and integration tests that are run by a [continues integration platform][travis]
but can also be executed using `composer phpunit` from the extension base directory.

## License

[GNU General Public License, version 2 or later][gpl-licence].

[gpl-licence]: https://www.gnu.org/copyleft/gpl.html
[smw]: https://github.com/SemanticMediaWiki/SemanticMediaWiki
[travis]: https://travis-ci.org/SemanticMediaWiki/SemanticFormsSelect
[pf]: https://www.mediawiki.org/wiki/Extension:Page_Forms
[composer]: https://getcomposer.org/
