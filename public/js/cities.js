
$(document).ready(function() {
    $('#country_begin').on('change', function() {
        var countryId = $(this).val();

        // Make AJAX request to fetch cities
        $.ajax({
            url: '/city_list/' + countryId, // Replace with your route URL
            method: 'get',
            data: {
                country_id: countryId
            },
            success: function(response) {
                $("#city_begin").html(response);
            },
            error: function(xhr, status, error) {
                console.log(error);
            }
        });
    });

    $('#country_end').on('change', function() {
        var countryId = $(this).val();

        // Make AJAX request to fetch cities
        $.ajax({
            url: '/city_list/' + countryId, // Replace with your route URL
            method: 'get',
            data: {
                country_id: countryId
            },
            success: function(response) {
                $("#city_end").html(response);
            },
            error: function(xhr, status, error) {
                console.log(error);
            }
        });
    });
});

$('#country').on('change', function() {
        var countryId = $(this).val();

        // Make AJAX request to fetch cities
        $.ajax({
            url: '/city_list/' + countryId, // Replace with your route URL
            method: 'get',
            data: {
                country_id: countryId
            },
            success: function(response) {
                $("#city").html(response);
            },
            error: function(xhr, status, error) {
                console.log(error);
            }
        });
    });