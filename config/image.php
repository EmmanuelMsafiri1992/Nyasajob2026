<?php

return array(

    /*
    |--------------------------------------------------------------------------
    | Image Driver
    |--------------------------------------------------------------------------
    |
    | Intervention Image supports "GD Library" and "Imagick" to process images
    | internally. You may choose one of them according to your PHP
    | configuration. By default PHP's "GD Library" implementation is used.
    |
    | Supported: "gd", "imagick"
    |
    */

    'driver' => env('IMAGE_DRIVER', 'gd'),

    /*
    |--------------------------------------------------------------------------
    | Supported Image Formats
    |--------------------------------------------------------------------------
    |
    | Supported formats per driver. GD has limited format support compared
    | to Imagick. Imagick supports additional formats like HEIC and JPEG 2000.
    |
    */

    'formats' => [
        'gd' => ['jpg', 'jpeg', 'gif', 'png', 'avif', 'webp'],
        'imagick' => ['jpg', 'jpeg', 'gif', 'png', 'avif', 'webp', 'jp2', 'heic'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Animated Image Formats
    |--------------------------------------------------------------------------
    |
    | Image formats that support animation. When preserve_image_format is
    | enabled, these formats will keep their original encoding to preserve
    | animation frames.
    |
    */

    'animated_formats' => ['gif', 'webp', 'avif'],

);
