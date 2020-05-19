@extends('layout.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 box-shadow">
                <div class="card-body">
                    <form action="/" method="post" accept-charset="utf-8">
                        @csrf
                        <input type="text" class="form-control" id="name" placeholder="FzgNr." name="vehicle_name">
                        <hr/>
                        <h5 class="card-title">{{__('Last scans')}}</h5>
                        <table class="table">
                            @foreach ($lastScan as $scan)
                                <tr>
                                    <td>
                                        {{$scan->ssid}} (ID: {{$scan->scanDeviceId}})<br/><small>
                                            @php
                                                $possible = \App\Http\Controllers\VehicleController::getPossibleVehicles($scan->bssid);
                                                $vID =  \App\Device::where('bssid', $scan->bssid)->first()->vehicle;
                                            @endphp

                                            @foreach($possible as $p)
                                                <small>{{$p}}</small><br/>
                                            @endforeach

                                            @isset($vID)
                                                <br/><span style="color: darkgreen;">Verifiziert ({{$vID->vehicle_name}})</span>
                                            @else
                                                <br/><span style="color: #E70000;">Unverifiziert</span>
                                            @endif
                                        </small>
                                    </td>
                                    <td style="min-width: 50%;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="scans[{{$scan->id}}]">
                                            <label class="form-check-label"><small>{{$scan->vehicle_name}}</small></label>
                                        </div>
                                        {{\Carbon\Carbon::createFromTimeStamp(strtotime($scan->created_at))->diffForHumans()}}
                                        <small>({{\Carbon\Carbon::createFromTimeStamp(strtotime($scan->created_at))->format('H:i:s')}}
                                            )</small></td>
                                </tr>
                            @endforeach
                        </table>
                    </form>
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
