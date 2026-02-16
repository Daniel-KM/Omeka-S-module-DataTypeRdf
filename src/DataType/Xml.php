<?php declare(strict_types=1);

namespace DataTypeRdf\DataType;

use Laminas\Form\Element;
use Laminas\View\Renderer\PhpRenderer;
use Omeka\Api\Adapter\AbstractEntityAdapter;
use Omeka\Api\Representation\ValueRepresentation;
use Omeka\Entity\Value;

/**
 * @link https://www.w3.org/TR/rdf11-concepts/#section-XMLLiteral
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

    public function prepareForm(PhpRenderer $view): void
    {
        parent::prepareForm($view);
        $assetUrl = $view->plugin('assetUrl');
        $view->headScript()
            ->appendFile($assetUrl('js/codemirror-data-type-rdf.js', 'DataTypeRdf'));
    }

    public function form(PhpRenderer $view)
    {
        $element = new Element\Textarea('xml');
        $element->setAttributes([
            'class' => 'value to-require xml xml-edit',
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
        return !empty($valueObject['@value'])
            && self::isWellFormed($valueObject['@value']);
    }

    public function hydrate(array $valueObject, Value $value, AbstractEntityAdapter $adapter): void
    {
        $value->setValue(trim((string) $valueObject['@value']));
        // Set defaults.
        // According to the recommandation, the language must be included
        // explicitly in the XML literal.
        // TODO Manage the language for xml.
        $value->setLang(null);
        $value->setUri(null);
        $value->setValueResource(null);
    }

    public function render(PhpRenderer $view, ValueRepresentation $value, $options = [])
    {
        $options = (array) $options;
        // Option "escape": force HTML escaping (show source code).
        // Option "native": return unescaped value (same as default for xml).
        // Option "raw" (deprecated): same as "escape".
        if (!empty($options['escape']) || !empty($options['raw'])) {
            return $view->escapeHtml((string) $value->value());
        }
        return (string) $value->value();
    }

    public function getFulltextText(PhpRenderer $view, ValueRepresentation $value)
    {
        return strip_tags((string) $value->value());
    }

    public function getJsonLd(ValueRepresentation $value)
    {
        $jsonLd = [
            '@value' => $value->value(),
            '@type' => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#XMLLiteral',
        ];
        $lang = $value->lang();
        if ($lang) {
            $jsonLd['@language'] = $lang;
        }
        return $jsonLd;
    }

    /**
     * Check if a string is a well-formed xml. Don't check validity or security.
     *
     * Require a root tag, according to the w3c spec for the lexical space of
     * the data type rdf:XMLLiteral, that is the set of all strings which are
     * well-balanced and self-contained XML content.
     * @see https://www.w3.org/TR/rdf11-concepts/#section-XMLLiteral
     */
    public static function isWellFormed($string): bool
    {
        if (!$string) {
            return false;
        }

        // Skip non scalar, except stringable object.
        if (!is_scalar($string)
            && !(is_object($string) && method_exists($string, '__toString'))
        ) {
            return false;
        }

        $string = trim((string) $string);

        // Do some quick checks.
        if (!$string
            || mb_substr($string, 0, 1) !== '<'
            || mb_substr($string, -1) !== '>'
            // TODO Is it really a quick check to use strip_tags before simplexml?
            || $string === strip_tags($string)
        ) {
            return false;
        }

        // A root is required, so the first tag must be the same than the last.
        // Namespaces are not managed, as the specs indicates that it can be
        // specified by the wrapper of the fragment.
        if (mb_substr($string, 0, 5) !== '<?xml') {
            $tag = mb_substr($string, 1, min(mb_strpos($string, ' ') ?: mb_strlen($string), mb_strpos($string, '>')) - 1);
            if (mb_substr($string, - mb_strlen($tag) - 3) !== "</$tag>") {
                return false;
            }
        }

        libxml_use_internal_errors(true);
        libxml_clear_errors();
        $simpleXml = simplexml_load_string(
            $string,
            'SimpleXMLElement',
            LIBXML_COMPACT | LIBXML_NONET
        );

        return $simpleXml !== false
            && !count(libxml_get_errors());
    }
}
