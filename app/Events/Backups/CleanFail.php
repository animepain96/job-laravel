<?php

namespace App\Events\Backups;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CleanFail
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $exception;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($exception)
    {
        $this->exception = $exception;
    }
}
