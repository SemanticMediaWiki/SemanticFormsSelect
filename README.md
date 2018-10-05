# Semantic Forms Select

[![Build Status](https://secure.travis-ci.org/SemanticMediaWiki/SemanticFormsSelect.svg?branch=master)](http://travis-ci.org/SemanticMediaWiki/SemanticFormsSelect)
[![Code Coverage](https://scrutinizer-ci.com/g/SemanticMediaWiki/SemanticFormsSelect/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/SemanticMediaWiki/SemanticFormsSelect/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/SemanticMediaWiki/SemanticFormsSelect/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/SemanticMediaWiki/SemanticFormsSelect/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/mediawiki/semantic-forms-select/version.png)](https://packagist.org/packages/mediawiki/semantic-forms-select)
[![Packagist download count](https://poser.pugx.org/mediawiki/semantic-forms-select/d/total.png)](https://packagist.org/packages/mediawiki/semantic-forms-select)

Semantic Forms Select (a.k.a. SFS) can generate a select form element with values retrieved from a `#ask` query or a parser function.

## Requirements

- PHP 5.6 or later
- MediaWiki 1.27 or later
- [Semantic MediaWiki][smw] 2.5 or later
- [Page Forms][pf] 4.0.2 or later

## Installation

The recommended way to install Semantic Forms Select is using [Composer](http://getcomposer.org) with
[MediaWiki's built-in support for Composer](https://www.mediawiki.org/wiki/Composer).

Note that the required extensions Semantic MediaWiki and Page Forms must be installed first according to the installation
instructions provided for them.

### Step 1

Change to the base directory of your MediaWiki installation. This is where the "LocalSettings.php"
file is located. If you have not yet installed Composer do it now by running the following command
in your shell:

    wget https://getcomposer.org/composer.phar

### Step 2
    
If you do not have a "composer.local.json" file yet, create one and add the following content to it:

```
{
	"require": {
		"mediawiki/semantic-forms-select": "~3.0"
	}
}
```

If you already have a "composer.local.json" file add the following line to the end of the "require"
section in your file:

    "mediawiki/semantic-forms-select": "~3.0"

Remember to add a comma to the end of the preceding line in this section.

### Step 3

Run the following command in your shell:

    php composer.phar update --no-dev

Note if you have Git installed on your system add the `--prefer-source` flag to the above command. Also
note that it may be necessary to run this command twice. If unsure do it twice right away.

### Step 4

Add the following line to the end of your "LocalSettings.php" file:

    wfLoadExtension( 'SemanticFormsSelect' );

### Verify installation success

As final step, you can verify SFS got installed by looking at the "Special:Version" page on your wiki and check that it is listed in the semantic extensions section.

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
