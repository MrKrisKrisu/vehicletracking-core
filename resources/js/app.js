import {Notyf} from 'notyf';

require('./bootstrap');

require("datatables.net-bs4");
require("datatables.net-responsive-bs4");

require("moment");
require("chart.js");
require("leaflet");

require("select2");

require("./api/api")

document.addEventListener("DOMContentLoaded", function () {
    window.notyf = new Notyf({
        duration: 5000,
        position: {x: "right", y: "top"}
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $('[data-toggle="tooltip"]').tooltip()
});

window.createMap = function (containerId = 'map', ormLayer = true) {
    let map = L.map(containerId).setView([52.37707, 9.73811], 13);

    let OpenStreetMap_DE = L.tileLayer('https://{s}.tile.openstreetmap.de/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    });
    let OpenRailwayMap = L.tileLayer('https://{s}.tiles.openrailwaymap.org/standard/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '<a href="https://www.OpenRailwayMap.org">OpenRailwayMap</a>'
    });

    OpenStreetMap_DE.addTo(map);
    if (ormLayer) {
        OpenRailwayMap.addTo(map);
    }
    return map;
}

window.defaultIcon = new L.Icon.Default({
    iconUrl: '/images/vendor/leaflet/dist/marker-icon.png',
    shadowUrl: '/images/vendor/leaflet/dist/marker-shadow.png',
});