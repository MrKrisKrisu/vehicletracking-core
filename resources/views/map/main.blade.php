@extends('layout.app')

@section('title') Karte @endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card" style="margin-bottom: 10px;">
                <div class="card-body">
                    <small>Die Daten der Sichtungen und Standortinformationen wurden automatisch durch installierte und
                        mobile Scanner erfasst. Die Genauigkeit der Standortinformationen wurde künstlich auf 111 Meter
                        verschlechtert.</small>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div id="mapid" style="width: 100%; height: 700px;"></div>
                    <script>
                        $(document).ready(loadMap);

                        function loadMap() {
                            let map = L.map('mapid').setView([52.37707, 9.73811], 13);

                            L.tileLayer('https://osmcache.k118.de/carto/{z}/{x}/{y}.png', {
                                maxZoom: 18,
                                attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
                            }).addTo(map);

                            @foreach($companies as $company)
                            $.ajax({
                                url: "/data/{{$company->slug}}.json",
                                success: function (data) {
                                    $.each(data.vehicles, function (k, vehicle) {
                                        if (vehicle.last_position.length !== 0) {
                                            L.marker([vehicle.last_position.latitude, vehicle.last_position.longitude], {
                                                icon: L.icon({
                                                    iconUrl: '/img/icons/' + vehicle.type + '.svg',
                                                    iconSize: [40, 40],
                                                })
                                            })
                                                .bindPopup('<b>Fahrzeug <a href="ROUTE TO VEHICLE">' + vehicle.name +
                                                    '</a></b><br/>' + vehicle.last_position.timestamp
                                                )
                                                .addTo(map);
                                        }
                                    });
                                }
                            });
                            @endforeach

                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
@endsection
