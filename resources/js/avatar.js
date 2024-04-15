 // avatar.js
console.log("avatar.js is loaded");
// For Avatar initials
var stateNum = Math.floor(Math.random() * 6);
var states = ['success', 'danger', 'warning', 'info', 'dark', 'primary', 'secondary'];
var $state = states[stateNum],
    $name = '{{ Auth::user() ? Auth::user()->name : "User" }}', // Replace 'name' with the appropriate field from your user object
    $initials = $name.match(/\b\w/g) || [],
    $output;
$initials = (($initials.shift() || '') + ($initials.pop() || '')).toUpperCase();
$output = '<span class="avatar-initial rounded-circle bg-label-' + $state + '">' + $initials + '</span>';

// Replace the existing avatar with the generated initials
$('.avatar-online img').replaceWith($output);
