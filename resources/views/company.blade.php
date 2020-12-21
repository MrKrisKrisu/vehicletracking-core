@extends('layout.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <h2>{{$company->name}}</h2>
                    <table class="table">
                        <tbody>
                            @foreach($company->vehicles as $vehicle)
                                <tr>
                                    <td>{{$vehicle->vehicle_name}}</td>
                                    <td>{{$vehicle->devices->count()}} APs</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
