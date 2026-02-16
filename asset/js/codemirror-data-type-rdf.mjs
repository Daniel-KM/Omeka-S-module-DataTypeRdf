/**
 * CodeMirror 6 bundle for DataTypeRdf module.
 *
 * This is the source file that gets bundled by esbuild into codemirror-data-type-rdf.js.
 * Do not load this file directly in the browser.
 *
 * Exposes window.OmekaDataTypeRdfEditor.create(textarea, options) for use by
 * data-type-rdf.js.
 *
 * Build: npm run build (from module root)
 */

import {EditorView, basicSetup} from 'codemirror';
import {xml} from '@codemirror/lang-xml';
import {json} from '@codemirror/lang-json';
import {EditorState} from '@codemirror/state';
import {history} from '@codemirror/commands';

/**
 * Create a CodeMirror 6 editor replacing a textarea.
 *
 * @param {HTMLTextAreaElement} textarea
 * @param {object} options
 * @param {string} options.format - 'xml' or 'json'
 * @param {boolean} [options.lineNumbers=false]
 * @param {boolean} [options.autofocus=false]
 * @returns {EditorView}
 */
function create(textarea, options) {
    const format = options.format || 'json';
    const showLineNumbers = options.lineNumbers || false;

    let langExtension;
    switch (format) {
        case 'xml':
            langExtension = xml();
            break;
        case 'json':
            langExtension = json();
            break;
        default:
            langExtension = [];
    }

    // Build extensions conditionally to match the old CM5 config:
    // lineWrapping, indentUnit 4, no lineNumbers (default), large undo.
    const extensions = [
        basicSetup,
        history({minDepth: 10000}),
        langExtension,
        EditorView.lineWrapping,
        EditorState.tabSize.of(4),
        // Sync content back to textarea on every change.
        EditorView.updateListener.of(update => {
            if (update.docChanged) {
                textarea.value = update.state.doc.toString();
            }
        }),
    ];

    // basicSetup includes lineNumbers; hide them when not wanted.
    if (!showLineNumbers) {
        extensions.push(EditorView.theme({
            '.cm-gutters': {display: 'none'},
        }));
    }

    const view = new EditorView({
        doc: textarea.value,
        extensions: extensions,
        parent: textarea.parentNode,
    });

    // Hide the original textarea.
    textarea.style.display = 'none';

    // Autofocus if requested.
    if (options.autofocus) {
        view.focus();
    }

    return view;
}

window.OmekaDataTypeRdfEditor = {create};
