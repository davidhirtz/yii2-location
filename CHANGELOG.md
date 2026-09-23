## 3.0.0 (in development)

- Renamed the namespace `davidhirtz\yii2\location\` to `Hirtz\Location\` and every directory to StudlyCase (`Models\`, `Modules\Admin\Widgets\Grids\`); requires PHP 8.3 and `davidhirtz/yii2-skeleton` 3.0
- Replaced the three verb permissions per model with one: `Models\Location::AUTH_LOCATION` (`location`) and `Models\Tag::AUTH_TAG` (`tag`); `findLocation()` and `findTag()` take no permission and `LocationTrait::checkLocationPermission()` is gone
- Moved the admin routes under the module: `/admin/location/location/*`, `/admin/location/tag/*` and `/admin/location/location-tag/*`
- Replaced the array-based `getTypes()` with `Models\Types\LocationType` objects carrying `slug()` and `allowTags()`; `Controllers\ApiController::findTypeBySlug()` reads `getSlug()`
- Renamed `Location::hasTagsEnabled()` and `Tag::hasTagsEnabled()` to `allowsTags()`; a location's answer also honours its type
- Renamed `getTrailModelName()` and `getTrailModelType()` to `getAdminName()` and `getAdminType()`; removed `getTrailModelAdminRoute()`, `getAdminRoute()` returns `false` for an unsaved record (`Hirtz\Skeleton\Models\Interfaces\AdminModelInterface`)
- Moved the translated attributes of `Location` and `Tag` from their `_<language>` columns into the skeleton's `translation` table; `TagQuery::withLocationTag()` lost its `$eagerLoading` parameter and takes the join type second
- Removed the per-language tables (`migrations\traits\I18nTablesTrait`); `tableName()` is `{{%location}}`, `{{%tag}}` and `{{%location_tag}}`
- Removed the jQuery autocomplete assets, `AutocompleteInputWidget` and `AutocompleteAssetBundle`; `Modules\Admin\Widgets\Forms\LocationProviderIdField` extends the skeleton's `Widgets\Forms\Fields\AutocompleteField` and `LocationController::actionAutocomplete()` answers an option list for `q`, ignoring a query shorter than `$autocompleteMinLength`
- Changed `Modules\Admin\Interfaces\AutocompleteInterface::getResults()` to take `$input` and return `list<array{text: string, value: mixed}>`
- Replaced the static `Location::getCountryCodes()` map with an instance method returning the codes; `getCountryName()` reads `Hirtz\Skeleton\Helpers\CountryList`
- Removed `Validators\CoordinateValidator`; the skeleton's `AttributeTypecastBehavior` formats `lat` and `lng` at the column's scale, and `0` is no longer stored as `null`
- Replaced the English message texts with `UPPER_SNAKE_CASE` keys; removed the `ru`, `zh-CN` and `zh-TW` translations
- Rewrote the admin on the skeleton's widget system: `Submenu` is `Modules\Admin\Widgets\Navs\LocationSubmenu`, added `LocationHeader`, `TagHeader`, `LocationNavItem`, `LocationActionDropdown`, `TagActionDropdown`, `LocationDeleteButton` and `TagDeleteButton`; forms declare their fields in `getDefaultRows()`, grids their columns in `configure()`, and `LocationGridView`'s `$show*` flags are protected
- Changed `Modules\Admin\Module` to answer `aside()` and `dashboard()` instead of `getNavBarItems()`, `getName()`, `getRoute()` and `getDashboardPanels()`
- Added `custom_attributes` to `location` and `tag` (`CustomAttributeInterface`); the forms render the fields and their type select reloads the form on a change
- Added fulltext search for `Location` and `Tag` (`SearchableInterface`), a POST-only `status` action on `LocationController` and `TagController`, and `TagCollection::reset()`
- Changed `Location::fields()` to include `type` when more than one type is declared, and `Tag::fields()` to return the tag's own attributes instead of the location's
- Changed `LocationTagGridView` into a picker: the name, type icon and count badge no longer link out of the grid
- Changed `ApiController` to extend `Hirtz\Skeleton\Web\Controller`

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
