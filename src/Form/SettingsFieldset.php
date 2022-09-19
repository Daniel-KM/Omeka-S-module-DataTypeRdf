<?php declare(strict_types=1);

namespace DataTypeRdf\Form;

use DataTypeRdf\Form\Element as DataTypeRdfElement;
use Laminas\Form\Fieldset;

class SettingsFieldset extends Fieldset
{
    protected $label = 'Data Type Rdf'; // @translate

    public function init(): void
    {
        $this
            ->setAttribute('id', 'data-type-rdf')
            ->add([
                'name' => 'datatyperdf_html_mode_resource',
                'type' => DataTypeRdfElement\OptionalRadio::class,
                'options' => [
                    'label' => 'Html edition mode for resources', // @translate
                    'value_options' => [
                        'inline' => 'Inline (default)', // @translate
                        'document' => 'Document (maximizable)', // @translate
                    ],
                ],
                'attributes' => [
                    'id' => 'datatyperdf_html_mode_resource',
                ],
            ])
            ->add([
                'name' => 'datatyperdf_html_config_resource',
                'type' => DataTypeRdfElement\OptionalRadio::class,
                'options' => [
                    'label' => 'Html edition config and toolbar for resources', // @translate
                    'value_options' => [
                        // @see https://ckeditor.com/cke4/presets-all
                        'default' => 'Default', // @translate
                        'standard' => 'Standard', // @translate
                        'full' => 'Full', // @translate
                    ],
                ],
                'attributes' => [
                    'id' => 'datatyperdf_html_config_resource',
                ],
            ])
        ;
    }
}
