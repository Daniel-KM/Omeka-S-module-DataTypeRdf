<?php declare(strict_types=1);

namespace DataTypeRdf\DataType;

use Laminas\Form\Element;
use Laminas\View\Renderer\PhpRenderer;
use Omeka\Api\Adapter\AbstractEntityAdapter;
use Omeka\Api\Representation\ValueRepresentation;
use Omeka\Entity\Value;
use Omeka\Stdlib\HtmlPurifier;

/**
 * @link https://www.w3.org/TR/rdf11-concepts/#section-html
 */
class Html extends AbstractDataTypeRdf
{
    /**
     * @var HtmlPurifier
     */
    protected $htmlPurifier;

    public function __construct(HtmlPurifier $htmlPurifier)
    {
        $this->htmlPurifier = $htmlPurifier;
    }

    public function getName()
    {
        return 'html';
    }

    public function getLabel()
    {
        return 'Html';
    }

    public function form(PhpRenderer $view)
    {
        // CKEditor is not enabled for item sets neither medias (the helper
        // avoids to reload it for items).
        $view->ckEditor();
        $element = new Element\Textarea('html');
        $element->setAttributes([
            'class' => 'value to-require html wyziwyg',
            'data-value-key' => '@value',
            // 'placeholder' => '<p>input <em>your</em> <strong>html</strong> content</p>', // @translate
        ]);

        $translate = $view->plugin('translate');
        $html = $view->hyperlink('', '#', ['class' => 'value-language o-icon-language', 'title' => $translate('Set language')]);
        $html .= '<input class="value-language" type="text" data-value-key="@language" aria-label="' . $translate('Language') . '">';
        $html .= $view->formTextarea($element);
        return $html;
    }

    public function isValid(array $valueObject)
    {
        return isset($valueObject['@value'])
            && $this->htmlPurifier->purify(trim((string) $valueObject['@value'])) !== '';
    }

    public function hydrate(array $valueObject, Value $value, AbstractEntityAdapter $adapter): void
    {
        $val = $this->htmlPurifier->purify(trim((string) $valueObject['@value']));
        $value->setValue($val);
        // Set defaults.
        // According to the recommandation, the language must be included
        // explicitly in the HTML literal. Nevertheless, it can be saved for
        // Omeka purposes.
        // TODO Manage the language for html.
        if (isset($valueObject['@language'])) {
            $value->setLang($valueObject['@language']);
        } else {
            $value->setLang(null);
        }
        $value->setUri(null);
        $value->setValueResource(null);
    }

    public function getFulltextText(PhpRenderer $view, ValueRepresentation $value)
    {
        return strip_tags((string) $value->value());
    }

    public function getJsonLd(ValueRepresentation $value)
    {
        $jsonLd = [
            '@value' => $value->value(),
            '@type' => 'http://www.w3.org/1999/02/22-rdf-syntax-ns#HTML',
        ];
        $lang = $value->lang();
        if ($lang) {
            $jsonLd['@language'] = $lang;
        }
        return $jsonLd;
    }
}
