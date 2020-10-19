<?php

namespace DataTypeRdf\DataType;

use Omeka\Api\Adapter\AbstractEntityAdapter;
use Omeka\Entity\Value;
use Laminas\Form\Element;
use Laminas\View\Renderer\PhpRenderer;

/**
 * @url https://www.w3.org/TR/rdf11-concepts/#section-XMLLiteral
 */
class Xml extends AbstractDataTypeRdf
{
    public function getName()
    {
        return 'xml';
    }

    public function getLabel()
    {
        return 'Xml';
    }

    public function form(PhpRenderer $view)
    {
        $element = new Element\Textarea('xml');
        $element->setAttributes([
            'class' => 'value to-require xml',
            'data-value-key' => '@value',
            /*
            'placeholder' => '<oai_dcterms:dcterms>
    <dcterms:title>Resource Description Framework (RDF)</dcterms:title>
</oai_dcterms:dcterms>',
            */
        ]);
        return $view->formTextarea($element);
    }

    public function isValid(array $valueObject)
    {
        return isset($valueObject['@value']);
    }

    public function hydrate(array $valueObject, Value $value, AbstractEntityAdapter $adapter)
    {
        $value->setValue(trim($valueObject['@value']));
        // Set defaults.
        // According to the recommandation, the language must be included
        // explicitly in the XML literal.
        // TODO Manage the language for xml.
        $value->setLang(null);
        $value->setUri(null);
        $value->setValueResource(null);
    }
}
