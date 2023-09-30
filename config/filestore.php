<?php

use Yormy\FilestoreLaravel\Domain\Shared\Services\Resolvers\IpResolver;
use Yormy\FilestoreLaravel\Domain\Shared\Services\Resolvers\UserAgentResolver;
use Yormy\FilestoreLaravel\Domain\Shared\Services\Resolvers\UserResolver;
use Yormy\FilestoreLaravel\Domain\Upload\DataObjects\Enums\MimeTypeEnum;

return [
    'encryption' => [
        'enabled' => true,
    ],

    'resolvers' => [
        'ip' => IpResolver::class,
        'user' => UserResolver::class,
        'useragent' => UserAgentResolver::class,
    ],

    'allowed_mimes' => [
        MimeTypeEnum::ImageJpeg,
        MimeTypeEnum::ImagePng,
        MimeTypeEnum::ImageGif,
        MimeTypeEnum::ImageBmp,
        MimeTypeEnum::ApplicationPdf,
        MimeTypeEnum::TextPlain,
    ],

    'max_file_size_kb' => 40000,

    'storage' => [
        'persistent' => [
            'disk' => 'digitalocean',
        ],
        'local' => [
            'disk' => 'local',
        ],
    ],

    // Auto generate the following variants of the uploaded images
    'variants' => [
        'small' => [
            'width' => 30,
            'height' => 40,
        ],
        'medium' => [
            'width' => 100,
            'height' => 150,
        ],
        'large' => [
            'width' => 200,
            'height' => 300,
        ],
    ],

    'vault' => [
        'key' => env('FILE_VAULT_KEY', env('APP_KEY')),

        /*
         * The cipher used for encryption.
         * Supported options are AES-128-CBC and AES-256-CBC
         */
        'cipher' => 'AES-256-CBC',

        'extension' => '.xfile',

        /*
         * The Storage disk used by default to locate your files.
         */
        'disk' => 'local',
    ],
];
