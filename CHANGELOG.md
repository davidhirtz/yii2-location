## 1.3.1 (Jan 11, 2025)

- Enhanced `Location` and `Tag` deletion to recalculate related counters and delete `LocationTag` records accordingly.

## 1.3.0 (Dec 1, 2025)

- Requires PHP 8.3 or higher
- Added Russian language support

## 1.2.6 (Mar 7, 2025)

- Added validation for `Location::$lat` and `Location::$lng`
- Added `LocationTag::getIsBatch()` check for updating multiple tags at once
- Added `LocationTag::populateTagRelation()`

## 1.2.5 (Mar 6, 2025)

- Fixed `Location::$location` validation rule

## 1.2.4 (Jan 23, 2025)

- Changed `Bootstrap` I18N configuration

## 1.2.3 (Oct 1, 2024)

- Fixed misspelled Module parameter `Module::enableApiRoutes` in `Bootstrap`

## 1.2.2 (Aug 23, 2024)

- Added `unqiue` validation rule for `Tag::$name`
- Added `Module::$enableApiRoutes` flag to enable the API route, defaults to `true`
- Changed default of `Module::$enableTags` to `false`

## 1.2.1 (Aug 19, 2024)

- Changed `Bootstrap` to use `ApplicationTrait::addUrlManagerRules()` to prevent the initialization of the URL manager
  before the bootstrap is completed

## 1.2.0 (Aug 1, 2024)

- Added tag functionality

## 1.1.0 (Jul 18, 2024)

- Added frontend controller with route `api/location/<type-slug>.<format>`.
- Added backend autocomplete JavaScript assets for location search.