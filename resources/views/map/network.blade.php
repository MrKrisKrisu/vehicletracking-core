@extends('layout.app')

@section('title') Karte @endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div id="mapid" style="width: 100%; height: 700px;"></div>
                    <script>
                        var map = L.map('mapid').setView([52.37707, 9.73811], 19);
                        var markers = [];

                        L.tileLayer('https://osmcache.k118.de/carto/{z}/{x}/{y}.png', {
                            maxZoom: 18,
                            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
                        }).addTo(map);

                        map.on('dragend', loadMap);
                        map.on('zoomend', loadMap);

                        function loadMap() {
                            console.log('Load networks in bbox...');
                            $.ajax({
                                'url': '/api/v1/networks/' +
                                    '?maxLat=' + map.getBounds().getNorth() +
                                    '&minLon=' + map.getBounds().getWest() +
                                    '&minLat=' + map.getBounds().getSouth() +
                                    '&maxLon=' + map.getBounds().getEast(),
                                success: function (data) {
                                    $.each(data, function (i, network) {
                                        if (network.id in markers) return;
                                        markers[network.id] = L.circleMarker([network.lat, network.lon], {
                                            color: '#3388ff'
                                        }).addTo(map);
                                    });
                                }
                            });
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
@endsection
