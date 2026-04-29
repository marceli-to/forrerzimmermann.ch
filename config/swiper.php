<?php

return [

	'image_slide' => [

		'orientations' => [

			'portrait' => [
				'mode' => 'intrinsic',
				'sizes' => '(min-width: 768px) 50vw, 100vw',
			],

			'default' => [
				'mode' => 'grid',
				'span' => 'md:col-span-full xl:col-span-8 xl:col-start-3',
				'sizes' => '(min-width: 768px) 67vw, 100vw',
			],

		],

	],

];
