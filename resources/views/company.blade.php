@extends('layout.app')

@section('title') {{$company->name}} - Verkehrsunternehmen @endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-3">
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
                                        @if($vehicle->devices->count() > 0)
                                            <a href="{{route('vehicle', ['vehicle_id' => $vehicle->id])}}">Anzeigen</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card mb-4 box-shadow">
                <div class="card-body">
                    <h5 class="card-title">Lokalisierungen nach Tag</h5>

                    <table class="table table-sm text-center">
                        <thead>
                            <tr>
                                <th class="text-end">Monat</th>
                                @for($day = 1; $day <= 31; $day++)
                                    <th>{{$day}}</th>
                                @endfor
                            </tr>
                        </thead>
                        @for($month = \Carbon\Carbon::now()->firstOfMonth(); $month->isAfter(\Carbon\Carbon::parse('-12 months')); $month->subMonth())
                            <tr>
                                <td class="text-end">{{$month->isoFormat('MMMM YY')}}</td>
                                @for($day = 1; $day <= $month->daysInMonth; $day++)
                                    @isset($dateCount[$month->format('Y-m-') . $day])
                                        <td class="text-success table-warning fw-bold">
                                            {{$dateCount[$month->format('Y-m-') . $day]}}
                                        </td>
                                    @else
                                        <td class="text-muted table-secondary bg-dark">0</td>
                                    @endisset
                                @endfor

                                @for($day = $day; $day <= 31; $day++)
                                    <td class="table-secondary text-muted bg-dark">-</td>
                                @endfor
                            </tr>
                        @endfor
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
