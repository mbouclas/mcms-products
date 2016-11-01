<?php

return [
    'product' => \Mcms\Products\Models\Product::class,
    'related' => \Mcms\Products\Models\Related::class,
    'featured' => \Mcms\Products\Models\Featured::class,
    'money' => [
        'decimals' => 2,
        'divideBy' => 100,
        'currency' => 'EUR'
    ],
    'items' => [
        'slug_pattern' => '/product/%slug$s',
        'previewController' => '\FrontEnd\Http\Controllers\HomeController@preview',
        'images' => [
            'keepOriginals' => true,
            'optimize' => true,
            'dirPattern' => 'products/product_%id$s',
            'filePattern' => '',
            'types' => [
                [
                    'uploadAs' => 'image',
                    'name' => 'images',
                    'title' => 'Images',
                    'settings' => [
                        'default' => true
                    ]
                ],
                [
                    'name' => 'floor_plans',
                    'title' => 'Floor Plans',
                    'uploadAs' => 'image',
                    'settings' => [
                        'default' => false
                    ]
                ]
            ],
            'copies' => [
                'thumb' => [
                    'width' => 70,
                    'height' => 70,
                    'quality' => 100,
                    'prefix' => 't_',
                    'resizeType' => 'fit',
                    'dir' => 'thumbs/',
                ],
                'big_thumb' => [
                    'width' => 170,
                    'height' => 170,
                    'quality' => 100,
                    'prefix' => 't1_',
                    'resizeType' => 'fit',
                    'dir' => 'big_thumbs/',
                ],
                'main' => [
                    'width' => 500,
                    'height' => 500,
                    'quality' => 100,
                    'prefix' => 'm_',
                    'resizeType' => 'fit',
                    'dir' => '/',
                ],
            ]
        ],
        'files' => [
            'dirPattern' => 'products/product_%id$s',
            'filePattern' => '',
        ]
    ],
    'categories' => [
        'slug_pattern' => '/products/%slug$s',
        'images' => [
            'keepOriginals' => true,
            'optimize' => true,
            'dirPattern' => 'products/category_%id$s',
            'filePattern' => '',
            'types' => [
                [
                    'uploadAs' => 'image',
                    'name' => 'images',
                    'title' => 'Images',
                    'settings' => [
                        'default' => true
                    ]
                ]
            ],
            'copies' => [
                'thumb' => [
                    'width' => 70,
                    'height' => 70,
                    'quality' => 100,
                    'prefix' => 't_',
                    'resizeType' => 'fit',
                    'dir' => 'thumbs/',
                ],
                'big_thumb' => [
                    'width' => 170,
                    'height' => 170,
                    'quality' => 100,
                    'prefix' => 't1_',
                    'resizeType' => 'fit',
                    'dir' => 'big_thumbs/',
                ],
                'main' => [
                    'width' => 500,
                    'height' => 500,
                    'quality' => 100,
                    'prefix' => 'm_',
                    'resizeType' => 'fit',
                    'dir' => '/',
                ],
            ]
        ]
    ]
];