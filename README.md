# Semantic Forms Select

[![CI](https://github.com/SemanticMediaWiki/SemanticFormsSelect/actions/workflows/ci.yml/badge.svg)](https://github.com/SemanticMediaWiki/SemanticFormsSelect/actions/workflows/ci.yml)
[![codecov](https://codecov.io/gh/SemanticMediaWiki/SemanticFormsSelect/branch/master/graph/badge.svg?token=sSjXlzUDXI)](https://codecov.io/gh/SemanticMediaWiki/SemanticFormsSelect)

Semantic Forms Select (a.k.a. SFS) can generate a select form element with values retrieved from a `#ask` query or a parser function.

## Requirements

- PHP 7.4 or later
- MediaWiki 1.39 or later
- [Semantic MediaWiki][smw] 4.0.0 or later
- [Page Forms][pf] 5.3.0 or later

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
		"mediawiki/semantic-forms-select": "~4.0"
	}
}
```

If you already have a "composer.local.json" file add the following line to the end of the "require"
section in your file:

    "mediawiki/semantic-forms-select": "~4.0"

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

This extension provides PHP and JavaScript tests that are run by continues integration platform
but can also be executed using `composer phpunit` and `npm ci && npm test` from the extension base directory.

## License

[GNU General Public License, version 2 or later][gpl-licence].

[gpl-licence]: https://www.gnu.org/copyleft/gpl.html
[smw]: https://github.com/SemanticMediaWiki/SemanticMediaWiki
[pf]: https://www.mediawiki.org/wiki/Extension:Page_Forms
[composer]: https://getcomposer.org/
