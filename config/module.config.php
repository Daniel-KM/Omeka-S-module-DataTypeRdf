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
    'js_translate_strings' => [
        'Decimal', // @translate
        'Number', // @translate
        'Please enter a valid decimal number.', // @translate
        'True/False', // @translate
    ],
];
