<div class="container">
    <div class="container-fluid padding0">
        <div class="row margin0">
            <div class="col-xs-12 col-md-12 padding0">
                <div class="pull-right location-content">
                    <h2><span>LOCATIONS</span></h2>
                    <div class="location-content-listing">
                        <ul class="loaction-listing list-style-none clearfix">
                            <?php
                            if (!empty($store)) {
                                $location = array();
                                foreach ($store as $sData) {
                                    $address = $sData['Store']['address'] . '+' . $sData['Store']['city'] . '+' . $sData['Store']['state'] . '+' . $sData['Store']['zipcode'];
                                    $address = str_replace('+', ' ', $address);
                                    $temp['storeName'] = ucfirst($sData['Store']['store_name']);
                                    $temp['latitude'] = $sData['Store']['latitude'];
                                    $temp['logitude'] = $sData['Store']['logitude'];
                                    $temp['address'] = $address;
                                    array_push($location, $temp);
                                    ?>
                                    <li>
                                        <div>
                                            <h3><?php echo ucfirst($sData['Store']['store_name']); ?></h3>
                                            <span class="col-1"><?php echo $sData['Store']['address'] . ', ' . $sData['Store']['city'] . ', ' . $sData['Store']['state'] . ', ' . $sData['Store']['zipcode']; ?></span>
                                            <span class="col-1"><span>T.</span> <?php echo $sData['Store']['phone']; ?></span>
                                            <span class="showMap col-2 visit-link" data-name="<?php echo $temp['storeName']; ?>" data-lat="<?php echo $temp['latitude']; ?>" data-long="<?php echo $temp['logitude']; ?>" data-add="<?php echo $temp['address']; ?>">
                                                <a href="javascript:void(0)">VIEW MAP</a>
                                            </span>
                                            <?php if (!empty($sData['StoreSetting']['merchant_online_order_btn'])) { ?>
                                                <span class="col-2 visit-link">
                                                    <?php echo $this->Html->link('ORDER ONLINE', array('controller' => 'hqusers', 'action' => 'storeRedirect', $this->Encryption->encode($sData['Store']['id'])), array("target" => "_blank")); ?>
                                                </span>
                                            <?php } ?>
                                        </div>
                                    </li>
                                    <?php
                                }
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="contact-map">
    <div class="container-fluid padding0">
        <div class="row margin0">
            <div class="col-sm-12 padding0">
                <div class="map-img-wrap" id="default" style="height:600px;">
                </div>
            </div>
        </div>
    </div>
</div>
<?php if (!empty($store)) { ?>
    <!--    https://mapstyle.withgoogle.com/-->
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAP_API_KEY; ?>&sensor=false"></script>
    <script type="text/javascript">
        var zoomLevel = <?php echo (empty($logoPosition['MerchantConfiguration']['map_zoom_level']) ? 4 : $logoPosition['MerchantConfiguration']['map_zoom_level']); ?>;
        var style = [
            {
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#f5f5f5"
                    }
                ]
            },
            {
                "elementType": "labels.icon",
                "stylers": [
                    {
                        "visibility": "off"
                    }
                ]
            },
            {
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#616161"
                    }
                ]
            },
            {
                "elementType": "labels.text.stroke",
                "stylers": [
                    {
                        "color": "#f5f5f5"
                    }
                ]
            },
            {
                "featureType": "administrative.land_parcel",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#bdbdbd"
                    }
                ]
            },
            {
                "featureType": "poi",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#eeeeee"
                    }
                ]
            },
            {
                "featureType": "poi",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#757575"
                    }
                ]
            },
            {
                "featureType": "poi.park",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#e5e5e5"
                    }
                ]
            },
            {
                "featureType": "poi.park",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#9e9e9e"
                    }
                ]
            },
            {
                "featureType": "road",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#ffffff"
                    }
                ]
            },
            {
                "featureType": "road.arterial",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#757575"
                    }
                ]
            },
            {
                "featureType": "road.highway",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#dadada"
                    }
                ]
            },
            {
                "featureType": "road.highway",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#616161"
                    }
                ]
            },
            {
                "featureType": "road.local",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#9e9e9e"
                    }
                ]
            },
            {
                "featureType": "transit.line",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#e5e5e5"
                    }
                ]
            },
            {
                "featureType": "transit.station",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#eeeeee"
                    }
                ]
            },
            {
                "featureType": "water",
                "elementType": "geometry",
                "stylers": [
                    {
                        "color": "#c9c9c9"
                    }
                ]
            },
            {
                "featureType": "water",
                "elementType": "labels.text.fill",
                "stylers": [
                    {
                        "color": "#9e9e9e"
                    }
                ]
            }
        ];
        var locations =<?php echo json_encode(@$location); ?>
        //    var locations = [
        //        ['loan 1', 30.3165, 78.0322, 'address 1'],
        //        ['loan 2', 30.4165, 78.1322, 'address 2'],
        //        ['loan 3', 30.5165, 78.2322, 'address 3'],
        //        ['loan 4', 30.6165, 78.3322, 'address 4'],
        //        ['loan 5', 30.7165, 78.4322, 'address 5']
        //    ];


        if (jQuery.isEmptyObject(locations))
        {
            google.maps.event.addDomListener(window, "load", initializeEmptyMap);
        } else {
            google.maps.event.addDomListener(window, "load", initialize);
        }

        function initializeEmptyMap() {
            var latlng = new google.maps.LatLng(39.809739, -98.557899);
            var myOptions = {
                zoom: 4,
                minZoom: 3,
                center: latlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                styles: style
            };
            var map = new google.maps.Map(document.getElementById("default"), myOptions);
        }


        function initialize() {
            var myOptions = {
                scrollwheel: false,
                center: new google.maps.LatLng(39.809739, -98.557899),
                //zoom: zoomLevel,
                minZoom: 4,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                styles: style

            };
            var map = new google.maps.Map(document.getElementById("default"),
                    myOptions);
            setMarkers(map, locations);
        }


        function setMarkers(map, locations) {
            var marker, i;
            if (!$.isEmptyObject(locations)) {
                var bounds = new google.maps.LatLngBounds();
                for (i = 0; i < locations.length; i++)
                {
                    var storeName = locations[i].storeName;
                    var lat = locations[i].latitude;
                    var long = locations[i].logitude;
                    var add = locations[i].address;

                    latlngset = new google.maps.LatLng(lat, long);
                    var marker = new google.maps.Marker({
                        map: map,
                        title: storeName,
                        position: latlngset
                    });
                    bounds.extend(latlngset);
                    //map.setCenter(marker.getPosition())


                    var content = "<strong>Store Name: </strong>" + storeName + '<br/>' + "<strong>Address: </strong>" + add;

                    var infowindow = new google.maps.InfoWindow();

                    google.maps.event.addListener(marker, 'click', (function (marker, content, infowindow) {
                        return function () {
                            infowindow.setContent(content);
                            infowindow.open(map, marker);
                        };
                    })(marker, content, infowindow));
                }
                map.fitBounds(bounds);
                var listener = google.maps.event.addListener(map, "idle", function () {
                    if (map.getZoom() > 16)
                        map.setZoom(16);
                    google.maps.event.removeListener(listener);
                });
            }
        }
        $(document).on('click', '.showMap', function () {
            var storeName = $(this).data('name');
            var lat = $(this).data('lat');
            var long = $(this).data('long');
            var add = $(this).data('add');
            var myOptions = {
                //center: new google.maps.LatLng(33.890542, 151.274856),
                scrollwheel: false,
                zoom: zoomLevel,
                minZoom: 4,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                styles: style
            };
            var map = new google.maps.Map(document.getElementById("default"),
                    myOptions);

            latlngset = new google.maps.LatLng(lat, long);

            var marker = new google.maps.Marker({
                map: map, title: storeName, position: latlngset
            });

            map.setCenter(marker.getPosition())

            var content = "<strong>Store Name: </strong>" + storeName + '<br/>' + "<strong>Address: </strong>" + add

            var infowindow = new google.maps.InfoWindow()

            google.maps.event.addListener(marker, 'click', (function (marker, content, infowindow) {
                return function () {
                    infowindow.setContent(content);
                    infowindow.open(map, marker);
                };
            })(marker, content, infowindow));
            $("html, body").delay(1000).animate({
                scrollTop: $('#default').offset().top
            }, 500);
            infowindow.setContent(content);
            infowindow.open(map, marker);
        });
    </script>
<?php } //http://stackoverflow.com/questions/1556921/google-map-api-v3-set-bounds-and-center
?>
