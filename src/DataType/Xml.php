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
        $plugins = $view->getHelperPluginManager();
        $assetUrl = $plugins->get('assetUrl');

        $view->headLink()
            ->appendStylesheet($assetUrl('vendor/codemirror/lib/codemirror.css', 'DataTypeRdf'))
            ->appendStylesheet($assetUrl('vendor/codemirror/addon/hint/show-hint.css', 'DataTypeRdf'));
        $view->headScript()
            ->appendFile($assetUrl('vendor/codemirror/lib/codemirror.js', 'DataTypeRdf'))
            // ->appendFile($assetUrl('vendor/codemirror/addon/display/placeholder.js', 'DataTypeRdf'))
            // Required in resource form.
            ->appendFile($assetUrl('vendor/codemirror/addon/display/autorefresh.js', 'DataTypeRdf'))
            ->appendFile($assetUrl('vendor/codemirror/addon/edit/closetag.js', 'DataTypeRdf'))
            ->appendFile($assetUrl('vendor/codemirror/addon/edit/matchtags.js', 'DataTypeRdf'))
            ->appendFile($assetUrl('vendor/codemirror/addon/edit/trailingspace.js', 'DataTypeRdf'))
            ->appendFile($assetUrl('vendor/codemirror/addon/fold/xml-fold.js', 'DataTypeRdf'))
            ->appendFile($assetUrl('vendor/codemirror/addon/hint/show-hint.js', 'DataTypeRdf'))
            ->appendFile($assetUrl('vendor/codemirror/addon/hint/xml-hint.js', 'DataTypeRdf'))
            ->appendFile($assetUrl('vendor/codemirror/mode/xml/xml.js', 'DataTypeRdf'))
            ->appendFile($assetUrl('js/data-type-rdf.js', 'DataTypeRdf'), 'text/javascript', ['defer' => 'defer']);
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
        return empty($options['raw'])
            ? (string) $value->value()
            : $view->escapeHtml($value->value());
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
     * Support strings without a root tag, according to the w3c spec for the
     * lexical space of the data type rdf:XMLLiteral, but the string must be a
     * well-balanced and self-contained content.
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

        // With CodeMirror, the root node is not required, so append one.
        // Anyway, it is required by the specification for xml fragment.
        // False is already returned above for simple strings.
        if (mb_substr($string, 0, 5) !== '<?xml') {
            $string = '<root>' . $string . '</root>';
        }

        libxml_use_internal_errors(true);
        libxml_clear_errors();
        $simpleXml = simplexml_load_string(
            $string,
            'SimpleXMLElement',
            LIBXML_COMPACT | LIBXML_NONET | LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );

        return $simpleXml !== false
            && !count(libxml_get_errors());
    }
}
