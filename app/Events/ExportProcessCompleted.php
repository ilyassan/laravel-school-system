<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExportProcessCompleted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $exportId;
    public $fileName;

    public function __construct($exportId, $fileName)
    {
        $this->exportId = $exportId;
        $this->fileName = $fileName;
    }

    public function broadcastOn()
    {
        return new Channel('export.' . $this->exportId);
    }

    public function broadcastAs(): string
    {
        return "ExportCompleted";
    }
}
