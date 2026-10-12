// When a country select changes, reload the matching city select with that country's cities.
// Pairs by id: country -> city, country_from -> city_from, country_begin -> city_begin, etc.
// City text inputs (e.g. Supplier Search) are left alone.
$(document).ready(function() {
    $(document).on('change', 'select[id^="country"]', function() {
        var citySelect = $('#' + this.id.replace(/^country/, 'city'));
        if (!citySelect.length || !citySelect.is('select')) return;

        var countryCode = $(this).val();
        if (!countryCode) {
            citySelect.html('<option value="">Select a country first</option>').trigger('change');
            return;
        }

        citySelect.prop('disabled', true);
        $.ajax({
            url: '/city_list/' + encodeURIComponent(countryCode),
            method: 'get',
            dataType: 'html',
            success: function(options) {
                citySelect.html(options).val('').trigger('change');
            },
            error: function() {
                if (typeof window.appToast === 'function') {
                    window.appToast('The city list could not be loaded. Please try again.', 'error');
                }
            },
            complete: function() {
                citySelect.prop('disabled', false);
            }
        });
    });
});
