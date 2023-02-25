@extends('layout.app')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <b>SSID</b><br/>
                            <span>{{stripcslashes($device->ssid)}}</span>
                        </div>
                        <div class="col">
                            <b>BSSID</b><br/>
                            <span>{{$device->bssid}}</span>
                        </div>
                        <div class="col">
                            <b>FirstSeen</b><br/>
                            <span>{{$device->firstSeen->format('d.m.Y H:i')}}</span>
                        </div>
                        <div class="col">
                            <b>LastSeen</b><br/>
                            <span>{{$device->lastSeen->format('d.m.Y H:i')}}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">{{__('Scans')}}</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>SSID</th>
                                <th>Erfassung</th>
                                <th></th>
                                <th>Scantime</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($device->scans->where('vehicle_name', '<>', null) as $scan)
                                <tr>
                                    <td>
                                        @if($device->ssid !== $scan->ssid)
                                            <small>{{stripcslashes($scan->ssid)}}</small>
                                        @else
                                            <small><i class="fas fa-long-arrow-alt-up text-secondary"></i></small>
                                        @endif
                                    </td>
                                    <td>
                                        <form method="POST">
                                            @csrf
                                            <input type="hidden" name="modified_scan_id" value="{{$scan->id}}"/>
                                            <textarea type="text" class="form-control"
                                                      rows="{{count(explode(',', $scan->modified_vehicle_name ?? $scan->vehicle_name))}}"
                                                      name="modified_vehicle_name">{{str_replace(',', "\r\n", $scan->modified_vehicle_name ?? $scan->vehicle_name)}}</textarea>
                                            <button class="btn btn-sm btn-primary"><i class="fas fa-save"></i>
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        @if($scan->modified_vehicle_name !== null)
                                            <i class="fas fa-info-circle" data-toggle="tooltip" data-placement="top"
                                               title="Originale Erfassung: {{$scan->vehicle_name}}"></i>
                                        @endif
                                    </td>
                                    <td>
                                        {{$scan->created_at->format('d.m.Y H:i')}}
                                        @isset($scan->scanDevice)
                                            <br/>
                                            <small><i class="fas fa-wifi"></i> {{$scan->scanDevice->name}}</small>
                                        @endisset
                                        @if($scan->latitude !== null && $scan->longitude !== null)
                                            <br/>
                                            <small>
                                                <i class="fas fa-location-arrow"></i>
                                                <a href="https://www.openstreetmap.org/?mlat={{$scan->latitude}}&mlon={{$scan->longitude}}"
                                                   target="_blank">
                                                    {{$scan->latitude}}, {{$scan->longitude}}
                                                </a>
                                            </small>
                                        @endif
                                        @if($scan?->speed !== null)
                                            <br/>
                                            <small>
                                                <i class="fas fa-tachometer-alt"></i>
                                                {{$scan->speed}} km/h
                                            </small>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Fahrzeug zuweisen</h5>
                    <form method="POST" action="{{route('vehicle.assign')}}">
                        @csrf
                        <input type="hidden" name="id" value="{{$device->id}}"/>

                        <div>
                            <select name="vehicle_id" id="vehicleList" class="form-control">
                                <option value="">bitte wählen</option>
                                @foreach(\App\Vehicle::with(['company'])->get()->sortBy(['company.name', 'vehicle_name']) as $vehicle)
                                    <option value="{{$vehicle->id}}">
                                        {{$vehicle->company->name}} // {{$vehicle->vehicle_name}}
                                        @if($vehicle->hasUic)
                                            ({{$vehicle->uic}})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <script>
                                $('#vehicleList').select2({
                                    closeOnSelect: true
                                });
                            </script>
                        </div>

                        <div class="btn-group mt-2">
                            <button type="submit" name="action" value="save" class="btn btn-sm btn-success">
                                <i class="far fa-save"></i>
                                Speichern
                            </button>
                            <button type="submit" form="formSkip" class="btn btn-sm btn-secondary">
                                <i class="far fa-clock"></i>
                                Aufschieben
                            </button>
                        </div>
                        @if(session()->has('lastVehicle'))
                            <hr/>
                            <div class="d-grid">
                                <button type="submit" name="vehicle_id" value="{{session()->get('lastVehicle')->id}}"
                                        class="btn btn-sm btn-primary">
                                    <i class="fas fa-subway"></i>
                                    {{session()->get('lastVehicle')->vehicle_name}}
                                </button>
                            </div>
                        @endif
                    </form>

                    <form method="POST" id="formSkip" action="{{route('vehicle.assign.skip')}}">
                        @csrf
                        <input type="hidden" name="id" value="{{$device->id}}"/>
                    </form>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Fahrzeug erstellen</h5>
                    <form method="POST" action="{{route('vehicle.create')}}">
                        @csrf

                        <div>
                            <label>Unternehmen bzw. Kategorie</label>
                            <select class="form-control" name="company_id" required>
                                <option value="">bitte wählen</option>
                                @foreach($companies as $company)
                                    <option value="{{$company->id}}">{{$company->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label>Fahrzeugtyp</label>
                            <select class="form-control" name="type" required>
                                <option value="">bitte wählen</option>
                                <option value="bus">Bus</option>
                                <option value="tram">Straßen-/Stadtbahn</option>
                                <option value="train">Eisenbahn</option>
                            </select>
                        </div>
                        <div>
                            <label>Bezeichnung</label>
                            <input type="text" class="form-control" placeholder="Vehicle name" required
                                   name="vehicle_name">
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="far fa-save"></i>
                            Speichern
                        </button>
                    </form>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <p>Es gibt noch <b>{{$count}} Funknetze</b> zum zuordnen.</p>
                </div>
            </div>
        </div>

        @if($locationScans->count() > 0)
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <div id="map" style="width: 100%; height: 500px;"></div>
                        <script>
                            $(document).ready(loadMap);

                            function loadMap() {
                                let map = createMap();
                                let featureGroup = L.featureGroup().addTo(map);

                                @foreach($locationScans as $scan)
                                L.marker([{{$scan->latitude}}, {{$scan->longitude}}], {
                                    icon: L.icon({
                                        iconUrl: '/img/icons/dot_red.svg',
                                        iconSize: [15, 15],
                                    })
                                })
                                    .bindPopup('Position am {{$scan->created_at->format('d.m.Y H:i')}}')
                                    .addTo(featureGroup);
                                @endforeach

                                map.fitBounds(featureGroup.getBounds());
                            }
                        </script>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
