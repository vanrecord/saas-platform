<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductImportSuccess implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    protected $reader;
    protected $user;
    protected $tenantId;
    
    public function __construct($tenantId,$user,array $datas)
    {   
        $this->tenantId = $tenantId;
        $this->user     = $user;
        $this->reader   = $datas;
    }

    public function broadcastWith()
    {
        return ['datas' => $this->reader];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-product-import.'.$this->tenantId.'.'. $this->user->id);
    }
}
