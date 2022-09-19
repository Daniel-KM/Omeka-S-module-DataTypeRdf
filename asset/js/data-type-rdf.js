(function($) {

    /**
     * Prepare the markup for data types.
     */
    $(document).on('o:prepare-value', function(e, dataType, value, valueObj) {
        // Prepare markup for some specific resource data types.
        // TODO Maximize in inline mode.
        // Inline maximize was hard, but Omeka still use an old version of ckeditor.
        // So the simpler way is to set mode to "document", then add some code
        // to show/hide the toolbar.
        // @link https://stackoverflow.com/questions/14177434/add-maximize-and-source-editing-plugins-for-inline-editing#answer-14181331
        if (dataType === 'html') {
            var thisValue = $(value);
            // Append the ckeditor.
            thisValue.find('.wyziwyg').each(function () {
                // Adaptation of BlockPlus / site-page-edit.js.
                var editor = null;
                const ckeditorParams = {
                    // The option "customConfig" is set in hml code.
                    on: {change: function() {
                        this.updateElement();
                    }},
                };
                if (CKEDITOR.config.customHtmlMode === 'document') {
                    editor = CKEDITOR.replace(this, ckeditorParams);
                } else {
                    editor = CKEDITOR.inline(this, ckeditorParams);
                }
                $(this).data('ckeditorInstance', editor);
            })
        } else if (dataType === 'boolean') {
            var thisValue = $(value);
            var userInput = thisValue.find('.input-value');
            var valueInput = thisValue.find('input[data-value-key="@value"]');

            // Set existing values during initial load.
            var val = valueInput.val();
            val = (val === '1' || val === 'true') ? '1' : '0';
            userInput.prop('checked', val === '1');
            userInput.val(val);
            valueInput.val(val);

            // Synchronize the user input with the true but hidden value.
            userInput.on('change', function(e) {
                var val = $(this).is(':checked') ? '1' : '0';
                userInput.val(val);
                valueInput.val(val);
            });
        }
    });

    $(document).ready( function() {

        // Initial load.
        initDataTypeRdfs();

    });

    /**
     * Prepare the rdf datatypes for the main resource template.
     *
     * There is no event in resource-form.js and common/resource-form-templates.phtml,
     * except the generic view.add.after and view.edit.after, so the default
     * form is completed dynamically during the initial load.
     */
    var initDataTypeRdfs = function() {
        var defaultSelectorAndFields = $('.resource-values.field.template .add-values.default-selector, #properties .resource-values div.default-selector');
        appendDataTypeRdfs(defaultSelectorAndFields);
    }

    /**
     * Append the configured rdf datatypes to a list of element.
     */
    var appendDataTypeRdfs = function(selector) {
        if (dataTypeRdfs.indexOf('html') !== -1) {
            $('<a>', {'class': 'add-value button o-icon-html', 'href': '#', 'data-type': 'html'})
                .text(Omeka.jsTranslate('Html'))
                .appendTo(selector);
            selector.append("\n");
        }
        if (dataTypeRdfs.indexOf('xml') !== -1) {
            $('<a>', {'class': 'add-value button o-icon-xml', 'href': '#', 'data-type': 'xml'})
                .text(Omeka.jsTranslate('Xml'))
                .appendTo(selector);
            selector.append("\n");
        }
        if (dataTypeRdfs.indexOf('boolean') !== -1) {
            $('<a>', {'class': 'add-value button o-icon-boolean', 'href': '#', 'data-type': 'boolean'})
                .text(Omeka.jsTranslate('True/False'))
                .appendTo(selector);
            selector.append("\n");
        }
    };

})(jQuery);
