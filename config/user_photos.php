<?php

return [
    'sizes' => [
        'large' => [
            'max_width' => 1200,
            'webp_quality' => 85,
        ],
        'medium' => [
            'max_width' => 600,
            'webp_quality' => 82,
        ],
        'thumbnail' => [
            'max_width' => 200,
            'webp_quality' => 80,
        ],
    ],
    'watermark' => [
        'text' => [
            'large' => 'DulhaDulhan',
            'medium' => 'DulhaDulhan',
            'thumbnail' => 'DD',
        ],
        'font_size_percent' => [
            'large' => 2.5,
            'medium' => 2.5,
            'thumbnail' => 3,
        ],
        'horizontal_margin_percent' => [
            'large' => 3,
            'medium' => 3,
            'thumbnail' => 4,
        ],
        'vertical_margin_percent' => [
            'large' => 3,
            'medium' => 3,
            'thumbnail' => 4,
        ],
        'text_opacity_percent' => [
            'large' => 50,
            'medium' => 50,
            'thumbnail' => 55,
        ],
        'shadow' => [
            'large' => true,
            'medium' => true,
            'thumbnail' => false,
        ],
        'font_paths' => [
            'C:\\Windows\\Fonts\\DejaVuSans-Bold.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
            'C:\\Windows\\Fonts\\arialbd.ttf',
        ],
    ],
];