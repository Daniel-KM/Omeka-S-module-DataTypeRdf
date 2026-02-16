<?php declare(strict_types=1);

namespace DataTypeRdf\View\Helper;

use Laminas\View\Helper\AbstractHelper;

/**
 * View helper for loading scripts necessary to use CKEditor on a page.
 *
 * Override core view helper to load a specific config.
 *
 * Used in various modules:
 * @see \Omeka\View\Helper\CkEditor
 * @see \BlockPlus\View\Helper\CkEditor
 * @see \DataTypeRdf\View\Helper\CkEditor
 */
class CkEditor extends AbstractHelper
{
    /**
     * Load the scripts necessary to use CKEditor on a page.
     */
    public function __invoke(): void
    {
        static $loaded;

        if ($loaded !== null) {
            return;
        }

        $loaded = true;

        $view = $this->getView();
        $plugins = $view->getHelperPluginManager();
        $assetUrl = $plugins->get('assetUrl');
        $escapeJs = $plugins->get('escapeJs');
        $params = $view->params();

        $isAdmin = $params->fromRoute('__ADMIN__');
        $isSiteAdmin = $params->fromRoute('__SITEADMIN__');
        $controller = $params->fromRoute('__CONTROLLER__');
        $action = $params->fromRoute('action');

        // Check if module BlockPlus is available.
        // Use class_exists without autoloading: the class is loaded only if
        // the module is active.
        $hasBlockPlus = class_exists('BlockPlus\Module', false);

        $isSiteAdminPage = $isSiteAdmin
            && $hasBlockPlus
            && ($controller === 'Page' || $controller === 'page')
            && $action === 'edit';

        $isSiteAdminResource = $isAdmin
            && in_array($controller, ['Item', 'ItemSet', 'Media', 'Annotation', 'item', 'item-set', 'media', 'annotation'])
            && ($action === 'edit' || $action === 'add');

        $script = '';
        $customConfigJs = 'js/ckeditor_config.js';
        if ($isSiteAdminPage || $isSiteAdminResource) {
            $setting = $plugins->get('setting');
            $pageOrResource = $isSiteAdminPage ? 'page' : 'resource';
            $module = $isSiteAdminPage ? 'blockplus' : 'datatyperdf';
            $htmlMode = $setting($module . '_html_mode_' . $pageOrResource);
            if ($htmlMode && $htmlMode !== 'inline') {
                $escapedMode = $escapeJs($htmlMode);
                $script = <<<JS
                    CKEDITOR.config.customHtmlMode = '$escapedMode';

                    JS;
            }

            if ($hasBlockPlus) {
                $htmlConfig = $setting($module . '_html_config_' . $pageOrResource);
                if ($htmlConfig && in_array($htmlConfig, ['standard', 'full'], true)) {
                    $customConfigJs = 'js/ckeditor_config_' . $htmlConfig . '.js';
                }
            }
        }

        // Use BlockPlus config files when available, otherwise DataTypeRdf's.
        $customConfigModule = $hasBlockPlus ? 'BlockPlus' : 'DataTypeRdf';
        $customConfigUrl = $escapeJs($assetUrl($customConfigJs, $customConfigModule));

        $script .= <<<JS
            CKEDITOR.config.customConfig = '$customConfigUrl';
            JS;

        // Check if the footnotes plugin is available (requires external
        // assets). Register it via addExternal so CKEditor knows where to
        // find it, and gracefully degrade when missing.
        $footnotesPluginPath = dirname(__DIR__, 3) . '/asset/vendor/ckeditor-footnotes/footnotes/plugin.js';
        $hasFootnotes = file_exists($footnotesPluginPath);
        if ($hasFootnotes) {
            $footnotesUrl = $escapeJs($assetUrl('vendor/ckeditor-footnotes/footnotes/', 'DataTypeRdf'));
            $script .= "\n" . <<<JS
                CKEDITOR.plugins.addExternal('footnotes', '$footnotesUrl', 'plugin.js');
                JS;
        } else {
            // Remove footnotes from extraPlugins to prevent CKEditor from
            // failing to load the editor when the plugin is missing.
            $script .= "\n" . <<<'JS'
                CKEDITOR.on('instanceCreated', function(event) {
                    var ep = event.editor.config.extraPlugins;
                    if (typeof ep === 'string') {
                        event.editor.config.extraPlugins = ep.replace(/,?footnotes/, '').replace(/^,/, '');
                    } else if (Array.isArray(ep)) {
                        event.editor.config.extraPlugins = ep.filter(function(p) { return p !== 'footnotes'; });
                    }
                });
                JS;
        }

        // The footnotes icon is not loaded automatically, so add css.
        // Only this css rule is needed.
        // The js for data-type-rdf is already loaded with the data types.
        $view->headLink()
            ->appendStylesheet($assetUrl('css/data-type-rdf.css', 'DataTypeRdf'));

        $view->headScript()
            ->appendFile($assetUrl('vendor/ckeditor/ckeditor.js', 'Omeka'));
        if ($hasFootnotes) {
            $view->headScript()
                ->appendFile($assetUrl('vendor/ckeditor-footnotes/footnotes/plugin.js', 'DataTypeRdf'), 'text/javascript', ['defer' => 'defer']);
        }
        $view->headScript()
            ->appendFile($assetUrl('vendor/ckeditor/adapters/jquery.js', 'Omeka'))
            ->appendScript($script);
    }
}
