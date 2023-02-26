@extends('layout.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>
                    <i class="fa-solid fa-wifi"></i>
                    Willkommen bei {{config('app.name')}}
                </h1>

                <div class="alert alert-primary" role="alert">
                    <h4 class="alert-heading">
                        <i class="fa-solid fa-bugs"></i>
                        Alpha-Phase
                    </h4>
                    <p>Das Spiel befindet sich derzeit in der Alpha-Phase und nur ausgewählte Personen haben Zugriff
                       darauf.
                       Es wird daran gearbeitet, das Spiel zu verbessern und in naher Zukunft für alle verfügbar zu
                       machen.</p>
                    <hr>
                    <p class="mb-0">Bitte habt Geduld und bleibt dran. Sobald das Spiel für alle verfügbar ist, könnt
                                    ihr euch registrieren und daran teilnehmen.</p>
                </div>

                <p>Das Spiel basiert auf einer Idee ähnlich wie Geocaching. Anstatt jedoch physisch versteckte
                   Gegenstände zu suchen, geht es darum, BSSIDs zu entdecken, die von öffentlichen Verkehrsmitteln wie
                   Bussen und Bahnen ausgestrahlt werden.</p>
                <p>BSSIDs (Basic Service Set Identifier) sind einzigartige Kennungen für WLAN-Netzwerke, die von Geräten
                   ausgestrahlt werden. Jedes WLAN-Netzwerk hat eine eindeutige BSSID, die als seine
                   Identifikationsnummer fungiert. Dieses Spiel nutzt diese BSSIDs um einen Wettbewerb zwischen
                   Benutzern zu ermöglichen.</p>
                <p>Das Ziel des Spiels ist es, eine unterhaltsame und interaktive Möglichkeit zu schaffen, um die
                   Nutzung von öffentlichen Verkehrsmitteln zu fördern. Durch das Suchen und Finden von BSSIDs von
                   Bussen und Bahnen können Benutzer auf spielerische Weise ihre Stadt erkunden und neue Orte entdecken.
                   Gleichzeitig soll das Bewusstsein für die Bedeutung von öffentlichen Verkehrsmitteln als
                   umweltfreundliche Alternative zum Autoverkehr gestärkt werden.</p>
            </div>
            <div class="col-md-8">
                <h2><i class="fa-regular fa-circle-question"></i> So funktioniert das Spiel</h2>
                <p>Das Spiel ermöglicht es Benutzern, mit einem Scanner (z.B. einem Raspberry Pi oder einem Smartphone)
                   nach BSSIDs zu suchen, die von öffentlichen Verkehrsmitteln ausgestrahlt werden. Die Plattform
                   zeichnet die
                   gefundenen BSSIDs auf und die Spieler können ihre Ergebnisse in einer Rangliste mit anderen Spielern
                   vergleichen. Der Spieler, der die meisten BSSIDs findet steht ganz oben in der
                   Rangliste.</p>


                <h2>
                    <i class="fa-solid fa-users-between-lines"></i>
                    Teilnahme am Spiel
                </h2>
                <p>Um am Spiel teilzunehmen, können Benutzer gefundene BSSIDs mit der dazugehörigen Location und einem
                   Timestamp an eine API senden.
                   Dazu können sie entweder eigene Skripte schreiben oder ein bereits vorhandenes Python-Skript (z.B.
                   auf einem Raspberry Pi) ausführen, während sie die Gegend erkunden.
                   Die Ergebnisse werden dann automatisch in die Rangliste eingetragen und Spieler können ihre
                   Ergebnisse mit anderen vergleichen.
                </p>
            </div>

            <div class="col-md-4">
                <h2>
                    <i class="fa-solid fa-code"></i>
                    Skripte und Apps
                </h2>

                <h3>1. Python-Skript</h3>
                <p>
                    Dieses Python-Skript kann z.B. auf einem Raspberry Pi gestartet werden, welches von unterwegs alle
                    gefundenen BSSIDs speichert.
                    <br/>
                    Github:
                    <i>
                        <a href="https://github.com/MrKrisKrisu/rpi-vehicletracking" target="gh">
                            MrKrisKrisu/rpi-vehicletracking
                        </a>
                    </i>
                </p>

                <h3>2. Airport Utility</h3>
                <p>Über <a href="https://apps.apple.com/de/app/airport-dienstprogramm/id427276530" target="airport">Airport-Utility</a>
                   lässt sich eine Momentaufnahme von den aktuellen Netzwerken erstellen. Der so entstehende Export kann
                   im Webinterface manuell importiert werden.</p>

                <h3>3. Mach dein eigenes Ding</h3>
                <p>Es gibt eine <a href="javascript:alert('Dokumentation folgt')">API</a>, also bau dir dein eigenes Vorgehen und hab einfach Spaß. :)</p>
            </div>
        </div>
    </div>
@endsection
