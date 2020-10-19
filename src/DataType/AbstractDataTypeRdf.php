<?php declare(strict_types=1);

namespace DataTypeRdf\DataType;

use Laminas\View\Renderer\PhpRenderer;
use Omeka\Api\Adapter\AbstractEntityAdapter;
use Omeka\Api\Representation\ValueRepresentation;
use Omeka\DataType\AbstractDataType;
use Omeka\Entity\Value;

abstract class AbstractDataTypeRdf extends AbstractDataType
{
    public function getOptgroupLabel()
    {
        return 'Data Type RDF'; // @translate
    }

    public function prepareForm(PhpRenderer $view): void
    {
        $assetUrl = $view->plugin('assetUrl');
        $view->headLink()->appendStylesheet($assetUrl('css/data-type-rdf.css', 'DataTypeRdf'));
        $view->headScript()->appendFile($assetUrl('js/data-type-rdf.js', 'DataTypeRdf'), 'text/javascript', ['defer' => 'defer']);
    }

    public function hydrate(array $valueObject, Value $value, AbstractEntityAdapter $adapter): void
    {
        $value->setValue(trim($valueObject['@value']));
        // Set defaults.
        $value->setLang(null);
        $value->setUri(null);
        $value->setValueResource(null);
    }

    public function render(PhpRenderer $view, ValueRepresentation $value)
    {
        return $value->value();
    }

    public function getJsonLd(ValueRepresentation $value)
    {
        return [
            '@value' => $value->value(),
        ];
    }
}
