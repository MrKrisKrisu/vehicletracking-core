@extends('layout.app')

@section('title') {{$company->name}} - Verkehrsunternehmen @endsection

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
                                    <td style="font-weight: bold;">
                                        @if($vehicle->devices->count() == 0)
                                            <span class="text-danger">nicht lokalisierbar</span>
                                        @elseif($vehicle->devices->count() == 1)
                                            <span class="text-info">möglicherweise bzw. ungenau lokalisierbar</span>
                                        @elseif($vehicle->devices->count() < 10)
                                            <span class="text-success">lokalisierbar</span>
                                        @elseif($vehicle->devices->count() >= 10)
                                            <span class="text-success">sehr gut lokalisierbar</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{route('vehicle', ['vehicle_id' => $vehicle->id])}}">Anzeigen</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
