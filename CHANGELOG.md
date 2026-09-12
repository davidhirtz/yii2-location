## 3.0.0 (in development)

- `TagCollection::invalidateCache()` also drops the static list, which it left in place before, so a saved tag is seen
  by the next `getAll()` in the same process; `reset()` drops the static alone and `Bootstrap` calls it, so an
  application starts without the tags of the one before it. `$_tags` is `$tags`
- `TagQuery::withLocationTag()` lost its `$eagerLoading` parameter and takes the join type second: the location tag
  is read off the joined row (`ActiveQuery::selectWith()`) rather than queried again; `LocationQuery::andWhereTagId()`
  does the same when called with eager loading
- `Models\Location::getAdminRoute()` and `Models\Tag::getAdminRoute()` return `false` for a record without an id
  rather than a route with a null id — the guard moved here from `getTrailModelAdminRoute()`, which is gone. Both
  models implement the skeleton `Models\Interfaces\AdminRouteInterface`
- `Models\Location` and `Models\Tag` implement `CustomAttributeInterface`. Added the `custom_attributes` column to
  `location` and `tag`, excluded from the trail. Their `rules()` spread `parent::rules()` now, which is what injects
  the custom attribute rules
- The admin forms render the custom attribute fields and their type select is a `TypeSelectField`, so a type change
  reloads the form when the types render different fields; `LocationController` and `TagController` guard their save
  with `Request::isFormReload()`

- Translated attributes of `Location` and `Tag` moved from their `_xx` columns into the skeleton's
  `translation` table (`M260910140000Translations`)
- Changed the API URL rule to a `Route` registered via `Application::addRoutes()`

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