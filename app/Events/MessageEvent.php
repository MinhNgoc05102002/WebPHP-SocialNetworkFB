<?php
namespace App\Events;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\Channel;

class MessageEvent implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public $data;
  public $username;
  public function __construct($data,$username)
  {
      $this->data = $data;
      $this->username = $username;
  }

  public function broadcastOn()
  {
      return 'message.'.$this->username;
  }

  public function broadcastAs()
  {
      return 'messageNotification';
  }
}