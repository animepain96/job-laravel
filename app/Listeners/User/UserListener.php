<?php

namespace App\Listeners\User;

use App\Jobs\User\SendMailPasswordChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UserListener
{
    public function onPasswordChanged($events)
    {
        $data['password'] = $events->password;
        $data['email'] = $events->user->email;
        $data['name'] = $events->user->name;
        $mailJob = new SendMailPasswordChanged($data);
        dispatch($mailJob);
    }

    public function subscribe($events) {
        $events->listen('App\Events\User\PasswordChanged', 'App\Listeners\User\UserListener@onPasswordChanged');
    }
}
