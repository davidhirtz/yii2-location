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