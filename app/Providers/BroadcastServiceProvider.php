<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Broadcast::routes();

       ///In this way we can setup witch user is going to the private channel
    Broadcast::channel('usr_{userId}', function ($user, $userId) {
    // $user    User model instance passed by Auth authentication
    // $userId   The userId value to which the channel rule matches
    $cuser = User::findOrFail($userId);
    return $user->id == $cuser->id;
    }); 
        /*
         * Authenticate the user's personal channel...
         */
        Broadcast::channel('App.User.*', function ($user, $userId) {
            return (int) $user->id === (int) $userId;
        });
    }
}
