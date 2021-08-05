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