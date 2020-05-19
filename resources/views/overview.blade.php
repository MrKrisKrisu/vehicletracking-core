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
                        <table class="table">
                            @foreach ($lastVehicles as $vehicle)
                                <tr>
                                    <td>
                                        <a href="{{route('vehicle', [$vehicle->id])}}">{{$vehicle->vehicle_name}}</a>
                                    </td>
                                    <td>{{\Carbon\Carbon::createFromTimeStamp(strtotime($vehicle->created_at))->diffForHumans()}}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{__('New Devices')}}</h5>
                    <div class="d-flex justify-content-between align-items-center">
                        <table class="table">
                            @foreach ($newDevices as $device)
                                <tr>
                                    <td>{{$device->ssid}}</td>
                                    <td>{{\Carbon\Carbon::createFromTimeStamp(strtotime($device->firstSeen))->diffForHumans()}}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
