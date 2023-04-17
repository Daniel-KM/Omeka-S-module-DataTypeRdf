(function($) {

    /**
     * Prepare the markup for data types.
     */
    $(document).on('o:prepare-value o:prepare-value-annotation', function(e, dataType, value, valueObj) {
        // Prepare markup for some specific resource data types.
        // TODO Maximize in inline mode.
        // Inline maximize was hard, but Omeka still use an old version of ckeditor.
        // So the simpler way is to set mode to "document", then add some code
        // to show/hide the toolbar.
        // @link https://stackoverflow.com/questions/14177434/add-maximize-and-source-editing-plugins-for-inline-editing#answer-14181331
        if (dataType === 'html') {
            const thisValue = $(value);
            // Append the ckeditor.
            thisValue.find('.wyziwyg').each(function () {
                // Adaptation of BlockPlus / site-page-edit.js.
                var editor = null;
                const ckeditorParams = {
                    // The option "customConfig" is set in hml code.
                    on: { change: function() {
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
            const thisValue = $(value);
            const userInput = thisValue.find('.input-value');
            const valueInput = thisValue.find('input[data-value-key="@value"]');

            // Set existing values during initial load.
            // Force default val to "0".
            var val = valueInput.val();
            val = (val === '1' || val === 'true') ? '1' : '0';
            userInput.val([val]);
            valueInput.val(val);

            // Synchronize the user input with the true but hidden value.
            userInput.on('change', function() {
                const val = $(this).val();
                userInput.val([val]);
                valueInput.val(val);
            });
        }
    });

})(jQuery);
