require('./bootstrap');

require("datatables.net-bs4");
require("datatables.net-responsive-bs4");

require("moment");
require("chart.js");
require("leaflet");

require("select2");

$(function () {
    $('[data-toggle="tooltip"]').tooltip()
});

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

window.createMap = function (containerId = 'map', ormLayer = true) {
    let map = L.map(containerId).setView([52.37707, 9.73811], 13);

    let Stadia_AlidadeSmoothDark = L.tileLayer('https://tiles.stadiamaps.com/tiles/alidade_smooth_dark/{z}/{x}/{y}{r}.png', {
        maxZoom: 20,
        attribution: '&copy; <a href="https://stadiamaps.com/">Stadia Maps</a>, &copy; <a href="https://openmaptiles.org/">OpenMapTiles</a>'
    });
    let OpenRailwayMap = L.tileLayer('https://{s}.tiles.openrailwaymap.org/standard/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, <a href="https://www.OpenRailwayMap.org">OpenRailwayMap</a>'
    });

    Stadia_AlidadeSmoothDark.addTo(map);
    if (ormLayer) {
        OpenRailwayMap.addTo(map);
    }
    return map;
}