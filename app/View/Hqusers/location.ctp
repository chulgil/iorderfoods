<!-- static-banner -->
<?php echo $this->element('hquser/static_banner'); ?>
<!-- /banner -->
<div class="location common-padding">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <div class="common-title clearfix">
                    <span class="yello-dash"></span>
                    <h2>Locations</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12 clearfix">
                <div class="row">
                    <div class="col-sm-8 col-sm-offset-2 col-xs-12">
                        <?php echo $this->Form->create('Store', array('inputDefaults' => array('label' => false, 'div' => false, 'required' => false, 'error' => false, 'legend' => false, 'autocomplete' => 'off'), 'enctype' => 'multipart/form-data')); ?>
                        <div class="location-search-panel">
                            <div class="col-sm-8 col-xs-6 clearfix">
                                <?php
                                echo $this->Form->input('keyword', array('class' => 'form-control custom-text', 'label' => false, 'div' => false, 'placeholder' => 'Enter Address or Zip Code'));
                                ?>
                            </div>
                            <div class="col-sm-4 col-xs-6 clearfix">
                                <?php
                                $options = array('10' => '10', '20' => '20', '50' => '50', '100' => '100', '500' => '500');
                                echo $this->Form->input('miles', array('type' => 'select', 'options' => $options, 'class' => 'form-control custom-text', 'empty' => 'Select Miles', 'style' => 'background-color:#ffffff;padding:11px 11px 12px;'));
                                ?>
                            </div>
                            <?php echo $this->Form->button('SEARCH', array('type' => 'submit', 'class' => 'contact-btn')); ?>
                        </div>
                        <?php echo $this->Form->end(); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        if (!empty($store)) {
            $location = array();
            ?>
            <div class="row">
                <div class="col-sm-12">
                    <div class="map-content">
                        <div class="map-img">
                            <div id="default" style="height:500px;"></div>
                            <div class="ext-border">
                                <img src="/img/hq/thick-border.png" alt="border">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <?php foreach ($store as $key => $stores) {
                    ?>
                    <?php
                    $address = $stores['Store']['address'] . '+' . $stores['Store']['city'] . '+' . $stores['Store']['state'] . '+' . $stores['Store']['zipcode'];
                    $address = str_replace('+', ' ', $address);
                    $temp['storeName'] = ucfirst($stores['Store']['store_name']);
                    $temp['latitude'] = $stores['Store']['latitude'];
                    $temp['logitude'] = $stores['Store']['logitude'];
                    $temp['address'] = $address;
                    array_push($location, $temp);
                    ?>
                    <div class="col-xs-12 col-sm-6 col-md-4">
                        <div class="address-card">
                            <h3><?php echo ucfirst($stores['Store']['store_name']); ?></h3>
                            <p><?php echo ucwords($stores['Store']['address']); ?></p>
                            <p><?php echo ucfirst($stores['Store']['city']) . ', ' . ucfirst($stores['Store']['state']) . ' ' . $stores['Store']['zipcode']; ?></p>
                            <p><?php echo ' T.' . $stores['Store']['phone']; ?></p>
                            <div class="address-btn">
                                <span class="showMap contact-btn" data-name="<?php echo $temp['storeName']; ?>" data-lat="<?php echo $temp['latitude']; ?>" data-long="<?php echo $temp['logitude']; ?>" data-add="<?php echo $temp['address']; ?>">VIEW MAP</span>
                                <?php if (!empty($stores['StoreSetting']['merchant_online_order_btn'])) { ?>
                                    <?php echo $this->Html->link('ORDER ONLINE', array('controller' => 'hqusers', 'action' => 'storeRedirect', $this->Encryption->encode($stores['Store']['id'])), array("class" => "contact-btn", "target" => "_blank")); ?>
                                <?php } ?>
                            </div>
                        </div>
                        <hr class="border-fix">
                    </div>
                    <?php
                }
                ?>
            </div>
        <?php } else { ?>
            <div class="row">
                <div class="col-sm-12">
                    <div class="coffee-process">
                        <p>No Location Found</p>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<?php if (!empty($store)) { ?>
    <!--    https://mapstyle.withgoogle.com/-->
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?php echo GOOGLE_MAP_API_KEY; ?>"></script>
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
                //zoom: 4,
                minZoom: 3,
                //maxZoom: zoomLevel,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                styles: style

            };
            var map = new google.maps.Map(document.getElementById("default"), myOptions);
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
                        map: map, title: storeName, position: latlngset
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
                minZoom: 3,
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
<?php } ?>