<?php declare(strict_types=1);

namespace DataTypeRdf\DataType;

use Laminas\Form\Element;
use Laminas\View\Renderer\PhpRenderer;
use Omeka\Api\Adapter\AbstractEntityAdapter;
use Omeka\Api\Representation\ValueRepresentation;
use Omeka\Entity\Value;

/**
 * @link https://www.w3.org/TR/xmlschema11-2/#boolean
 */
class Boolean extends AbstractDataTypeRdf
{
    public function getName()
    {
        return 'boolean';
    }

    public function getLabel()
    {
        return 'Boolean'; // @translate
    }

    public function form(PhpRenderer $view)
    {
        $element = new Element\Checkbox('boolean-input');
        $element->setAttributes([
            'class' => 'input-value to-require boolean-input',
        ]);
        $hidden = new Element\Hidden('boolean');
        $hidden->setAttributes([
            'data-value-key' => '@value',
        ]);
        return $view->formCheckbox($element)
            . $view->formHidden($hidden);
    }

    public function isValid(array $valueObject)
    {
        return isset($valueObject['@value'])
            // See the lexical space of xsd:boolean.
            // @link https://www.w3.org/TR/xmlschema11-2/#f-booleanLexmap
            && in_array(trim((string) $valueObject['@value']), ['0', '1', 'false', 'true'], true);
    }

    public function hydrate(array $valueObject, Value $value, AbstractEntityAdapter $adapter): void
    {
        $val = in_array(trim((string) $valueObject['@value']), ['1', 'true'], true) ? '1' : '0';
        $value->setValue($val);
        // Set defaults.
        $value->setLang(null);
        $value->setUri(null);
        $value->setValueResource(null);
    }

    public function render(PhpRenderer $view, ValueRepresentation $value)
    {
        return (int) $value->value()
            ? $view->translate('true') // @translate
            : $view->translate('false'); // @translate
    }

    public function getFulltextText(PhpRenderer $view, ValueRepresentation $value)
    {
        return $this->render($view, $value);
    }

    public function getJsonLd(ValueRepresentation $value)
    {
        return [
            '@value' => (bool) (int) $value->value(),
            '@type' => 'http://www.w3.org/2001/XMLSchema#boolean',
        ];
    }
}
