'use strict';
window.Device = class Device {

    static update(id, data) {
        return API.request('/model/device/' + id, 'PUT', data);
    }
}