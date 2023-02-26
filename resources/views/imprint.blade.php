@extends('layout.app')

@section('jumbotron')
    <section class="jumbotron text-center">
        <div class="container">
            <h1 class="jumbotron-heading">Impressum</h1>
        </div>
    </section>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card" style="margin-bottom: 10px;">
                <div class="card-body">
                    <h2>Verantwortliche Stelle</h2>
                    <b>Angaben gemäß § 5 TMG</b>
                    <p>{{ config('app.imprint.name') }}<br/>
                        {{ config('app.imprint.address') }}<br/>
                        {{ config('app.imprint.city') }}<br/>
                    </p>
                    <p><strong>Kontakt:</strong><br/>
                        Telefon: {{ config('app.imprint.phone') }}<br/>
                        E-Mail: {{ config('app.imprint.email') }}<br/>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
