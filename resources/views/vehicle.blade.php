@extends('layout.app')

@section('jumbotron')
    <section class="jumbotron text-center">
        <div class="container">
            <h1 class="jumbotron-heading">{{__('Vehicle')}} {{$vehicle->vehicle_name}}</h1>
            <p class="lead text-muted">Company: {{$vehicle->companyName}}</p>
        </div>
    </section>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4 box-shadow">
                <div class="card-body">
                    <h5 class="card-title">{{__('Occurrences today')}}</h5>
                    <div class="d-flex justify-content-between align-items-center">
                        <table class="table">
                            @foreach ($occursToday as $occur)
                                <tr>
                                    <td>{{\Carbon\Carbon::createFromTimeStamp(strtotime($occur->created_at))->format('d.m.Y H:i')}}</td>
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
                    <h5 class="card-title">{{__('Occurrences yesterday')}}</h5>
                    <div class="d-flex justify-content-between align-items-center">
                        <table class="table">
                            @foreach ($occursYesterday as $occur)
                                <tr>
                                    <td>{{\Carbon\Carbon::createFromTimeStamp(strtotime($occur->created_at))->format('d.m.Y H:i')}}</td>
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
                    <h5 class="card-title">{{__('Older Occurrences')}}</h5>
                    <div class="d-flex justify-content-between align-items-center">
                        <table class="table">
                            @foreach ($occursOlder as $occur)
                                <tr>
                                    <td>{{\Carbon\Carbon::createFromTimeStamp(strtotime($occur->created_at))->format('d.m.Y H:i')}}</td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
