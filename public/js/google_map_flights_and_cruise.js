var google_map_v2 = {
    marker: {},
    map: {
        gestureHandling: 'cooperative',
    },
    init: function () {
        google_map_v2.config = {
            name: $(document).find('#name'),
            country_from: $(document).find('#country_from'),
            city_from: $(document).find('#city_from'),
            place_id: $(document).find('#place_id'),
            map: $(document).find('#map'),
            span_error: $(document).find('#error_map'),
            page: $('#page').attr('data-page'),
            switch_location: false,
            place_object: $('#place').attr('data-info')
        };

        google_map_v2.bindEvents();
    },

    bindEvents: function () {
        if(google_map_v2.config.page === 'show' || google_map_v2.config.page === 'edit'){
            $(document).ready(google_map_v2.generateMap());
        }

        $(document).on("click", "#btn_generate_map", function () {
            if(google_map_v2.config.page === 'create' && $.isEmptyObject(google_map_v2.map)){
                $(document).ready(google_map_v2.createMap());
            }
            else if(google_map_v2.config.page === 'edit' && google_map_v2.config.place_object === '' && $.isEmptyObject(google_map_v2.map)){
                console.log('qwe');
                $(document).ready(google_map_v2.createMap());
            }
            setTimeout(google_map_v2.updateLocation, 1000);
            return false;
        });

        $(document).on("click", '#btn_select_location', function () {
            google_map_v2.config.switch_location = true;
            return false;
        });
    },

    createMap(){
        google_map_v2.map = new google.maps.Map(document.getElementById('map'), {gestureHandling: 'cooperative',});

        google_map_v2.selectLocation(google_map_v2.map);
    },
    selectLocation(map){
        google.maps.event.addListener(map, 'click', function (event) {
            if (google_map_v2.config.switch_location) {
                const geocoder = new google.maps.Geocoder;

                geocoder.geocode({'location': event.latLng}, function (results, status) {

                    google_map_v2.marker.setMap(null);
                    google_map_v2.marker = new google.maps.Marker({
                        map: map,
                        place: {
                            placeId: results[0].place_id,
                            location: event.latLng
                        }
                    });

                    google_map_v2.config.place_id.attr('value', results[0].place_id);
                    map.setCenter(event.latLng);
                    map.setZoom(17);


                    google_map_v2.config.switch_location = false;
                });
            }
        });
    },
    generateMap() {

        if(google_map_v2.config.place_object === '' || google_map_v2.config.place_object === null){
            return;
        }

        const object_place = JSON.parse(google_map_v2.config.place_object);

        google_map_v2.map = new google.maps.Map(document.getElementById('map'), {gestureHandling: 'cooperative',});

        if (object_place.status === google.maps.places.PlacesServiceStatus.OK) {
            google_map_v2.marker = new google.maps.Marker({
                map: google_map_v2.map,
                place: {
                    placeId: object_place.result.place_id,
                    location: object_place.result.geometry.location
                }
            });
            if (google_map_v2.config.page === 'edit') {
                google_map_v2.config.place_id.attr('value', object_place.result.place_id);
            }
            google_map_v2.map.setCenter(object_place.result.geometry.location);
            google_map_v2.map.setZoom(17);
        }

        google_map_v2.selectLocation(google_map_v2.map);
    },
    updateLocation(){
        google_map_v2.config.place_id.attr('value', '');
        if(google_map_v2.config.city_from.val() !== '' && google_map_v2.config.country_from.val() !== ''){
            google_map_v2.config.map.css({'display': 'block'});
            google_map_v2.config.span_error.attr('class', '');
            google_map_v2.config.span_error.empty();

            const service = new google.maps.places.PlacesService(google_map_v2.map);

            const request = {
                location: google_map_v2.map.getCenter(),
                query: (google_map_v2.config.name.val() !== '' ? google_map_v2.config.name.val()+'+' : '')
                +(google_map_v2.config.city_from.val() !== '' ? google_map_v2.config.city_from.val()+'+' : '')
                +(google_map_v2.config.country_from.val() !== '' ? google_map_v2.config.country_from.val() : '')
            };


            service.textSearch(request, callback);

            function callback(results, status) {
                if (status === google.maps.places.PlacesServiceStatus.OK) {
                    if (!results[0].geometry) {
                        return;
                    }

                    if(google_map_v2.config.page === 'create' && !$.isEmptyObject(google_map_v2.marker)){
                        google_map_v2.marker.setMap(null);
                    }
                    else if(google_map_v2.config.page === 'edit' && !$.isEmptyObject(google_map_v2.marker)){
                        google_map_v2.marker.setMap(null);
                    }

                    google_map_v2.marker = new google.maps.Marker({
                        map: google_map_v2.map,
                        place: {
                            placeId: results[0].place_id,
                            location: results[0].geometry.location
                        }
                    });

                    google_map_v2.config.place_id.attr('value', results[0].place_id);
                    google_map_v2.map.setCenter(results[0].geometry.location);
                    google_map_v2.map.setZoom(17);
                }else{
                    google_map_v2.config.map.css({'display': 'none'});
                    google_map_v2.config.span_error.empty();
                    google_map_v2.config.span_error.attr('class', 'alert alert-danger');
                    google_map_v2.config.span_error.append('There are no results for this data');
                }
            }
        }else{
            google_map_v2.config.map.css({'display': 'none'});
            google_map_v2.config.span_error.empty();
            google_map_v2.config.span_error.attr('class', 'alert alert-danger');
            google_map_v2.config.span_error.append('Select country From and city From for generate google maps.');
        }
    },
};

$(document).ready(setTimeout(google_map_v2.init), 5000);