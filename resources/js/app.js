/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the body of the page. From here, you may begin adding components to
 * the application, or feel free to tweak this setup for your needs.
 */

Vue.component('example', require('./components/Example.vue'));

const app = new Vue({
    el: 'body'
});

window._ = require('lodash');
window.$ = window.jQuery = require('jquery');
require('bootstrap-sass');

window.Pusher = require('pusher-js');
import Echo from "laravel-echo";

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '62b30867b25fc00fbe02',
    cluster: 'eu',
    encrypted: true
});


var notifications = [];

const NOTIFICATION_TYPES = {
    follow: 'App\\Notifications\\UserFollowed',
    newPost: 'App\\Notifications\\NewPost'
};
///////GET Notifications via AJAX///////
//getting the latest notifications from our API and putting them inside the dropdown
$(document).ready(function() {
    // check if there's a logged in user
    if (Laravel.userId) {
        $.get('/notifications', function(data) {
            addNotifications(data, "#notifications");
        });
        window.Echo.channel('App.User.${Laravel.userId}')
            .notification((notification) => {
                addNotifications([notification], '#notifications');
            });
    }
});

function addNotifications(newNotifications, target) {
    notifications = _.concat(notifications, newNotifications);
    // show only last 5 notifications
    notifications.slice(0, 5);
    showNotifications(notifications, target);
}
//builds a string of all notifications and puts it inside the dropdown.
//If no notifications were received, it just shows “No notifications”.
function showNotifications(notifications, target) {
    if (notifications.length) {
        var htmlElements = notifications.map(function(notification) {
            return makeNotification(notification);
        });
        $(target + 'Menu').html(htmlElements.join(''));
        $(target).addClass('has-notifications')
    } else {
        $(target + 'Menu').html('<li class="dropdown-header">No notifications</li>');
        $(target).removeClass('has-notifications');
    }
}
//helper functions to make notification strings.
// Make a single notification string
function makeNotification(notification) {
    var to = routeNotification(notification);
    var notificationText = makeNotificationText(notification);
    return '<li><a href="' + to + '">' + notificationText + '</a></li>';
}

// get the notification route based on it's type
function routeNotification(notification) {
    var to = '?read=${notification.id}';
    if (notification.type === NOTIFICATION_TYPES.follow) {
        to = 'users' + to;
    } else if (notification.type === NOTIFICATION_TYPES.newPost) {
        const postId = notification.data.post_id;
        to = 'posts/${postId}' + to;
    }
    return '/' + to;
}
// get the notification text based on it's type
function makeNotificationText(notification) {
    var text = '';
    if (notification.type === NOTIFICATION_TYPES.follow) {
        const name = notification.data.follower_name;
        text += '<strong>${{name}}</strong> followed you';
    } else if (notification.type === NOTIFICATION_TYPES.newPost) {
        const name = notification.data.following_name;
        text += '<strong>${{name}}</strong> published a post';
    }
    return text;
}

function getPusher() {
    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = true;

    var pusher = new Pusher(config('broadcasting.connections.pusher.key'), {
        cluster: 'eu'
    });

    var channel = pusher.subscribe('usr_{{Auth::user()->id}}');
    channel.bind('my-event', function(data) {
        alert(JSON.stringify(data));
    });
}