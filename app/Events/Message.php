<?php
namespace App\Events;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class Message implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public $data;
  public function __construct($data)
  {
      $this->data = $data;
  }

  public function broadcastOn()
  {
      return ['chat'];
  }

  public function broadcastAs()
  {
      return 'message';
  }
}