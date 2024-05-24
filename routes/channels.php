<?php

use Illuminate\Support\Facades\Broadcast;
use RTippin\Messenger\Broadcasting\Channels\CallChannel;
use RTippin\Messenger\Broadcasting\Channels\ProviderChannel;
use RTippin\Messenger\Broadcasting\Channels\ThreadChannel;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
  return (int) $user->id === (int) $id;
});
