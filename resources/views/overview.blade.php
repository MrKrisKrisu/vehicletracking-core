@extends('layout.app')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4 box-shadow">
                <div class="card-body">
                    <h5 class="card-title">{{__('Last scans')}}</h5>
                    <div class="d-flex justify-content-between align-items-center">
                        <table class="table">
                            @foreach ($lastScan as $scan)
                                <tr>
                                    <td>{{$scan->ssid}}<br /><small>
                                            @php
                                                $d = DB::table('scans')->where('bssid', $scan->bssid)->where('vehicle_name', '<>', null)->groupBy('vehicle_name')->select('vehicle_name')->get();
                                                $arr = [];
                                                foreach ($d as $da)
                                                    if(!in_array($da->vehicle_name, $arr)) $arr[] = $da->vehicle_name;
                                                    echo implode(', ', $arr);

                                                $vID =  \App\Device::where('bssid', $scan->bssid)
                                                ->join('vehicles', 'devices.vehicle_id', '=', 'vehicles.id')
                                                ->first();

                                                if($vID == NULL)
                                                    echo '<br /><span style="color: #E70000;">Unverifiziert</span>';
                                                else
                                                    echo '<br /><span style="color: darkgreen;">Verifiziert ('.$vID->vehicle_name.')</span>';
                                            @endphp
                                        </small></td>
                                    <td>
                                        <form action="/" method="post" accept-charset="utf-8">
                                            @csrf
                                            <input type="hidden" name="scanID" value="{{$scan->id}}"/>
                                            <div class="contact-form">
                                                <div class="form-group">
                                                    <div class="col-sm-10">
                                                        <input type="text" class="form-control" id="name" placeholder="FzgNr." name="vehicle_name" value="{{$scan->vehicle_name}}">
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    <br/>
                                    {{\Carbon\Carbon::createFromTimeStamp(strtotime($scan->created_at))->diffForHumans()}} <small>({{\Carbon\Carbon::createFromTimeStamp(strtotime($scan->created_at))->format('H:i:s')}})</small></td>
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
