@extends('layout.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 box-shadow">
                <div class="card-body">
                    <canvas id="chart_dayTime"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            var chart_dayTime = document.getElementById('chart_dayTime').getContext('2d');
            window.chart_dayTime = new Chart(chart_dayTime, {
                type: 'line',
                data: {
                    labels: [
                        @foreach($data as $dRow)
                            '{{$dRow->timestamp}}',
                        @endforeach
                    ],
                    datasets: [{
                        label: 'Anzahl Einkäufe',
                        backgroundColor: '#38a3a6',
                        borderWidth: 1,
                        data: [
                            @foreach($data as $dRow)
                            {{$dRow->cnt}},
                            @endforeach

                        ]
                    }]

                },
                options: {
                    responsive: true,
                    legend: {
                        display: false,
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    }
                }
            });
        });
    </script>
@endsection
