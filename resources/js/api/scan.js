'use strict';

document.addEventListener("DOMContentLoaded", function () {
    let input = document.querySelector('.model.scan .update');

    if (input) {
        input.addEventListener('change', function (obj) {
            let scanId = obj.target.closest('.model.scan').dataset.id;
            let newName = obj.target.value;
            Scan.update(scanId, {modified_vehicle_name: newName})
                .then((response) => {
                    if (response.ok) {
                        response.json().then((json) => {
                            if (json.success) {
                                notyf.success('Updated successfully');
                            } else {
                                notyf.error('Error: ' + json.message);
                            }
                        })
                    } else {
                        notyf.error(response.status + ' ' + response.statusText);
                    }
                });
        });
    }
});


window.Scan = class Scan {

    static update(id, data) {
        return API.request('/model/scan/' + id, 'PUT', data);
    }
}