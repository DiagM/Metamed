import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
    encrypted: true,
    authEndpoint: '/api/broadcasting/auth',


});
// Check connection status
window.Echo.connector.pusher.connection.bind('connected', function() {
    console.log('Pusher connection established.');
});

// Handle disconnection
window.Echo.connector.pusher.connection.bind('disconnected', function() {
    console.log('Pusher connection disconnected.');
});

// Handle reconnection
window.Echo.connector.pusher.connection.bind('error', function(err) {
    console.error('Error connecting to Pusher:', err);
});
