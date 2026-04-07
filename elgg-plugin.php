<?php

use hypeJunction\Icons\Cropper;
use hypeJunction\Icons\Icons;
use hypeJunction\Icons\Menus;

return [
	'actions' => [
		'icons/upload' => [],
		'icons/crop' => [],
		'icons/remove' => [],
	],
	'routes' => [
		'icons' => [
			'path' => '/icons/{segments}',
			'resource' => 'icons',
			'requirements' => [
				'segments' => '.+',
			],
			'defaults' => [
				'segments' => '',
			],
		],
	],
	'hooks' => [
		'entity:icon:url' => [
			'all' => [
				Icons::class . '::setDefaultIcon' => ['priority' => 900],
			],
			'object' => [
				Icons::class . '::setDefaultFileIcons' => ['priority' => 600],
			],
		],
		'entity:cover:url' => [
			'all' => [
				Icons::class . '::setDefaultIcon' => ['priority' => 900],
			],
		],
		'entity:cover:sizes' => [
			'all' => [
				Icons::class . '::setCoverSizes' => [],
			],
		],
		'entity:cover:saved' => [
			'all' => [
				Icons::class . '::saveCoverCroppingCoords' => [],
			],
		],
		'register' => [
			'menu:entity' => [
				Menus::class . '::setupEntityMenu' => [],
			],
			'menu:user_hover' => [
				Menus::class . '::setupUserHoverMenu' => [],
			],
		],
		'profile_buttons' => [
			'group' => [
				Menus::class . '::setupGroupProfileMenu' => [],
			],
		],
		'view_vars' => [
			'input/file' => [
				Cropper::class . '::filterFileInputVars' => [],
			],
		],
	],
	'view_extensions' => [
		'elements/icons.css' => [
			'elements/avatar.sizes.css' => [],
		],
		'page/layouts/elements/header' => [
			'page/layouts/elements/cover' => ['priority' => 1],
		],
		'elements/forms.css' => [
			'cropper.css' => [],
			'input/cropper.css' => [],
		],
		'input/file' => [
			'elements/input/file/cropper' => [],
		],
		'theme_sandbox/forms' => [
			'theme_sandbox/forms/cropper' => [],
		],
	],
	'views' => [
		'default' => [
			'cropper.js' => __DIR__ . '/vendor/bower-asset/cropper/dist/cropper.min.js',
			'cropper.css' => __DIR__ . '/vendor/bower-asset/cropper/dist/cropper.min.css',
		],
	],
];
