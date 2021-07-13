<?php

namespace App\Events\Backups;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CleanSuccess
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $fileList;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($fileList)
    {
        $this->fileList = $fileList;
    }

}
