@extends('layout.app')

@section('content')
    <div class="row">
        <div class="col">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="list">
                            <thead>
                                <tr>
                                    <th>SSID</th>
                                    <th>Letzter Scan</th>
                                    <th class="no-sort"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($devices as $device)
                                    <tr id="device{{$device->id}}">
                                        <td>{{$device->ssid}}</td>
                                        <td>{{$device->lastScan}}</td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                        onclick="if(!confirm('Wirklich blockieren?')) return; Device.update({{$device->id}}, {blocked: 1}).then(function() {document.getElementById('device{{$device->id}}').remove(); notyf.success('Netzwerk blockiert.')});"
                                                >
                                                    <i class="fa-solid fa-ban"></i>
                                                    Blockieren
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                                        onclick="document.getElementById('device{{$device->id}}').remove()"
                                                >
                                                    <i class="fa-solid fa-eye-slash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
            $('#list').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/German.json"
                },
                "order": [[1, 'desc']],
                "pageLength": 25,
                "lengthMenu": [5, 10, 25, 50, 75, 100, "All"],
                columnDefs: [
                    {targets: 'no-sort', orderable: false}
                ]
            });

    </script>
@endsection
