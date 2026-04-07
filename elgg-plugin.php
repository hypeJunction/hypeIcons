<?php

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
];
