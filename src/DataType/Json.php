<?php declare(strict_types=1);

namespace DataTypeRdf\DataType;

use Laminas\Form\Element;
use Laminas\View\Renderer\PhpRenderer;
use Omeka\Api\Adapter\AbstractEntityAdapter;
use Omeka\Api\Representation\ValueRepresentation;
use Omeka\Entity\Value;

/**
 * Not yet standard (working draft rdf v1.2).
 *
 * @link https://www.w3.org/TR/rdf12-concepts/#section-json
 */
class Json extends AbstractDataTypeRdf
{
    public function getName()
    {
        return 'json';
    }

    public function getLabel()
    {
        return 'Json';
    }

    public function prepareForm(PhpRenderer $view): void
    {
        parent::prepareForm($view);
        $assetUrl = $view->plugin('assetUrl');
        $view->headScript()
            ->appendFile($assetUrl('js/codemirror-data-type-rdf.js', 'DataTypeRdf'));
    }

    public function form(PhpRenderer $view)
    {
        $element = new Element\Textarea('json');
        $element->setAttributes([
            'class' => 'value to-require json json-edit',
            'data-value-key' => '@value',
            /*
            'placeholder' => '{"alpha": "beta"}',
            */
        ]);
        return $view->formTextarea($element);
    }

    public function isValid(array $valueObject)
    {
        return isset($valueObject['@value'])
            && (trim($valueObject['@value']) === 'null' || json_decode($valueObject['@value'], true) !== null);
    }

    public function hydrate(array $valueObject, Value $value, AbstractEntityAdapter $adapter): void
    {
        $value->setValue(trim((string) $valueObject['@value']));
        // Set defaults.
        // No language for json.
        $value->setLang(null);
        $value->setUri(null);
        $value->setValueResource(null);
    }

    public function render(PhpRenderer $view, ValueRepresentation $value, $options = [])
    {
        $options = (array) $options;
        // Option "native": return unescaped value (for JS/JSON contexts).
        // Option "escape": force HTML escaping (same as default for json).
        // Option "raw" (deprecated): same as "native".
        if (!empty($options['native']) || !empty($options['raw'])) {
            return (string) $value->value();
        }
        return $view->escapeHtml((string) $value->value());
    }

    /**
     * Only scalar json is returned.
     *
     * {@inheritDoc}
     * @see \Omeka\DataType\AbstractDataType::getFulltextText()
     */
    public function getFulltextText(PhpRenderer $view, ValueRepresentation $value)
    {
        $json = (string) $value->value();
        if ($json === 'null') {
            return '';
        }
        $json = json_decode($json, true);
        return is_scalar($json)
            ? (string) $json
            : '';
    }

    public function getJsonLd(ValueRepresentation $value)
    {
        return [
            '@value' => $value->value(),
            '@type' => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#JSON',
        ];
    }
}
