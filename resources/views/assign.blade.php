@extends('layout.app')

@section('content')
    <div class="row">
        <div class="col-md-8">
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
                            @foreach ($scansToCheck as $scan)
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
                    <h5 class="card-title">{{__('Assign vehicle')}}</h5>
                    <div class="d-flex justify-content-between align-items-center">
                        <form method="POST" action="{{ route('saveAssignee') }}">
                            @csrf

                            <input id="bssid" type="hidden" name="bssid" value="{{$bssid}}" required/>

                            <div class="input-group">
                                <input id="company_id" type="text"
                                       class="form-control"
                                       name="company_id"
                                       placeholder="Company id" required autofocus/>
                            </div>
                            <div class="input-group">
                                <input id="vehicle_name" type="text"
                                       class="form-control"
                                       name="vehicle_name"
                                       placeholder="Vehicle id" required autofocus/>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                {{ __('Save') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
