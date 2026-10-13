function initField() {
    initFields('city', 'city_code', 'country');
    initFields('city_from', 'city_code_from', 'country_from');
    initFields('city_to', 'city_code_to', 'country_to');
}

function initFields(inputId, codeId, countryId) {
    if (typeof google === 'undefined' || !google.maps || !google.maps.places) return false;
    document.querySelectorAll('[id="' + inputId + '"]').forEach(function (input) {
        if (input.tagName !== 'INPUT' || input._placesAutocomplete) return;
        var scope = input.closest('form') || document;
        var code = $(scope).find('[id="' + codeId + '"]');
        var country = $(scope).find('[id="' + countryId + '"]');
        var autocomplete = new google.maps.places.Autocomplete(input, { types: ['(cities)'] });
        input._placesAutocomplete = autocomplete;
        function restrictCountry(clear) {
            if (clear) { input.value = ''; code.val(''); }
            var selected = String(country.val() || '').toLowerCase();
            autocomplete.setComponentRestrictions({ country: /^[a-z]{2}$/.test(selected) ? [selected] : [] });
            input.removeAttribute('disabled');
        }
        google.maps.event.addDomListener(input, 'keydown', function (event) {
            if (event.keyCode === 13) event.preventDefault();
        });
        restrictCountry(false);
        autocomplete.addListener('place_changed', function () {
            var place = autocomplete.getPlace();
            code.val(place.place_id || '');
            var locality = (place.address_components || []).find(function (component) {
                return component.types.includes('locality') || component.types.includes('postal_town');
            });
            if (locality || place.name) input.value = locality ? locality.long_name : place.name;
        });
        country.off('select2:select.placesAutocomplete').on('select2:select.placesAutocomplete', function () {
            restrictCountry(true);
            input.focus();
        });
    });
}

$(document).ready(function () { initField(); });
