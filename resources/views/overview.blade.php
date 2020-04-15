@extends('layout.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 box-shadow">
                <div class="card-body">
                    <form action="/" method="post" accept-charset="utf-8">
                        @csrf
                        <input type="text" class="form-control" id="name" placeholder="FzgNr." name="vehicle_name">
                        <h5 class="card-title">{{__('Last scans')}}</h5>
                        <table class="table">
                            @foreach ($lastScan as $scan)
                                <tr>
                                    <td>{{$scan->ssid}} (ID: {{$scan->scanDeviceId}})<br/><small>
                                            @php
                                                $d = DB::table('scans')->where('bssid', $scan->bssid)->where('vehicle_name', '<>', null)->groupBy('vehicle_name')->select('vehicle_name')->get();
                                                $arr = [];
                                                foreach ($d as $da)
                                                    foreach(explode(',', $da->vehicle_name) as $da2) {
                                                        $tr = trim($da2);
                                                        if(strlen($tr) == 4 && !in_array($tr, $arr)) {
                                                            $arr[] = $tr;
                                                            echo '<small>'.$tr.'</small><br />';
                                                        }
                                                    }

                                                $vID =  \App\Device::where('bssid', $scan->bssid)
                                                ->join('vehicles', 'devices.vehicle_id', '=', 'vehicles.id')
                                                ->first();

                                                if($vID == NULL)
                                                    echo '<br /><span style="color: #E70000;">Unverifiziert</span>';
                                                else
                                                    echo '<br /><span style="color: darkgreen;">Verifiziert ('.$vID->vehicle_name.')</span>';
                                            @endphp
                                        </small></td>
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
        <div class="col-md-4">
            <div class="card mb-4 box-shadow">
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
        <div class="col-md-4">
            <div class="card mb-4 box-shadow">
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
