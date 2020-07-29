import cookieCutter from 'cookie-cutter-helpers';
import moment from 'moment-timezone';
import Echo from 'laravel-echo';

window._ = require('lodash');
window.axios = require('axios');
window.timezone = moment.tz.guess();
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.Pusher = require('pusher-js');

cookieCutter.set('timezone', moment.tz.guess());

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: true,
});