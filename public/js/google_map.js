
var google_map = {
    marker: {},
    map: {},
    init: function () {
        google_map.config = {
            name: $(document).find('#name'),
            address: $(document).find('#address_first'),
            city: $(document).find('#city'),
            country: $(document).find('#country'),
            place_id: $(document).find('#place_id'),
            map: $(document).find('#map'),
            span_error: $(document).find('#error_map'),
            page: $('#page').attr('data-page'),
            switch_location: false,
            place_object: $('#place').attr('data-info')

        };
        google_map.bindEvents();
    },
    bindEvents: function () {
        if(google_map.config.page === 'show' || google_map.config.page === 'edit'){
            $(document).ready(google_map.generateMap());
        }

        $(document).on("click", "#btn_generate_map", function (event) {
            event.preventDefault();
            if(google_map.config.page === 'create' && $.isEmptyObject(google_map.map)){
                $(document).ready(google_map.createMap());
            }else if(google_map.config.page === 'edit' && google_map.config.place_object === '' && $.isEmptyObject(google_map.map)){
                $(document).ready(google_map.createMap());
            }
            setTimeout(google_map.updateLocation, 1000);
            return false;
        });

        $(document).on("click", '#btn_select_location', function (event) {
            event.preventDefault();
            google_map.config.switch_location = true;
            $("#btn_generate_map").click();
            return false;
        });

    },
    updateLocation(){
        google_map.config.place_id.attr('value', '');
        if(google_map.config.city.val() !== '' && google_map.config.country.val() !== ''){
            google_map.config.map.css({'display': 'block'});
            google_map.config.span_error.attr('class', '');
            google_map.config.span_error.empty();

            const service = new google.maps.places.PlacesService(google_map.map);

            const request = {
                location: google_map.map.getCenter(),
                query: (google_map.config.name.val() !== '' ? google_map.config.name.val()+'+' : '')
                +(google_map.config.address.val() !== '' ? google_map.config.address.val()+'+' : '')
                +(google_map.config.city.val() !== '' ? google_map.config.city.val()+'+' : '')
                +(google_map.config.country.val() !== '' ? google_map.config.country.val() : '')
            };


            service.textSearch(request, callback);

            function callback(results, status) {
                if (status === google.maps.places.PlacesServiceStatus.OK) {
                    if (!results[0].geometry) {
                        return;
                    }

                    if(google_map.config.page === 'create' && !$.isEmptyObject(google_map.marker)){
                        google_map.marker.setMap(null);
                    }
                    else if(google_map.config.page === 'edit' && !$.isEmptyObject(google_map.marker)){
                        google_map.marker.setMap(null);
                    }

                    google_map.marker = new google.maps.Marker({
                        map: google_map.map,
                        place: {
                            placeId: results[0].place_id,
                            location: results[0].geometry.location
                        }
                    });

                    google_map.config.place_id.attr('value', results[0].place_id);
                    google_map.map.setCenter(results[0].geometry.location);
                    google_map.map.setZoom(17);
                }else{
                    google_map.config.map.css({'display': 'none'});
                    google_map.config.span_error.empty();
                    google_map.config.span_error.attr('class', 'alert alert-danger');
                    google_map.config.span_error.append('There are no results for this data');
                }
            }
        }else{
            google_map.config.map.css({'display': 'none'});
            google_map.config.span_error.empty();
            google_map.config.span_error.attr('class', 'alert alert-danger');
            google_map.config.span_error.append('Select country and city for generate google maps.');
        }
    },
    generateMap() {

        if(google_map.config.place_object === '' || google_map.config.place_object === null){
            return;
        }

        const object_place = JSON.parse(google_map.config.place_object);

        google_map.map = new google.maps.Map(document.getElementById('map'), {
            gestureHandling: 'cooperative'
        });

        if (object_place.status === google.maps.places.PlacesServiceStatus.OK) {
            google_map.marker = new google.maps.Marker({
                map: google_map.map,
                place: {
                    placeId: object_place.result.place_id,
                    location: object_place.result.geometry.location
                }
            });
            if (google_map.config.page === 'edit') {
                google_map.config.place_id.attr('value', object_place.result.place_id);
            }
            google_map.map.setCenter(object_place.result.geometry.location);
            google_map.map.setZoom(17);
        }

        google_map.selectLocation(google_map.map);
    },
    selectLocation(map){
        google.maps.event.addListener(map, 'click', function (event) {
            if (google_map.config.switch_location) {
                const geocoder = new google.maps.Geocoder;

                geocoder.geocode({'location': event.latLng}, function (results, status) {

                    google_map.marker.setMap(null);
                    google_map.marker = new google.maps.Marker({
                        map: map,
                        place: {
                            placeId: results[0].place_id,
                            location: event.latLng
                        }
                    });


                    google_map.config.place_id.attr('value', results[0].place_id);
                    map.setCenter(event.latLng);
                    map.setZoom(17);


                    google_map.config.switch_location = false;
                });
            }
        });
    },
    createMap(){
        google_map.map = new google.maps.Map(document.getElementById('map'), {});

        google_map.selectLocation(google_map.map);
    }
};
$(document).ready(setTimeout(google_map.init), 5000);