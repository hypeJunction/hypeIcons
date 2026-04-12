<a name="2.0.0"></a>
# 2.0.0 (Elgg 4.x)

### Breaking changes

* Requires Elgg `^4.0` and PHP `>=7.4`
* Plugin id lowercased from `hypeIcons` to `hypeicons` (Elgg 4.x requirement)
* `manifest.xml` removed; metadata moved to `composer.json` + `elgg-plugin.php`
* `start.php` removed; all wiring moved to declarative `elgg-plugin.php`
* Hook handlers rewritten to single-argument `\Elgg\Hook` signature
* `content` page layout replaced with `default`
* `elgg_instanceof()` replaced with native `instanceof`
* Weak-hash `md5()` DOM id replaced with `hash('sha256', ...)`

See `ARCHITECTURE.md` for the full post-migration structure.

<a name="1.0.0"></a>
# 1.0.0 (2017-04-19)


### Features

* **releases:** initial commit ([3250004](https://github.com/hypeJunction/hypeIcons/commit/3250004))



