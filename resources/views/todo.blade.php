@extends('layout.app')

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">{{__('Device')}}</h5>
                    <p>BSSID: {{$device->bssid}}</p>
                    <p>LastSeen: {{$device->lastSeen->isoFormat('DD.MM.YYYY HH:mm')}}</p>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">{{__('Scans')}}</h5>
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
                        @foreach($scans as $scan)
                            <tr>
                                <td><small>{{$scan->ssid}}</small></td>
                                <td>
                                    <form method="POST">
                                        @csrf
                                        <input type="hidden" name="modified_scan_id" value="{{$scan->id}}"/>
                                        <textarea type="text" class="form-control" rows="{{count(explode(',', $scan->modified_vehicle_name ?? $scan->vehicle_name))}}"
                                                  name="modified_vehicle_name">{{str_replace(',', "\r\n", $scan->modified_vehicle_name ?? $scan->vehicle_name)}}</textarea>
                                        <button class="btn btn-sm btn-primary"><i class="fas fa-save"></i></button>
                                    </form>
                                </td>
                                <td>
                                    @if($scan->modified_vehicle_name != NULL)
                                        <i class="fas fa-info-circle" data-toggle="tooltip" data-placement="top"
                                           title="Originale Erfassung: {{$scan->vehicle_name}}"></i>
                                    @endif
                                </td>
                                <td>{{$scan->created_at->isoFormat('DD.MM.YYYY HH:mm')}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">{{__('Verify Device')}}</h5>
                    <form method="post" accept-charset="utf-8">
                        @csrf
                        <input type="hidden" name="bssid" value="{{$device->bssid}}"/>

                        <div class="form-group">
                            <label>Unternehmen bzw. Kategorie</label>
                            <select class="form-control" name="company_id">
                                @foreach($companies as $company)
                                    <option value="{{$company->id}}">{{$company->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Bezeichnung</label>
                            <input type="text" class="form-control" placeholder="Vehicle name"
                                   name="vehicle_name">
                        </div>
                        <button type="submit" name="action" value="save" class="btn btn-info">Save</button>
                        <button type="submit" name="action" value="notVerifiable" class="btn btn-danger">Not
                            verifiable yet
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
    </div>
@endsection
