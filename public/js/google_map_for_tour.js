var google_map = {
    init: function () {
        google_map.config = {
            location: $('#location').attr('data-info'),
            routes: $('#routes').attr('data-info'),
            placeId: $('#placeId').attr('data-info')
        };
        google_map.bindEvents();
    },
    bindEvents: function () {
        if(google_map.config.location){
            $(document).ready(google_map.generateMap());
        }
    },
    generateMap(){
        const locations = JSON.parse(google_map.config.location);
        const routes = JSON.parse(google_map.config.routes);

        let checked =[];
        for (let i=0; i < locations.length; i++){
            if(parseInt(locations[i]['date_id']) === parseInt(locations[0].date_id)){
                checked.push(i);
            }
        }
        var map = new google.maps.Map(document.getElementById('map'), {
            mapTypeControl: true,
            gestureHandling: 'cooperative',
            mapTypeControlOptions: {
                mapTypeIds: true
            }
        });

        var marker = [];
        var markers = [];
        var routes_enable = [];


        var LatLngList  = new Array();
        var bounds = new google.maps.LatLngBounds();

        //new
        let wp = [];

        setRoute();


        // // Routes
        // routes.forEach(function (route, i , routes) {
        //     var directionsDisplay = new google.maps.DirectionsRenderer();
        //     var directionsService = new google.maps.DirectionsService();
        //     directionsDisplay.setMap(map);
        //     directionsDisplay.setOptions( { suppressMarkers: true, suppressInfoWindows: true } );
        //     var waypts = [];
        //
        //     route.waypts.forEach(function (item, i, arr1) {
        //              waypts.push({
        //                  location: item.location,
        //                  stopover: item.stopover
        //              })
        //          });
        //
        //     routes_enable.push({
        //         date_id : route.date_id,
        //         directionsDis: directionsDisplay
        //     });
        //
        //     var request = {
        //              origin: route.start,
        //              destination: route.end,
        //              travelMode: google.maps.TravelMode.DRIVING,
        //              unitSystem: google.maps.UnitSystem.METRIC,
        //              waypoints: waypts,
        //              optimizeWaypoints: true,
        //              provideRouteAlternatives: true,
        //              avoidHighways: true,
        //              avoidTolls: true
        //     };
        //
        //     directionsService.route(request, function(result, status) {
        //          if (status === google.maps.DirectionsStatus.OK) {
        //              directionsDisplay.setDirections(result);
        //              var routes = result.routes;
        //              var leg = routes[0].legs;
        //              var lenght = leg[0].distance.text;
        //              var duration = leg[0].duration.text;
        //          }
        //     });
        // });

        // Markers
        locations.forEach(function (item, i, locations) {
            marker = new google.maps.Marker({
                map: map,
                place: {
                    placeId: item.placeId,
                    location: item.location
                },

                icon: {
                    url: item.icon,
                }

            });
            markers.push(marker);

            LatLngList[i] = item.location;

            var contentString =
                '<div class="google-iw">' +
                    `<span class="google-title-info-window">
                        ${item.nameService}
                    </span>` +
                    `<span>
                        <strong>Address: </strong>
                        ${item.formatted_address}
                    </span>` +
                    `<span>
                        <strong>Time of visit: </strong>
                        ${item.timeState}
                    </span>`  +
                    `<span>
                        <strong>Rating: </strong>
                        ${item.rateService}
                    </span>`  +
                    `<span>
                        <strong>Work Phone: </strong>
                        ${item.phoneService}
                    </span>`  +
                    `<span>
                        <strong>Work E-mail: </strong>
                        ${item.emailService}
                    </span>` +
                '</div>'
            ;


            var iw = new google.maps.InfoWindow({
                content : contentString
            });


            google.maps.event.addListener(marker,'click', (function(marker,content, iw){
                return function() {
                    iw.setContent(iw.content);
                    iw.open(map,marker);
                };
            })(marker,content, iw));
        });


        function setRoute(date_id, destonation_key){

            // console.log(checked,locations);

            var directionsDisplay = new google.maps.DirectionsRenderer();
            var directionsService = new google.maps.DirectionsService();
            directionsDisplay.setMap(map);
            directionsDisplay.setOptions( { suppressMarkers: true, suppressInfoWindows: true } );
            wp = [];
            destonation = locations[0];
            locations.forEach(function (item, i, arr1) {
                if(item.date_id != date_id && _.has(checked,i+1)){
                    wp.push({
                        location: item.location.lat+','+item.location.lng,
                        stopover: true
                    })
                    destonation = locations[i];

                }
            });

            routes_enable.push({
                date_id : locations[0].date_id,
                directionsDis: directionsDisplay
            });

            var request = {
                origin: locations[0].location.lat+','+locations[0].location.lng,
                destination: destonation.location.lat+','+destonation.location.lng,
                travelMode: google.maps.TravelMode.DRIVING,
                unitSystem: google.maps.UnitSystem.METRIC,
                waypoints: wp,
                optimizeWaypoints: true,
                provideRouteAlternatives: true,
                avoidHighways: true,
                avoidTolls: true
            };

            directionsService.route(request, function(result, status) {
                if (status === google.maps.DirectionsStatus.OK) {
                    directionsDisplay.setDirections(result);
                    var routes = result.routes;
                    var leg = routes[0].legs;
                    var lenght = leg[0].distance.text;
                    var duration = leg[0].duration.text;
                }
            });
        }

        // Hide or Show Markers
        function show(date_id) {
            for (let i=0; i < locations.length; i++){
                if(parseInt(locations[i]['date_id']) === parseInt(date_id)){
                    checked.push(i);
                    markers[i].setVisible(true);
                }
            }

            for (let j=0; j < routes_enable.length; j++){
                if(parseInt(routes_enable[j]['date_id']) === parseInt(date_id)){
                    routes_enable[j]['directionsDis'].setMap(map);
                }
            }
            setRoute();
        }

        function hide(date_id) {
            for (let i=0; i < locations.length; i++){
                if(parseInt(locations[i]['date_id']) === parseInt(date_id)){

                    _.remove(checked, function(n) {
                        return n == i;
                    });
                    markers[i].setVisible(false);
                }
            }
            // checked = lchecked;

            // for (let j=0; j < routes_enable.length; j++){
            //     if(parseInt(routes_enable[j]['date_id']) === parseInt(date_id)){
            //         routes_enable[j]['directionsDis'].setMap(null);
            //     }
            // }
            setRoute();
        }

        $('.checkbox_google_map').click(function (e) {
            var date_id = $(this).attr('value');
            setRoute(date_id);
            if($(this).is(':checked')){
                show(date_id);
            }else{
                hide(date_id);
            }
        });

        $('#hide_all_markers').click(function (e) {
            var checkboxes = $('.checkbox_google_map');
            var checked = true;

            for (let i = 0; i < checkboxes.length; i++){
                if(!$(checkboxes[i]).is(':checked')){
                    checked = false;
                }
            }

            if($(this).hasClass('show_all') && checked){
                for (let i = 0; i < checkboxes.length; i++){
                    let date_id = $(checkboxes[i]).attr('value');

                    checkboxes[i].checked = false;
                    hide(date_id);
                }

                $(this).attr('class', '');
            }else{

                for (let i = 0; i < checkboxes.length; i++){
                    let date_id = $(checkboxes[i]).attr('value');

                    checkboxes[i].checked = true;
                    show(date_id);
                }
                $(this).attr('class', 'show_all');
            }



        });


        // let checkboxes = $('.checkbox_google_map');
        //
        // for (let i = 0; i < checkboxes.length; i++){
        //     if($(checkboxes[i]).attr('data-info-iteration') !== 'active_item'){
        //         let date_id = $(checkboxes[i]).attr('value');
        //         console.log('dd');
        //         hide(date_id);
        //     }
        // }


        // Center the map
        for (var i = 0, LtLgLen = LatLngList.length; i < LtLgLen; i++) {
            bounds.extend (LatLngList[i]);
        }

        map.fitBounds (bounds);

    }
};
$(document).ready(setTimeout(google_map.init), 5000);