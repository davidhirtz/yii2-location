# Upgrading to 3.0

Read the skeleton's `UPGRADE.md` first: the namespace rename, the widget system, the translation table, custom
attributes, the permission model and the search index are all skeleton changes this bundle only follows. What is
below is what a project using locations changes on top of that.

## Requirements

- PHP `^8.3`
- `davidhirtz/yii2-skeleton` 3.0. Nothing else; `davidhirtz/yii2-location-google` is optional and fills the
  autocomplete slot described below.
- A project's `composer.json` keeps `davidhirtz/yii2-location`; the bundle still bootstraps itself through
  `extra.bootstrap`.

## Renames

### Namespaces

| 1.x                                            | 3.0                                     |
|------------------------------------------------|-----------------------------------------|
| `davidhirtz\yii2\location\`                    | `Hirtz\Location\`                       |
| `davidhirtz\yii2\location\models\`             | `Hirtz\Location\Models\`                |
| `davidhirtz\yii2\location\models\collections\` | `Hirtz\Location\Models\Collections\`    |
| `davidhirtz\yii2\location\models\queries\`     | `Hirtz\Location\Models\Queries\`        |
| `davidhirtz\yii2\location\controllers\`        | `Hirtz\Location\Controllers\`           |
| `davidhirtz\yii2\location\modules\admin\…`     | `Hirtz\Location\Modules\Admin\…`        |
| `davidhirtz\yii2\location\validators\`         | `Hirtz\Location\Validators\`            |
| `davidhirtz\yii2\location\migrations\`         | `Hirtz\Location\Migrations\`            |

Every directory below `src/` is StudlyCase now (`controllers` → `Controllers`, `widgets/grids` →
`Widgets\Grids`, `interfaces` → `Interfaces`, `traits` → `Traits`). The class names themselves are unchanged
unless listed here.

### Classes

| 1.x                                                 | 3.0                                                                  |
|-----------------------------------------------------|----------------------------------------------------------------------|
| `modules\admin\widgets\navs\Submenu`                | `Modules\Admin\Widgets\Navs\LocationSubmenu` (record pages) and `LocationHeader` / `TagHeader` (titles, breadcrumbs, create button) |
| `modules\admin\widgets\forms\AutocompleteInputWidget` | `Modules\Admin\Widgets\Forms\LocationProviderIdField`               |
| `modules\admin\assets\AutocompleteAssetBundle`      | removed, no client script is needed                                  |
| `migrations\traits\I18nTablesTrait`                 | removed                                                              |
| `migrations\M240715115920Location`, `M240731193312Tag` | `Migrations\M260101000700LocationBaseline` (fresh installs only, see below) |
| —                                                   | `Models\Types\LocationType`                                          |
| —                                                   | `Modules\Admin\Widgets\Navs\LocationNavItem`, `LocationActionDropdown`, `TagActionDropdown`, `Modules\Admin\Widgets\Buttons\LocationDeleteButton`, `TagDeleteButton` |

### Constants

| 1.x                                                                             | 3.0                               |
|---------------------------------------------------------------------------------|-----------------------------------|
| `Location::AUTH_LOCATION_CREATE`, `AUTH_LOCATION_UPDATE`, `AUTH_LOCATION_DELETE` | `Location::AUTH_LOCATION` (`location`) |
| `Tag::AUTH_TAG_CREATE`, `AUTH_TAG_UPDATE`, `AUTH_TAG_DELETE`                    | `Tag::AUTH_TAG` (`tag`)           |

### Methods and properties

| 1.x                                                                          | 3.0                                                                                   |
|------------------------------------------------------------------------------|---------------------------------------------------------------------------------------|
| `Location::hasTagsEnabled()`, `Tag::hasTagsEnabled()`                        | `allowsTags()`                                                                        |
| `Location::getTrailModelName()`, `Tag::…`, `LocationTag::…`                  | `getAdminName()`                                                                      |
| `Location::getTrailModelType()`, `Tag::…`, `LocationTag::…`                  | `getAdminType()`                                                                      |
| `Location::getTrailModelAdminRoute()`, `Tag::…`                              | removed; `getAdminRoute()` returns `array\|false` and is `false` without an id        |
| `Location::getCountryCodes()` (static, `code => name`)                       | `getCountryCodes()` (instance, `list<string>`); names come from `Hirtz\Skeleton\Helpers\CountryList::getNames()` |
| `Location::getTypes()` (static array, `['slug' => …]`)                       | `getTypes()` (instance, `list<LocationType>`); `LocationType::slug()`, `allowTags()`  |
| `LocationQuery::replaceI18nAttributes()`, `TagQuery::…`                      | `withTranslations()`                                                                  |
| `TagQuery::withLocationTag(int $locationId, bool $eagerLoading = true, string $joinType = 'LEFT JOIN')` | `withLocationTag(int $locationId, string $joinType = 'LEFT JOIN')` |
| `TagCollection::$_tags`                                                      | `$tags`; `reset()` added, `invalidateCache()` resets it too                          |
| `AutocompleteInterface::getResults(string $term): array`                     | `getResults(string $input): list<array{text: string, value: mixed}>`                  |
| `LocationController::actionAutocomplete(string $term)` (JSON)                | `actionAutocomplete(?string $q = null)` (HTML option list); `$autocompleteMinLength`  |
| `LocationTrait::findLocation(int $id, ?string $permissionName = null)`       | `findLocation(int $id)`; `checkLocationPermission()` removed                          |
| `TagTrait::findTag(int $id, ?string $permissionName = null)`                 | `findTag(int $id)`                                                                    |
| `LocationActiveForm::countryCodeField()`, `providerIdField()`                | `getCountryCodeField()`, `getProviderIdField()` (protected, called from `getDefaultRows()`) |
| `LocationActiveForm::$fields`, `TagActiveForm::$fields` (set in `init()`)    | `getDefaultRows()`                                                                    |
| `LocationGridView::$showTagDropdown`, `$showTags`, `$showTypeDropdown` (public) | protected                                                                          |
| `LocationGridView::nameColumn()`, `tagCountColumn()`, `updatedAtColumn()`, `buttonsColumn()`, `tagDropdown()`, `renderTagButtons()` | `getNameColumn()`, `getTagCountColumn()`, `getUpdatedAtColumn()`, `getButtonColumn()`, `getTagDropdown()`, `getTagButtons()` |
| `TagGridView::nameColumn()`, `locationCountColumn()`, `updatedAtColumn()`, `buttonsColumn()` | `getNameColumn()`, `getLocationCountColumn()`, `getUpdatedAtColumn()`, `getButtonColumn()` |
| `Submenu::$location`                                                         | `LocationSubmenu::model()`                                                            |
| `Modules\Admin\Module::getName()`, `getRoute()`, `getNavBarItems()`, `getDashboardPanels()` | `aside(Nav $nav)`, `dashboard(Dashboard $dashboard)`                    |
| `Modules\Admin\Module::$layout`                                              | removed, the skeleton admin module sets it                                            |

### Admin routes

| 1.x                                                   | 3.0                                                              |
|-------------------------------------------------------|------------------------------------------------------------------|
| `/admin/location/index`, `create`, `update`, `delete`, `autocomplete` | `/admin/location/location/…`, plus `status` (POST)  |
| `/admin/tag/index`, `create`, `update`, `delete`      | `/admin/location/tag/…`, plus `status` (POST)                    |
| `/admin/location-tag/index`, `create`, `delete`       | `/admin/location/location-tag/…`                                 |

`/admin/location` alone still opens the location index: the admin submodule's `defaultRoute` is `location`.

### Tables and columns

| 1.x                                                             | 3.0                                                   |
|-----------------------------------------------------------------|-------------------------------------------------------|
| `location.<attribute>_<language>`, `tag.name_<language>`        | rows in the skeleton's `translation` table            |
| per-language copies of `location`, `tag`, `location_tag`        | removed, one table each                               |
| —                                                               | `location.custom_attributes`, `tag.custom_attributes` |
| auth items `locationCreate`, `locationUpdate`, `locationDelete` | `location`                                            |
| auth items `tagCreate`, `tagUpdate`, `tagDelete`                | `tag`                                                 |

### Message keys

The `location` category is addressed by key now. A project overriding a text replaces the English string with
the key in its own `messages/<language>/location.php`:

| 1.x                                                                             | 3.0                                                                                         |
|---------------------------------------------------------------------------------|---------------------------------------------------------------------------------------------|
| `Location`, `Locations`, `Tag`, `Tags`                                          | `COMMON_LOCATION`, `COMMON_LOCATIONS`, `COMMON_TAG`, `COMMON_TAGS`                          |
| `Name` (location), `Name` (tag)                                                 | `LOCATION_NAME_LABEL`, `TAG_NAME_LABEL`                                                     |
| `Formatted address`, `Street`, `House number`, `City`, `Postal code`, `District`, `State`, `Country`, `Latitude`, `Longitude`, `Provider ID` | `LOCATION_FORMATTED_ADDRESS_LABEL`, `LOCATION_STREET_LABEL`, `LOCATION_HOUSE_NUMBER_LABEL`, `LOCATION_LOCALITY_LABEL`, `LOCATION_POSTAL_CODE_LABEL`, `LOCATION_DISTRICT_LABEL`, `LOCATION_STATE_LABEL`, `LOCATION_COUNTRY_CODE_LABEL`, `LOCATION_LAT_LABEL`, `LOCATION_LNG_LABEL`, `LOCATION_PROVIDER_ID_LABEL` |
| `Tags` (location count), `Locations` (tag count)                                | `LOCATION_TAG_COUNT_LABEL`, `TAG_LOCATION_COUNT_LABEL`                                      |
| `Search for a location ...`                                                     | `LOCATION_PROVIDER_ID_SEARCH`                                                               |
| `New Location`, `Create New Location`                                           | `LOCATION_HEADER_NEW_LOCATION` (header button), `LOCATION_CREATE_BUTTON` (dashboard), `LOCATION_CREATE_TITLE` |
| `New Tag`, `Create New Tag`                                                     | `TAG_HEADER_NEW_TAG`, `TAG_CREATE_TITLE`                                                    |
| `Delete Location`, `Delete Tag`                                                 | `LOCATION_BUTTON_DELETE`, `TAG_BUTTON_DELETE`; confirmations `LOCATION_CONFIRM_DELETE`, `TAG_CONFIRM_DELETE` |
| `The location was created.` / `updated.` / `deleted.`                           | `LOCATION_SUCCESS_CREATED`, `LOCATION_SUCCESS_UPDATED`, `LOCATION_SUCCESS_DELETED`          |
| `The tag was created.` / `updated.` / `deleted.`                                | `TAG_SUCCESS_CREATED`, `TAG_SUCCESS_UPDATED`, `TAG_SUCCESS_DELETED`                         |
| `The tag was added to the location.` / `removed …`                              | `LOCATION_TAG_SUCCESS_ADDED`, `LOCATION_TAG_SUCCESS_REMOVED`                                |
| `Location–Tag`, `Location`, `Tag`, `Added` (the `LocationTag` labels)           | `LOCATION_TAG_LOCATION_TAG`, `LOCATION_TAG_LOCATION_ID_LABEL`, `LOCATION_TAG_TAG_ID_LABEL`, `LOCATION_TAG_UPDATED_AT_LABEL` |
| `Unnamed`                                                                       | `LOCATION_UNNAMED`                                                                          |
| `Update locations`, `Create locations`, `Delete locations`                      | `AUTH_LOCATION_DESCRIPTION`                                                                 |
| `Update location tags`, `Create location tags`, `Delete location tags`          | `AUTH_TAG_DESCRIPTION`                                                                      |
| `Edit Location`, `Edit Tag`                                                     | removed, the header shows the record's name                                                 |

## Configuration

The three module flags keep their names and defaults:

```php
'modules' => [
    'location' => [
        'enableApiRoutes' => true,
        'enableTags' => false,
        'tagCachedQueryDuration' => 60,
    ],
],
```

**Types are declared on the container, not by subclassing.** A 1.x project overrode the static `getTypes()`
with an array; the slug lived in a `'slug'` key. In 3.0 the declaration is a closure returning
`Models\Types\LocationType` objects, and the same shape works for `Models\Tag`:

```php
// 1.x
class Location extends \davidhirtz\yii2\location\models\Location
{
    public static function getTypes(): array
    {
        return [
            self::TYPE_DEFAULT => ['name' => Yii::t('app', 'Store'), 'slug' => 'stores'],
            2 => ['name' => Yii::t('app', 'Office'), 'slug' => 'offices'],
        ];
    }
}

// 3.0, in config/web.php
use Hirtz\Location\Models\Location;
use Hirtz\Location\Models\Types\LocationType;

'container' => [
    'definitions' => [
        Location::class => [
            'types' => fn (): array => [
                LocationType::make(Location::TYPE_DEFAULT)->name(Yii::t('app', 'Store'))->slug('stores'),
                LocationType::make(2)->name(Yii::t('app', 'Office'))->slug('offices')->allowTags(false),
            ],
            'i18nAttributes' => ['name', 'formatted_address'],
        ],
    ],
],
```

The value must be a closure: a type's name is a `Yii::t()` result and a literal in a config file resolves before
the `i18n` component exists. A class that still declares its own `getTypes()` owns them and ignores the container.

**The autocomplete slot is unchanged.** `modules.admin.modules.location.components.autocomplete` takes any
`Modules\Admin\Interfaces\AutocompleteInterface`; `davidhirtz/yii2-location-google` sets it from its bootstrap
when `params.googleApiKey` is present, so a project using that bundle configures nothing here.

**Gone from the configuration:** `enableI18nTables`, which the 1.x migrations read off the module to create a
table per language. There is one table per model now, and the translations live in the skeleton's `translation`
table.

## Code changes

### Permissions

One permission per model, checked without a record. Every `can()` that named a verb permission or passed the
record changes:

```php
// 1.x
Yii::$app->getUser()->can(Location::AUTH_LOCATION_UPDATE, ['location' => $location]);
$this->findLocation($id, Location::AUTH_LOCATION_DELETE);

// 3.0
$this->webuser->can(Location::AUTH_LOCATION);
$this->findLocation($id);
```

A project that granted `locationUpdate` to a role without `locationCreate` or `locationDelete` loses that
distinction: the upgrade migration folds all three into `location`. The same holds for the tag items.

### Types

Read a type through the record or the definitions, never through an array offset:

```php
// 1.x
$slug = Location::getTypes()[$location->type]['slug'] ?? null;

// 3.0
$slug = $location->getType()?->getSlug();
```

`Location::getType()` returns `?LocationType`. `ApiController::findTypeBySlug()` walks
`Location::instance()::getTypeDefinitions()` and matches `getSlug()`, so a type with no slug has no URL of its
own, as before. A type may switch tags off for its records with `allowTags(false)`; `Location::allowsTags()`
combines it with `Module::$enableTags`.

### Translated attributes

The `_<language>` columns are gone. Anything that read `$location->name_de` through the attribute still works
(the virtual attribute is served from the `translation` table), but raw SQL against the column, a
`replaceI18nAttributes()` call and a `select()` naming the column do not:

```php
// 1.x
Location::find()->replaceI18nAttributes()->whereStatus()->all();

// 3.0
Location::find()->withTranslations()->whereStatus()->all();
```

A query returning more than one row eager loads every configured language; a single record stays lazy. See the
skeleton's `UPGRADE.md` for `withTranslations()`, `withoutTranslations()` and `getI18nAttributeName()`.

### Custom attributes and `rules()`

`Location` and `Tag` implement `CustomAttributeInterface`, and `Hirtz\Skeleton\Db\ActiveRecord::rules()` is
where the custom attribute rules enter. A subclass overriding `rules()` must spread the parent's, which the 1.x
`Location::rules()` did not:

```php
public function rules(): array
{
    return [
        ...parent::rules(),
        // ...
    ];
}
```

A project storing extra location data in columns of its own can keep them; `custom_attributes` is for what a
type declares through `LocationType::customAttributes()`.

### Autocomplete providers

An `AutocompleteInterface` implementation renames its parameter and types its return; the shape is what
`Hirtz\Skeleton\Widgets\Forms\AutocompleteList::options()` renders:

```php
// 1.x
public function getResults(string $term): array

// 3.0
/** @return list<array{text: string, value: mixed}> */
public function getResults(string $input): array
```

The endpoint is `admin/location/location/autocomplete?q=…` and answers HTML, not JSON. A project that called the
old JSON endpoint from a script of its own has no replacement; the admin field does the work through htmx. Queries
shorter than `LocationController::$autocompleteMinLength` (3) answer an empty string.

### Admin widgets and views

The 1.x widgets extended Yii's widget layer; the 3.0 ones extend the skeleton's. A project subclass moves:

- `LocationActiveForm` / `TagActiveForm`: `$this->fields ??= [...]` in `init()` becomes `getDefaultRows()`
  returning field objects (`InputField::make()->property('street')`); the fieldsets are the nested arrays. A field
  method is `get<Name>Field()`.
- `LocationGridView` / `TagGridView`: `init()` becomes `configure()`, column methods are `get<Name>Column()` and
  return `Column` objects, `$this->columns ??= [...]` before `parent::configure()`. `initHeader()` / `initFooter()`
  are `$this->header ??=` / `$this->footer =` in `configure()`.
- `Submenu` became three widgets: `LocationHeader` / `TagHeader` (title, subtitle, breadcrumbs, create button,
  action dropdown) and `LocationSubmenu` (the record's tabs). The old `getLocationGridViewItems()` tabs are the
  aside's `LocationNavItem` now.
- The views under `resources/views/admin/` are rewritten; a project that copied one copies the 3.0 file again.

### `LocationTag` is an admin model

`Models\LocationTag` implements `Hirtz\Skeleton\Models\Interfaces\TrailModelInterface` and answers
`getPermissionName()` with `Location::AUTH_LOCATION`, `getAdminRoute()` with `false`. A subclass overriding
`getTrailModelName()` moves to `getAdminName()`.

### API output

`Location::fields()` adds `type` when more than one type is declared, and `tags` only while `enableTags` is on.
`Tag::fields()` returns `name` and, with more than one type, `type`; the 1.x version copied the location's list and
serialized `formatted_address`, `lat` and `lng` as `null`. A consumer of `api/location/<action>.json` that keyed on
those sees the change.

### `ApiController`

Extends `Hirtz\Skeleton\Web\Controller`, so `$this->request` is the skeleton `Web\Request` and a subclass reading
`Yii::$app->getRequest()` can use it directly. `allowedFormats`, `allowAllTypes` and `enablePageCache` are unchanged.

## Data and schema

The bundle ships one baseline migration, `Migrations\M260101000700LocationBaseline`, for a fresh install. The
1.x to 3.0 migrations live in `davidhirtz/yii2-upgrade` under `migrations/yii2-location/` and are applied by that
tool, in this order:

1. `M260910140000Translations` moves every `<attribute>_<language>` column of `location` and `tag` into the
   skeleton's `translation` table and drops the columns. It reads the columns off the table, so a language the
   configuration no longer names is migrated rather than stranded. Depends on the skeleton's translation table
   migration, which Yii orders before it by timestamp.
2. `M260911130000CustomAttributes` adds the `custom_attributes` JSON column to `location` and `tag`.
3. `M260914130000AuthItems` creates the `location` and `tag` permissions under `admin`, grants each to every
   parent and every user holding any of the six legacy verb items, then deletes those items.

Before: back up the database, and run the skeleton's upgrade first if the tool is run per bundle.

After: `./yii search/rebuild`, since locations and tags are searchable now and the index holds nothing for them.
No migration touches `location_tag`, `tag_ids` or `tag_count`.

Lost: the create/update/delete distinction between the legacy permissions, and any per-language table a project
created with `enableI18nTables` on. No migration merges those tables; a project that had them moves their rows
into `translation` by hand before running the above.

## Removed

- The jQuery UI autocomplete script, `AutocompleteAssetBundle` and `AutocompleteInputWidget`: the field is
  server-rendered.
- Per-language tables and `migrations\traits\I18nTablesTrait`.
- The `ru`, `zh-CN` and `zh-TW` message files.
- `LocationTrait::checkLocationPermission()` and the permission argument of `findLocation()` / `findTag()`.
- `getTrailModelAdminRoute()` on `Location` and `Tag`.
- The `Edit Location` / `Edit Tag` page titles; the header shows the record's name.
