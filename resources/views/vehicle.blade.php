@extends('layout.app')

@section('jumbotron')
    <section class="jumbotron text-center">
        <div class="container">
            <h1 class="jumbotron-heading">Fahrzeug "{{$vehicle->vehicle_name}}"</h1>
            <p class="lead text-muted"><i>{{$vehicle->company->name}}</i></p>
        </div>
    </section>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card" style="margin-bottom: 10px;">
                <div class="card-body">
                    <small>Die Daten der Sichtungen und Standortinformationen wurden automatisch durch installierte und
                        mobile Scanner erfasst. Die Genauigkeit der Standortinformationen wurde künstlich auf 111 Meter verschlechtert.</small>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card mb-4 box-shadow">
                <div class="card-body">
                    <h5 class="card-title">Letzte Sichtungen</h5>
                    <div class="d-flex justify-content-between align-items-center">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Zeitpunkt</th>
                                    <th>GPS-Koordinaten</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($found as $scan)
                                    <tr>
                                        <td>{{$scan->created_at->format('d.m.Y H:i')}}</td>
                                        <td>
                                            @isset($scan->latitude)
                                                <a href="https://www.openstreetmap.org/?mlat={{round($scan->latitude, 3)}}&mlon={{round($scan->longitude, 3)}}" target="osm">
                                                    {{round($scan->latitude, 3)}}, {{round($scan->longitude, 3)}}
                                                </a>
                                            @else
                                                <span class="text-danger">nicht bekannt</span>
                                            @endisset
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-4 box-shadow">
                <div class="card-body">
                    <h5 class="card-title">Letzter bekannter Standort</h5>
                    @isset($lastPosition)
                        <div id="mapid" style="width: 100%; height: 200px;"></div>
                        <script>
                            $(document).ready(loadMap);

                            function loadMap() {
                                let map = L.map('mapid').setView([{{round($lastPosition->latitude, 3)}}, {{round($lastPosition->longitude, 3)}}], 13);

                                L.tileLayer('https://osmcache.k118.de/carto/{z}/{x}/{y}.png', {
                                    maxZoom: 18,
                                    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
                                }).addTo(map);

                                L.marker([{{round($lastPosition->latitude, 3)}}, {{round($lastPosition->longitude, 3)}}])
                                    .bindPopup('Position am {{$lastPosition->created_at->format('d.m.Y H:i')}}')
                                    .addTo(map);
                            }
                        </script>
                    @else
                        <p class="text-danger">Es ist kein Standort bekannt.</p>
                    @endisset


                </div>
            </div>
        </div>
    </div>
@endsection
