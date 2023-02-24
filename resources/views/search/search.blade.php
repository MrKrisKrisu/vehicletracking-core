@extends('layout.app')

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h1 class="h5">Datenbank durchsuchen</h1>
                    <form method="POST" action="{{route('search.show')}}">
                        @csrf
                        <select name="operator" class="form-control">
                            <option value="=">exakt</option>
                            <option value=".%">beginnt mit</option>
                            <option value="%.">endet mit</option>
                            <option value="%%">enthält</option>
                        </select>

                        <input type="text" class="form-control mt-2 me-sm-2" id="inlineFormInputName2"
                               name="query" placeholder="Suchbegriff"/>

                        <button type="submit" class="btn btn-primary mt-2">Suchen</button>
                    </form>
                </div>
            </div>
        </div>

        @isset($data)
            <div class="col-md-9">
                <div class="card">
                    <div class="card-body">
                        <h1 class="h5">Suchergebnisse</h1>
                        @if($data->count() == 5000)
                            <p class="text-danger">Es werden maximal 5000 Ergebnisse angezeigt. Bitte Suche
                                spezifizieren.</p>
                        @else
                            <p>Es wurden {{$data->count()}} Netzwerke gefunden.</p>
                        @endif
                        <hr/>
                        <div id="mapid" style="width: 100%; height: 700px;"></div>
                        <script>
                            $(document).ready(loadMap);

                            function loadMap() {
                                let map = L.map('mapid').setView([52.37707, 9.73811], 13);

                                L.tileLayer('https://osmcache.k118.de/carto/{z}/{x}/{y}.png', {
                                    maxZoom: 18,
                                    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
                                }).addTo(map);

                                @foreach($data as $network)
                                L.marker([{{round($network->latitudeAvg, 3)}}, {{round($network->longitudeAvg, 3)}}])
                                    .bindPopup('<b>SSID: {{$network->ssid}}</b>@if($network->radiusMeter > 100)<br />Mehrfach gesichtet im Radius von {{$network->radiusMeter}}m.@endif')
                                    .addTo(map);
                                @endforeach
                            }
                        </script>
                    </div>
                </div>
            </div>
        @endisset
    </div>
@endsection
