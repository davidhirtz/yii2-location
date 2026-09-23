# yii2-location

Locations with an address, coordinates and an optional provider id (a Google Places id, say), plus tags to group
them by, for the [Yii 2](https://www.yiiframework.com/) admin platform
[yii2-skeleton](https://github.com/davidhirtz/yii2-skeleton). It ships the two models with their admin pages, a
read-only JSON/GeoJSON API and a slot for an address autocomplete provider, which
[yii2-location-google](https://github.com/davidhirtz/yii2-location-google) fills. It depends on the skeleton alone.

## Installation

```bash
composer require davidhirtz/yii2-location
./yii migrate
./yii search/rebuild
```

The bundle bootstraps itself through `extra.bootstrap` (`Hirtz\Location\Bootstrap`): it registers the `location`
module, the `location` submodule of the admin, the `location` message category, the migration namespace, the API
URL rule and both models on the `search` component. `search/rebuild` is only needed on an installation that
already holds locations.

## Configuration

Module properties, under `modules.location`:

| Property                 | Default | Meaning                                                                                                        |
|--------------------------|---------|----------------------------------------------------------------------------------------------------------------|
| `enableApiRoutes`        | `true`  | Registers the `api/location/<action>.<format>` URL rule. Read at bootstrap, so it has to be set in the config file. |
| `enableTags`             | `false` | Turns tags on: the tag pages, the location's tag tab and filter, `tags` in the API output, and the tag search index. |
| `tagCachedQueryDuration` | `60`    | Seconds `Models\Collections\TagCollection` caches the tag list through the `db` component; `null` disables the cache. |

Components the bundle reads: `db`, `cache` (the API's page cache and the tag list cache, invalidated on every
save), `search` and `i18n`. No params of its own.

The `Location` and `Tag` models are configured through the container. A project declares its types, which
attributes are translated and, per type, which custom attributes a record carries:

```php
use Hirtz\Location\Models\Location;
use Hirtz\Location\Models\Tag;
use Hirtz\Location\Models\Types\LocationType;
use Hirtz\Skeleton\Models\CustomAttributes\TextCustomAttribute;
use Hirtz\Skeleton\Models\Types\Type;

'container' => [
    'definitions' => [
        Location::class => [
            'i18nAttributes' => ['name', 'formatted_address'],
            'types' => fn (): array => [
                LocationType::make(Location::TYPE_DEFAULT)
                    ->name(Yii::t('app', 'Store'))
                    ->slug('stores')
                    ->customAttributes([TextCustomAttribute::make('opening_hours')]),
                LocationType::make(2)
                    ->name(Yii::t('app', 'Office'))
                    ->slug('offices')
                    ->allowTags(false),
            ],
        ],
        Tag::class => [
            'i18nAttributes' => ['name'],
            'types' => fn (): array => [
                Type::make(Tag::TYPE_DEFAULT)->name(Yii::t('app', 'Region')),
            ],
        ],
    ],
],
```

`types` must be a closure, because a type's name is a `Yii::t()` result. A `LocationType` adds two things to the
skeleton's `Type`: `slug()`, which gives the type a URL in the API, and `allowTags(false)`, which keeps tags off
records of that type while `enableTags` is on. `Location::allowsTags()` and `Tag::allowsTags()` are what the
code asks. Custom attribute values are stored in the `custom_attributes` column and rendered by the admin form.

## Console commands

None of its own. The skeleton's `search/rebuild` indexes locations and tags.

## API

With `enableApiRoutes` on, `Controllers\ApiController` answers:

| URL                                     | Result                                                                    |
|-----------------------------------------|---------------------------------------------------------------------------|
| `api/location/index.json`               | Every enabled location                                                    |
| `api/location/<slug>.json`              | The locations of the type whose `slug()` matches                          |
| `api/location/index.json?tag=<id>`      | Filtered by tag, while `enableTags` is on                                 |

`.geojson` is accepted as a format too; the controller serializes the same `Location::fields()` either way
(`name`, `type` where more than one type exists, `formatted_address`, `tags`, `lat`, `lng`). A draft request
(`Hirtz\Skeleton\Web\Request::getIsDraft()`) lists draft locations instead of enabled ones. The controller is
configured through the container:

| Property          | Default                | Meaning                                                              |
|-------------------|------------------------|----------------------------------------------------------------------|
| `allowedFormats`  | `['geojson', 'json']`  | Anything else is a 400                                               |
| `allowAllTypes`   | `true`                 | `false` makes `index.<format>` a 404, so only a type slug answers    |
| `enablePageCache` | `true`                 | Caches responses with `Hirtz\Skeleton\Filters\PageCache`, keyed by `action`, `format` and `tag` |

A project overriding `getLocationQuery()` changes what every response is built from.

## Autocomplete

The location form's `provider_id` field (`Modules\Admin\Widgets\Forms\LocationProviderIdField`) is a plain input
unless the admin submodule holds an `autocomplete` component implementing
`Modules\Admin\Interfaces\AutocompleteInterface`:

```php
interface AutocompleteInterface
{
    /** @return list<array{text: string, value: mixed}> */
    public function getResults(string $input): array;
}
```

With one configured, the field searches through `admin/location/location/autocomplete` as the user types and
`LocationController::actionAutocomplete()` renders the results as an option list; queries shorter than
`$autocompleteMinLength` (3) answer nothing. `yii2-location-google` sets the component from its own bootstrap when
`params.googleApiKey` is present. To use another provider, set it yourself:

```php
'modules' => [
    'admin' => [
        'modules' => [
            'location' => [
                'components' => [
                    'autocomplete' => App\Components\MyAutocomplete::class,
                ],
            ],
        ],
    ],
],
```

## Admin

The admin lives under `/admin/location`: `location/*` for locations, `tag/*` for tags and `location-tag/*` for a
location's tag picker. Two permissions guard it, `Models\Location::AUTH_LOCATION` (`location`) and
`Models\Tag::AUTH_TAG` (`tag`), both granted to `admin` and `manager` by the migration. The aside shows
*Locations* with a *Tags* subitem while tags are on, and the dashboard offers *New Location*.

Every widget extends the skeleton's and is open to `Widget::EVENT_CONFIGURE` listeners: `LocationActiveForm`,
`TagActiveForm`, `LocationGridView`, `TagGridView`, `LocationTagGridView`, `LocationHeader`, `TagHeader`,
`LocationSubmenu`, `LocationNavItem`, the action dropdowns and delete buttons.
