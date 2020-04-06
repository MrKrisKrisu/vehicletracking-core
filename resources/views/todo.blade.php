@extends('layout.app')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4 box-shadow">
                <div class="card-body">
                    <h5 class="card-title">{{__('Device')}}</h5>
                    <p>BSSID: {{$device->bssid}}</p>
                    <p>LastSeen: {{$device->lastSeen}}</p>
                </div>
            </div>
            <div class="card mb-4 box-shadow">
                <div class="card-body">
                    <h5 class="card-title">{{__('Scans')}}</h5>
                    <div class="d-flex justify-content-between align-items-center">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>SSID</th>
                                <th>VehicleID</th>
                                <th>Scantime</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($scans as $scan)
                                <tr>
                                    <td>{{$scan->ssid}}</td>
                                    <td>{{$scan->vehicle_name}}</td>
                                    <td>{{$scan->created_at}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4 box-shadow">
                <div class="card-body">
                    <h5 class="card-title">{{__('Verify Device')}}</h5>
                    <div class="d-flex justify-content-between align-items-center">
                        <form method="post" accept-charset="utf-8">
                            @csrf
                            <input type="hidden" name="bssid" value="{{$device->bssid}}" />
                            <div class="form-group">
                                <input type="number" class="form-control" placeholder="Vehicle name" name="vehicle_name">
                            </div>
                            <button type="submit" name="action" value="save" class="btn btn-info">Save</button>
                            <button type="submit" name="action" value="notVerifiable" class="btn btn-danger">Not verifiable yet</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
</div>
@endsection
