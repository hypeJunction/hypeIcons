# hypeIcons — Architecture (Elgg 4.x)

## Summary

hypeIcons provides a generic UI and API for uploading, managing, and cropping
entity icons and cover images. It supports entity icons (square/rounded/circle),
cover images with size presets, file-type icons (with SVG replacement), and
cropping of user/group avatars as well as file thumbnails.

Plugin id (4.x, lowercase): `hypeicons`
Composer name: `hypejunction/hypeicons`
Target: Elgg `^4.0`, PHP `>=7.4`

## Directory structure

```
hypeicons/
├── composer.json          # 4.x plugin metadata (no manifest.xml)
├── elgg-plugin.php        # actions, routes, hooks, view extensions, 'plugin' block
├── actions/               # upload / crop / remove icon actions
├── classes/hypeJunction/Icons/
│   ├── Cropper.php        # filterFileInputVars (input/file view extension)
│   ├── Icons.php          # default icon & cover URL providers, cropping hooks
│   ├── Menus.php          # entity / user hover / group profile menu setup
│   ├── Router.php         # /icons/* page handler resolver
│   └── Settings.php       # per-entity-type icon/cover support lookup
├── languages/             # translations
├── views/default/         # forms, input templates, cropper CSS/JS, icon views
└── tests/
    ├── phpunit/integration/  # Elgg IntegrationTestCase suites (PHPUnit 9.6)
    └── playwright/           # E2E flows (upload, crop, remove)
```

## Registered (via elgg-plugin.php)

### Actions
- `icons/upload` — upload new icon/cover source image
- `icons/crop` — persist crop coordinates for size presets
- `icons/remove` — delete icon/cover

### Routes
- `icons` → `/icons/{segments}` → resource view `icons`

### Hooks (Elgg 4.x hook-style, single `\Elgg\Hook` arg handlers)
- `entity:icon:url` (all)     → `Icons::setDefaultIcon` (priority 900)
- `entity:icon:url` (object)  → `Icons::setDefaultFileIcons` (priority 600)
- `entity:cover:url` (all)    → `Icons::setDefaultIcon` (priority 900)
- `entity:cover:sizes` (all)  → `Icons::setCoverSizes`
- `entity:cover:saved` (all)  → `Icons::saveCoverCroppingCoords`
- `register, menu:entity`     → `Menus::setupEntityMenu`
- `register, menu:user_hover` → `Menus::setupUserHoverMenu`
- `profile_buttons, group`    → `Menus::setupGroupProfileMenu`
- `view_vars, input/file`     → `Cropper::filterFileInputVars`

### View extensions
- `elements/icons.css`            ← `elements/avatar.sizes.css`
- `page/layouts/elements/header`  ← `page/layouts/elements/cover` (priority 1)
- `elements/forms.css`            ← `cropper.css`, `input/cropper.css`
- `input/file`                    ← `elements/input/file/cropper`
- `theme_sandbox/forms`           ← `theme_sandbox/forms/cropper`

### Simplecache views
- `cropper.js`  → vendored `bower-asset/cropper/dist/cropper.min.js`
- `cropper.css` → vendored `bower-asset/cropper/dist/cropper.min.css`

## Dependencies

- `elgg/elgg` `^4.0`
- `composer/installers` `^2.0`
- `bower-asset/cropper` `~2.1` (vendored JS/CSS via Composer)

hypeIcons does not hard-depend on other hypeJunction plugins, but is commonly
used alongside `hypeAttachments` and `hypeDropzone` in the bodyology stack.

## Plugin settings

- `replace_default_icons` — swap core entity icons for plugin-provided SVGs
- `replace_filetype_icons` — swap file-type icons (pdf, doc, image, ...) for SVGs
- `icon:{type}:{subtype}` / `cover:{type}:{subtype}` — per-entity-type toggles

All settings are read via `elgg_get_plugin_setting(..., 'hypeicons')`.

## Migration notes (3.x → 4.x)

- `manifest.xml` removed; plugin metadata now lives in `composer.json` and the
  `'plugin'` key in `elgg-plugin.php`.
- Plugin id lowercased to `hypeicons` to satisfy Elgg 4.x plugin directory
  naming requirement. All `elgg_get_plugin_setting(..., 'hypeIcons')` call
  sites updated to lowercase — existing installs must migrate stored settings
  (see `Elgg\Upgrade\Batch` note below).
- `start.php`, `activate.php`, `deactivate.php` removed — all wiring moved to
  `elgg-plugin.php` declarative arrays.
- Hook handlers rewritten to the Elgg 4.x single-argument signature using
  `\Elgg\Hook`.
- `elgg_instanceof()` replaced with native `instanceof`.
- `get_entity_subtypes()` / subtype table queries replaced with
  `elgg_entity_types_with_capability()`.
- `content` page layout replaced with `default`.
- `md5(serialize($vars))` used as a DOM id in `views/default/input/cropper.php`
  replaced with `substr(hash('sha256', ...), 0, 16)` to silence
  weak-crypto security warnings (the id is non-security DOM state).

### Data migration

The plugin-id rename (`hypeIcons` → `hypeicons`) means any existing site that
had this plugin installed under Elgg 3.x has `private_settings` rows keyed to
the old plugin entity (plugin_id `hypeIcons`). When this plugin is upgraded in
place, those settings will no longer be read by the code (which now looks up
`hypeicons`).

Sites with no existing hypeIcons install are unaffected. Sites upgrading from
3.x with a configured hypeIcons need an `Elgg\Upgrade\Batch` to copy settings
from the old plugin entity to the new one. This is tracked as follow-up work
— not shipped in this commit because the current deployment target
(bodyology) installs hypeIcons fresh against Elgg 4.x.

## Known issues / cross-plugin

- `MenusTest::testSetupEntityMenuReturnsValueForObject` currently errors
  because `hypeAttachments/classes/hypeJunction/Attachments/Permissions.php:47`
  calls `elgg_set_ignore_access()` without the global-namespace prefix from
  inside a namespaced class. The failure is in the hypeAttachments plugin,
  not hypeIcons; the test itself is correct. Tracked under the
  hypeAttachments 3.x→4.x migration task.
