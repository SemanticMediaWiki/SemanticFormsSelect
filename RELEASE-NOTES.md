This file contains the RELEASE-NOTES of the Semantic Forms Select (a.k.a. SFS) extension.

### 3.0.0

not yet released

* #53 Support "mapping property" / "mapping template" in value field (by Alexander Gesinn), requires changes in PF
* #62 Make SFS compliant with recent PF versions (by Alexander Gesinn)
* refactored SemanticFormsSelectInput class -> moved logic to new SelectField class (by Alexander Gesinn)
* added Unit Tests (by Felix Ashu)

### 2.1.0

Released on January 19, 2017.

* #54 Fixed select fields no to update for an existing page (by Alexander Gesinn)
* #56 Fixed issue when multiple `sf_select` have the same parameter in the same page (by Toni Hermoso Pulido)
* Provided translation updates (by TranslateWiki.net community)

### 2.0.0

Released on December 19, 2016.

* Dropped support for MediaWiki 1.26 and lower
* Dropped support for PHP 5.4 and lower
* Dropped support for the Semantic Forms extension
* Added support for the Page Forms extension
* #29 Added support for I18n (by James Hong Kong)
* #29 Made internal code improvements (by James Hong Kong)
* #30 Migrated to MediaWiki 1.25 extension registration method (by James Hong Kong)
* #31 Fixed missing input not being populated when parent input only has one value (by Pierre Rudloff)
* #34 Added escaping for spaces in template names (by Pierre Rudloff)
* #42, #45 Migrated extension to use the Page Forms extension instead of the Semantic Forms extension (by Thomas Mulhall and Sébastien Beyou)
* #50 Internal code changes regarding bootstrap (by James Hong Kong)
* Provided translation updates (by TranslateWiki.net community)
* Updated testing environment (by Thomas Mulhall and James Hong Kong)

### 1.3.0

Released on November 30, 2015.

* Added the `SFS` PHP namespace (by James Hong Kong)
* Added full Composer compatibility and autoloading (PSR-4) (by James Hong Kong)
* Added stricter control over how the `scriptSelect.js` is being used (removed JS elements from PHP) and accessed from MediaWiki (by James Hong Kong)
* Added `ResourceLoader` support (by James Hong Kong)
* Added unit tests and general test coverage support (by James Hong Kong)
* Fixed parser call from `Special:FormEdit/DemoAjax1` (by James Hong Kong)
* Provided COPYING file (by James Hong Kong)

### 1.2.2

Released on September 18, 2015.

* Fixed options filling in internal script (by Toni Hermoso Pulido)

### 1.2.0

Released on September 11, 2015.

* Dropped support for MediaWiki 1.22 and earlier (by Toni Hermoso Pulido)
* Dropped support for PHP 5.2 and earlier (by Toni Hermoso Pulido)
* Extension converted to use Composer (by Toni Hermoso Pulido)
* Converted depreciated AJAX methods to API methods (by Toni Hermoso Pulido)
* Add support for the Semantic Forms "mapping template" parameter (by Toni Hermoso Pulido)
* Improved README (by Toni Hermoso Pulido)
* Several internal improvements (by Toni Hermoso Pulido)

### 1.1.0

Released on Febrary 23, 2013.

* Various improvements (by Jason Zhang)

### 1.0.0

Released on January 17, 2012.

* Initial release (by Jason Zhang)
