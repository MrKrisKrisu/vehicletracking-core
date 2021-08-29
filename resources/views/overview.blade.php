@extends('layout.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4 box-shadow">
                <div class="card-body">
                    <form method="post" accept-charset="utf-8" id="main">@csrf</form>

                    <input type="text" class="form-control" id="name" placeholder="FzgNr." name="vehicle_name"
                           form="main"/>
                    <hr/>
                    @foreach(\App\ScanDevice::where('user_id', auth()->user()->id)->get() as $device)
                        <a href="/?device={{$device->id}}" class="btn btn-sm btn-primary">{{$device->name}}</a>
                    @endforeach
                    <hr/>
                    <h5 class="card-title">{{__('Last scans')}}</h5>

                    @if($lastScan->count() == 0)
                        <p class="font-weight-bold text-success">
                            <i class="fas fa-check"></i> Alles abgearbeitet.
                        </p>
                    @else

                        <table class="table">
                            @foreach ($lastScan as $scan)
                                <tr id="scan{{$scan->id}}" data-ssid="{{$scan->ssid}}"
                                    data-deviceid="{{$scan->device->id}}">
                                    <td>
                                        {{str_replace("\\x00", "", $scan->ssid)}}<br/>
                                        @isset($possibleVehicles[$scan->bssid])
                                            @if(!isset($scan->device) || !isset($scan->device->vehicle))
                                                @foreach($possibleVehicles[$scan->bssid] as $p)
                                                    <small>{{$p}}</small><br/>
                                                @endforeach
                                            @endif
                                        @endisset

                                        @if(isset($scan->device) && isset($scan->device->vehicle))
                                            <small class="text-success">Verifiziert:
                                                {{$scan->device->vehicle->vehicle_name}},
                                                {{$scan->device->vehicle->company->name}}</small><br/>
                                        @else
                                            <small class="text-danger">Unverifiziert</small><br/>
                                        @endif

                                        @isset($scan->scanDevice)
                                            <small><i class="fas fa-wifi"></i> {{$scan->scanDevice->name}}
                                            </small><br/>
                                        @endisset

                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-primary hideScan" data-id="{{$scan->id}}">
                                                <i class="fas fa-eye-slash"></i>
                                            </button>
                                            <button class="btn btn-sm btn-secondary hideDevice"
                                                    onclick="hideDevice('{{$scan->device->id}}')">
                                                <i class="fas fa-ban"></i> <i class="fas fa-code"></i>
                                            </button>
                                            @if(strlen(str_replace("\\x00", "", $scan->ssid)) > 0)
                                                <button class="btn btn-sm btn-danger hideNetwork"
                                                        onclick="hideNetwork('{{str_replace("'","\\'",$scan->ssid)}}')">
                                                    <i class="fas fa-ban"></i> <i class="fas fa-tag"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                    <td style="min-width: 50%;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="scans[{{$scan->id}}]"
                                                   form="main">
                                            <label class="form-check-label"><small>{{$scan->vehicle_name}}</small></label>
                                        </div>
                                        <span>
                                        {{$scan->created_at->diffForHumans()}}
                                        <small>({{$scan->created_at->format('H:i:s')}})</small>
                                    </span><br/>
                                        @isset($scan->latitude)
                                            <small class="text-info">
                                                <i class="fas fa-location-arrow"></i>
                                                {{$scan->latitude}}, {{$scan->longitude}}
                                            </small>
                                        @else
                                            <small class="text-danger"><i class="fas fa-times"></i> kein
                                                Standort</small>
                                        @endisset
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                        {{$lastScan->links()}}

                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        $('.hideScan').on('click', hideScan);

        function hideScan() {
            let hideButton = $(this);
            let scanId = hideButton.data('id');
            $.ajax({
                url: '{{route('scans.update')}}',
                type: "POST",
                data: {
                    id: scanId,
                    hidden: 1
                },
                success: function () {
                    $('#scan' + scanId).remove();
                }
            });
        }

        function hideNetwork(ssid, contains = 0, id = 0) {
            $('*[data-ssid="' + ssid + '"] .hideNetwork').prop("disabled", true);

            $.ajax({
                url: '{{route('ignoredNetwork.create')}}',
                type: "POST",
                data: {
                    ssid: ssid,
                    contains: contains
                },
                success: function (data) {
                    console.log(data);
                    $('*[data-ssid="' + ssid + '"]').remove();
                }
            });
        }

        function hideDevice(id) {
            $('*[data-deviceid="' + id + '"] .hideDevice').prop("disabled", true);

            $.ajax({
                url: '{{route('device.update')}}',
                type: "POST",
                data: {
                    id: id,
                    ignore: 1
                },
                success: function (data) {
                    console.log(data);
                    $('*[data-deviceid="' + id + '"]').remove();
                }
            });
        }
    </script>
@endsection
