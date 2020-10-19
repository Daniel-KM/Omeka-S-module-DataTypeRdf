<?php

namespace DataTypeRdf;

return [
    'data_types' => [
        'invokables' => [
            'xml' => DataType\Xml::class,
            'boolean' => DataType\Boolean::class,
        ],
        'factories' => [
            'html' => Service\DataType\HtmlFactory::class,
        ],
    ],
    'translator' => [
        'translation_file_patterns' => [
            [
                'type' => 'gettext',
                'base_dir' => dirname(__DIR__) . '/language',
                'pattern' => '%s.mo',
                'text_domain' => null,
            ],
        ],
    ],
];
