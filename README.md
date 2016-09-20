hypeIcons
=========
Interface for uploading and cropping entity icons and covers

![Elgg 2.2](https://img.shields.io/badge/Elgg-2.2-orange.svg?style=flat-square)

## Features

 * Generic API for uploading, handling and cropping entity icons and covers
 * Admin settings to enable icons/covers for all entity types
 * Allows to crop file thumbnails
 * Allows to crop user and group avatars and cover images
 * An option switch between square, rounded and circle entity icons
 * An option to replace default entity icons with SVG
 * An option to replace default filetype icons with SVG
 * Responsive icon/cover cropping

## Screenshots ##

![Icon/Cover Cropper](https://raw.github.com/hypeJunction/hypeIcons/master/screenshots/cropper.png "Icon/Cover Cropper")
![Layout with cover](https://raw.github.com/hypeJunction/hypeIcons/master/screenshots/layout-cover.png "Layout with cover")

## Notes

### Default entity type icons

To replace a default entity icon/cover, simply place an image in `views/default/<icon_type>/<entity_type>/<entity_subtype>.<ext>`, where:
 - `ext` is either `svg`, `png`, `gif` or `jpg`.
 - `icon_type` is either `cover` or `icon`

### Add a cropper as a form input

```php
// in your form
echo elgg_view('input/cropper', array(
	'src' => 'http://example.com/uri/image.jpg',
	'ratio' => 16/9,
	'name' => 'crop_coords',
));

// in your action
$coords = get_input('crop_coords');
```

### Add cropper to a file input

This will allow users to crop an image before uploading it to the server.

```php
// in your form
echo elgg_view('input/file', array(
    'name' => 'avatar',
    'use_cropper' => true,
));

// in your action
$coords = get_input('crop_coords');
```

You can as well pass preset coordinates and images source.

```php
// in your form
echo elgg_view('input/file', array(
	'name' => 'cover',
	'use_cropper' => array(
		'name' => 'cover_crop_coords',
		'ratio' => 16/9,
		'src' => '/uri/image.jpg', // previously uploaded file
		'x1' => 100,
		'y1' => 100,
		'x2' => 260,
		'y2' => 190,
	),
));

// in your action
$coords = get_input('cover_crop_coords');
```

In your action, be sure to use the same image source for cropping. If you passed master image source to the file input,
you will need to implement the logic for both new file upload and master image, as cropping coordinates may change even without new
file upload.


### Displaying a cover image

```php
echo elgg_view('output/cover', [
   'entity' => $entity,
]);
```

### Adding cover image in layout header

```php
echo elgg_view_layout('one_sidebar', [
   'entity' => $entity,
   'show_cover' => true,
   'title' => 'Page with cover',
   'content' => 'Page content',
]);