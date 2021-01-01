@extends('layout.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card" style="margin-bottom: 10px;">
                <div class="card-body">
                    <small>Die Daten der Sichtungen und Standortinformationen wurden automatisch durch installierte und
                        mobile Scanner erfasst. Die Genauigkeit der Standortinformationen wurde künstlich auf 111 Meter verschlechtert.</small>
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

                            @foreach($positions as $position)
                            L.marker([{{round($position->latitude, 3)}}, {{round($position->longitude, 3)}}])
                                .bindPopup('<b>Fahrzeug <a href="{{route('vehicle', ['vehicle_id' => $position->vehicle_id])}}">{{$position->vehicle_name}}</a></b><br/>{{$position->timestamp}}')
                                .addTo(map);
                            @endforeach
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
@endsection
