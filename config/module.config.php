<?php declare(strict_types=1);

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
    'view_helpers' => [
        'invokables' => [
            'ckEditor' => View\Helper\CkEditor::class,
        ],
    ],
    'form_elements' => [
        'invokables' => [
            Form\Element\OptionalRadio::class => Form\Element\OptionalRadio::class,
            Form\SettingsFieldset::class => Form\SettingsFieldset::class,
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
    'datatyperdf' => [
        'settings' => [
            'datatyperdf_html_mode_resource' => 'inline',
            'datatyperdf_html_config_resource' => 'default',
        ],
    ],
];
