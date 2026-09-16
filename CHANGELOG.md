## 3.0.0 (in development)

- **`Modules\Admin\Controllers\LocationController::actionCreate()` and `TagController::actionCreate()` honour
  their `type` parameter again**, building the record through `instantiate()` so the type decides the class
  (monorepo issue #105), and take the type a form posted over it. The assignment was `$location->type ??= $type`
  *after* `loadDefaultValues()`, and the column carries a default — so the parameter never applied.

- **`Modules\Admin\Widgets\Grids\LocationTagGridView` is a picker**, as the cms and media pickers already were:
  the tag's name, type icon and location count badge no longer lead out of the grid, and the tag's own page is an
  external link button instead. `TagGridView::isPicker()` and `getRecordUrl()` are the hooks; a subclass that
  linked the name itself moves to the latter.

- `Modules\Admin\Widgets\Forms\LocationActiveForm` and `TagActiveForm` declare their fields in
  `getDefaultRows()` instead of assigning `$this->rows ??=` in `configure()`, which the skeleton's
  `Widgets\Forms\ActiveForm` needs to normalize them before an `EVENT_CONFIGURE` listener sees them (monorepo
  issue #120). A subclass overriding `configure()` to change the fields has to move to the hook.

- **`Models\Location::hasTagsEnabled()` and `Models\Tag::hasTagsEnabled()` are `allowsTags()`**, matching the
  platform's vocabulary for a capability a record has. The location's answers for its type too:
  `Models\Types\LocationType::allowTags(false)` narrows the module's `enableTags`.

- **Tom Select is gone, and with it the bundle's whole asset pipeline** — `package.json`, `esbuild.js`,
  `resources/assets` and `Modules\Admin\Assets\AutocompleteAssetBundle`. `Modules\Admin\Widgets\Forms\LocationProviderIdField`
  extends the skeleton's `Widgets\Forms\Fields\AutocompleteField` instead, so the suggestions are rendered by the
  server and look like every other dropdown in the admin. `LocationController::actionAutocomplete()` therefore
  answers with the option list rather than JSON, and ignores a query shorter than `$autocompleteMinLength`.

- `Models\Types\LocationType` is the location's type class and carries `slug()`, what
  `Controllers\ApiController::findTypeBySlug()` matches to give a type a URL of its own. See the skeleton's
  UPGRADE.md

- `Models\Tag::fields()` returns the tag's own attributes. It was a copy of `Location::fields()`, so a serialized
  tag carried `formatted_address`, `lat` and `lng` — always null on a tag — and left out its own `type`
- `Modules\Admin\Widgets\Grids\LocationGridView::getNameColumnContent()` returns `string|Stringable`, as the cms
  grids of the same shape already did. It declared `string` while composing the link from `Html\A`, so the whole
  location index was a `TypeError` for a location with neither an address nor a tag
- The tag index translates through `COMMON_TAGS`, and `TAG_CREATE_TITLE` had no English text. The stray
  `src/messages/ru/location.php` left over from 2.x is gone
- **One permission per admin-managed model.** `Models\Location::AUTH_LOCATION` (`location`) and
  `Models\Tag::AUTH_TAG` (`tag`) replace the three verb permissions each.
  `Migrations\M260914130000AuthItems` grants the new item to every parent and assignee of any old one.
  `findLocation()` and `findTag()` lost their permission argument, `LocationTrait::checkLocationPermission()` is
  gone, and no `can()` call takes a record any more
- `Models\Location`, `Models\Tag` and `Models\LocationTag` implement the skeleton's
  `Models\Interfaces\AdminModelInterface`: `getTrailModelName()` and `getTrailModelType()` are `getAdminName()`
  and `getAdminType()`, and the boilerplate name is `Models\Traits\AdminModelTrait`'s
- `Models\Location` and `Models\Tag` are searchable: they implement the skeleton's
  `Models\Interfaces\SearchableInterface`, and `Bootstrap` registers them on the `search` component. A location
  indexes its name and address parts at weight 0.6, a tag its name at 0.5. `Module::$enableTags` gates the tag
  both ways — while it is off nothing is written, and a row an earlier rebuild left behind stays out of the
  results
- `esbuild.js` uses the skeleton's shared `esbuild.config.js`, so the styles are built by sass with autoprefixer
  instead of esbuild's css loader. `resources/assets/src/css/autocomplete.css` is now `autocomplete.scss` — it
  already nested with `&`, which sass flattens into plain selectors rather than shipping native CSS nesting
- `Controllers\ApiController` extends the skeleton `Web\Controller` instead of `yii\web\Controller`, so `$this->request`
  is the skeleton `Request` and the hand-declared `@property Response $response` is gone
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