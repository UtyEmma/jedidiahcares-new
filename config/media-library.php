<?php

use App\Models\CmsMedia;
use App\Support\CmsUrlGenerator;
use Spatie\MediaLibrary\Conversions\ImageGenerators\Image;

$defaults = require base_path('vendor/spatie/laravel-medialibrary/config/media-library.php');

return array_replace($defaults, [
    'disk_name' => 'cms',
    'conversions_disk_name' => 'cms',
    'max_file_size' => 100 * 1024 * 1024,
    'media_model' => CmsMedia::class,
    'url_generator' => CmsUrlGenerator::class,
    'image_driver' => 'gd',
    'queue_conversions_by_default' => false,
    'image_optimizers' => [],
    'image_generators' => [Image::class],
]);
