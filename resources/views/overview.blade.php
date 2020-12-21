@extends('layout.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 box-shadow">
                <div class="card-body">
                    <form method="post" accept-charset="utf-8" id="main">@csrf</form>

                    <input type="text" class="form-control" id="name" placeholder="FzgNr." name="vehicle_name"
                           form="main"/>
                    <hr/>
                    @foreach(\App\ScanDevice::all() as $device)
                        <a href="/?device={{$device->id}}" class="btn btn-sm btn-primary">{{$device->name}}</a>
                    @endforeach
                    <hr/>
                    <h5 class="card-title">{{__('Last scans')}}</h5>
                    <table class="table">
                        @foreach ($lastScan as $scan)
                            <tr>
                                <td>
                                    {{str_replace("\\x00", "", $scan->ssid)}}<br/>
                                    @isset($possibleVehicles[$scan->bssid])
                                        @foreach($possibleVehicles[$scan->bssid] as $p)
                                            <small>{{$p}}</small><br/>
                                        @endforeach
                                    @endisset

                                    @if(isset($scan->device) && isset($scan->device->vehicle))
                                        <br/><small class="text-success">Verifiziert
                                            ({{$scan->device->vehicle->vehicle_name}})</small>
                                    @else
                                        <br/><small class="text-danger">Unverifiziert</small>
                                    @endif

                                    @isset($scan->scanDevice)
                                        <br/><small><i class="fas fa-wifi"></i> {{$scan->scanDevice->name}}
                                        </small>
                                    @endisset
                                    <br/>
                                    <form method="POST" action="{{route('ignoreDevice')}}"
                                          id="formIgnore{{$scan->id}}">
                                        @csrf
                                        <input type="hidden" name="bssid" value="{{$scan->bssid}}"
                                               form="formIgnore{{$scan->id}}"/>
                                        <input type="hidden" name="ssid" value="{{$scan->ssid}}"
                                               form="formIgnore{{$scan->id}}"/>
                                        <button class="btn btn-sm btn-danger" type="submit" name="ban" value="bssid"
                                                form="formIgnore{{$scan->id}}">
                                            <i class="fas fa-ban"></i> <i class="fas fa-code"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" type="submit" name="ban"
                                                value="ssid" form="formIgnore{{$scan->id}}">
                                            <i class="fas fa-ban"></i> <i class="fas fa-tag"></i>
                                        </button>
                                    </form>
                                </td>
                                <td style="min-width: 50%;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="scans[{{$scan->id}}]"
                                               form="main">
                                        <label class="form-check-label"><small>{{$scan->vehicle_name}}</small></label>
                                    </div>
                                    <p>Found: {{$scan->created_at->diffForHumans()}}
                                        <small>({{$scan->created_at->format('H:i:s')}}
                                            )</small></p>
                                    <p>Uploaded: {{$scan->updated_at->diffForHumans()}}
                                        <small>({{$scan->updated_at->format('H:i:s')}}
                                            )</small></p>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{__('Last vehicles')}}</h5>
                    <div class="d-flex justify-content-between align-items-center">
                        <table class="table" id="vehicles">
                            <thead>
                                <tr>
                                    <th>Fahrzeug</th>
                                    <th>Timestamp</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <script>
                            $('#vehicles').DataTable({
                                "language": {
                                    "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/German.json"
                                },
                                "order": [[1, 'desc']],
                                "pageLength": 5,
                                "lengthMenu": [5, 10, 25, 50, 75, 100],
                                "ajax": '/api/vehicle/last_seen',
                                columns: [
                                    {data: 'vehicle_name'},
                                    {
                                        data: {
                                            _: 'last_seen.display',
                                            sort: 'last_seen.timestamp'
                                        }
                                    }
                                ]
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{__('New Devices')}}</h5>
                    <div class="d-flex justify-content-between align-items-center">
                        <table class="table" id="new_devices">
                            <thead>
                                <tr>
                                    <th>SSID</th>
                                    <th>Timestamp</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                        <script>
                            $('#new_devices').DataTable({
                                "language": {
                                    "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/German.json"
                                },
                                "order": [[1, 'desc']],
                                "pageLength": 5,
                                "lengthMenu": [5, 10, 25, 50, 75, 100],
                                "ajax": '/api/vehicle/new',
                                columns: [
                                    {data: 'ssid'},
                                    {
                                        data: {
                                            _: 'last_seen.display',
                                            sort: 'last_seen.timestamp'
                                        }
                                    }
                                ]
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
