import cookieCutter from 'cookie-cutter-helpers';
import moment from 'moment-timezone';
import Echo from 'laravel-echo';

window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.Pusher = require('pusher-js');
window.timezone = moment.tz.guess();
window.cookieCutter = cookieCutter;
window._ = require('lodash');

if (!cookieCutter.get('timezone')) cookieCutter.set('timezone', moment.tz.guess());

window.Echo = new Echo({
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    key: process.env.MIX_PUSHER_APP_KEY,
    broadcaster: 'pusher',
    forceTLS: true,
});